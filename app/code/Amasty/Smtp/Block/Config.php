<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Block;

use Amasty\Smtp\Model\Config as ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Url;

class Config extends Template
{
    /**
     * @var \Amasty\Smtp\Model\Provider\Config
     */
    protected $providersConfig;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Url
     */
    private $urlBuilder;

    public function __construct(
        Context $context,
        \Amasty\Smtp\Model\Provider\Config $providersConfig,
        ConfigProvider $configProvider,
        Url $urlBuilder,
        array $data = []
    ) {
        $this->providersConfig = $providersConfig;
        $this->configProvider = $configProvider;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    public function getProviders()
    {
        return $this->providersConfig->get();
    }

    protected function _toHtml()
    {
        if ($this->_request->getParam('section') == 'amsmtp') {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'store' => $this->getRequest()->getParam('store'),
            'website' => $this->getRequest()->getParam('website')
        ];
    }

    public function getMicrosoftAuthorizationUrl(): string
    {
        return $this->urlBuilder->getUrl('amasty_smtp/url/build', $this->getParams());
    }

    public function isNeedToShowInteractiveAuthFrame(): bool
    {
        return !$this->configProvider->getRefreshToken();
    }
}
