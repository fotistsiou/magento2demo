<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Config\Backend\Serialized;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class LicenseRegistrationArraySerialized extends ArraySerialized
{
    public function beforeSave(): self
    {
        $value = $this->getValue();
        if (is_array($value)) {
            foreach ($value as &$record) {
                if (isset($record['license_key'])) {
                    $record['license_key'] = trim($record['license_key']);
                }
            }
        }
        $this->setValue($value);

        return parent::beforeSave();
    }
}
