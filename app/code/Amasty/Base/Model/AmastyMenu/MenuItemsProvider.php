<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu;

use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Filter\Iterator;
use Magento\Backend\Model\Menu\Filter\IteratorFactory;
use Magento\Backend\Model\Menu\Item;

class MenuItemsProvider
{
    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var AmastyConfigItemsProvider
     */
    private $configItemsProvider;

    /**
     * @var MenuItemFactory
     */
    private $menuItemFactory;

    /**
     * @var Menu
     */
    private $defaultMenu;

    /**
     * Storage for generated items
     *
     * @var MenuItem[]
     */
    private $amastyItems = [];

    public function __construct(
        IteratorFactory $iteratorFactory,
        AmastyConfigItemsProvider $configItemsProvider,
        MenuItemFactory $menuItemFactory,
        Config $menuConfig
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->menuItemFactory = $menuItemFactory;
        $this->defaultMenu = $menuConfig->getMenu();
        $this->configItemsProvider = $configItemsProvider;
    }

    /**
     * Get all available Amasty Menu Items
     *
     * @return MenuItem[]
     */
    public function get(): array
    {
        if (!$this->amastyItems) {
            $resources = $this->getAmastyResources($this->defaultMenu);
            $itemsData = [];

            foreach ($resources as $resource) {
                $moduleCode = current(explode('::', $resource));
                if (!isset($itemsData[$moduleCode])) {
                    $itemsData[$moduleCode][MenuItem::RESOURCES] = [];
                }
                $itemsData[$moduleCode][MenuItem::RESOURCES][] = $resource;
            }

            $configItems = $this->configItemsProvider->getConfigItems();
            foreach ($configItems as $moduleCode => $configData) {
                $itemsData[$moduleCode][MenuItem::CONFIG] = $configData;
            }

            foreach ($itemsData as $moduleCode => $itemData) {
                $this->amastyItems[$moduleCode] = $this->menuItemFactory->create(['data' => $itemData]);
            }
        }

        return $this->amastyItems;
    }

    /**
     * Get Amasty Menu Item by module code
     *
     * @param string $moduleCode
     * @return MenuItem|null
     */
    public function getByModuleCode(string $moduleCode): ?MenuItem
    {
        return $this->get()[$moduleCode] ?? null;
    }

    /**
     * @param Menu $menu
     * @return array
     */
    private function getAmastyResources(Menu $menu): array
    {
        $items = [];

        foreach ($this->getMenuIterator($menu) as $menuItem) {
            if ($this->isCollectableNode($menuItem)) {
                $items[] = $menuItem->getId();
            }
            if ($menuItem->hasChildren()) {
                foreach ($this->getAmastyResources($menuItem->getChildren()) as $menuChild) {
                    $items[] = $menuChild;
                }
            }
        }

        return $items;
    }

    /**
     * @param Item $menuItem
     * @return bool
     */
    private function isCollectableNode(Item $menuItem): bool
    {
        if (strpos($menuItem->getId(), 'Amasty') === false
            || strpos($menuItem->getId(), 'Amasty_Base') !== false
        ) {
            return false;
        }

        if (empty($menuItem->getAction())
            || strpos($menuItem->getAction(), 'system_config') === false
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Menu $menu
     * @return Iterator
     */
    private function getMenuIterator(Menu $menu): Iterator
    {
        return $this->iteratorFactory->create(['iterator' => $menu->getIterator()]);
    }
}
