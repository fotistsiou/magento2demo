<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Data\LicenseValidation\InstanceKey;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ExtensibleDataInterface;

class Support extends SimpleDataObject implements ExtensibleDataInterface
{
    public const ACTIVE = 'active';

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->getData(self::ACTIVE);
    }

    /**
     * @param bool $active
     * @return void
     */
    public function setActive(bool $active): void
    {
        $this->setData(self::ACTIVE, $active);
    }
}
