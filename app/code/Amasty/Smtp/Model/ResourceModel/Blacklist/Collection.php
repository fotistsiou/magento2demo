<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\ResourceModel\Blacklist;

use Amasty\Smtp\Model\Blacklist as BlacklistModel;
use Amasty\Smtp\Model\ResourceModel\Blacklist as BlacklistResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(BlacklistModel::class, BlacklistResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
