<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\ViewModel;

use Amasty\Base\Block\Adminhtml\InstanceRegistrationMessages;
use Amasty\Base\Model\SysInfo\Command\LicenceService\GetCurrentLicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\Message as ValidationMessage;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\MessageFactory;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\Message as ModuleMessage;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class LicenseRegistration implements ArgumentInterface
{
    /**
     * @var GetCurrentLicenseValidation
     */
    private $getCurrentLicenseValidation;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        GetCurrentLicenseValidation $getCurrentLicenseValidation,
        UrlInterface $url,
        MessageFactory $messageFactory,
        RequestInterface $request
    ) {
        $this->getCurrentLicenseValidation = $getCurrentLicenseValidation;
        $this->url = $url;
        $this->messageFactory = $messageFactory;
        $this->request = $request;
    }

    public function getMessage(): ?ValidationMessage
    {
        $errorMessage = $this->getErrorMessage();
        if ($errorMessage) {
            $validationMessage = $this->messageFactory->create();
            $validationMessage->setType(ValidationMessage::WARNING);
            $validationMessage->setContent($errorMessage);
        }

        return $validationMessage ?? null;
    }

    private function getErrorMessage(): ?string
    {
        $licenseValidation = $this->getCurrentLicenseValidation->get();
        $isLicenseValid = $this->isLicenseValid($licenseValidation);
        if ($this->request->getParam('section') === InstanceRegistrationMessages::SECTION_NAME
            || $licenseValidation->isNeedCheckLicense() === false
            || ($licenseValidation->isNeedCheckLicense() === true && $isLicenseValid)
        ) {
            return null;
        }

        $moduleMessageType = $this->getModulesMessagesError($licenseValidation);
        if ($moduleMessageType === ModuleMessage::ERROR) {
            return __(
                'Amasty notice: Some Amasty extensions have missing or expired licenses.'
                . ' Please go to <a href="%1">Configuration</a> and check the details in the License Status column.',
                $this->getConfigSectionUrl()
            )->render();
        }
        if ($moduleMessageType === ModuleMessage::WARNING) {
            return __(
                'Amasty notice: Some Amasty subscriptions have been canceled or have limited access. '
                . 'Please go to <a href="%1">Configuration</a> and check the details in the License Status column.',
                $this->getConfigSectionUrl()
            )->render();
        }
        if (!$isLicenseValid || $moduleMessageType === null) {
            return __(
                'Amasty notice: Please go to <a href="%1">Configuration</a> and register your '
                . 'instance to avoid unlicensed product usage.',
                $this->getConfigSectionUrl()
            )->render();
        }

        return null;
    }

    private function isLicenseValid(LicenseValidation $license): bool
    {
        $messages = $license->getMessages();
        if (!$messages) {
            return false; //we must have success message on validation
        }
        foreach ($messages as $message) {
            if ($message->getType() !== ValidationMessage::SUCCESS) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checking for module validation error or warning
     */
    private function getModulesMessagesError(LicenseValidation $license): ?string
    {
        foreach ($license->getModules() as $module) {
            foreach ($module->getMessages() as $message) {
                $type = $message->getType();
                if ($type && $type !== ModuleMessage::SUCCESS) {
                    return $type;
                }
            }
        }

        return null; //no module messages - no validation status
    }

    private function getConfigSectionUrl(): string
    {
        return $this->url->getUrl(
            'adminhtml/system_config/edit/',
            ['section' => 'amasty_products']
        );
    }
}
