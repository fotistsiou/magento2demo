<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token\Generator\Google;

use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Token;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

class ClientIdAccessTokenGenerator implements Token\Generator\AuthTokenGeneratorInterface
{
    public const AUTH_URL = 'https://oauth2.googleapis.com/token';

    /**
     * @var Curl
     */
    private $curlClient;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(
        Curl $curlClient,
        Json $jsonSerializer,
        Config $configProvider
    ) {
        $this->curlClient = $curlClient;
        $this->jsonSerializer = $jsonSerializer;
        $this->configProvider = $configProvider;
    }

    public function generateToken(): string
    {
        $this->curlClient->post(
            self::AUTH_URL,
            [
                'client_id' => $this->configProvider->getGoogleClientId(),
                'client_secret' => $this->configProvider->getGoogleClientSecret(),
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->configProvider->getGoogleClientRefreshToken(),
            ]
        );
        $googleResponse = $this->jsonSerializer->unserialize($this->curlClient->getBody());

        if ($this->curlClient->getStatus() !== 200) {
            throw new \RuntimeException(
                'Unable to fetch access token from Google: ' . $googleResponse['error_description'] ?? ''
            );
        }

        return $googleResponse['access_token'] ?? '';
    }
}
