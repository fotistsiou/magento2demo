<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */
namespace Amasty\Smtp\Block\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Check extends Field
{
    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData([
            'id' => 'check_button',
            'label' => __('Test Connection'),
        ])
            ->setDataAttribute(
                ['role' => 'amsmtp-check-button']
            )
        ;

        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->_layout->createBlock(\Magento\Backend\Block\Template::class);

        $block
            ->setTemplate('Amasty_Smtp::config/check.phtml')
            ->setChild('button', $button)
            ->setData('select_html', parent::_getElementHtml($element))
        ;

        return $block->toHtml();
    }
}
