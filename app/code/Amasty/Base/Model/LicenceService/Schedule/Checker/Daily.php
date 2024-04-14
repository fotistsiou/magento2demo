<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\LicenceService\Schedule\Checker;

use Amasty\Base\Model\LicenceService\Schedule\Data\ScheduleConfigFactory;
use Amasty\Base\Model\LicenceService\Schedule\ScheduleConfigRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Daily implements SenderCheckerInterface
{
    public const TIME_INTERVAL = 86400;

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

    public function __construct(
        DateTime $dateTime,
        ScheduleConfigFactory $scheduleConfigFactory,
        ScheduleConfigRepository $scheduleConfigRepository
    ) {
        $this->dateTime = $dateTime;
        $this->scheduleConfigFactory = $scheduleConfigFactory;
        $this->scheduleConfigRepository = $scheduleConfigRepository;
    }

    public function isNeedToSend(string $flag): bool
    {
        $currentTime = $this->dateTime->gmtTimestamp();
        try {
            $scheduleConfig = $this->scheduleConfigRepository->get($flag);
        } catch (\InvalidArgumentException $exception) {
            $scheduleConfig = $this->scheduleConfigFactory->create();
            $scheduleConfig->setLastSendDate($currentTime);
            $this->scheduleConfigRepository->save($flag, $scheduleConfig);

            return true;
        }
        $isNeedToSend = $currentTime > $scheduleConfig->getLastSendDate() + self::TIME_INTERVAL;
        if ($isNeedToSend) {
            $scheduleConfig->setLastSendDate($currentTime);
            $this->scheduleConfigRepository->save($flag, $scheduleConfig);
        }

        return $isNeedToSend;
    }
}
