<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector;

use Magento\Config\Model\ResourceModel\Config\Data\Collection as ConfigCollection;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;

class Config implements CollectorInterface
{
    public const CONFIG_PATH_KEY = 'path';
    public const CONFIG_VALUE_KEY = 'value';

    /**
     * @var ConfigCollectionFactory
     */
    private $configCollectionFactory;

    public function __construct(
        ConfigCollectionFactory $configCollectionFactory
    ) {
        $this->configCollectionFactory = $configCollectionFactory;
    }

    public function get(): array
    {
        $configData = [];

        $configCollection = $this->configCollectionFactory->create()
            ->addFieldToSelect([self::CONFIG_PATH_KEY, self::CONFIG_VALUE_KEY]);

        $this->addConditionSql($configCollection);

        foreach ($configCollection->getData() as $config) {
            $path = $this->preparePath($config[self::CONFIG_PATH_KEY]);
            $configData[$path] = $config[self::CONFIG_VALUE_KEY];
        }

        return $configData;
    }

    private function preparePath(string $path): string
    {
        return str_replace('/', '_', $path);
    }

    private function addConditionSql(ConfigCollection $collection): void
    {
        $connection = $collection->getConnection();
        $likeSqlCondition = $this->getConditions($connection, $this->getLikeKeywords(), true);
        $notLikeSqlCondition = $this->getConditions($connection, $this->getNotLikeKeywords(), false);

        $collection->getSelect()
            ->where($likeSqlCondition)
            ->where($notLikeSqlCondition);
    }

    private function getLikeKeywords(): array
    {
        return ['am', 'carriers', 'payment'];
    }

    private function getNotLikeKeywords(): array
    {
        return [
            'token',
            'key',
            'secret',
            'password',
            'pwd',
            'credentials',
            'access_license_number',
            'userid',
            'account_id',
            'merchant_id',
            'username',
            'amazon'
        ];
    }

    private function getConditions(
        AdapterInterface $connection,
        array $keywords,
        bool $isLike
    ): string {
        $conditions = [];
        $operator = $isLike ? Select::SQL_OR : Select::SQL_AND;

        foreach ($keywords as $keyword) {
            $conditions[] = $connection->prepareSqlCondition(
                self::CONFIG_PATH_KEY,
                $isLike ? ['like' => $keyword . '%'] : ['nlike' => '%' . $keyword . '%']
            );
        }

        return implode(' ' . $operator . ' ', $conditions);
    }
}
