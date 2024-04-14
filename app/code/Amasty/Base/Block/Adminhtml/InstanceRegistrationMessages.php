<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml;

use Amasty\Base\Model\SysInfo\Command\LicenceService\GetCurrentLicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class InstanceRegistrationMessages extends Field
{
    public const SECTION_NAME = 'amasty_products';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Base::config/instance_registration.phtml';

    /**
     * @var GetCurrentLicenseValidation
     */
    private $getCurrentLicenseValidation;

    /**
     * @var LicenseValidation
     */
    private $license;

    public function __construct(
        Context $context,
        GetCurrentLicenseValidation $getCurrentLicenseValidation,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->getCurrentLicenseValidation = $getCurrentLicenseValidation;
    }

    public function isAmastyProductsSection(): bool
    {
        return $this->getRequest()->getParam('section') === self::SECTION_NAME;
    }

    public function getLicenseValidation(): LicenseValidation
    {
        if (!$this->license) {
            $this->license = $this->getCurrentLicenseValidation->get();
        }

        return $this->license;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
