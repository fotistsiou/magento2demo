<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token;

use Amasty\Smtp\Model\Config;
use Magento\Backend\Model\Session as BackendSession;

class GetInternalToken
{
    public const INTERNAL_TOKEN_CODE = 'amasty_smtp_internal_token';

    /**
     * @var BackendSession
     */
    private $backendSession;

    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(
        Config $configProvider,
        BackendSession $backendSession
    ) {
        $this->configProvider = $configProvider;
        $this->backendSession = $backendSession;
    }

    public function execute(): string
    {
        $internalToken = $this->backendSession->getData(self::INTERNAL_TOKEN_CODE);
        if (null === $internalToken) {
            $internalToken = $this->configProvider->getInternalToken(true);
            $this->backendSession->setData(self::INTERNAL_TOKEN_CODE, $internalToken);
        }

        return $internalToken;
    }
}
