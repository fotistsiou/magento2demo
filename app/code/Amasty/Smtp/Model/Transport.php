<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model;

use Amasty\Base\Model\GetCustomerIp;
use Amasty\Smtp\Api\BlacklistRepositoryInterface;
use Amasty\Smtp\Helper\Data;
use Amasty\Smtp\Model\Logger\DebugLogger;
use Amasty\Smtp\Model\Logger\MessageLogger;
use Amasty\Smtp\Model\Provider\ConnectionProviderAdapter;
use Amasty\Smtp\Model\Transport\MessageIdGenerator;
use Amasty\Smtp\Plugin\Mail\Template\TransportBuilderByStorePlugin;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Mail\Header\MessageId;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Validator\EmailAddress as EmailAddressValidator;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Transport implements TransportInterface
{
    public const SOCK_AUTH_OPTIONS = [
        'timeout' => 20
    ];

    /**
     * Remote smtp hostname or i.p.
     *
     * @var string
     */
    protected $_host;

    /**
     * Port number
     *
     * @var int|null
     */
    protected $_port;

    /**
     * Local client hostname or i.p.
     *
     * @var string
     */
    protected $_name = 'localhost';

    /**
     * Parameters.
     *
     * @var array
     */
    protected $_config;

    /**
     * Authentication type OPTIONAL
     *
     * @var string
     */
    protected $_auth;

    /**
     * @var MessageInterface|EmailMessageInterface
     */
    protected $_message;

    /**
     * @var MessageInterface
     */
    protected $_mailMessage;

    /**
     * @var MessageLogger
     */
    protected $messageLogger;

    /**
     * @var DebugLogger
     */
    protected $debugLogger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var ConnectionProviderAdapter
     */
    private $connectionProviderAdapter;

    /**
     * @var MessageId
     */
    private $messageId;

    /**
     * @var MessageIdGenerator
     */
    private $messageIdGenerator;

    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    /**
     * @var bool
     */
    private $isTest = false;

    /**
     * @var EmailAddressValidator
     */
    private $emailValidator;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var MessageManager
     */
    private $messageManager;

    public function __construct(
        $message,
        MessageInterface $mailMessage,
        MessageLogger $messageLogger,
        DebugLogger $debugLogger,
        Data $helper,
        ObjectManagerInterface $objectManager,
        Config $config,
        Registry $registry,
        Socket $socket,
        ConnectionProviderAdapter $connectionProviderAdapter,
        MessageId $messageId,
        MessageIdGenerator $messageIdGenerator,
        $host = GetCustomerIp::LOCAL_IP,
        array $parameters = [],
        ?BlacklistRepositoryInterface $blacklistRepository = null,
        ?EmailAddressValidator $emailValidator = null,
        ?State $appState = null,
        ?MessageManager $messageManager = null
    ) {
        if (isset($parameters['name'])) {
            $this->_name = $parameters['name'];
        }
        if (isset($parameters['port'])) {
            $this->_port = $parameters['port'];
        }
        if (isset($parameters['auth'])) {
            $this->_auth = $parameters['auth'];
        }

        $this->_host = $host;
        $this->_config = $parameters;
        $this->_message = $message;
        $this->_mailMessage = $mailMessage;
        $this->messageLogger = $messageLogger;
        $this->debugLogger = $debugLogger;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->registry = $registry;
        $this->socket = $socket;
        $this->connectionProviderAdapter = $connectionProviderAdapter;
        $this->messageId = $messageId;
        $this->messageIdGenerator = $messageIdGenerator;
        $this->blacklistRepository = $blacklistRepository ?? $objectManager->get(BlacklistRepositoryInterface::class);
        $this->emailValidator = $emailValidator ?? $objectManager->get(EmailAddressValidator::class);
        $this->appState = $appState ?? $objectManager->get(State::class);
        $this->messageManager = $messageManager ?? $objectManager->get(MessageManager::class);
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws MailException
     */
    public function sendMessage()
    {
        $this->debugLogger->log(__('Ready to send e-mail at amsmtp/transport::sendMessage()'));

        if ($this->_message->getBody()) {
            $logId = $this->messageLogger->log($this->_message);
            $message = $this->_message;
        } else {
            $logId = $this->messageLogger->log($this->_mailMessage);
            $message = $this->_mailMessage;
        }

        $toEmail = Message::fromString($message->getRawMessage())->getTo()->key();
        $storeId = $this->helper->getCurrentStore();

        if ($this->config->isBlacklistEnable() && $this->isInBlacklist($toEmail)) {
            $this->debugLogger->log(__(
                'Error sending e-mail: email %1 is in blacklist.',
                $toEmail
            ));
            $this->messageLogger->updateStatus($logId, Log::STATUS_BLACKLISTED);

            $currentArea = Area::AREA_GLOBAL;

            try {
                $currentArea = $this->appState->getAreaCode();
            } catch (LocalizedException $e) {
                ;
            }

            if ($currentArea === Area::AREA_ADMINHTML) {
                $this->messageManager->addWarningMessage(
                    __(
                        'Error sending e-mail: email %1 is in blacklist.',
                        $toEmail
                    )
                );

                throw new MailException(
                    __(
                        'Error sending e-mail: email %1 is in blacklist.',
                        $toEmail
                    )
                );
            }

            return null;
        }

        try {
            $isEnabledDelivery = !$this->config->getDisableDelivery($storeId);

            if (!$this->isTest && $isEnabledDelivery) {
                $this->setFromData(false, $storeId, ScopeInterface::SCOPE_STORE);
            }

            if ($this->isTest || $isEnabledDelivery) {
                /** @var Smtp $laminasTransport */
                $laminasTransport = $this->objectManager->create(Smtp::class);
                $laminasSmtpOptions = new SmtpOptions(
                    [
                        'name' => $this->_name,
                        'host' => $this->_host,
                        'port' => $this->_port,
                        'connection_config' => $this->_config
                    ]
                );
                if ($this->_auth) {
                    $laminasSmtpOptions->setConnectionClass($this->_auth);
                }

                try {
                    $provider = $this->connectionProviderAdapter->get((string)$this->_auth);
                    $laminasTransport->setConnection($provider->getConnection($laminasSmtpOptions));
                } catch (\LogicException $e) {
                    null;
                }

                $laminasTransport->setOptions($laminasSmtpOptions);
                $laminasTransport->send(
                    $this->modifyMessage(Message::fromString($this->_message->getRawMessage()))
                );

                $this->debugLogger->log(__('E-mail sent successfully at amsmtp/transport::sendMessage().'));
            } else {
                $this->debugLogger->log(__('Actual delivery disabled under settings.'));
            }

            $this->messageLogger->updateStatus($logId, Log::STATUS_SENT);
        } catch (\Exception $e) {
            $this->debugLogger->log(__('Error sending e-mail: %1', $e->getMessage()));
            $this->messageLogger->updateStatus($logId, Log::STATUS_FAILED);

            throw new MailException(__($e->getMessage()));
        }
    }

    /**
     * @param $testEmail
     * @param $storeId
     * @param string $scope
     *
     * @throws MailException
     */
    public function runTest($testEmail, $storeId, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $this->checkConnection();

        if ($testEmail) {
            $this->setFromData(true, $storeId, $scope);
            $this->isTest = true;
            $this->debugLogger->log(
                __(
                    'Preparing to send test e-mail to %1 from %2',
                    $testEmail,
                    $this->_config['custom_sender']['email']
                )
            );

            $this->_mailMessage->addTo($testEmail)
                ->setSubject((string)__('Amasty SMTP Email Test Message'))
                ->setBodyText((string)__('If you see this e-mail, your configuration is OK.'));

            try {
                $this->sendMessage();
                $this->debugLogger->log(__('Test e-mail was sent successfully!'));
            } catch (\Exception $e) {
                $this->debugLogger->log(__('Test e-mail failed: %1', $e->getMessage()));
                throw $e;
            }
        }
    }

    /**
     * @return \Laminas\Mail\Message|MessageInterface
     */
    public function getMessage()
    {
        return $this->_mailMessage;
    }

    /**
     * @return $this
     */
    private function checkConnection()
    {
        $this->socket->setOptions(self::SOCK_AUTH_OPTIONS);

        try {
            $this->socket->connect($this->_host, $this->_port);
            $this->debugLogger->log(
                __(
                    'Connection test successful: connected to %1',
                    $this->_host . ':' . $this->_port
                )
            );
        } catch (\Exception $e) {
            $this->debugLogger->log(__($e->getMessage()));

            throw new MailException(__('Connection failed'));
        }

        return $this;
    }

    /**
     * @param Message|null $message
     * @return Message|MessageInterface
     */
    private function modifyMessage($message = null)
    {
        if ($message) {
            $message->setSubject(htmlspecialchars_decode((string)$message->getSubject()));
            $message->setEncoding('utf-8');
            $message->getHeaders()->addHeader($this->messageId->setId($this->messageIdGenerator->generate()));
            $message->getHeaders()->removeHeader("Content-Disposition");
        }

        if (isset($this->_config['custom_sender'])) {
            return $this->setFrom(
                $this->_config['custom_sender']['email'],
                $this->_config['custom_sender']['name'],
                $message
            );
        } elseif ($message && !count($message->getFrom())
            && $this->registry->registry(TransportBuilderByStorePlugin::REGISTRY_KEY)
        ) {
            $defaultFrom = $this->registry->registry(TransportBuilderByStorePlugin::REGISTRY_KEY);

            return $this->setFrom(
                $defaultFrom['email'],
                $defaultFrom['name'],
                $message
            );
        }

        return $message;
    }

    /**
     * Set email sender
     * Function for compatibility of Zend Framework 1 and 2
     *
     * @param string $email
     * @param string $name
     * @param \Laminas\Mail\Message|null $message
     *
     * @return MessageInterface|\Laminas\Mail\Message|null
     */
    private function setFrom($email, $name, $message = null)
    {
        if (class_exists(\Laminas\Mail\Message::class, false)
            && $message instanceof \Laminas\Mail\Message
        ) {
            $message->setEncoding('utf-8');
            $message->setFrom($email, $name);

            return $message;
        } else {
            $this->_mailMessage->clearFrom();
            $this->_mailMessage->setFrom($email, $name);
        }

        return $this->getMessage();
    }

    /**
     * @param string $storeId
     * @param string $scope
     *
     * @throws MailException
     */
    private function setFromData($testEmail, $storeId, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $useCustomSender = $this->config->getUseCustomSender($storeId, $scope);

        if ($useCustomSender || $testEmail) {
            $from = $useCustomSender ? $this->config->getCustomSender($storeId, $scope) : [];

            if (!$from) {
                $from = $this->config->getGeneralEmail($storeId, $scope);

                if (!$from['email'] || !$from['name']) {
                    throw new MailException(
                        __(
                            '\'Sender Email\' or \'Sender Name\' is empty. Please ensure that all values in the '
                            . '\'General Contact\' section are correctly set by visiting Stores > Configuration > '
                            . 'General > Store Email Addresses.'
                        )
                    );
                }
            }

            $this->_config['custom_sender']['email'] = $from['email'];
            $this->_config['custom_sender']['name'] = $from['name'];
        }
    }

    /**
     * @TODO move to a separate service model
     */
    private function isInBlacklist(string $email): bool
    {
        try {
            if ($this->blacklistRepository->getByCustomerEmail($email)) {
                return true;
            }
        } catch (NotFoundException $e) {
            null; // the email has not been blacklisted yet
        }

        [, $domain] = explode('@', $email);

        if (!$this->emailValidator->isValid($email)
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !checkdnsrr($domain)
        ) {
            $this->blacklistRepository->addByEmail($email);

            return true;
        }

        return false;
    }
}
