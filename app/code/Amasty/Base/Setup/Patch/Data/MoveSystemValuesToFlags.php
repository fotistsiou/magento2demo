<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Setup\Patch\Data;

use Amasty\Base\Model\FlagsManager;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MoveSystemValuesToFlags implements DataPatchInterface
{
    private const SYSTEM_CONFIG_PATH = 'amasty_base/system_value/';
    private const SYSTEM_VALUE_FIRST_MODULE_RUN = 'first_module_run';
    private const SYSTEM_VALUE_LAST_UPDATE = 'last_update';
    private const SYSTEM_VALUE_REMOVE_DATE = 'remove_date';

    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var FlagsManager
     */
    private $flagsManager;

    public function __construct(
        ConfigInterface $resourceConfig,
        ScopeConfigInterface $scopeConfig,
        FlagsManager $flags
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
        $this->flagsManager = $flags;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        if ($firstModuleRun = $this->getOldValue(self::SYSTEM_VALUE_FIRST_MODULE_RUN)) {
            $this->flagsManager->setFirstModuleRun($firstModuleRun);
        }
        if ($lastUpdate = $this->getOldValue(self::SYSTEM_VALUE_LAST_UPDATE)) {
            $this->flagsManager->setLastUpdate($lastUpdate);
        }
        if ($lastRemoval = $this->getOldValue(self::SYSTEM_VALUE_REMOVE_DATE)) {
            $this->flagsManager->setLastRemoval($lastRemoval);
        }

        $this->resourceConfig->getConnection()->delete(
            $this->resourceConfig->getMainTable(),
            'path LIKE "' . self::SYSTEM_CONFIG_PATH . '%"'
        );

        return $this;
    }

    private function getOldValue(string $path): ?int
    {
        $result = $this->scopeConfig->getValue(self::SYSTEM_CONFIG_PATH . $path);

        return $result
            ? (int)$result
            : null;
    }
}
