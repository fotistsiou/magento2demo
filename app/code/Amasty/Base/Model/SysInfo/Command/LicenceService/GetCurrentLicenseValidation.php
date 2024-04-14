<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService;

use Amasty\Base\Model\LicenceService\Schedule\ScheduleConfigRepository;
use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse\DefaultResponseProvider;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidationFactory;
use Amasty\Base\Model\SysInfo\LicenseValidationRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;

class GetCurrentLicenseValidation
{
    public const TIME_INTERVAL = 604800; //7 days

    /**
     * @var ProcessLicenseValidationResponse
     */
    private $processLicenseValidationResponse;

    /**
     * @var LicenseValidationRepository
     */
    private $licenseValidationRepository;

    /**
     * @var ScheduleConfigRepository
     */
    private $scheduleConfigRepository;

    /**
     * @var DefaultResponseProvider
     */
    private $defaultResponseProvider;

    /**
     * @var LicenseValidationFactory
     */
    private $licenseValidationFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var LicenseValidation
     */
    private $currentLicense;

    public function __construct(
        ProcessLicenseValidationResponse $processLicenseValidationResponse,
        LicenseValidationRepository $licenseValidationRepository,
        ScheduleConfigRepository $scheduleConfigRepository,
        DefaultResponseProvider $defaultResponseProvider,
        LicenseValidationFactory $licenseValidationFactory,
        DateTime $dateTime
    ) {
        $this->processLicenseValidationResponse = $processLicenseValidationResponse;
        $this->licenseValidationRepository = $licenseValidationRepository;
        $this->scheduleConfigRepository = $scheduleConfigRepository;
        $this->defaultResponseProvider = $defaultResponseProvider;
        $this->licenseValidationFactory = $licenseValidationFactory;
        $this->dateTime = $dateTime;
    }

    public function get(): LicenseValidation
    {
        if (!$this->currentLicense) {
            $this->currentLicense = $this->prepareCurrentLicense();
        }

        return $this->currentLicense;
    }

    private function prepareCurrentLicense(): LicenseValidation
    {
        try {
            $scheduleConfig = $this->scheduleConfigRepository->get(ProcessLicenseValidationResponse::FLAG_NAME);
        } catch (\InvalidArgumentException $exception) {
            $licenseValidation = $this->licenseValidationFactory->create();
            $licenseValidation->setIsNeedCheckLicense(false);

            return $licenseValidation;
        }

        $currentLicense = $this->licenseValidationRepository->get();
        if ($currentLicense->isNeedCheckLicense() === false) {
            return $currentLicense;
        }

        $currentTime = $this->dateTime->gmtTimestamp();
        $isValid = $currentTime < $scheduleConfig->getLastSendDate() + self::TIME_INTERVAL;
        if (!$isValid) {
            $this->processLicenseValidationResponse->process(
                $this->defaultResponseProvider->getVerificationProcessError()
            );
        }

        return $this->licenseValidationRepository->get();
    }
}
