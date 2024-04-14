<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Cron;

use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;
use Amasty\Base\Model\LicenceService\Schedule\Checker\Daily;

class DailySendSystemInfo
{
    public const FLAG_KEY = 'amasty_base_daily_send_system_info';

    /**
     * @var SendSysInfo
     */
    private $sysInfo;

    /**
     * @var Daily
     */
    private $dailyChecker;

    public function __construct(
        SendSysInfo $sysInfo,
        Daily $dailyChecker
    ) {
        $this->sysInfo = $sysInfo;
        $this->dailyChecker = $dailyChecker;
    }

    public function execute()
    {
        if ($this->dailyChecker->isNeedToSend(self::FLAG_KEY)) {
            $this->sysInfo->execute();
        }
    }
}
