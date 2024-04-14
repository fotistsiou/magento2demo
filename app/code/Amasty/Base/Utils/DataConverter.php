<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Utils;

use Magento\Framework\Api\SimpleDataObjectConverter;

class DataConverter
{
    /**
     * Converts associative array's key names from camelCase to snake_case, recursively.
     *
     * @param array $properties
     * @return array
     */
    public function convertArrayToSnakeCase(array $properties): array
    {
        foreach ($properties as $name => $value) {
            $snakeCaseName = SimpleDataObjectConverter::camelCaseToSnakeCase($name);
            if (is_array($value)) {
                $value = $this->convertArrayToSnakeCase($value);
            }
            unset($properties[$name]);
            $properties[$snakeCaseName] = $value;
        }

        return $properties;
    }
}
