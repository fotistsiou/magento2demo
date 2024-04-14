<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Setup;

use Amasty\Smtp\Model\ResourceModel\Blacklist;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public const TABLES_TO_DROP = [
        'amasty_amsmtp_log',
        'amasty_amsmtp_debug',
        Blacklist::TABLE_NAME
    ];
    public const TABLE_CORE_CONFIG_DATA = 'core_config_data';
    public const AMASTY_SMTP_CONFIG_SECTION = 'amsmtp';

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();
        $this->uninstallTables($setup)->uninstallConfigData($setup);
        $setup->endSetup();
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        foreach (self::TABLES_TO_DROP as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): void
    {
        $configTable = $setup->getTable(self::TABLE_CORE_CONFIG_DATA);
        $setup->getConnection()->delete(
            $configTable,
            sprintf("`path` LIKE '%s%%'", self::AMASTY_SMTP_CONFIG_SECTION)
        );
    }
}
