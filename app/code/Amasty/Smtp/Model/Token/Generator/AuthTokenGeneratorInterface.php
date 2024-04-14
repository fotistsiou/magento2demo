<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token\Generator;

interface AuthTokenGeneratorInterface
{
    public const TOKEN_LIFETIME = 3600;

    public function generateToken(): string;
}
