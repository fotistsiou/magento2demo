<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Cron;

use Amasty\Base\Model\LicenceService\Schedule\Checker\Schedule;
use Amasty\Base\Model\LicenceService\Schedule\ScheduleConfigRepository;
use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\Exception\LocalizedException;

class InstanceRegistration
{
    public const FLAG_KEY = 'amasty_base_instance_registration';

    /**
     * @var Schedule
     */
    private $scheduleChecker;

    /**
     * @var RegisterLicenceKey
     */
    private $registerLicenceKey;

    /**
     * @var ScheduleConfigRepository
     */
    private $scheduleConfigRepository;

    /**
     * @var RegisteredInstanceRepository
     */
    private $registeredInstanceRepository;

    public function __construct(
        Schedule $scheduleChecker,
        RegisterLicenceKey $registerLicenceKey,
        ScheduleConfigRepository $scheduleConfigRepository,
        RegisteredInstanceRepository $registeredInstanceRepository
    ) {
        $this->scheduleChecker = $scheduleChecker;
        $this->registerLicenceKey = $registerLicenceKey;
        $this->scheduleConfigRepository = $scheduleConfigRepository;
        $this->registeredInstanceRepository = $registeredInstanceRepository;
    }

    public function execute()
    {
        $registeredInstance = $this->registeredInstanceRepository->get();
        $systemInstanceKey = $registeredInstance->getCurrentInstance()
            ? $registeredInstance->getCurrentInstance()->getSystemInstanceKey()
            : null;
        if ($systemInstanceKey) {
            return;
        }
        try {
            if ($this->scheduleChecker->isNeedToSend(self::FLAG_KEY)) {
                $this->registerLicenceKey->execute();
            }
        } catch (LocalizedException $exception) {
            $scheduleConfig = $this->scheduleConfigRepository->get(self::FLAG_KEY);
            if (empty($scheduleConfig->getTimeIntervals())) {
                $scheduleConfig->addData($this->scheduleChecker->getScheduleConfig());
                $this->scheduleConfigRepository->save(self::FLAG_KEY, $scheduleConfig);
            }
        }
    }
}
