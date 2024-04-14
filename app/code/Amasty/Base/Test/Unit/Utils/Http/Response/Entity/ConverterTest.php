<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Utils\Http\Response\Entity;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Utils\Http\Response\Entity\Config;
use Amasty\Base\Utils\Http\Response\Entity\Converter;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $model;

    /**
     * @var ObjectManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectManagerMock;

    /**
     * @var DataObjectHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dataObjectHelperMock;

    protected function setUp(): void
    {
        $this->objectManagerMock = $this->createPartialMock(
            ObjectManagerInterface::class,
            ['create', 'get', 'configure']
        );
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);
        $this->model = new Converter($this->objectManagerMock, $this->dataObjectHelperMock);
    }

    public function testConvertToObject(): void
    {
        $row = [];
        $entityConfig = [
            Config::CLASS_NAME => InstanceInfo::class
        ];
        $instanceInfoMock = $this->createMock(InstanceInfo::class);
        $entityConfigMock = $this->createPartialMock(Config::class, ['getDataProcessor', 'getClassName']);
        $entityConfigMock
            ->expects($this->exactly(2))
            ->method('getClassName')
            ->willReturn($entityConfig[Config::CLASS_NAME]);

        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with($entityConfig[Config::CLASS_NAME])
            ->willReturn($instanceInfoMock);
        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($instanceInfoMock, $row, $entityConfig[Config::CLASS_NAME])
            ->willReturn($instanceInfoMock);

        $this->assertEquals($instanceInfoMock, $this->model->convertToObject($row, $entityConfigMock));
    }
}
