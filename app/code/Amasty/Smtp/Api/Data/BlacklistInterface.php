<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Api\Data;

interface BlacklistInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const BLACKLIST_ID = 'id';
    public const CUSTOMER_EMAIL = 'customer_email';
    /**#@-*/

    /**
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * @param string $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     */
    public function setCustomerEmail(string $customerEmail): BlacklistInterface;
}
