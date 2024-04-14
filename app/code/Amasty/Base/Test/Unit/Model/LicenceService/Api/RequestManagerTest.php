<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\LicenceService\Api;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Model\LicenceService\Request\Url\Builder;
use Amasty\Base\Model\LicenceService\Response\Data\RegisteredInstance;
use Amasty\Base\Utils\Http\Curl;
use Amasty\Base\Utils\Http\CurlFactory;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

class RequestManagerTest extends TestCase
{
    /**
     * @var RequestManager
     */
    private $model;

    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverterMock;

    /**
     * @var CurlFactory
     */
    private $curlFactoryMock;

    /**
     * @var Builder
     */
    private $urlBuilderMock;

    protected function setUp(): void
    {
        $this->simpleDataObjectConverterMock = $this->createPartialMock(
            SimpleDataObjectConverter::class,
            ['convertKeysToCamelCase']
        );
        $this->curlFactoryMock = $this->createPartialMock(CurlFactory::class, ['create']);
        $this->urlBuilderMock = $this->createPartialMock(Builder::class, ['build']);

        $this->model = new RequestManager(
            $this->simpleDataObjectConverterMock,
            $this->curlFactoryMock,
            $this->urlBuilderMock
        );
    }

    public function testRegisterInstance(): void
    {
        [$curlMock, $domain, $url, $postParams, $registeredInstanceMock] = $this->registerInstanceInit(200);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $postParams)
            ->willReturn($registeredInstanceMock);

        $this->assertEquals($registeredInstanceMock, $this->model->registerInstance($domain));
    }

    public function testRegisterInstanceOnException(): void
    {
        [$curlMock, $domain, $url, $postParams] = $this->registerInstanceInit(500);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $postParams);

        $this->expectException(LocalizedException::class);
        $this->model->registerInstance($domain);
    }

    private function registerInstanceInit(int $responseCode): array
    {
        $domain = 'https://amasty.com';
        $path = '/api/v1/instance/registration';
        $url = 'https://amasty-licence.com' . $path;
        $postParams = json_encode(['domain' => $domain]);
        $curlMock = $this->createPartialMock(Curl::class, ['request']);
        $registeredInstanceMock = $this->createPartialMock(RegisteredInstance::class, []);
        $registeredInstanceMock->setData(['code' => $responseCode]);

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        return [$curlMock, $domain, $url, $postParams, $registeredInstanceMock];
    }

    public function testUpdateInstanceInfo(): void
    {
        [$curlMock, $instanceInfoMock, $url, $instanceInfoString] = $this->updateInstanceInfoInit(200);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $this->model->updateInstanceInfo($instanceInfoMock);
    }

    public function testUpdateInstanceInfoOnException(): void
    {
        [$curlMock, $instanceInfoMock, $url, $instanceInfoString] = $this->updateInstanceInfoInit(500);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $this->expectException(LocalizedException::class);
        $this->model->updateInstanceInfo($instanceInfoMock);
    }

    private function updateInstanceInfoInit(int $responseCode): array
    {
        $instanceInfo = [
            'systemInstanceKey' => 'key',
            'modules' => [],
            'domains' => [],
            'customerInstanceKey' => [],
            'isProduction' => null
        ];
        $instanceInfoString = json_encode($instanceInfo);

        $path = '/api/v1/instance_client/' . $instanceInfo['systemInstanceKey'] . '/collect';
        $url = 'https://amasty-licence.com' . $path;
        $curlMock = $this->createPartialMock(Curl::class, ['request']);
        $registeredInstanceMock = $this->createPartialMock(RegisteredInstance::class, []);
        $registeredInstanceMock->setData(['code' => $responseCode]);
        $curlMock->method('request')->willReturn($registeredInstanceMock);
        $instanceInfoMock = $this->createPartialMock(
            InstanceInfo::class,
            ['getSystemInstanceKey', 'toArray']
        );

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        $instanceInfoMock
            ->expects($this->once())
            ->method('getSystemInstanceKey')
            ->willReturn($instanceInfo['systemInstanceKey']);
        $instanceInfoMock
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($instanceInfo);

        $this->simpleDataObjectConverterMock
            ->expects($this->once())
            ->method('convertKeysToCamelCase')
            ->willReturn($instanceInfo);

        return [$curlMock, $instanceInfoMock, $url, $instanceInfoString];
    }

    public function testPing(): void
    {
        [$curlMock, $instanceInfoMock, $url, $instanceInfoString] = $this->pingInit(200);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $this->model->pingRequest($instanceInfoMock);
    }

    public function testPingOnException(): void
    {
        [$curlMock, $instanceInfoMock, $url, $instanceInfoString] = $this->pingInit(500);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $response = $this->model->pingRequest($instanceInfoMock);
        $this->assertEquals(500, $response->getData('code'));
    }

    private function pingInit(int $responseCode): array
    {
        $instanceInfo = [
            'systemInstanceKey' => 'key',
            'customerInstanceKey' => ['test-key'],
            'isProduction' => true
        ];
        $instanceInfoString = json_encode($instanceInfo);

        $path = '/api/v1/instance_client/' . $instanceInfo['systemInstanceKey'] . '/ping';
        $url = 'https://amasty-licence.com' . $path;
        $curlMock = $this->createPartialMock(Curl::class, ['request']);
        $registeredInstanceMock = $this->createPartialMock(RegisteredInstance::class, []);
        $registeredInstanceMock->setData(['code' => $responseCode]);
        $curlMock->method('request')->willReturn($registeredInstanceMock);
        $instanceInfoMock = $this->createConfiguredMock(
            InstanceInfo::class,
            ['getSystemInstanceKey' => $instanceInfo['systemInstanceKey'], 'toArray' => $instanceInfo]
        );

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        $this->simpleDataObjectConverterMock
            ->expects($this->once())
            ->method('convertKeysToCamelCase')
            ->willReturn($instanceInfo);

        return [$curlMock, $instanceInfoMock, $url, $instanceInfoString];
    }

    public function testVerify(): void
    {
        [$curlMock, $instanceInfoMock, $url, $instanceInfoString] = $this->verifyInit(200);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $this->model->verify($instanceInfoMock);
    }

    public function testVerifyOnException(): void
    {
        [$curlMock, $instanceInfoMock, $url, $instanceInfoString] = $this->verifyInit(500);

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $response = $this->model->verify($instanceInfoMock);
        $this->assertEquals(500, $response->getData('code'));
    }

    private function verifyInit(int $responseCode): array
    {
        $instanceInfo = [
            'systemInstanceKey' => 'key',
            'modules' => [],
            'domains' => [],
            'customerInstanceKey' => ['test-key'],
            'isProduction' => true
        ];
        $instanceInfoString = json_encode($instanceInfo);

        $path = '/api/v1/instance_client/' . $instanceInfo['systemInstanceKey'] . '/verify';
        $url = 'https://amasty-licence.com' . $path;
        $curlMock = $this->createPartialMock(Curl::class, ['request']);
        $registeredInstanceMock = $this->createPartialMock(RegisteredInstance::class, []);
        $registeredInstanceMock->setData(['code' => $responseCode]);
        $curlMock->method('request')->willReturn($registeredInstanceMock);
        $instanceInfoMock = $this->createPartialMock(
            InstanceInfo::class,
            ['getSystemInstanceKey', 'toArray']
        );

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        $instanceInfoMock
            ->expects($this->once())
            ->method('getSystemInstanceKey')
            ->willReturn($instanceInfo['systemInstanceKey']);
        $instanceInfoMock
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($instanceInfo);

        $this->simpleDataObjectConverterMock
            ->expects($this->once())
            ->method('convertKeysToCamelCase')
            ->willReturn($instanceInfo);

        return [$curlMock, $instanceInfoMock, $url, $instanceInfoString];
    }
}
