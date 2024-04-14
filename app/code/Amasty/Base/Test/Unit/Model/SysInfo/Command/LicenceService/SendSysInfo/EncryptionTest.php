<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Encryption;
use Magento\Framework\Serialize\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase
{
    /**
     * @var Encryption
     */
    private $model;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializerMock;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->model = new Encryption($this->serializerMock);
    }

    public function testEncryptArray(): void
    {
        $value = [];
        $serializedValue = 'serialized';
        $expected = hash('sha256', $serializedValue);

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($value)
            ->willReturn($serializedValue);

        $this->assertEquals($expected, $this->model->encryptArray($value));
    }

    public function testEncryptString(): void
    {
        $value = 'string';
        $expected = hash('sha256', $value);

        $this->assertEquals($expected, $this->model->encryptString($value));
    }
}
