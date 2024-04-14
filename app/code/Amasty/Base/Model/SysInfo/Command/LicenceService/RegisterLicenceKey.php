<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Converter;
use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Domain\Provider;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\Exception\LocalizedException;

class RegisterLicenceKey
{
    /**
     * @var RegisteredInstanceRepository
     */
    private $registeredInstanceRepository;

    /**
     * @var RequestManager
     */
    private $requestManager;

    /**
     * @var Provider
     */
    private $domainProvider;

    /**
     * @var Converter
     */
    private $converter;

    public function __construct(
        RegisteredInstanceRepository $registeredInstanceRepository,
        RequestManager $requestManager,
        Provider $domainProvider,
        Converter $converter
    ) {
        $this->registeredInstanceRepository = $registeredInstanceRepository;
        $this->requestManager = $requestManager;
        $this->domainProvider = $domainProvider;
        $this->converter = $converter;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute(): void
    {
        $currentDomains = $this->domainProvider->getCurrentDomains();
        $storedDomains = $this->domainProvider->getStoredDomains();
        $domains = array_diff($currentDomains, $storedDomains);
        if (!$domains) {
            return;
        }

        $instance = null;
        $instances = [];
        $registrationCompleted = true;
        try {
            foreach ($domains as $domain) {
                $registeredInstanceResponse = $this->requestManager->registerInstance($domain);
                $instanceArray = [
                    Instance::DOMAIN => $domain,
                    Instance::SYSTEM_INSTANCE_KEY => $registeredInstanceResponse->getSystemInstanceKey()
                ];
                $instance = $this->converter->convertArrayToInstance($instanceArray);
                $instances[] = $instance;
            }
        } catch (LocalizedException $exception) {
            $registrationCompleted = false;
        }

        $registeredInstance = $this->registeredInstanceRepository->get();
        $registeredInstance
            ->setCurrentInstance($instance ?? $registeredInstance->getCurrentInstance())
            ->setInstances(array_merge($registeredInstance->getInstances(), $instances));
        $this->registeredInstanceRepository->save($registeredInstance);

        if (!$registrationCompleted) {
            throw new LocalizedException(__('Registration failed, please try again later.'));
        }
    }
}
