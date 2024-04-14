<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Transport;

use Amasty\Smtp\Api\BlacklistRepositoryInterface;
use Amasty\Smtp\Model\Logger\DebugLogger;
use Amasty\Smtp\Model\Logger\MessageLogger;
use Amasty\Smtp\Model\Provider\ConnectionProviderAdapter;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Mail\Header\MessageId;
use Laminas\Validator\EmailAddress as EmailAddressValidator;
use Magento\Framework\App\State;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class TestEmailRunner extends \Amasty\Smtp\Model\Transport
{
    public function __construct(//phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
        MessageInterface $message,
        MessageInterface $mailMessage,
        MessageLogger $messageLogger,
        DebugLogger $debugLogger,
        \Amasty\Smtp\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Smtp\Model\Config $config,
        \Magento\Framework\Registry $registry,
        Socket $socket,
        ConnectionProviderAdapter $connectionProviderAdapter,
        MessageId $messageId,
        MessageIdGenerator $messageIdGenerator,
        $host = '127.0.0.1',
        array $parameters = [],
        ?BlacklistRepositoryInterface $blacklistRepository = null,
        ?EmailAddressValidator $emailValidator = null,
        ?State $appState = null,
        ?MessageManager $messageManager = null
    ) {
        parent::__construct(
            $message,
            $mailMessage,
            $messageLogger,
            $debugLogger,
            $helper,
            $objectManager,
            $config,
            $registry,
            $socket,
            $connectionProviderAdapter,
            $messageId,
            $messageIdGenerator,
            $host,
            $parameters,
            $blacklistRepository,
            $emailValidator,
            $appState,
            $messageManager
        );
    }
}
