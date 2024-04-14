<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Security implements ArrayInterface
{
    public const SECURITY_NONE = '';
    public const SECURITY_SSL  = 'ssl';
    public const SECURITY_TLS  = 'tls';

    public function toOptionArray()
    {
        return [
            ['value' => self::SECURITY_NONE,    'label' => __('None')],
            ['value' => self::SECURITY_SSL,     'label' => __('SSL')],
            ['value' => self::SECURITY_TLS,     'label' => __('TLS')],
        ];
    }
}
