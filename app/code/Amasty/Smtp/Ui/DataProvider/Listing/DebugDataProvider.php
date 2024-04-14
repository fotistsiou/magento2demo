<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Ui\DataProvider\Listing;

use Magento\Ui\DataProvider\AbstractDataProvider;

class DebugDataProvider extends AbstractDataProvider
{
    /**
     * @var \Amasty\Smtp\Model\ResourceModel\Debug\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\Smtp\Model\ResourceModel\Debug\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }
}
