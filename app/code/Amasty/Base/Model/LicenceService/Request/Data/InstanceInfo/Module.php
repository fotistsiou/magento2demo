<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;

use Amasty\Base\Model\SimpleDataObject;

class Module extends SimpleDataObject
{
    public const STATUS = 'status';
    public const CODE = 'code';
    public const VERSION = 'version';

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->getData(self::CODE);
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version): self
    {
        return $this->setData(self::VERSION, $version);
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->getData(self::VERSION);
    }
}
