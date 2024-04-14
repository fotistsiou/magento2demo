<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Data;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Magento\Framework\Api\ExtensibleDataInterface;

class RegisteredInstance extends SimpleDataObject implements ExtensibleDataInterface
{
    public const INSTANCES = 'instances';
    public const CURRENT_INSTANCE = 'current_instance';

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance|null
     */
    public function getCurrentInstance(): ?Instance
    {
        return $this->getData(self::CURRENT_INSTANCE);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance|null $instance
     * @return $this
     */
    public function setCurrentInstance(?Instance $instance): self
    {
        return $this->setData(self::CURRENT_INSTANCE, $instance);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance[]
     */
    public function getInstances(): array
    {
        return $this->getData(self::INSTANCES) ?? [];
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance[] $instances
     * @return $this
     */
    public function setInstances(array $instances): self
    {
        return $this->setData(self::INSTANCES, $instances);
    }
}
