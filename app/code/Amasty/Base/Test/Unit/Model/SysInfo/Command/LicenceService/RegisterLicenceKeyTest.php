<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\LicenceService\Response\Data\RegisteredInstance as ResponseRegisteredInstance;
use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Converter;
use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Domain\Provider;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisterLicenceKeyTest extends TestCase
{
    /**
     * @var RegisterLicenceKey
     */
    private $model;

    /**
     * @var RegisteredInstanceRepository|MockObject
     */
    private $registeredInstanceRepositoryMock;

    /**
     * @var RequestManager|MockObject
     */
    private $requestManagerMock;

    /**
     * @var Provider|MockObject
     */
    private $domainProviderMock;

    /**
     * @var Converter|MockObject
     */
    private $converterMock;

    protected function setUp(): void
    {
        $this->registeredInstanceRepositoryMock = $this->createMock(RegisteredInstanceRepository::class);
        $this->requestManagerMock = $this->createMock(RequestManager::class);
        $this->domainProviderMock = $this->createMock(Provider::class);
        $this->converterMock = $this->createMock(Converter::class);

        $this->model = new RegisterLicenceKey(
            $this->registeredInstanceRepositoryMock,
            $this->requestManagerMock,
            $this->domainProviderMock,
            $this->converterMock
        );
    }

    /**
     * @param array $currentDomains
     * @param array $storedDomains
     * @dataProvider executeNotDifferentDataProvider
     * @return void
     */
    public function testExecuteNotDifferent(array $currentDomains, array $storedDomains): void
    {
        $this->domainProviderMock
            ->expects($this->once())
            ->method('getCurrentDomains')
            ->willReturn($currentDomains);
        $this->domainProviderMock
            ->expects($this->once())
            ->method('getStoredDomains')
            ->willReturn($storedDomains);

        $this->model->execute();
    }

    public function executeNotDifferentDataProvider(): array
    {
        return [
            [['am.com'], []],
            [['am.com'], ['am.com']],
            [['am.com'], ['am.com', 'am2.com']],
        ];
    }

    public function testExecute(): void
    {
        $domain = 'am.com';
        $systemInstanceKey = 'key';
        $instanceArray = [
            Instance::DOMAIN => $domain,
            Instance::SYSTEM_INSTANCE_KEY => $systemInstanceKey
        ];
        $currentDomains = [$domain];
        $storedDomains = [];
        $registeredInstanceResponseMock = $this->createMock(ResponseRegisteredInstance::class);

        $instanceMock = $this->createMock(Instance::class);
        $instances = [$instanceMock];
        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);

        $this->domainProviderMock
            ->expects($this->once())
            ->method('getCurrentDomains')
            ->willReturn($currentDomains);
        $this->domainProviderMock
            ->expects($this->once())
            ->method('getStoredDomains')
            ->willReturn($storedDomains);

        $this->requestManagerMock
            ->expects($this->once())
            ->method('registerInstance')
            ->with($domain)
            ->willReturn($registeredInstanceResponseMock);
        $registeredInstanceResponseMock
            ->expects($this->once())
            ->method('getSystemInstanceKey')
            ->willReturn($systemInstanceKey);
        $this->converterMock
            ->expects($this->once())
            ->method('convertArrayToInstance')
            ->with($instanceArray)
            ->willReturn($instanceMock);

        $this->registeredInstanceRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($registeredInstanceMock);
        $registeredInstanceMock
            ->expects($this->once())
            ->method('setCurrentInstance')
            ->with($instanceMock)
            ->willReturnSelf();
        $registeredInstanceMock
            ->expects($this->once())
            ->method('setInstances')
            ->with($instances)
            ->willReturnSelf();
        $registeredInstanceMock
            ->expects($this->once())
            ->method('getInstances')
            ->willReturn([]);
        $this->registeredInstanceRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($registeredInstanceMock);

        $this->model->execute();
    }

    public function testExecuteOnException(): void
    {
        $exception = new LocalizedException(__('Invalid request.'));
        $domain = 'am.com';
        $currentDomains = [$domain];
        $storedDomains = [];

        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);
        $instance = null;
        $instances = [];

        $this->domainProviderMock
            ->expects($this->once())
            ->method('getCurrentDomains')
            ->willReturn($currentDomains);
        $this->domainProviderMock
            ->expects($this->once())
            ->method('getStoredDomains')
            ->willReturn($storedDomains);

        $this->requestManagerMock
            ->expects($this->once())
            ->method('registerInstance')
            ->with($domain)
            ->willThrowException($exception);

        $this->registeredInstanceRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($registeredInstanceMock);
        $registeredInstanceMock
            ->expects($this->once())
            ->method('setCurrentInstance')
            ->with($instance)
            ->willReturnSelf();
        $registeredInstanceMock
            ->expects($this->once())
            ->method('setInstances')
            ->with($instances)
            ->willReturnSelf();
        $registeredInstanceMock
            ->expects($this->once())
            ->method('getInstances')
            ->willReturn([]);
        $this->registeredInstanceRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($registeredInstanceMock);

        $this->expectException(LocalizedException::class);
        $this->model->execute();
    }
}
