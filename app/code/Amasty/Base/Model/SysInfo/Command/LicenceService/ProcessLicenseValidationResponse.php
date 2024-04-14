<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService;

use Amasty\Base\Model\LicenceService\Schedule\Data\ScheduleConfigFactory;
use Amasty\Base\Model\LicenceService\Schedule\ScheduleConfigRepository;
use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse\Converter;
use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse\DefaultResponseProvider;
use Amasty\Base\Model\SysInfo\LicenseValidationRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ProcessLicenseValidationResponse
{
    public const FLAG_NAME = 'amasty_base_license_validation_response_received';

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var ScheduleConfigFactory
     */
    private $scheduleConfigFactory;

    /**
     * @var ScheduleConfigRepository
     */
    private $scheduleConfigRepository;

    /**
     * @var LicenseValidationRepository
     */
    private $licenseValidationRepository;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var DefaultResponseProvider
     */
    private $defaultResponseProvider;

    public function __construct(
        DateTime $dateTime,
        ScheduleConfigFactory $scheduleConfigFactory,
        ScheduleConfigRepository $scheduleConfigRepository,
        LicenseValidationRepository $licenseValidationRepository,
        Converter $converter,
        DefaultResponseProvider  $defaultResponseProvider
    ) {
        $this->dateTime = $dateTime;
        $this->scheduleConfigFactory = $scheduleConfigFactory;
        $this->scheduleConfigRepository = $scheduleConfigRepository;
        $this->licenseValidationRepository = $licenseValidationRepository;
        $this->converter = $converter;
        $this->defaultResponseProvider = $defaultResponseProvider;
    }

    public function process(SimpleDataObject $response, bool $updateTime = true): void
    {
        $currentLicense = $this->licenseValidationRepository->get(true);
        $isError = preg_match('/5\d{2}/', (string)$response->getData('code')); //500 error
        if ($isError) {
            if ($currentLicense->isNeedCheckLicense()) {
                $response = $this->defaultResponseProvider->getServiceUnavailable();
                $updateTime = true;
            } else {
                return;
            }
        }
        $responseArray = $response->toArray();
        unset($responseArray['code']);
        if (count($responseArray) < 1) {
            return;
        }

        $licenseValidation = $this->converter->convertArrayToEntity($responseArray);
        $this->licenseValidationRepository->save($licenseValidation);
        if ($updateTime) {
            $this->updateTime();
        }
    }

    private function updateTime(): void
    {
        $currentTime = $this->dateTime->gmtTimestamp();
        try {
            $scheduleConfig = $this->scheduleConfigRepository->get(self::FLAG_NAME);
        } catch (\InvalidArgumentException $exception) {
            $scheduleConfig = $this->scheduleConfigFactory->create();
        }
        $scheduleConfig->setLastSendDate($currentTime);
        $this->scheduleConfigRepository->save(self::FLAG_NAME, $scheduleConfig);
    }
}
