<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Plugin\Indexer\Model\Indexer;

use Magento\Framework\Mview\ConfigInterface;
use Magento\Indexer\Model\Indexer;

/**
 * Fix an issue - after first setup:upgrade, mview.xml of a module is not collecting.
 * @since 1.9.4
 * @see https://github.com/magento/magento2/issues/34668
 */
class SkipException
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function aroundIsScheduled(Indexer $subject, callable $proceed): bool
    {
        if ($this->isAmastyIndexer($subject)
            && !$this->config->getView($subject->getViewId())
        ) {
            return false;
        }

        return $proceed();
    }

    public function aroundReindexAll(Indexer $subject, callable $proceed): void
    {
        if ($this->isAmastyIndexer($subject)
            && !$this->config->getView($subject->getViewId())
        ) {
            return;
        }

        $proceed();
    }

    private function isAmastyIndexer(Indexer $subject): bool
    {
        return stripos($subject->getActionClass(), 'Amasty') === 0;
    }
}
