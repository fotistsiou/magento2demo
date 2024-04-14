<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Plugin;

use Amasty\Smtp\Helper\Data;
use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Transport;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

class MailFactory
{
    /**
     * @var string|null
     */
    protected $_instanceName = null;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface|null
     */
    protected $_objectManager = null;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager,
        Data $helper,
        Config $config,
        $instanceName = Transport::class
    ) {
        $this->_instanceName = $instanceName;
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * @param TransportInterfaceFactory $subject
     * @param \Closure $proceed
     * @param array $data
     *
     * @return mixed
     */
    public function aroundCreate(
        TransportInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        $storeId = $this->helper->getCurrentStore();

        if ($this->config->isSmtpEnable($storeId)) {
            $data = array_merge($data, $this->config->getSmtpConfig($storeId));

            return $this->_objectManager->create($this->_instanceName, $data);
        } else {
            return $proceed($data);
        }
    }
}
