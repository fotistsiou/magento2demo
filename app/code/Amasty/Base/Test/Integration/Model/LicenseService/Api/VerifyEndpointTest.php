<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Integration\Model\LicenseService\Api;

use Amasty\Base\Model\SysInfo\Command\LicenceService\GetCurrentLicenseValidation;
use Magento\Framework\Event\ManagerInterface;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea adminhtml
 */
class VerifyEndpointTest extends AbstractEndpointTest
{
    /**
     * @var GetCurrentLicenseValidation
     */
    private $getCurrentLicense;

    protected function setUp(): void
    {
        parent::setUp();
        $this->getCurrentLicense = $this->objectManager->get(GetCurrentLicenseValidation::class);
    }

    /**
     * @magentoConfigFixture default_store amasty_base/instance_registration/is_production 0
     * @magentoConfigFixture default_store amasty_base/instance_registration/keys {"item1":{"license_key":"test_key"}}
     * @magentoDataFixture Amasty_Base::Test/Integration/_files/system_instance_key_exists.php
     */
    public function testValidResponse(): void
    {
        $response = json_encode($this->readResponseFile('valid_verify_response.json'));
        $this->mockResponse($response);

        $eventManager = $this->objectManager->get(ManagerInterface::class);
        $eventManager->dispatch('admin_system_config_changed_section_amasty_products');

        $licenseValidation = $this->getCurrentLicense->get();
        $this->assertTrue($licenseValidation->isNeedCheckLicense());
        $this->assertEquals('success', $licenseValidation->getMessages()[0]->getType());
        $this->assertNotEmpty($licenseValidation->getModules());
        $this->assertNotEmpty($licenseValidation->getInstanceKeys());
    }

    /**
     * @magentoConfigFixture default_store amasty_base/instance_registration/is_production 0
     * @magentoConfigFixture default_store amasty_base/instance_registration/keys {"item1":{"license_key":"test_key"}}
     * @magentoDataFixture Amasty_Base::Test/Integration/_files/system_instance_key_exists.php
     */
    public function testErrorResponse(): void
    {
        $response = json_encode($this->readResponseFile('invalid_verify_response.json'));
        $this->mockResponse($response, 400);

        $eventManager = $this->objectManager->get(ManagerInterface::class);
        $eventManager->dispatch('admin_system_config_changed_section_amasty_products');

        $licenseValidation = $this->getCurrentLicense->get();
        $this->assertTrue($licenseValidation->isNeedCheckLicense());
        $this->assertEquals('error', $licenseValidation->getMessages()[0]->getType());
        $this->assertEmpty($licenseValidation->getModules());
        $this->assertEmpty($licenseValidation->getInstanceKeys());
    }

    /**
     * @magentoConfigFixture default_store amasty_base/instance_registration/is_production 0
     * @magentoConfigFixture default_store amasty_base/instance_registration/keys {"item1":{"license_key":"test_key"}}
     * @magentoDataFixture Amasty_Base::Test/Integration/_files/system_instance_key_exists.php
     */
    public function testNoNeedCheckLicense(): void
    {
        $response = json_encode(["isNeedCheckLicense" => false]);
        $this->mockResponse($response);

        $eventManager = $this->objectManager->get(ManagerInterface::class);
        $eventManager->dispatch('admin_system_config_changed_section_amasty_products');

        $licenseValidation = $this->getCurrentLicense->get();
        $this->assertFalse($licenseValidation->isNeedCheckLicense());
    }

    /**
     * @magentoDataFixture Amasty_Base::Test/Integration/_files/system_instance_key_exists.php
     */
    public function testUnavailableError(): void
    {
        $response = json_encode([]);
        $this->mockResponse($response, 504);

        $eventManager = $this->objectManager->get(ManagerInterface::class);
        $eventManager->dispatch('admin_system_config_changed_section_amasty_products');

        $licenseValidation = $this->getCurrentLicense->get();
        $this->assertTrue($licenseValidation->isNeedCheckLicense());
        $this->assertEquals('error', $licenseValidation->getMessages()[0]->getType());
    }
}
