<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\RegisterLicenceKey;

use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Converter;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\InstanceFactory;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstanceFactory;
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
     * @var RegisteredInstanceFactory|MockObject
     */
    private $registeredInstanceFactoryMock;

    /**
     * @var InstanceFactory|MockObject
     */
    private $instanceFactoryMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    protected function setUp(): void
    {
        $this->registeredInstanceFactoryMock = $this->createMock(RegisteredInstanceFactory::class);
        $this->instanceFactoryMock = $this->createMock(InstanceFactory::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->model = new Converter(
            $this->registeredInstanceFactoryMock,
            $this->instanceFactoryMock,
            $this->dataObjectHelperMock
        );
    }

    public function testConvertToObject(): void
    {
        $data = [];
        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);

        $this->registeredInstanceFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($registeredInstanceMock);

        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($registeredInstanceMock, $data, RegisteredInstance::class);

        $this->assertEquals($registeredInstanceMock, $this->model->convertArrayToRegisteredInstance($data));
    }

    public function convertArrayToInstance(): void
    {
        $data = [];
        $instanceMock = $this->createMock(Instance::class);

        $this->registeredInstanceFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($instanceMock);

        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($instanceMock, $data, Instance::class);

        $this->assertEquals($instanceMock, $this->model->convertArrayToInstance($data));
    }
}
