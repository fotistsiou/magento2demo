<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Source;

class NotificationType implements \Magento\Framework\Option\ArrayInterface
{
    public const GENERAL = 'INFO';
    public const SPECIAL_DEALS = 'PROMO';
    public const AVAILABLE_UPDATE = 'INSTALLED_UPDATE';
    public const UNSUBSCRIBE_ALL = 'UNSUBSCRIBE_ALL';
    public const TIPS_TRICKS = 'TIPS_TRICKS';

    public function toOptionArray()
    {
        $types = [
            [
                'value' => self::GENERAL,
                'label' => __('General Info')
            ],
            [
                'value' => self::SPECIAL_DEALS,
                'label' => __('Special Deals')
            ],
            [
                'value' => self::AVAILABLE_UPDATE,
                'label' => __('Available Updates')
            ],
            [
                'value' => self::TIPS_TRICKS,
                'label' => __('Magento Tips & Tricks')
            ],
            [
                'value' => self::UNSUBSCRIBE_ALL,
                'label' => __('Unsubscribe from all')
            ]
        ];

        return $types;
    }
}
