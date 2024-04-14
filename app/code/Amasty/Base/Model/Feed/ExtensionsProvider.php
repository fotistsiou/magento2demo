<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Feed;

class ExtensionsProvider
{
    /**
     * @var array|null
     */
    protected $modulesData = null;

    /**
     * @var FeedTypes\Extensions
     */
    private $extensionsFeed;

    public function __construct(
        FeedTypes\Extensions $extensionsFeed
    ) {
        $this->extensionsFeed = $extensionsFeed;
    }

    /**
     * @return array
     */
    public function getAllFeedExtensions(): array
    {
        if ($this->modulesData === null) {
            $this->modulesData = $this->extensionsFeed->execute();
        }

        return $this->modulesData;
    }

    /**
     * @param string $moduleCode
     *
     * @return array
     */
    public function getFeedModuleData(string $moduleCode): array
    {
        $allModules = $this->getAllFeedExtensions();
        $moduleData = [];

        if ($allModules && isset($allModules[$moduleCode])) {
            $module = $allModules[$moduleCode];
            if ($module && is_array($module)) {
                $module = array_shift($module);
            }
            $moduleData = $module;
        }

        return $moduleData;
    }

    public function getAllSolutionsData(): array
    {
        $result = [];

        foreach (array_keys($this->getAllFeedExtensions()) as $moduleCode) {
            $moduleFeedData = $this->getFeedModuleData($moduleCode);
            if (empty($moduleFeedData['is_solution']) || $moduleFeedData['is_solution'] == 'No') {
                continue;
            }
            if (!empty($moduleFeedData['additional_extensions'])) {
                $solutionSubModules = explode(',', $moduleFeedData['additional_extensions']);
                sort($solutionSubModules);
                $moduleFeedData['additional_extensions'] = $solutionSubModules;
            } else {
                $moduleFeedData['additional_extensions'] = [];
            }
            $result[$moduleCode] = $moduleFeedData;
        }

        return $result;
    }
}
