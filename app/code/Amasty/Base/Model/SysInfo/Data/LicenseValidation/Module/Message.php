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

class Message extends SimpleDataObject implements ExtensibleDataInterface
{
    /**
     * Expected message types
     */
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const INFO = 'info';
    public const WARNING = 'warning';

    public const TYPE = 'type';
    public const CONTENT = 'content';

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

    /**
     * @return string
     */
    public function getContent(): string
    {
        return (string)$this->getData(self::CONTENT);
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->setData(self::CONTENT, $content);
    }
}
