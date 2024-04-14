<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token\Generator\Microsoft;

use Amasty\Base\Model\Serializer;
use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Token\Generator\AuthTokenGeneratorInterface;
use Amasty\Smtp\Model\Token\ScopeResolver;
use Amasty\Smtp\Model\UrlBuilder\GenerateAuthorizationUrl;
use Amasty\Smtp\Model\UrlBuilder\MicrosoftUrlBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\HTTP\Client\Curl;
use Symfony\Component\HttpFoundation\Response;

class ClientIdAccessTokenGenerator implements AuthTokenGeneratorInterface
{
    public const ACCESS_TOKEN = 'access_token';
    public const REFRESH_TOKEN = 'refresh_token';
    public const MICROSOFT_ERROR = 'error_description';
    public const AUTH_CODE = 'code';

    private const ALLOWED_HTTP_STATUSES = [
        Response::HTTP_OK,
        Response::HTTP_CONTINUE
    ];

    /**
     * @var Curl
     */
    private $curlClient;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GenerateAuthorizationUrl
     */
    private $generateAuthUrl;

    /**
     * @var ScopeResolver
     */
    private $scopeResolver;

    /**
     * @var MicrosoftUrlBuilder
     */
    private $microsoftUrlBuilder;

    public function __construct(
        Curl $curlClient,
        Serializer $serializer,
        Config $configProvider,
        RequestInterface $request,
        GenerateAuthorizationUrl $generateAuthUrl,
        ScopeResolver $scopeResolver,
        MicrosoftUrlBuilder $microsoftUrlBuilder
    ) {
        $this->curlClient = $curlClient;
        $this->serializer = $serializer;
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->generateAuthUrl = $generateAuthUrl;
        $this->scopeResolver = $scopeResolver;
        $this->microsoftUrlBuilder = $microsoftUrlBuilder;
    }

    /**
     * @throws LocalizedException
     * @throws ValidatorException
     * @throws RuntimeException
     */
    public function generateToken(): string
    {
        $this->curlClient->post(
            $this->microsoftUrlBuilder->forToken()->get(),
            $this->getRequestParams()
        );

        return $this->processMicrosoftData($this->curlClient->getBody());
    }

    private function getRequestParams(): array
    {
        $requestParams = [
            'client_id' => $this->configProvider->getMicrosoftClientId(),
            'client_secret' => $this->configProvider->getMicrosoftClientSecret(),
            'scope' => $this->scopeResolver->execute()
        ];

        if ($this->configProvider->getRefreshToken()) {
            $requestParams += [
                'refresh_token' => $this->configProvider->getRefreshToken(),
                'grant_type' => self::REFRESH_TOKEN
            ];
        } else {
            $requestParams += [
                'code' => $this->request->getParam(self::AUTH_CODE),
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->generateAuthUrl->getRedirectUrl()
            ];
        }

        return $requestParams;
    }

    /**
     * @throws LocalizedException
     * @throws RuntimeException
     */
    private function processMicrosoftData(string $response): string
    {
        try {
            $unserializedResponse = $this->checkMicrosoftAnswer($response);
        } catch (\Exception $exception) {
            $this->configProvider->deleteRefreshToken();

            throw $exception;
        }

        $this->configProvider->setRefreshToken($unserializedResponse[self::REFRESH_TOKEN]);

        return $unserializedResponse[self::ACCESS_TOKEN];
    }

    /**
     * @throws LocalizedException
     * @throws RuntimeException
     */
    private function checkMicrosoftAnswer(string $response): array
    {
        $microsoftResponse = $this->serializer->unserialize($response);
        $microsoftErrorMessage = $microsoftResponse[self::MICROSOFT_ERROR] ?? '';

        if (!in_array($this->curlClient->getStatus(), self::ALLOWED_HTTP_STATUSES)) {
            throw new RuntimeException(
                __('Invalid Microsoft response code: %1', $this->curlClient->getStatus())
            );
        }

        if (!isset($microsoftResponse[self::ACCESS_TOKEN]) || !$microsoftResponse[self::ACCESS_TOKEN]) {
            throw new LocalizedException(
                __('Unable to fetch Microsoft access token: %1', $microsoftErrorMessage)
            );
        }

        if (!isset($microsoftResponse[self::REFRESH_TOKEN]) || !$microsoftResponse[self::REFRESH_TOKEN]) {
            throw new LocalizedException(
                __('Unable to fetch Microsoft refresh token: %1', $microsoftErrorMessage)
            );
        }

        return $microsoftResponse;
    }
}
