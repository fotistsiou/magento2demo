<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml\Backend;

use Amasty\Base\Plugin\Backend\Model\Menu\Builder;
use Magento\Backend\Model\UrlInterface;

class Menu extends \Magento\Backend\Block\Menu
{
    protected function _addSubMenu($menuItem, $level, $limit, $id = null)
    {
        if (stripos($menuItem->getId(), Builder::BASE_MENU) !== false) {
            return $this->getLayout()->createBlock(
                AmastyMenu::class,
                'amasty_menu'
            )->toHtml();
        }

        return parent::_addSubMenu($menuItem, $level, $limit, $id);
    }

    protected function _afterToHtml($html)
    {
        return preg_replace_callback(
            '#' . UrlInterface::SECRET_KEY_PARAM_NAME . '\\\\\/\$([^\/].*)\/([^\/].*)\/([^\$].*)\$#U',
            [$this, '_callbackSecretKey'],
            parent::_afterToHtml($html)
        );
    }

    protected function _callbackSecretKey($match)
    {
        foreach ($match as &$part) {
            $part = rtrim($part, '\\');
        }

        return parent::_callbackSecretKey($match);
    }
}
