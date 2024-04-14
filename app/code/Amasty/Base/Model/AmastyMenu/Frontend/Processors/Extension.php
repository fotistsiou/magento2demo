<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu\Frontend\Processors;

use Amasty\Base\Block\Adminhtml\System\Config\Information;
use Amasty\Base\Model\AmastyMenu\Frontend\ItemsProvider;
use Amasty\Base\Model\AmastyMenu\MenuItemsProvider;
use Amasty\Base\Model\Feed\ExtensionsProvider;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Item;
use Magento\Backend\Model\UrlInterface;

class Extension
{
    public const USER_GUIDE_SEO_CAMPAIGN = 'main_menu_to_user_guide';

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var Menu
     */
    private $defaultMenu;

    /**
     * @var MenuItemsProvider
     */
    private $menuItemsProvider;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        ExtensionsProvider $extensionsProvider,
        Config $menuConfig,
        MenuItemsProvider $menuItemsProvider,
        UrlInterface $url
    ) {
        $this->extensionsProvider = $extensionsProvider;
        $this->defaultMenu = $menuConfig->getMenu();
        $this->menuItemsProvider = $menuItemsProvider;
        $this->url = $url;
    }

    /**
     * Process extension for menu output
     *
     * @param string $moduleCode
     * @param string $title
     * @return array|null
     */
    public function process(string $moduleCode, string $title): ?array
    {
        $item = [
            ItemsProvider::LABEL => $title,
            ItemsProvider::ID => $moduleCode . '::container',
            ItemsProvider::TYPE => ItemsProvider::TYPE_SIMPLE,
            ItemsProvider::URL => ''
        ];

        $childItems = $this->getFormattedModuleMenuItems($moduleCode);
        if (!empty($childItems)) {
            $item[ItemsProvider::ITEMS] = $childItems;

            return $item;
        }

        return null;
    }

    /**
     * Get menu items to retrieve correct urls and check action existence and ACLs and then format
     *
     * @param string $moduleCode
     * @return array
     */
    private function getFormattedModuleMenuItems(string $moduleCode): array
    {
        $items = [];
        $moduleLinks = $this->menuItemsProvider->getByModuleCode($moduleCode)
            ? $this->menuItemsProvider->getByModuleCode($moduleCode)->getResources()
            : [];

        foreach ($moduleLinks as $link) {
            if (($item = $this->defaultMenu->get($link)) && $item->isAllowed()) {
                $itemData = $item->toArray();
                if (isset($itemData['id'], $itemData['resource'], $itemData['title'], $itemData['action'])) {
                    $items[] = $this->convertMenuItem($item);
                }
            }
        }
        $this->addCommonMenuItems($items, $moduleCode);

        return array_filter($items);
    }

    /**
     * Add config and user guide links to module items
     *
     * @param array $items
     * @param string $moduleCode
     * @return void
     */
    private function addCommonMenuItems(array &$items, string $moduleCode): void
    {
        $configId = $this->menuItemsProvider->getByModuleCode($moduleCode)
            ? $this->menuItemsProvider->getByModuleCode($moduleCode)->getConfigByKey('id')
            : '';

        if ($configId) { //add config
            $items[] = [
                ItemsProvider::LABEL => __('Configuration')->render(),
                ItemsProvider::ID => $moduleCode . '::menuconfig',
                ItemsProvider::TYPE => ItemsProvider::TYPE_SIMPLE,
                ItemsProvider::URL => $this->url->getUrl(
                    'adminhtml/system_config/edit/section/' . $configId,
                    ['_cache_secret_key' => true]
                )
            ];
        }

        $moduleInfo = $this->extensionsProvider->getFeedModuleData($moduleCode);
        if (!empty($moduleInfo['guide'])) { //add user guide
            $url = $moduleInfo['guide'];
            $seoParams = Information::SEO_PARAMS;
            if (strpos($url, '?') !== false) {
                $seoParams = str_replace('?', '&', $seoParams);
            }
            $url .= $seoParams . self::USER_GUIDE_SEO_CAMPAIGN;

            $items[] = [
                ItemsProvider::LABEL => __('User Guide')->render(),
                ItemsProvider::ID => $moduleCode . '::menuguide',
                ItemsProvider::TYPE => ItemsProvider::TYPE_SIMPLE,
                ItemsProvider::URL => $url
            ];
        }
    }

    /**
     * Convert Menu\Item to common output
     *
     * @param Item $menuItem
     * @return array
     */
    private function convertMenuItem(Item $menuItem): array
    {
        return [
            ItemsProvider::LABEL => __((string)$menuItem->getTitle())->render(),
            ItemsProvider::ID => $menuItem->getId(),
            ItemsProvider::TYPE => ItemsProvider::TYPE_SIMPLE,
            ItemsProvider::URL => $menuItem->getUrl() === '#' ? '' : $menuItem->getUrl()
        ];
    }
}
