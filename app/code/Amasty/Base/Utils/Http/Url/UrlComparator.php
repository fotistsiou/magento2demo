<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Utils\Http\Url;

class UrlComparator
{
    /**
     * Check two urls, if the first url contains a mask (for example {})
     * and urls differ only by mask then urls is equals
     *
     * @param string $url1
     * @param string $url2
     * @param string $mask
     * @return bool
     */
    public function isEqual(string $url1, string $url2, string $mask = '{}'): bool
    {
        $result = true;
        $arrUrl1 = explode('/', $url1);
        $arrUrl2 = explode('/', $url2);
        $diff = array_diff($arrUrl1, $arrUrl2);

        foreach ($diff as $value) {
            if ($value != $mask) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
