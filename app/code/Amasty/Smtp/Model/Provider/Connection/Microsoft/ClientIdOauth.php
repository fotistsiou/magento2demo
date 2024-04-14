<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Provider\Connection\Microsoft;

use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Protocol\Smtp\Auth\Xoauth2;
use Amasty\Smtp\Model\Protocol\Smtp\Auth\Xoauth2Factory;
use Amasty\Smtp\Model\Provider\ConnectionProviderInterface;
use Amasty\Smtp\Model\Token\Provider\AuthTokenProviderInterface;
use Laminas\Mail\Protocol\Smtp;
use Laminas\Mail\Transport\SmtpOptions;

class ClientIdOauth implements ConnectionProviderInterface
{
    /**
     * @var Xoauth2
     */
    private $connection;

    /**
     * @var Xoauth2Factory
     */
    private $xoauth2Factory;

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var AuthTokenProviderInterface
     */
    private $accessTokenProvider;

    public function __construct(
        Config $configProvider,
        Xoauth2Factory $xoauth2Factory,
        AuthTokenProviderInterface $accessTokenProvider
    ) {
        $this->configProvider = $configProvider;
        $this->xoauth2Factory = $xoauth2Factory;
        $this->accessTokenProvider = $accessTokenProvider;
    }

    /**
     * @throws \Exception
     */
    public function getConnection(SmtpOptions $options): Smtp
    {
        if (null === $this->connection) {
            /** @var Xoauth2 $connection */
            $this->connection = $this->xoauth2Factory->create([
                'host' => $options->getHost(),
                'port' => $options->getPort(),
                'config' => $options->getConnectionConfig()
            ]);

            try {
                $this->connection->setAccessToken($this->accessTokenProvider->get());
            } catch (\Exception $exception) {
                $this->accessTokenProvider->delete();
                throw $exception;
            }
            $this->connection->setUsername($this->configProvider->getMicrosoftSenderEmail());
        }

        return $this->connection;
    }
}
