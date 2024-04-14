<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\LicenceService\Schedule\Checker;

use Amasty\Base\Model\LicenceService\Schedule\Checker\Daily;
use Amasty\Base\Model\LicenceService\Schedule\Data\ScheduleConfig;
use Amasty\Base\Model\LicenceService\Schedule\Data\ScheduleConfigFactory;
use Amasty\Base\Model\LicenceService\Schedule\ScheduleConfigRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DailyTest extends TestCase
{
    /**
     * @var Daily
     */
    private $model;

    /**
     * @var DateTime|MockObject
     */
    private $dateTime;

    /**
     * @var ScheduleConfigFactory|MockObject
     */
    private $scheduleConfigFactory;

    /**
     * @var ScheduleConfigRepository|MockObject
     */
    private $scheduleConfigRepositoryMock;

    /**
     * @var ScheduleConfig|MockObject
     */
    private $scheduleConfigMock;

    protected function setUp(): void
    {
        $this->dateTime = $this->createMock(DateTime::class);
        $this->scheduleConfigFactory = $this->createMock(ScheduleConfigFactory::class);
        $this->scheduleConfigRepositoryMock = $this->createMock(ScheduleConfigRepository::class);
        $this->scheduleConfigMock = $this->createMock(ScheduleConfig::class);

        $this->model = new Daily(
            $this->dateTime,
            $this->scheduleConfigFactory,
            $this->scheduleConfigRepositoryMock
        );
    }

    /**
     * @param int $lastSendDate
     * @param int $currentTime
     * @param bool $result
     * @dataProvider isNeedToSendDataProvider
     * @return void
     */
    public function testIsNeedToSend(int $lastSendDate, int $currentTime, bool $result): void
    {
        $flag = 'amasty_base_daily_send_system_info';
        $this->scheduleConfigMock
            ->expects($this->once())
            ->method('getLastSendDate')
            ->willReturn($lastSendDate);
        $this->scheduleConfigRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with($flag)
            ->willReturn($this->scheduleConfigMock);
        $this->dateTime
            ->expects($this->once())
            ->method('gmtTimestamp')
            ->willReturn($currentTime);

        $this->assertEquals($result, $this->model->isNeedToSend($flag));
    }

    public function isNeedToSendDataProvider(): array
    {
        return [
            [
                ScheduleConfig::LAST_SEND_DATE => 1640198000,
                1640198457,
                false
            ],
            [
                ScheduleConfig::LAST_SEND_DATE => 1640000000,
                1640198457,
                true
            ]
        ];
    }
}
