<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\UrlBuilder;

use Amasty\Smtp\Model\Config;
use Magento\Framework\Exception\ValidatorException;

class MicrosoftUrlBuilder
{
    public const TOKEN_URL = 'https://login.microsoftonline.com/' . self::TENANT_ID . '/oauth2/v2.0/token';
    public const AUTHORIZE_URL = 'https://login.microsoftonline.com/' . self::TENANT_ID . '/oauth2/v2.0/authorize';
    public const TENANT_ID = '{tenant_id}';

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var bool
     */
    private $isAuthorizedUrl;

    public function __construct(
        Config $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function forAuthorize(): self
    {
        $this->isAuthorizedUrl = true;

        return $this;
    }

    public function forToken(): self
    {
        $this->isAuthorizedUrl = false;

        return $this;
    }

    /**
     * @throws ValidatorException
     */
    public function get(): string
    {
        if (!is_bool($this->isAuthorizedUrl)) {
            throw new ValidatorException(__('The type of URL you want to generate is not selected!'));
        }

        $microsoftUrl = self::TOKEN_URL;
        if ($this->isAuthorizedUrl) {
            $microsoftUrl = self::AUTHORIZE_URL;
        }

        return str_replace(
            self::TENANT_ID,
            $this->configProvider->getMicrosoftTenantId(),
            $microsoftUrl
        );
    }
}
