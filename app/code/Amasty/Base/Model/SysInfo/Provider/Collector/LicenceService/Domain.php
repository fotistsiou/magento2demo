<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\LicenceService;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Domain as RequestDomain;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Domain implements CollectorInterface
{
    public const CONFIG_PATH_KEY = 'path';
    public const CONFIG_VALUE_KEY = 'value';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ?ConfigCollectionFactory $configCollectionFactory, // @deprecated
        ScopeConfigInterface $scopeConfig = null,
        StoreManagerInterface $storeManager = null
    ) {
        $this->scopeConfig = $scopeConfig ?? ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        $this->storeManager = $storeManager ?? ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    public function get(): array
    {
        $urls = [];
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int) $store->getId();
            $urls[] = $this->scopeConfig->getValue(
                Store::XML_PATH_SECURE_BASE_URL,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $urls[] = $this->scopeConfig->getValue(
                Store::XML_PATH_UNSECURE_BASE_URL,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        $domains = [];
        foreach (array_unique($urls) as $url) {
            $domains[][RequestDomain::URL] = $url;
        }

        return $domains;
    }
}
