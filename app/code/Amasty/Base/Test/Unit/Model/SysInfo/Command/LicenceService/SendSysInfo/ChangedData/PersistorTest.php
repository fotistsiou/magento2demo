<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\SendSysInfo\ChangedData;

use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\CacheStorage;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\ChangedData\Persistor;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Checker;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Encryption;
use Amasty\Base\Model\SysInfo\Provider\Collector;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PersistorTest extends TestCase
{
    /**
     * @var Persistor
     */
    private $model;

    /**
     * @var Collector|MockObject
     */
    private $collectorMock;

    /**
     * @var Checker|MockObject
     */
    private $checkerMock;

    /**
     * @var CacheStorage|MockObject
     */
    private $cacheStorageMock;

    /**
     * @var Encryption|MockObject
     */
    private $encryptionMock;

    protected function setUp(): void
    {
        $this->collectorMock = $this->createMock(Collector::class);
        $this->checkerMock = $this->createMock(Checker::class);
        $this->cacheStorageMock = $this->createMock(CacheStorage::class);
        $this->encryptionMock = $this->createMock(Encryption::class);

        $this->model = new Persistor(
            $this->collectorMock,
            $this->checkerMock,
            $this->cacheStorageMock,
            $this->encryptionMock
        );
    }

    /**
     * @param bool $isChangedCacheValue
     * @dataProvider getDataProvider
     * @return void
     */
    public function testGet(bool $isChangedCacheValue): void
    {
        $cacheValue = 'cache_value';
        $newValue = 'cache_value';
        $sysInfoName = 'key';
        $sysInfo = [];
        $data = [$sysInfoName => $sysInfo];
        if ($isChangedCacheValue) {
            $changedData = $data;
        } else {
            $changedData = [];
        }

        $this->collectorMock
            ->expects($this->once())
            ->method('collect')
            ->with(CollectorPool::LICENCE_SERVICE_GROUP)
            ->willReturn($data);
        $this->cacheStorageMock
            ->expects($this->once())
            ->method('get')
            ->with($sysInfoName)
            ->willReturn($cacheValue);
        $this->encryptionMock
            ->expects($this->once())
            ->method('encryptArray')
            ->with($sysInfo)
            ->willReturn($cacheValue);
        $this->checkerMock
            ->expects($this->once())
            ->method('isChangedCacheValue')
            ->with($cacheValue, $newValue)
            ->willReturn($isChangedCacheValue);

        $this->assertEquals($changedData, $this->model->get());
    }

    public function getDataProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }

    public function testSave(): void
    {
        $sysInfoName = 'key';
        $sysInfo = [];
        $encryptionValue = 'encrypted';
        $changedData = [$sysInfoName => $sysInfo];

        $this->encryptionMock
            ->expects($this->once())
            ->method('encryptArray')
            ->with($sysInfo)
            ->willReturn($encryptionValue);
        $this->cacheStorageMock
            ->expects($this->once())
            ->method('set')
            ->with($sysInfoName, $encryptionValue);

        $this->model->save($changedData);
    }
}
