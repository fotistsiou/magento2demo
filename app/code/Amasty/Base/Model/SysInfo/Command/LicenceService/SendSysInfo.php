<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\ChangedData\Persistor as ChangedDataPersistor;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Converter;
use Amasty\Base\Model\SysInfo\Provider\Collector;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;

class SendSysInfo
{
    public const PING_REQUEST_GROUP = 'pingRequest';

    /**
     * @var RegisteredInstanceRepository
     */
    private $registeredInstanceRepository;

    /**
     * @var ChangedDataPersistor
     */
    private $changedDataPersistor;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var RequestManager
     */
    private $requestManager;

    /**
     * @var Collector
     */
    private $collector;

    /**
     * @var ProcessLicenseValidationResponse
     */
    private $processLicenseValidationResponse;

    public function __construct(
        RegisteredInstanceRepository $registeredInstanceRepository,
        ChangedDataPersistor $changedDataPersistor,
        Converter $converter,
        RequestManager $requestManager,
        Collector $collector = null,
        ProcessLicenseValidationResponse $processLicenseValidationResponse = null
    ) {
        $this->registeredInstanceRepository = $registeredInstanceRepository;
        $this->changedDataPersistor = $changedDataPersistor;
        $this->converter = $converter;
        $this->requestManager = $requestManager;
        $this->collector = $collector
            ?? ObjectManager::getInstance()->get(Collector::class);
        $this->processLicenseValidationResponse = $processLicenseValidationResponse
            ?? ObjectManager::getInstance()->get(ProcessLicenseValidationResponse::class);
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function execute(): void
    {
        $registeredInstance = $this->registeredInstanceRepository->get();
        $systemInstanceKey = $registeredInstance->getCurrentInstance()
            ? $registeredInstance->getCurrentInstance()->getSystemInstanceKey()
            : null;
        if (!$systemInstanceKey) {
            return;
        }

        $changedData = $this->changedDataPersistor->get();
        if ($changedData) {
            $instanceInfo = $this->converter->convertToObject(
                $this->collector->collect(CollectorPool::LICENCE_SERVICE_GROUP)
            );
            $instanceInfo->setSystemInstanceKey($systemInstanceKey);
            try {
                $this->requestManager->updateInstanceInfo($instanceInfo);
                $this->changedDataPersistor->save($changedData);
            } catch (LocalizedException $exception) {
                throw $exception;
            }
        } else {
            $instanceInfo = $this->converter->convertToObject(
                $this->collector->collect(self::PING_REQUEST_GROUP)
            );
            $instanceInfo->setSystemInstanceKey($systemInstanceKey);
            $response = $this->requestManager->pingRequest($instanceInfo);
            $this->processLicenseValidationResponse->process($response);
        }
    }
}
