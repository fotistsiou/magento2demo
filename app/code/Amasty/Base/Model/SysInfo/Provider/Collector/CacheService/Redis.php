<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\CacheService;

use Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Info\CacheInfoInterface;
use Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Info\CacheInfoInterfaceFactory;
use Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Redis\RedisTypesResolver;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;

class Redis implements CollectorInterface
{
    private const CACHE_NAME = 'Redis';

    /**
     * @var RedisTypesResolver
     */
    private $redisTypesResolver;

    /**
     * @var CacheInfoInterfaceFactory
     */
    private $cacheInfoFactory;

    public function __construct(
        CacheInfoInterfaceFactory $cacheInfoFactory,
        RedisTypesResolver $redisTypesResolver
    ) {
        $this->cacheInfoFactory = $cacheInfoFactory;
        $this->redisTypesResolver = $redisTypesResolver;
    }

    public function get(): CacheInfoInterface
    {
        $enabledRedisTypes = $this->redisTypesResolver->get();

        $cacheInfo = $this->cacheInfoFactory->create();
        $cacheInfo->setName($this->getName());
        $cacheInfo->setStatus($this->resolveStatus($enabledRedisTypes));
        $cacheInfo->setAdditionalInfo($this->resolveAdditionalInfo($enabledRedisTypes));

        return $cacheInfo;
    }

    private function getName(): string
    {
        return self::CACHE_NAME;
    }

    private function resolveStatus(array $enabledRedisTypes): string
    {
        return !empty($enabledRedisTypes) ? (string)__('Active') : (string)__('Inactive');
    }

    private function resolveAdditionalInfo(array $enabledRedisTypes): string
    {
        return !empty($enabledRedisTypes) ? implode(', ', $enabledRedisTypes) : '';
    }
}
