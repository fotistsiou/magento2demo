<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Feed;

use Amasty\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection\ExpiredFactory;
use Amasty\Base\Model\Config;
use Amasty\Base\Model\Feed\FeedTypes\News;
use Amasty\Base\Model\FlagsManager;
use Magento\AdminNotification\Model\InboxFactory;

class NewsProcessor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var InboxFactory
     */
    private $inboxFactory;

    /**
     * @var ExpiredFactory
     */
    private $expiredFactory;

    /**
     * @var FeedTypes\News
     */
    private $newsFeed;

    /**
     * @var FlagsManager
     */
    private $flagsManager;

    public function __construct(
        Config $config,
        FlagsManager $flagsManager,
        InboxFactory $inboxFactory,
        ExpiredFactory $expiredFactory,
        News $newsFeed
    ) {
        $this->config = $config;
        $this->inboxFactory = $inboxFactory;
        $this->expiredFactory = $expiredFactory;
        $this->newsFeed = $newsFeed;
        $this->flagsManager = $flagsManager;
    }

    /**
     * @return void
     */
    public function checkUpdate()
    {
        if ($this->config->getFrequencyInSec() + $this->flagsManager->getLastUpdate() > time()) {
            return;
        }

        if ($feedData = $this->newsFeed->execute()) {
            $inbox = $this->inboxFactory->create();
            $inbox->parse([$feedData]);
        }
        $this->flagsManager->setLastUpdate();
    }

    /**
     * @return void
     */
    public function removeExpiredItems()
    {
        if ($this->flagsManager->getLastRemoval() + Config::REMOVE_EXPIRED_FREQUENCY > time()) {
            return;
        }

        $collection = $this->expiredFactory->create();
        foreach ($collection as $model) {
            $model->setIsRemove(1)->save();
        }
        $this->flagsManager->setLastRemoval();
    }
}
