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
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;

class Page implements CollectorInterface
{
    private const CACHE_NAME = 'Page Cache';
    private const TYPE_FASTLY = 42;
    private const TYPE_LITEMAGE = 'LITEMAGE';
    private const UNKNOWN_TYPE = 'Unknown';

    private const NAMES_MAP = [
        PageCacheConfig::BUILT_IN => 'Built-in',
        PageCacheConfig::VARNISH => 'Varnish',
        self::TYPE_LITEMAGE => 'Litemage',
        self::TYPE_FASTLY => 'Fastly'
    ];

    /**
     * @var string
     */
    private $type = self::UNKNOWN_TYPE;

    /**
     * @var PageCacheConfig
     */
    private $pageCacheConfig;

    /**
     * @var CacheInfoInterfaceFactory
     */
    private $cacheInfoFactory;

    public function __construct(
        PageCacheConfig $pageCacheConfig,
        CacheInfoInterfaceFactory $cacheInfoFactory
    ) {
        $this->pageCacheConfig = $pageCacheConfig;
        $this->cacheInfoFactory = $cacheInfoFactory;
    }

    public function get(): CacheInfoInterface
    {
        $type = $this->pageCacheConfig->getType() ?: PageCacheConfig::BUILT_IN;
        if (!empty(self::NAMES_MAP[$type])) {
            $this->setType(self::NAMES_MAP[$type]);
        }

        $cacheInfo = $this->cacheInfoFactory->create();
        $cacheInfo->setName($this->getName());
        $cacheInfo->setStatus($this->resolveStatus());
        $cacheInfo->setAdditionalInfo($this->resolveAdditionalInfo());

        return $cacheInfo;
    }

    private function setType(string $type): void
    {
        $this->type = $type;
    }

    private function isActive(): bool
    {
        return $this->pageCacheConfig->isEnabled();
    }

    private function getName(): string
    {
        return self::CACHE_NAME;
    }

    private function resolveStatus(): string
    {
        return $this->isActive() ? (string)__('Active') : (string)__('Inactive');
    }

    private function resolveAdditionalInfo(): string
    {
        return $this->isActive() ? (string) __('Type: %1', $this->type) : '';
    }
}
