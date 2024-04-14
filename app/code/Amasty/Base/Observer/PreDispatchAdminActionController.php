<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Observer;

use Amasty\Base\Model\Feed\NewsProcessor;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager;
use Psr\Log\LoggerInterface;

class PreDispatchAdminActionController implements ObserverInterface
{
    /**
     * @var Session
     */
    private $backendSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NewsProcessor
     */
    private $newsProcessor;

    /**
     * @var Manager
     */
    private $manager;

    public function __construct(
        NewsProcessor $newsProcessor,
        Session $backendAuthSession,
        LoggerInterface $logger,
        Manager $manager
    ) {
        $this->backendSession = $backendAuthSession;
        $this->logger = $logger;
        $this->newsProcessor = $newsProcessor;
        $this->manager = $manager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->backendSession->isLoggedIn() && $this->manager->isEnabled('Magento_AdminNotification')) {
            try {
                $this->newsProcessor->checkUpdate();
                $this->newsProcessor->removeExpiredItems();
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }
    }
}
