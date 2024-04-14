<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token;

class ScopeResolver
{
    /**
     * @var array
     */
    private $scopeTypes = [];

    public function __construct(
        array $scopeTypes
    ) {
        $this->scopeTypes = array_merge($this->scopeTypes, $scopeTypes);
    }

    public function execute(): string
    {
        return implode(' ', $this->scopeTypes);
    }
}
