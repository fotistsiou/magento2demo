<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Utils\Http\Response\Entity;

use Magento\Framework\DataObject;

class Config extends DataObject
{
    public const CLASS_NAME = 'class_name';
    public const TYPE = 'type';
    public const DATA_PROCESSOR = 'data_processor';

    public function getClassName(): string
    {
        return $this->getData(self::CLASS_NAME);
    }

    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    public function getDataProcessor(): ?DataProcessorInterface
    {
        return $this->getData(self::DATA_PROCESSOR);
    }
}
