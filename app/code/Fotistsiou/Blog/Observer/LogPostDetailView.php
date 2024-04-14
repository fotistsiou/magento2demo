<?php declare(strict_types=1);

namespace Fotistsiou\Blog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class LogPostDetailView implements ObserverInterface
{

    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer)
    {
        $request = $observer->getData('request');
        $this->logger->info('Blog Post Detail Viewed', [
            'params' => $request->getParams()
        ]);
    }
}
