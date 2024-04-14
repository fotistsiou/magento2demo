<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Ui\DataProvider\Blacklist;

use Amasty\Smtp\Api\Data\BlacklistInterface;
use Amasty\Smtp\Model\ResourceModel\Blacklist\CollectionFactory as BlacklistCollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Form extends AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        BlacklistCollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function getData(): array
    {
        $data = parent::getData();

        if ($data['totalRecords'] > 0 && isset($data['items'][0][BlacklistInterface::BLACKLIST_ID])) {
            $blacllistId = (int)$data['items'][0][BlacklistInterface::BLACKLIST_ID];
            $data[$blacllistId] = $data['items'][0];
        } else {
            $data = [];
        }

        return $data;
    }
}
