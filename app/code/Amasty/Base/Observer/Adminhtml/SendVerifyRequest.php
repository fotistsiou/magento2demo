<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Observer\Adminhtml;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseValidationResponse;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Converter;
use Amasty\Base\Model\SysInfo\Provider\Collector;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * admin_system_config_changed_section_amasty_products
 * @see \Magento\Config\Model\Config::save
 */
class SendVerifyRequest implements ObserverInterface
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
     * @var Collector
     */
    private $collector;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ProcessLicenseValidationResponse
     */
    private $processLicenseValidationResponse;

    public function __construct(
        RegisteredInstanceRepository $registeredInstanceRepository,
        RequestManager $requestManager,
        Collector $collector,
        Converter $converter,
        ProcessLicenseValidationResponse $processLicenseValidationResponse
    ) {
        $this->registeredInstanceRepository = $registeredInstanceRepository;
        $this->requestManager = $requestManager;
        $this->collector = $collector;
        $this->converter = $converter;
        $this->processLicenseValidationResponse = $processLicenseValidationResponse;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $registeredInstance = $this->registeredInstanceRepository->get();
        $systemInstanceKey = $registeredInstance->getCurrentInstance()
            ? $registeredInstance->getCurrentInstance()->getSystemInstanceKey()
            : null;
        if (!$systemInstanceKey) {
            return;
        }

        $systemData = $this->collector->collect(CollectorPool::LICENCE_SERVICE_GROUP);
        if ($systemData) {
            $instanceInfo = $this->converter->convertToObject($systemData);
            $instanceInfo->setSystemInstanceKey($systemInstanceKey);
            $response = $this->requestManager->verify($instanceInfo);
            $this->processLicenseValidationResponse->process($response);
        }
    }
}
