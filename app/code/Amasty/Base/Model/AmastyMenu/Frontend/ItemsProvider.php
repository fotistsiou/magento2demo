<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu\Frontend;

use Amasty\Base\Model\AmastyMenu\ActiveSolutionsProvider;
use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Extension;
use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Links;
use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Solution;
use Amasty\Base\Model\AmastyMenu\ModuleTitlesResolver;
use Amasty\Base\Model\AmastyMenu\MenuItemsProvider;

class ItemsProvider
{
    /**
     * Const for item types
     */
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_SOLUTION = 'solution';
    public const TYPE_UPGRADE_URL = 'upgrade_url';
    public const TYPE_LINK = 'link';

    /**
     * Const for array keys
     */
    public const ID = 'id';
    public const LABEL = 'label';
    public const TYPE = 'type';
    public const URL = 'url';
    public const PLAN_LABEL = 'plan_label';
    public const ADD_INFO = 'add_info';
    public const ITEMS = 'items';

    /**
     * @var ModuleTitlesResolver
     */
    private $moduleTitlesResolver;

    /**
     * @var Extension
     */
    private $extensionProcessor;

    /**
     * @var Solution
     */
    private $solutionProcessor;

    /**
     * @var Links
     */
    private $linksProcessor;

    /**
     * @var MenuItemsProvider
     */
    private $menuItemsProvider;

    /**
     * @var ActiveSolutionsProvider
     */
    private $activeSolutionsProvider;

    public function __construct(
        ModuleTitlesResolver $moduleTitlesResolver,
        MenuItemsProvider $menuItemsProvider,
        Extension $extensionProcessor,
        Solution $solutionProcessor,
        Links $linksProcessor,
        ActiveSolutionsProvider $activeSolutionsProvider
    ) {
        $this->moduleTitlesResolver = $moduleTitlesResolver;
        $this->extensionProcessor = $extensionProcessor;
        $this->solutionProcessor = $solutionProcessor;
        $this->linksProcessor = $linksProcessor;
        $this->menuItemsProvider = $menuItemsProvider;
        $this->activeSolutionsProvider = $activeSolutionsProvider;
    }

    /**
     * Retrieve formatted Amasty Menu Items for menu building

     * @return array
     */
    public function getItems(): array
    {
        $items = [];
        $installedSolutions = $this->activeSolutionsProvider->get();

        foreach (array_keys($installedSolutions) as $solutionCode) {
            $items[] = $this->solutionProcessor->process($solutionCode);
        }

        $availableModules = $this->getAvailableModules($installedSolutions);
        foreach ($availableModules as $moduleCode => $title) {
            $items[] = $this->extensionProcessor->process($moduleCode, $title);
        }

        $this->filterAndSortItems($items);
        $this->linksProcessor->process($items);

        return $items;
    }

    /**
     * Get available modules based on installed
     * and skip modules that have been added in solutions
     *
     * @param array $installedSolutions
     *
     * @return string[]
     */
    private function getAvailableModules(array $installedSolutions): array
    {
        $modulesToSkip = [];

        foreach ($installedSolutions as $installedSolution) {
            if (isset($installedSolution['additional_extensions'])
                && is_array($installedSolution['additional_extensions'])
            ) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $modulesToSkip = array_merge($modulesToSkip, $installedSolution['additional_extensions']);
            }
        }

        return array_diff_key(
            $this->moduleTitlesResolver->getTitles($this->menuItemsProvider->get()),
            array_flip(array_unique($modulesToSkip))
        );
    }

    /**
     * Clean items from 'null' elements
     * and then filter them in alphabetical order
     *
     * @param array $items
     * @return void
     */
    private function filterAndSortItems(array &$items): void
    {
        $items = array_values(array_filter($items));
        usort($items, function ($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });
    }
}
