<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Debug extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_amsmtp_debug', 'id');
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function truncate()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    /**
     * @param int $days
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clear($days)
    {
        $days = (int)$days;
        $connection = $this->getConnection();
        $expr = new \Zend_Db_Expr("DATEDIFF(NOW(), created_at) > $days");

        $connection->delete($this->getMainTable(), $expr);
    }
}
