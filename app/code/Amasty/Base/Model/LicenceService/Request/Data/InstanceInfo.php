<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\LicenceService\Request\Data;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Platform;
use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ExtensibleDataInterface;

class InstanceInfo extends SimpleDataObject implements ExtensibleDataInterface
{
    public const SYSTEM_INSTANCE_KEY = 'system_instance_key';
    public const MODULES = 'modules';
    public const DOMAINS = 'domains';
    public const PLATFORM = 'platform';
    public const CUSTOMER_INSTANCE_KEY = 'customer_instance_key';
    public const IS_PRODUCTION = 'is_production';

    /**
     * @param string|null $systemInstanceKey
     * @return $this
     */
    public function setSystemInstanceKey(?string $systemInstanceKey): self
    {
        return $this->setData(self::SYSTEM_INSTANCE_KEY, $systemInstanceKey);
    }

    /**
     * @return string|null
     */
    public function getSystemInstanceKey(): ?string
    {
        return $this->getData(self::SYSTEM_INSTANCE_KEY);
    }

    /**
     * @param \Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Module[]|null $modules
     * @return $this
     */
    public function setModules(array $modules): self
    {
        return $this->setData(self::MODULES, $modules);
    }

    /**
     * @return \Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Module[]|null
     */
    public function getModules(): ?array
    {
        return $this->getData(self::MODULES);
    }

    /**
     * @param \Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Domain[]|null $domains
     * @return $this
     */
    public function setDomains(array $domains): self
    {
        return $this->setData(self::DOMAINS, $domains);
    }

    /**
     * @return \Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Domain[]|null
     */
    public function getDomains(): ?array
    {
        return $this->getData(self::DOMAINS);
    }

    /**
     * @param \Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Platform $platform
     * @return $this
     */
    public function setPlatform(Platform $platform): self
    {
        return $this->setData(self::PLATFORM, $platform);
    }

    /**
     * @return \Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Platform
     */
    public function getPlatform(): Platform
    {
        return $this->getData(self::PLATFORM);
    }

    /**
     * @param string[] $instanceKey
     * @return $this
     */
    public function setCustomerInstanceKey(array $instanceKey): self
    {
        return $this->setData(self::CUSTOMER_INSTANCE_KEY, $instanceKey);
    }

    /**
     * @return string[]
     */
    public function getCustomerInstanceKey(): array
    {
        return $this->getData(self::CUSTOMER_INSTANCE_KEY);
    }

    /**
     * @param bool|null $isProduction
     * @return $this
     */
    public function setIsProduction(?bool $isProduction): self
    {
        return $this->setData(self::IS_PRODUCTION, $isProduction);
    }

    /**
     * @return bool|null
     */
    public function getIsProduction(): ?bool
    {
        $isProduction = $this->getData(self::IS_PRODUCTION);

        return $isProduction === null
            ? $isProduction
            : (bool)$isProduction;
    }
}
