<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Plugin\Framework\Mview\View\Subscription;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Mview\View\Subscription;
use Magento\Framework\Mview\View\SubscriptionInterface;

/**
 * Wrap triggers creation/removal with table exist check
 *
 * Fix an extensions install after deletion while an indexers in a schedule mode
 * @since 1.9.4
 * @see https://github.com/magento/magento2/issues/34668
 */
class FixInstall
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function aroundRemove(Subscription $subject, callable $proceed): SubscriptionInterface
    {
        if ($this->isShouldTerminateOperation($subject->getTableName())) {
            return $subject;
        }

        return $proceed();
    }

    public function aroundCreate(Subscription $subject, callable $proceed, bool $save = true): SubscriptionInterface
    {
        if ($this->isShouldTerminateOperation($subject->getTableName())) {
            return $subject;
        }

        return $proceed($save);
    }

    private function isShouldTerminateOperation(string $tableName): bool
    {
        return $this->isAmastyTable($tableName) && !$this->isTableExist($tableName);
    }

    private function isAmastyTable(string $tableName): bool
    {
        return strripos($tableName, 'amasty') !== false || strripos($tableName, 'am_') === 0;
    }

    public function isTableExist(string $tableName): bool
    {
        return $this->resource->getConnection()->isTableExists($this->resource->getTableName($tableName));
    }
}
