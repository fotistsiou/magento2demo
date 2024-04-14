<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Redis;

class RedisTypeConfig
{
    /**
     * Config path
     *
     * @var string
     */
    private $path;

    /**
     * Searched values in config
     *
     * @var array
     */
    private $values;

    public function __construct(
        string $path,
        array $values
    ) {
        $this->path = $path;
        $this->values = $values;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
