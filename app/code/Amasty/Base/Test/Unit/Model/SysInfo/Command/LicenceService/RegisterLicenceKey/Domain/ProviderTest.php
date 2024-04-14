<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Domain;

use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Domain\Provider;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $model;

    /**
     * @var RegisteredInstanceRepository|MockObject
     */
    private $registeredInstanceRepositoryMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    protected function setUp(): void
    {
        $this->registeredInstanceRepositoryMock = $this->createMock(RegisteredInstanceRepository::class);
        $this->urlMock = $this->createMock(UrlInterface::class);

        $this->model = new Provider(
            $this->registeredInstanceRepositoryMock,
            $this->urlMock
        );
    }

    public function testGetStoredDomains(): void
    {
        $domain = 'am.com';
        $expected = [$domain];
        $instanceMock = $this->createMock(Instance::class);
        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);
        $instances = [$instanceMock];

        $this->registeredInstanceRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($registeredInstanceMock);
        $registeredInstanceMock
            ->expects($this->exactly(2))
            ->method('getInstances')
            ->willReturn($instances);
        $instanceMock
            ->expects($this->once())
            ->method('getDomain')
            ->willReturn($domain);

        $this->assertEquals($expected, $this->model->getStoredDomains());
    }

    public function testGetCurrentDomains(): void
    {
        $baseUrl = 'https://am.com/';
        $host = 'am.com';
        $this->urlMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($baseUrl);

        $this->assertEquals([$host], $this->model->getCurrentDomains());
    }
}
