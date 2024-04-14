<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Integration\Model\LicenseService\Api;

use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\ChangedData\Persistor;
use Magento\Framework\Exception\LocalizedException;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea adminhtml
 */
class CollectEndpointTest extends AbstractEndpointTest
{
    /**
     * @var Persistor
     */
    private $dataPersistor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataPersistor = $this->objectManager->get(Persistor::class);
    }

    /**
     * @magentoConfigFixture default_store amasty_base/instance_registration/is_production 0
     * @magentoConfigFixture default_store amasty_base/instance_registration/keys {"item1":{"license_key":"test_key"}}
     * @magentoDataFixture Amasty_Base::Test/Integration/_files/system_instance_key_exists.php
     */
    public function testCollect(): void
    {
        $response = json_encode([]);
        $this->mockResponse($response);

        $sendSysInfo = $this->objectManager->get(SendSysInfo::class);
        $sendSysInfo->execute();

        $this->assertEmpty($this->dataPersistor->get()); //data hash must be saved after update
    }

    /**
     * @magentoDataFixture Amasty_Base::Test/Integration/_files/system_instance_key_exists.php
     */
    public function testCollectErrorResponse(): void
    {
        $response = json_encode([]);
        $this->mockResponse($response, 504);

        $this->expectException(LocalizedException::class);
        $sendSysInfo = $this->objectManager->get(SendSysInfo::class);
        $sendSysInfo->execute();
    }

    public function testCollectNoRegistration(): void
    {
        $response = json_encode([]);
        $this->mockResponse($response);

        $sendSysInfo = $this->objectManager->get(SendSysInfo::class);
        $sendSysInfo->execute();
        $this->assertNotEmpty($this->dataPersistor->get()); //data wasn't saved
    }
}
