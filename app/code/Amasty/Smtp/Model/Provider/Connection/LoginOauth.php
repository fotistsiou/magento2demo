<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Provider\Connection;

use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Provider\ConnectionProviderInterface;
use Laminas\Mail\Protocol\Smtp;
use Laminas\Mail\Protocol\Smtp\Auth\Login;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Protocol\Smtp\Auth\LoginFactory;

class LoginOauth implements ConnectionProviderInterface
{
    /**
     * @var Login
     */
    private $connection;

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var LoginFactory
     */
    private $loginFactory;

    public function __construct(
        Config $configProvider,
        LoginFactory $loginFactory
    ) {
        $this->configProvider = $configProvider;
        $this->loginFactory = $loginFactory;
    }

    /**
     * @throws \Exception
     */
    public function getConnection(SmtpOptions $options): Smtp
    {
        if ($this->connection === null) {
            /** @var LoginFactory $connection */
            $this->connection = $this->loginFactory->create([
                'host' => $options->getHost(),
                'port' => $options->getPort(),
                'config' => $options->getConnectionConfig()
            ]);

            $this->connection->setUsername($this->configProvider->getSmtpLogin());
        }

        return $this->connection;
    }
}
