<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\UrlBuilder;

use Amasty\Base\Model\Serializer;
use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Token\GetInternalToken;
use Amasty\Smtp\Model\Token\ScopeResolver;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Url;
use Magento\Framework\Url\QueryParamsResolverInterface;

class GenerateAuthorizationUrl
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var Url
     */
    private $urlBuilder;

    /**
     * @var QueryParamsResolverInterface
     */
    private $queryParamsResolver;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var GetInternalToken
     */
    private $getInternalToken;

    /**
     * @var ScopeResolver
     */
    private $scopeResolver;

    /**
     * @var MicrosoftUrlBuilder
     */
    private $microsoftUrlBuilder;

    public function __construct(
        Config $configProvider,
        Url $urlBuilder,
        QueryParamsResolverInterface $queryParamsResolver,
        Serializer $serializer,
        GetInternalToken $getInternalToken,
        ScopeResolver $scopeResolver,
        MicrosoftUrlBuilder $microsoftUrlBuilder
    ) {
        $this->configProvider = $configProvider;
        $this->urlBuilder = $urlBuilder;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->serializer = $serializer;
        $this->getInternalToken = $getInternalToken;
        $this->scopeResolver = $scopeResolver;
        $this->microsoftUrlBuilder = $microsoftUrlBuilder;
    }

    /**
     * @throws ValidatorException
     */
    public function getMicrosoftAuthUrl(array $storeParams): string
    {
        return $this->microsoftUrlBuilder->forAuthorize()->get() . '?' . $this->getQueryString(
            [
                'client_id' => $this->configProvider->getMicrosoftClientId(),
                'redirect_uri' => $this->getRedirectUrl(),
                'response_type' => 'code',
                'access_type' => 'offline',
                'include_granted_scopes' => true,
                'scope' => $this->scopeResolver->execute(),
                'state' => $this->generateParamsString($storeParams)
            ]
        );
    }

    private function generateParamsString(array $storeParams): string
    {
        $data = $storeParams;
        $data['internal_token'] = $this->getInternalToken->execute();

        return base64_encode($this->serializer->serialize($data));
    }

    private function getQueryString(array $params): string
    {
        $this->queryParamsResolver->addQueryParams($params);
        $query = $this->queryParamsResolver->getQuery();
        $this->queryParamsResolver->unsetData('query');
        $this->queryParamsResolver->unsetData('query_params');

        return $query;
    }

    public function getRedirectUrl(): string
    {
        return rtrim($this->urlBuilder->getUrl('amasty_smtp/oauth/callback'), '/');
    }
}
