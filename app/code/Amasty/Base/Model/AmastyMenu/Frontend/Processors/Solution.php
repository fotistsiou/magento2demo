<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu\Frontend\Processors;

use Amasty\Base\Model\AmastyMenu\Frontend\ItemsProvider;
use Amasty\Base\Model\AmastyMenu\ModuleTitlesResolver;
use Amasty\Base\Model\AmastyMenu\MenuItemsProvider;
use Amasty\Base\Model\Feed\ExtensionsProvider;

class Solution
{
    /**
     * @var Extension
     */
    private $extensionProcessor;

    /**
     * @var ModuleTitlesResolver
     */
    private $moduleTitlesResolver;

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var MenuItemsProvider
     */
    private $menuItemsProvider;

    public function __construct(
        Extension $extensionProcessor,
        ModuleTitlesResolver $moduleTitlesResolver,
        ExtensionsProvider $extensionsProvider,
        MenuItemsProvider $menuItemsProvider
    ) {
        $this->extensionProcessor = $extensionProcessor;
        $this->moduleTitlesResolver = $moduleTitlesResolver;
        $this->extensionsProvider = $extensionsProvider;
        $this->menuItemsProvider = $menuItemsProvider;
    }

    /**
     * Process solution for menu output
     *
     * @param string $solutionCode
     * @return array|null
     */
    public function process(string $solutionCode): ?array
    {
        $solutionData = $this->extensionsProvider->getAllSolutionsData()[$solutionCode] ?? [];
        if (empty($solutionData['is_solution']) || $solutionData['is_solution'] == 'No') {
            return null;
        }

        $item = [
            ItemsProvider::LABEL => $this->moduleTitlesResolver->getModuleTitle($solutionCode),
            ItemsProvider::ID => $solutionCode . '::solution',
            ItemsProvider::TYPE => ItemsProvider::TYPE_SOLUTION,
            ItemsProvider::URL => '',
            ItemsProvider::PLAN_LABEL => $solutionData['solution_version'] ?? ''
        ];
        $subModules = $this->processSubModules($solutionData);
        if (!empty($subModules)) {
            $item[ItemsProvider::ITEMS] = $subModules;

            return $item;
        }

        return null;
    }

    /**
     * @param array $solutionData
     * @return array
     */
    private function processSubModules(array $solutionData): array
    {
        if (isset($solutionData['upgrade_url']) && !empty($solutionData['upgrade_url'])) {
            $upgradeLinkItem = [
                ItemsProvider::LABEL => __('Upgrade Your Plan')->render(),
                ItemsProvider::ID => '',
                ItemsProvider::TYPE => ItemsProvider::TYPE_UPGRADE_URL,
                ItemsProvider::URL => $solutionData['upgrade_url']
            ];
        }

        $subModules = [];
        if (!empty($solutionData['additional_extensions']) && is_array($solutionData['additional_extensions'])) {
            foreach ($solutionData['additional_extensions'] as $moduleCode) {
                if (!$this->menuItemsProvider->getByModuleCode($moduleCode)) {
                    continue;
                }

                $title = $this->menuItemsProvider->getByModuleCode($moduleCode)->getConfigByKey('label') ?: '';
                if (!$title) {
                    $title = $this->moduleTitlesResolver->getModuleTitle($moduleCode);
                }

                $subModule = $this->extensionProcessor->process($moduleCode, $title);
                if (isset($upgradeLinkItem) && $subModule !== null) {
                    $upgradeLinkItem['id'] = $moduleCode . '::upgrade_plan_link';
                    $subModule['items'][] = $upgradeLinkItem;
                }
                if (!empty($subModule)) {
                    $subModules[] = $subModule;
                }
            }
        }
        usort($subModules, function ($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });

        return $subModules;
    }
}
