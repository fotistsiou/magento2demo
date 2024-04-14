<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\LicenceService\Schedule\Data;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ExtensibleDataInterface;

class ScheduleConfig extends SimpleDataObject implements ExtensibleDataInterface
{
    public const LAST_SEND_DATE = 'last_send_date';
    public const TIME_INTERVALS = 'time_intervals';
    public const IS_NEED_TO_SHOW_NOTIFICATION = 'is_need_to_how_notification';

    /**
     * @return int|null
     */
    public function getLastSendDate(): ?int
    {
        return $this->getData(self::LAST_SEND_DATE);
    }

    /**
     * @param int|null $lastSendDate
     * @return $this
     */
    public function setLastSendDate(?int $lastSendDate): self
    {
        return $this->setData(self::LAST_SEND_DATE, $lastSendDate);
    }

    /**
     * @return int[]|null
     */
    public function getTimeIntervals(): ?array
    {
        return $this->getData(self::TIME_INTERVALS);
    }

    /**
     * @param int[]|null $timeIntervals
     * @return $this
     */
    public function setTimeIntervals(?array $timeIntervals): self
    {
        return $this->setData(self::TIME_INTERVALS, $timeIntervals);
    }

    /**
     * @return bool
     */
    public function isNeedToShowNotification(): bool
    {
        return (bool)$this->getData(self::IS_NEED_TO_SHOW_NOTIFICATION);
    }

    /**
     * @param bool $isNeedToShowNotification
     * @return $this
     */
    public function setIsNeedToShowNotification(bool $isNeedToShowNotification): self
    {
        return $this->setData(self::IS_NEED_TO_SHOW_NOTIFICATION, $isNeedToShowNotification);
    }
}
