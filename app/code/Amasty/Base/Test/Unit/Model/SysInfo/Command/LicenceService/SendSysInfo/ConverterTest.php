<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfoFactory;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Converter;
use Magento\Framework\Api\DataObjectHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $model;

    /**
     * @var InstanceInfoFactory|MockObject
     */
    private $instanceInfoFactoryMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    protected function setUp(): void
    {
        $this->instanceInfoFactoryMock = $this->createPartialMock(InstanceInfoFactory::class, ['create']);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = new Converter($this->instanceInfoFactoryMock, $this->dataObjectHelperMock);
    }

    public function testConvertToObject(): void
    {
        $data = [InstanceInfo::DOMAINS => [], InstanceInfo::MODULES => [], InstanceInfo::CUSTOMER_INSTANCE_KEY => []];
        $instanceInfoMock = $this->createMock(InstanceInfo::class);

        $this->instanceInfoFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($instanceInfoMock);

        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($instanceInfoMock, $data, InstanceInfo::class);

        $this->assertEquals($instanceInfoMock, $this->model->convertToObject($data));
    }
}
