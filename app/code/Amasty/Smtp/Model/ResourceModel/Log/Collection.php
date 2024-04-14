<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\ResourceModel\Log;

use Amasty\Smtp\Model\ResourceModel\Log;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method Log[] getItems()
 */
class Collection extends AbstractCollection
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\Smtp\Model\Log::class,
            \Amasty\Smtp\Model\ResourceModel\Log::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
