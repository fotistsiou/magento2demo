<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Domain;

use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\UrlInterface;

class Provider
{
    /**
     * @var RegisteredInstanceRepository
     */
    private $registeredInstanceRepository;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        RegisteredInstanceRepository $registeredInstanceRepository,
        UrlInterface $url
    ) {
        $this->registeredInstanceRepository = $registeredInstanceRepository;
        $this->url = $url;
    }

    public function getStoredDomains(): array
    {
        $domains = [];
        $registeredInstance = $this->registeredInstanceRepository->get();
        if ($registeredInstance->getInstances()) {
            foreach ($registeredInstance->getInstances() as $instance) {
                $domains[] = $instance->getDomain();
            }
        }

        return $domains;
    }

    public function getCurrentDomains(): array
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        $baseUrl = parse_url($this->url->getBaseUrl(), PHP_URL_HOST);

        return [$baseUrl];
    }
}
