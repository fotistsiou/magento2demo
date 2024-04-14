<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Auth implements OptionSourceInterface
{
    public const AUTH_TYPE_NONE = '';
    public const AUTH_TYPE_LOGIN = 'login';
    public const AUTH_TYPE_OAUTH2_GOOGLE = 'xoauth2_google_client';
    public const AUTH_TYPE_OAUTH2_SERVICE_GOOGLE = 'xoauth2_google_service';
    public const AUTH_TYPE_OAUTH2_MICROSOFT = 'xoauth2_microsoft_client';

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::AUTH_TYPE_NONE,
                'label' => __('Authentication Not Required')
            ],
            [
                'value' => self::AUTH_TYPE_LOGIN,
                'label' => __('Login/Password (Deprecated for Microsoft)')
            ],
            [
                'value' => self::AUTH_TYPE_OAUTH2_GOOGLE,
                'label' => __('OAUTH2 Client ID (Google)')
            ],
            [
                'value' => self::AUTH_TYPE_OAUTH2_SERVICE_GOOGLE,
                'label' => __('OAUTH2 Service Account (Google)')
            ],
            [
                'value' => self::AUTH_TYPE_OAUTH2_MICROSOFT,
                'label' => __('OAUTH2 Client ID (Microsoft)')
            ]
        ];
    }
}
