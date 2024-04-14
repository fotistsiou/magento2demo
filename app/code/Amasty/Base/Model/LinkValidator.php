<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model;

class LinkValidator
{
    public const ALLOWED_DOMAINS = [
        'amasty.com',
        'marketplace.magento.com'
    ];

    /**
     * @param string $link
     *
     * @return bool
     */
    public function validate(string $link): bool
    {
        if (! (string) $link) { // fix for xml object
            return true;
        }

        foreach (static::ALLOWED_DOMAINS as $allowedDomain) {
            if (preg_match('/^http[s]?:\/\/' . $allowedDomain . '\/.*$/', $link) === 1) {
                return true;
            }
        }

        return false;
    }
}
