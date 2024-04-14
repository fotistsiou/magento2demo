<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Observer;

use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Config\Source\Auth;
use Amasty\Smtp\Model\Token\Provider\AccessTokenProviderFactory;
use Amasty\Smtp\Model\Token\Generator\Microsoft\ClientIdAccessTokenGenerator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ClearMicrosoftTokenData implements ObserverInterface
{
    public const ALLOWED_PATHS = [
        'amsmtp/smtp/oauth2_microsoft_tenant_id',
        'amsmtp/smtp/oauth2_microsoft_client_id'
    ];

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var AccessTokenProviderFactory
     */
    private $accessTokenProviderFactory;

    /**
     * @var ClientIdAccessTokenGenerator
     */
    private $microsoftTokenGenerator;

    public function __construct(
        Config $configProvider,
        AccessTokenProviderFactory $accessTokenProviderFactory,
        ClientIdAccessTokenGenerator $microsoftTokenGenerator
    ) {
        $this->configProvider = $configProvider;
        $this->accessTokenProviderFactory = $accessTokenProviderFactory;
        $this->microsoftTokenGenerator = $microsoftTokenGenerator;
    }

    public function execute(Observer $observer): void
    {
        $changedPaths = $observer->getEvent()->getData()['changed_paths'] ?? [];
        if (array_intersect($changedPaths, self::ALLOWED_PATHS)) {
            $accessTokenProvider = $this->accessTokenProviderFactory->create([
                'accessTokenGenerator' => $this->microsoftTokenGenerator,
                'connectionProviderName' => Auth::AUTH_TYPE_OAUTH2_MICROSOFT
            ]);
            $accessTokenProvider->delete();
            $this->configProvider->deleteRefreshToken();
        }
    }
}
