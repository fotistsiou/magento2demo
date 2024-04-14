<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Plugin\Mail\Template;

use Amasty\Smtp\Model\Config;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Framework\Registry;

/**
 * Plugin for set from header in email, because in magento 2.3.0
 * TransportBuilderByStore is used to set from header
 */
class TransportBuilderByStorePlugin
{
    public const REGISTRY_KEY = 'amsmtp_from_by_store';
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @param Registry $registry
     * @param Config $config
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        Registry $registry,
        Config $config,
        SenderResolverInterface $senderResolver
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->senderResolver = $senderResolver;
    }

    /**
     * @param TransportBuilderByStore $subject
     * @param string $from
     * @param string $store
     */
    public function beforeSetFromByStore(TransportBuilderByStore $subject, $from, $store)
    {
        if ($this->config->isSmtpEnable($store)) {
            $result = $this->senderResolver->resolve($from, $store);
            $this->registry->register('amsmtp_from_by_store', $result, true);
        }
    }
}
