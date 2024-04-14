<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model;

use Amasty\Smtp\Api\Data\BlacklistInterface;
use Magento\Framework\Model\AbstractModel;

class Blacklist extends AbstractModel implements BlacklistInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Blacklist::class);
    }

    public function getCustomerEmail(): string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail(string $customerEmail): BlacklistInterface
    {
        $this->setData(self::CUSTOMER_EMAIL, $customerEmail);

        return $this;
    }
}
