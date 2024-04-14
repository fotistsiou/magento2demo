<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Block\Adminhtml\System\Config\Oauth;

use Amasty\Smtp\Model\Config as ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Result extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Smtp::config/oauth/result.phtml';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        Context $context,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    public function getErrorMessage(): string
    {
        return (string)$this->getMessage();
    }

    public function isTokenGenerated(): bool
    {
        return !$this->getErrorMessage() && $this->configProvider->getRefreshToken();
    }
}
