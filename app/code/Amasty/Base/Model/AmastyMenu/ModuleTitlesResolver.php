<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu;

use Amasty\Base\Model\Feed\ExtensionsProvider;
use Amasty\Base\Model\ModuleInfoProvider;
use Magento\Framework\Escaper;
use Magento\Framework\Module\ModuleListInterface;

class ModuleTitlesResolver
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        ModuleListInterface $moduleList,
        ModuleInfoProvider $moduleInfoProvider,
        ExtensionsProvider $extensionsProvider,
        Escaper $escaper
    ) {
        $this->moduleList = $moduleList;
        $this->moduleInfoProvider = $moduleInfoProvider;
        $this->extensionsProvider = $extensionsProvider;
        $this->escaper = $escaper;
    }

    /**
     * Get installed Amasty modules titles for menu building
     *
     * @param MenuItem[] $menuItems module data to intersect with
     * @return string[] [module_code => title]
     */
    public function getTitles(array $menuItems): array
    {
        $amastyModules = array_intersect($this->getAmastyModules(), array_keys($menuItems));

        $modules = [];
        foreach ($amastyModules as $moduleCode) {
            $title = $menuItems[$moduleCode]->getConfigByKey('label');
            if (!$title) {
                $title = $this->getModuleTitle($moduleCode);
            }
            $modules[$moduleCode] = __($title)->render();
        }

        return $modules;
    }

    /**
     * @param string $moduleCode
     * @return string
     */
    public function getModuleTitle(string $moduleCode): string
    {
        $module = $this->extensionsProvider->getFeedModuleData($moduleCode);
        if ($module && isset($module['name'])) {
            $title = $module['name'];
        } else {
            $title = str_replace('Amasty_', '', $moduleCode);
            $title = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $title);
        }
        $extraWord = $module['solution_version'] ?? '';

        return $this->normalizeTitle($this->trimTitle($title, $extraWord));
    }

    /**
     * @param string $title
     * @param string $trimWord
     * @return string
     */
    public function trimTitle(string $title, string $trimWord = ''): string
    {
        $title = trim($title);
        $title = str_replace(' for Magento 2', '', $title);

        if ($trimWord) {
            $lastSpacePos = strrpos($title, ' ');
            $lastWord = substr($title, $lastSpacePos + 1);
            if (strcasecmp($trimWord, $lastWord) === 0) {
                $title = substr($title, 0, $lastSpacePos);
            }
        }

        return $this->escaper->escapeHtml($title);
    }

    /**
     * According to default validation rules, title can't be longer than 50 characters
     *
     * @param string $title
     * @return string
     */
    private function normalizeTitle(string $title): string
    {
        if (mb_strlen($title) > 50) {
            $title = mb_substr($title, 0, 47) . '...';
        }

        return $title;
    }

    /**
     * Retrieve all installed amasty module names
     *
     * @return array
     */
    private function getAmastyModules(): array
    {
        $restrictedModules = $this->moduleInfoProvider->getRestrictedModules();

        return array_filter($this->moduleList->getNames(), function ($moduleCode) use ($restrictedModules) {
            return $moduleCode !== 'Amasty_Base'
                && strpos($moduleCode, 'Amasty_') !== false
                && !in_array($moduleCode, $restrictedModules, true);
        });
    }
}
