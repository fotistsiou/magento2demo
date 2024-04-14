<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Redis;

use Magento\Framework\App\DeploymentConfig\Reader as ConfigReader;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Stdlib\ArrayManager;

class RedisTypesResolver
{
    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RedisTypeConfig[]
     */
    private $redisTypeConfigPool;

    /**
     * @param ConfigReader $configReader
     * @param ArrayManager $arrayManager
     * @param array $redisTypeConfigPool
     */
    public function __construct(
        ConfigReader $configReader,
        ArrayManager $arrayManager,
        array $redisTypeConfigPool
    ) {
        $this->configReader = $configReader;
        $this->arrayManager = $arrayManager;
        $this->initRedisTypeConfigPool($redisTypeConfigPool);
    }

    /**
     * Returns enabled redis cache types
     *
     * @return string[]
     */
    public function get(): array
    {
        $result = [];

        try {
            $config = $this->configReader->load(ConfigFilePool::APP_ENV);
        } catch (\Exception $e) {
            return $result;
        }
        foreach ($this->redisTypeConfigPool as $name => $redisConfig) {
            if ($this->resolveConfig($config, $redisConfig->getPath(), $redisConfig->getValues())) {
                $result[] = $name;
            }
        }

        return $result;
    }

    private function resolveConfig(array $config, string $path, array $values): bool
    {
        return in_array(
            $this->arrayManager->get($path, $config),
            $values
        );
    }

    private function initRedisTypeConfigPool(array $redisTypeConfigPool): void
    {
        foreach ($redisTypeConfigPool as $typeConfig) {
            if (!($typeConfig instanceof RedisTypeConfig)) {
                throw new \LogicException(
                    'The RedisTypeConfig instance "' . $typeConfig . '" must be type of'
                    . RedisTypeConfig::class
                );
            }
        }
        $this->redisTypeConfigPool = $redisTypeConfigPool;
    }
}
