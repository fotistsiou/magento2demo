<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Plugin\Backend\Model\Menu;

use Amasty\Base\Model\Config;
use Magento\Backend\Model\Menu;

class Builder
{
    public const BASE_MENU = 'MenuAmasty_Base::menu';

    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(
        Config $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Menu\Builder $subject
     * @param Menu $menu
     * @return Menu
     */
    public function afterGetResult(Menu\Builder $subject, Menu $menu): Menu
    {
        $this->validateMenu($menu);

        return $menu;
    }

    /**
     * Validates Menu for further processing
     *
     * @param Menu $menu
     */
    private function validateMenu(Menu $menu): void
    {
        if (!$menu->get(self::BASE_MENU)) {
            return;
        }

        if (!$this->configProvider->isAmastyMenuEnabled()) {
            $menu->remove(self::BASE_MENU);
        }
    }
}
