<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml;

use Amasty\Base\ViewModel\GetSupport as GetSupportViewModel;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class GetSupport extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Base::get_support.phtml';

    public function __construct(
        Context $context,
        GetSupportViewModel $getSupportViewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setData('view_model', $getSupportViewModel);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
