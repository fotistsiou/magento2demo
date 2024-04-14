<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu;

use Magento\Framework\DataObject;

/**
 * Representation of Amasty Menu Item to be used for further menu building
 */
class MenuItem extends DataObject
{
    public const CONFIG = 'config';
    public const RESOURCES = 'resources';

    /**
     * @param array $config
     * @return MenuItem
     */
    public function setConfig(array $config): MenuItem
    {
        return $this->setData(self::CONFIG, $config);
    }

    /**
     * @return string[]
     */
    public function getConfig(): array
    {
        return (array)$this->_getData(self::CONFIG);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getConfigByKey(string $key)
    {
        return $this->getConfig()[$key] ?? null;
    }

    /**
     * @param string[] $resources
     * @return MenuItem
     */
    public function setResources(array $resources): MenuItem
    {
        return $this->setData(self::RESOURCES, $resources);
    }

    /**
     * @return string[]
     */
    public function getResources(): array
    {
        return (array)$this->_getData(self::RESOURCES);
    }
}
