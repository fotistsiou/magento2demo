<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\ResourceModel;

use Amasty\Smtp\Api\Data\BlacklistInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Blacklist extends AbstractDb
{
    public const TABLE_NAME = 'amasty_amsmtp_blacklist';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, BlacklistInterface::BLACKLIST_ID);
    }
}
