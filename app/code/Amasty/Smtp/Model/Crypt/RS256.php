<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Crypt;

class RS256
{
    public function sign(string $message, $key): string
    {
        openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256);

        return $signature;
    }
}
