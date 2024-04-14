<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Ui\DataProvider\Listing;

use Amasty\Smtp\Model\ResourceModel\Log;
use Amasty\Smtp\Model\ResourceModel\Log\CollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

class LogDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
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

    /**
     * Add full text search filter to collection
     *
     * @param Filter $filter
     * @return void
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() !== 'fulltext') {
            $this->getCollection()->addFieldToFilter(
                $filter->getField(),
                [$filter->getConditionType() => $filter->getValue()]
            );
        } else {
            $value = trim($filter->getValue());
            $this->getCollection()->addFieldToFilter(
                [
                    ['attribute' => Log::SUBJECT],
                    ['attribute' => Log::RECIPIENT_EMAIL]
                ],
                [
                    ['like' => "%{$value}%"],
                    ['like' => "%{$value}%"]
                ]
            );
        }
    }
}
