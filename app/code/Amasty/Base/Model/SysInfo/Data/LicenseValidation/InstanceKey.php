<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Data\LicenseValidation;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\InstanceKey\Support;
use Magento\Framework\Api\ExtensibleDataInterface;

class InstanceKey extends SimpleDataObject implements ExtensibleDataInterface
{
    public const INSTANCE_KEY = 'instance_key';
    public const SUPPORT = 'support';

    /**
     * @return string
     */
    public function getInstanceKey(): string
    {
        return (string)$this->getData(self::INSTANCE_KEY);
    }

    /**
     * @param string $key
     * @return void
     */
    public function setInstanceKey(string $key): void
    {
        $this->setData(self::INSTANCE_KEY, $key);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\LicenseValidation\InstanceKey\Support
     */
    public function getSupport(): Support
    {
        return $this->getData(self::SUPPORT);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\LicenseValidation\InstanceKey\Support $support
     * @return void
     */
    public function setSupport(Support $support): void
    {
        $this->setData(self::SUPPORT, $support);
    }
}
