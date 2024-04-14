<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Setup\Patch\Data;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\MediaStorage\Model\File\Storage\ConfigFactory;

/**
 * Updates resoucre config path to add pub/media/amasty folder in allowed list.
 * Need for Remote Storage compatibility.
 */
class UpdateResourceConfigPatch implements DataPatchInterface
{
    private const CONFIG_CACHE_FILE = 'resource_config.json';

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $varDir;

    public function __construct(
        ConfigFactory $configFactory,
        Filesystem $filesystem
    ) {
        $this->configFactory = $configFactory;
        $this->varDir = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
    }

    public function apply()
    {
        $configCacheFile = $this->varDir->getAbsolutePath(self::CONFIG_CACHE_FILE);
        $config = $this->configFactory->create(['cacheFile' => $configCacheFile]);
        $config->save();

        return $this;
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
