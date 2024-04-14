<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ExtensibleDataInterface;

class VerifyStatus extends SimpleDataObject implements ExtensibleDataInterface
{
    public const STATUS = 'status';
    public const TYPE = 'type';

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (string)$this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->setData(self::TYPE, $type);
    }
}
