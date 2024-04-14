<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Data;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ExtensibleDataInterface;

class LicenseValidation extends SimpleDataObject implements ExtensibleDataInterface
{
    public const IS_NEED_CHECK_LICENSE = 'is_need_check_license';
    public const INSTANCE_KEYS = 'instance_keys';
    public const MODULES = 'modules';
    public const MESSAGES = 'messages';

    /**
     * @return bool
     */
    public function isNeedCheckLicense(): bool
    {
        return (bool)$this->getData(self::IS_NEED_CHECK_LICENSE);
    }

    /**
     * @param bool $isNeed
     * @return void
     */
    public function setIsNeedCheckLicense(bool $isNeed): void
    {
        $this->setData(self::IS_NEED_CHECK_LICENSE, $isNeed);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\LicenseValidation\InstanceKey[]|null
     */
    public function getInstanceKeys(): ?array
    {
        return (array)$this->getData(self::INSTANCE_KEYS);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\LicenseValidation\InstanceKey[] $instanceKeys
     * @return void
     */
    public function setInstanceKeys(array $instanceKeys): void
    {
        $this->setData(self::INSTANCE_KEYS, $instanceKeys);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module[]|null
     */
    public function getModules(): ?array
    {
        return (array)$this->getData(self::MODULES);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module[] $modules
     * @return void
     */
    public function setModules(array $modules): void
    {
        $this->setData(self::MODULES, $modules);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Message[]|null
     */
    public function getMessages(): ?array
    {
        return (array)$this->getData(self::MESSAGES);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Message[] $messages
     * @return void
     */
    public function setMessages(array $messages): void
    {
        $this->setData(self::MESSAGES, $messages);
    }
}
