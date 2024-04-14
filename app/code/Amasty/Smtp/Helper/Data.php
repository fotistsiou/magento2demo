<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Helper;

use Magento\Framework\App\Area;
use Amasty\Smtp\Plugin\Email\Model\Template;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    public const DOTMAILER_STORE_ID_KEY = 'transportBuilderPluginStoreId';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var State
     */
    protected $appState;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        State $appState,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->appState = $appState;
    }

    public function getCurrentStore()
    {
        $store = $this->storeManager->getStore();

        if ($this->appState->getAreaCode() !== Area::AREA_FRONTEND) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($order = $this->registry->registry('current_order')) {
                return $order->getStoreId();
            }

            if ($storeId = $this->registry->registry(self::DOTMAILER_STORE_ID_KEY)) {
                return $storeId;
            } elseif ($storeId = $this->registry->registry(Template::TEMPLATE_STORE_ID_REGISTRY_KEY)) {
                return $storeId;
            }

            return 0;
        }

        return $store->getId();
    }
}
