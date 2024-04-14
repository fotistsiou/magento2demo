<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\LicenceService\Schedule\Checker;

use Amasty\Base\Model\LicenceService\Schedule\Checker\Schedule;
use Amasty\Base\Model\LicenceService\Schedule\Data\ScheduleConfig;
use Amasty\Base\Model\LicenceService\Schedule\Data\ScheduleConfigFactory;
use Amasty\Base\Model\LicenceService\Schedule\ScheduleConfigRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
    /**
     * @var Schedule
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

        $this->model = new Schedule(
            $this->dateTime,
            $this->scheduleConfigFactory,
            $this->scheduleConfigRepositoryMock
        );
    }

    /**
     * @param int $lastSendDate
     * @param int $currentTime
     * @param array $timeIntervals
     * @param bool $result
     * @dataProvider isNeedToSendDataProvider
     * @return void
     */
    public function testIsNeedToSend(int $lastSendDate, int $currentTime, array $timeIntervals, bool $result): void
    {
        $flag = 'amasty_base_instance_registration';
        $this->scheduleConfigMock
            ->expects($this->once())
            ->method('getLastSendDate')
            ->willReturn($lastSendDate);
        $this->scheduleConfigMock
            ->expects($this->once())
            ->method('getTimeIntervals')
            ->willReturn($timeIntervals);
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
                ScheduleConfig::TIME_INTERVALS => [300, 900, 3600, 86400],
                true
            ],
            [
                ScheduleConfig::LAST_SEND_DATE => 1640198457,
                1640198400,
                ScheduleConfig::TIME_INTERVALS => [300],
                false
            ],
            [
                ScheduleConfig::LAST_SEND_DATE => 1640198457,
                1640198000,
                ScheduleConfig::TIME_INTERVALS => [86400],
                false
            ],
            [
                ScheduleConfig::LAST_SEND_DATE => 1640198457,
                1640198000,
                ScheduleConfig::TIME_INTERVALS => [],
                false
            ]
        ];
    }
}
