<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Plugin\AdminNotification\Block;

use Magento\AdminNotification\Block\ToolbarEntry as NativeToolbarEntry;

/**
 * Add html attributes to amasty notifications
 */
class ToolbarEntry
{
    public const AMASTY_ATTRIBUTE = ' data-ambase-logo="1"';

    public function afterToHtml(
        NativeToolbarEntry $subject,
        $html
    ) {
        $collection = $subject->getLatestUnreadNotifications()
            ->clear()
            ->addFieldToFilter('is_amasty', 1);

        foreach ($collection as $item) {
            $search = 'data-notification-id="' . $item->getId() . '"';
            if ($item->getData('image_url')) {
                $html = str_replace(
                    $search,
                    $search . ' style='
                    . '"background: url(' . $item->getData('image_url') . ') no-repeat 5px 7px; background-size: 30px;"'
                    . self::AMASTY_ATTRIBUTE,
                    $html
                );
            } else {
                $html = str_replace($search, $search . self::AMASTY_ATTRIBUTE, $html);
            }
        }

        return $html;
    }
}
