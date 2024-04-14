<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo;

use Amasty\Base\Model\FlagRepository;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstanceFactory;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisteredInstanceRepositoryTest extends TestCase
{
    /**
     * @var RegisteredInstanceRepository
     */
    private $model;

    /**
     * @var FlagRepository|MockObject
     */
    private $flagRepositoryMock;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializerMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var RegisteredInstanceFactory|MockObject
     */
    private $registeredInstanceFactoryMock;

    protected function setUp(): void
    {
        $this->flagRepositoryMock = $this->createMock(FlagRepository::class);
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->registeredInstanceFactoryMock = $this->createMock(RegisteredInstanceFactory::class);

        $this->model = new RegisteredInstanceRepository(
            $this->flagRepositoryMock,
            $this->serializerMock,
            $this->dataObjectHelperMock,
            $this->registeredInstanceFactoryMock
        );
    }

    /**
     * @param string $regInstSerialized
     * @param array $regInstArray
     * @dataProvider getDataProvider
     * @return void
     */
    public function testGet(string $regInstSerialized, array $regInstArray): void
    {
        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);
        $this->registeredInstanceFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($registeredInstanceMock);
        $this->flagRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with(RegisteredInstanceRepository::REGISTERED_INSTANCE)
            ->willReturn($regInstSerialized);
        if ($regInstSerialized) {
            $this->serializerMock
                ->expects($this->once())
                ->method('unserialize')
                ->with($regInstSerialized)
                ->willReturn($regInstArray);
        }
        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with(
                $registeredInstanceMock,
                $regInstArray,
                RegisteredInstance::class
            );

        $this->assertEquals($registeredInstanceMock, $this->model->get());
    }

    public function getDataProvider(): array
    {
        return [
            ['', []],
            ['val', ['val']]
        ];
    }

    public function testSave(): void
    {
        $regInstArray = [];
        $regInstSerialized = '';
        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);
        $registeredInstanceMock
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($regInstArray);
        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($regInstArray)
            ->willReturn($regInstSerialized);
        $this->flagRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with(RegisteredInstanceRepository::REGISTERED_INSTANCE, $regInstSerialized);

        $this->assertEquals(true, $this->model->save($registeredInstanceMock));
    }
}
