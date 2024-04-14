<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Provider\Connection\Google;

use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Protocol\Smtp\Auth;
use Amasty\Smtp\Model\Provider\ConnectionProviderInterface;
use Amasty\Smtp\Model\Token\Provider\AuthTokenProviderInterface;

class ServiceAccountOauth implements ConnectionProviderInterface
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var Auth\Xoauth2Factory
     */
    private $xoauth2Factory;

    /**
     * @var AuthTokenProviderInterface
     */
    private $accessTokenProvider;

    /**
     * @var Auth\Xoauth2
     */
    private $connection;

    public function __construct(
        Config $configProvider,
        Auth\Xoauth2Factory $xoauth2Factory,
        AuthTokenProviderInterface $accessTokenProvider
    ) {
        $this->configProvider = $configProvider;
        $this->xoauth2Factory = $xoauth2Factory;
        $this->accessTokenProvider = $accessTokenProvider;
    }

    public function getConnection(\Laminas\Mail\Transport\SmtpOptions $options): \Laminas\Mail\Protocol\Smtp
    {
        if ($this->connection === null) {
            /** @var Auth\Xoauth2 $connection */
            $this->connection = $this->xoauth2Factory->create([
                'host' => $options->getHost(),
                'port' => $options->getPort(),
                'config' => $options->getConnectionConfig()
            ]);
            $this->connection->setAccessToken($this->accessTokenProvider->get());
            $this->connection->setUsername($this->configProvider->getGoogleServiceGSuiteUserEmail());
        }

        return $this->connection;
    }
}
