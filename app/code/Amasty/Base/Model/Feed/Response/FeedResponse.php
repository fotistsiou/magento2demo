<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Feed\Response;

use Magento\Framework\DataObject;

class FeedResponse extends DataObject implements FeedResponseInterface
{
    public const CONTENT = 'content';
    public const STATUS = 'status';
    public const IS_NEED_TO_UPDATE_CACHE = 'is_need_to_update_cache';

    /**
     * @var string[]
     */
    private $failedStatuses = ['404'];

    /**
     * @var string[]
     */
    private $skipCacheUpdateStatuses = ['404', '304'];

    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    public function setContent(?string $content): FeedResponseInterface
    {
        $this->setData(self::CONTENT, $content);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus(?string $status): FeedResponseInterface
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    public function isNeedToUpdateCache(): bool
    {
        return !empty($this->getContent()) && !in_array($this->getStatus(), $this->skipCacheUpdateStatuses);
    }

    public function isFailed(): bool
    {
        return empty($this->getContent()) || in_array($this->getStatus(), $this->failedStatuses);
    }
}
