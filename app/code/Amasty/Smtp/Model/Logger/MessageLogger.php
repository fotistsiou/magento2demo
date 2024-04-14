<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Logger;

use Amasty\Smtp\Model\Log;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;

class MessageLogger
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Amasty\Smtp\Model\Logger\DebugLogger
     */
    protected $debugLogger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;

    /**
     * @var \Amasty\Smtp\Helper\Data
     */
    protected $helper;

    /**
     * @var \Amasty\Smtp\Model\LogFactory
     */
    private $logFactory;

    /**
     * @var \Amasty\Smtp\Model\ResourceModel\Log
     */
    private $logResource;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        DebugLogger $debugLogger,
        \Amasty\Smtp\Helper\Data $helper,
        DateTime $coreDate,
        \Amasty\Smtp\Model\LogFactory $logFactory,
        \Amasty\Smtp\Model\ResourceModel\Log $logResource
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->coreDate = $coreDate;
        $this->debugLogger = $debugLogger;
        $this->helper = $helper;
        $this->logFactory = $logFactory;
        $this->logResource = $logResource;
    }

    public function log($message)
    {
        $storeId = $this->helper->getCurrentStore();

        if ($this->scopeConfig->isSetFlag(
            'amsmtp/general/log',
            ScopeInterface::SCOPE_STORE,
            $storeId
        )) {
            if (class_exists(\Laminas\Mail\Message::class, false)) {
                $recipients = [\Laminas\Mail\Message::fromString($message->getRawMessage())->getTo()->key()];
                $body = $message->getBody();

                if ($message instanceof \Magento\Framework\Mail\EmailMessage) {
                    $body = current($message->getBody()->getParts())->getRawContent();
                } elseif ($body instanceof \Laminas\Mime\Message) {
                    $body = $body->generateMessage();
                } else {
                    $body = (string)$body;
                }
            } else {
                $recipients = $message->getRecipients();
                $body = ($message->getBody()) ? $message->getBody()->getRawContent() : '';
            }

            $recipient = implode(', ', $recipients);

            /** @var Log $logMessage */
            $logMessage = $this->logFactory->create();
            $logMessage->setData([
                'created_at'        => $this->coreDate->gmtDate(),
                'subject'           => $message->getSubject(),
                'body'              => $body,
                'recipient_email'   => $recipient,
                'status'            => Log::STATUS_PENDING
            ]);

            $this->logResource->save($logMessage);

            return $logMessage->getId();
        } else {
            return false;
        }
    }

    public function updateStatus($logId, $status)
    {
        $storeId = $this->helper->getCurrentStore();

        if ($this->scopeConfig->isSetFlag('amsmtp/general/log', ScopeInterface::SCOPE_STORE, $storeId)) {
            /** @var Log $logMessage */
            $logMessage = $this->logFactory->create();

            $this->logResource->load($logMessage, $logId);

            if ($logMessage->getId()) {
                $logMessage
                    ->setStatus($status)
                    ->save();
            }
        }
    }

    public function autoClear()
    {
        $days = (int)$this->scopeConfig->getValue('amsmtp/clear/email');

        if ($days) {
            $this->debugLogger->log(__('Starting to auto clear debug log (after %1 days)', $days));

            $this->logResource->clear($days);
        }
    }
}
