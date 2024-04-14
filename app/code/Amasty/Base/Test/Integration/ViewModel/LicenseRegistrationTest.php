<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Integration\ViewModel;

use Amasty\Base\Model\SysInfo\Command\LicenceService\GetCurrentLicenseValidation;
use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse\Converter;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation;
use Amasty\Base\Utils\Http\Response\ResponseFactory;
use Amasty\Base\ViewModel\LicenseRegistration;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class LicenseRegistrationTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function testNoValidationRequired(): void
    {
        $licenseRegistration = $this->prepareClassForTest('no_validation_required_response.json');
        $this->assertNull($licenseRegistration->getMessage());
    }

    public function testSuccessMessage(): void
    {
        $licenseRegistration = $this->prepareClassForTest('valid_verify_response.json');
        $this->assertNull($licenseRegistration->getMessage());
    }

    public function testValidationErrorMessage(): void
    {
        $licenseRegistration = $this->prepareClassForTest('invalid_verify_response.json');
        $message = $licenseRegistration->getMessage();
        $this->assertEquals('warning', $message->getType());
        $this->assertStringContainsString('unlicensed product usage', $message->getContent());
    }

    public function testModuleErrorMessage(): void
    {
        $licenseRegistration = $this->prepareClassForTest('validation_module_error_response.json');
        $message = $licenseRegistration->getMessage();
        $this->assertEquals('warning', $message->getType());
        $this->assertStringContainsString('extensions have missing or expired licenses', $message->getContent());
    }

    public function testModuleWarningMessage(): void
    {
        $licenseRegistration = $this->prepareClassForTest('validation_module_warning_response.json');
        $message = $licenseRegistration->getMessage();
        $this->assertEquals('warning', $message->getType());
        $this->assertStringContainsString('subscriptions have been canceled', $message->getContent());
    }

    private function prepareClassForTest(string $responseFile): LicenseRegistration
    {
        $licenseValidation = $this->prepareLicenseValidation($responseFile);
        $licenseProviderMock = $this->createMock(GetCurrentLicenseValidation::class);
        $licenseProviderMock->method('get')->willReturn($licenseValidation);
        return $this->objectManager->create(
            LicenseRegistration::class,
            ['getCurrentLicenseValidation' => $licenseProviderMock]
        );
    }

    private function prepareLicenseValidation(string $responseFile): LicenseValidation
    {
        $converter = $this->objectManager->get(Converter::class);
        $responseFactory = $this->objectManager->get(ResponseFactory::class);
        $responseData = $this->readResponseFile($responseFile);

        return $converter->convertArrayToEntity($responseFactory->create('test', $responseData)->toArray());
    }

    private function readResponseFile(string $fileName): array
    {
        $filepath = __DIR__ . '/../_files/responses/' . $fileName;
        $content = (string)file_get_contents($filepath);

        return json_decode($content, true);
    }
}
