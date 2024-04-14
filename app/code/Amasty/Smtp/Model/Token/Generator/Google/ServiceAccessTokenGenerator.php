<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token\Generator\Google;

use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Crypt;
use Amasty\Smtp\Model\Token;
use Amasty\Smtp\Model\Serialize\Serializer\Base64UrlEncoder;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

class ServiceAccessTokenGenerator implements Token\Generator\AuthTokenGeneratorInterface
{
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

    /**
     * @var Crypt\RS256
     */
    private $rs256signer;

    /**
     * @var Token\JwtFactory
     */
    private $jwtFactory;

    /**
     * @var Base64UrlEncoder
     */
    private $base64UrlEncoder;

    public function __construct(
        Curl $curlClient,
        Json $jsonSerializer,
        Config $configProvider,
        Crypt\RS256 $rs256signer,
        Token\JwtFactory $jwtFactory,
        Base64UrlEncoder $base64UrlEncoder
    ) {
        $this->curlClient = $curlClient;
        $this->jsonSerializer = $jsonSerializer;
        $this->configProvider = $configProvider;
        $this->rs256signer = $rs256signer;
        $this->jwtFactory = $jwtFactory;
        $this->base64UrlEncoder = $base64UrlEncoder;
    }

    public function generateToken(): string
    {
        $jsonCredentials = $this->configProvider->getGoogleServiceCredentialsJson();
        $credentials = $this->jsonSerializer->unserialize($jsonCredentials);
        $currentUnixTime = time();
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];
        $payload = [
            'iss' => $credentials['client_email'] ?? '',
            'sub' => $this->configProvider->getGoogleServiceGSuiteUserEmail(),
            'scope' => 'https://mail.google.com/',
            'aud' => $credentials['token_uri'] ?? '',
            'exp' => $currentUnixTime + self::TOKEN_LIFETIME,
            'iat' => $currentUnixTime
        ];
        $jwt = $this->jwtFactory->create([
            'header' => $header,
            'payload' => $payload,
        ]);
        $jwt->setSignClosure(function ($message) use ($credentials) {
            return $this->rs256signer->sign($message, $credentials['private_key'] ?? '');
        });
        $signedJwtToken = $jwt->toString();
        $this->curlClient->post(
            $credentials['token_uri'] ?? '',
            [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $signedJwtToken,
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
