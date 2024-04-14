<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml;

use Amasty\Base\Model\SysInfo\Command\LicenceService\GetCurrentLicenseValidation;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class InstanceRegistration extends Fieldset
{
    /**
     * @var GetCurrentLicenseValidation
     */
    private $getCurrentLicenseValidation;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        GetCurrentLicenseValidation $getCurrentLicenseValidation,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->getCurrentLicenseValidation = $getCurrentLicenseValidation;
    }

    public function render(AbstractElement $element): string
    {
        $licenseValidation = $this->getCurrentLicenseValidation->get();
        if ($licenseValidation->isNeedCheckLicense() === false) {
            return '';
        }

        return parent::render($element);
    }
}
