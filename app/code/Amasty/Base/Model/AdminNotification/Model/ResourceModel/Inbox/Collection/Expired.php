<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection;

class Expired extends \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
{
    /**
     * @return \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection\Unread
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('is_remove', 0)
            ->addFieldToFilter('is_amasty', 1)
            ->addFieldToFilter('expiration_date', ['notnull' => true]);
        $this->getSelect()->where('expiration_date < NOW()');

        return $this;
    }
}
