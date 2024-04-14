<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Utils\Http\Response\Entity;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\ObjectManagerInterface;

class Converter
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        ObjectManagerInterface $objectManager,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->objectManager = $objectManager;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param mixed $row
     * @param Config $entityConfig
     * @return SimpleDataObject
     */
    public function convertToObject($row, Config $entityConfig): SimpleDataObject
    {
        if ($entityConfig->getDataProcessor()) {
            $row = $entityConfig->getDataProcessor()->process($row);
        }

        $object = $this->objectManager->create($entityConfig->getClassName());
        $this->dataObjectHelper->populateWithArray(
            $object,
            $row,
            $entityConfig->getClassName()
        );

        return $object;
    }
}
