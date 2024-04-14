<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\FlagRepository;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\CacheStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CacheStorageTest extends TestCase
{
    /**
     * @var CacheStorage
     */
    private $model;

    /**
     * @var FlagRepository|MockObject
     */
    private $flagRepositoryMock;

    protected function setUp(): void
    {
        $this->flagRepositoryMock = $this->createPartialMock(
            FlagRepository::class,
            ['get', 'save']
        );

        $this->model = new CacheStorage(
            $this->flagRepositoryMock
        );
    }

    /**
     * @param string $identifier
     * @param string|null $expected
     * @dataProvider getDataProvider
     * @return void
     */
    public function testGet(string $identifier, ?string $expected): void
    {
        $this->flagRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with(CacheStorage::PREFIX . $identifier)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->get($identifier));
    }

    public function getDataProvider(): array
    {
        return [
            ['identifier1', 'val1'],
            ['identifier2', null]
        ];
    }

    /**
     * @param string $identifier
     * @param string $value
     * @dataProvider setDataProvider
     * @return void
     */
    public function testSet(string $identifier, string $value): void
    {
        $this->flagRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with(CacheStorage::PREFIX . $identifier, $value);

        $this->assertEquals(true, $this->model->set($identifier, $value));
    }

    public function setDataProvider(): array
    {
        return [
            ['identifier1', 'val1']
        ];
    }
}
