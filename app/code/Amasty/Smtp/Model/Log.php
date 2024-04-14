<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model;

use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel
{
    public const STATUS_SENT    = 0;
    public const STATUS_FAILED  = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_BLACKLISTED = 3;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Smtp\Model\ResourceModel\Log::class);
    }
}
