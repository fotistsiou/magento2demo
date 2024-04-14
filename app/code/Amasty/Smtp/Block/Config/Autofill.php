<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */
namespace Amasty\Smtp\Block\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Autofill extends Field
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
            'id' => 'synchronize_button',
            'label' => __('Autofill'),
        ])
            ->setDataAttribute(
                ['role' => 'amsmtp-fill-button']
            )
        ;

        $selectHtml = parent::_getElementHtml($element);
        $buttonHtml = $button->toHtml();

        return "$buttonHtml<div class='amsmtp-buttoned-input'>$selectHtml</div>";
    }
}
