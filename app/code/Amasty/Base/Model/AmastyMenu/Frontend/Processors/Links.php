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
use Amasty\Base\Model\ModuleInfoProvider;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;

class Links
{
    public const MARKET_URL = 'https://amasty.com/magento-2-extensions.html';
    public const MARKET_SEO_CAMPAIGN_NAME = 'main_menu_to_catalog';
    public const MAGENTO_MARKET_URL = 'https://marketplace.magento.com/partner/Amasty';

    /**
     * @var Menu
     */
    private $defaultMenu;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    public function __construct(
        Config $menuConfig,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->defaultMenu = $menuConfig->getMenu();
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * Add additional links to menu output
     *
     * @param array $items
     * @return void
     */
    public function process(array &$items): void
    {
        if ($extensionLink = $this->getExtensionsLink()) {
            $items[] = $extensionLink;
        }
        if ($settingsLink = $this->getSettingsLink()) {
            $items[] = $settingsLink;
        }
        if ($marketLink = $this->getMarketplaceLink()) {
            $items[] = $marketLink;
        }
        // TODO: Add additional items pool
        if ($getSupportLink = $this->getSupportLink()) {
            $items[] = $getSupportLink;
        }
    }

    /**
     * @return array|null
     */
    private function getExtensionsLink(): ?array
    {
        if (($item = $this->defaultMenu->get('Amasty_Base::extensions')) && $item->isAllowed()) {
            return [
                ItemsProvider::LABEL => $item->getTitle(),
                ItemsProvider::ID => $item->getId(),
                ItemsProvider::TYPE => ItemsProvider::TYPE_LINK,
                ItemsProvider::URL => $item->getUrl(),
                ItemsProvider::ADD_INFO => 'open_current'
            ];
        }

        return null;
    }

    /**
     * @return array|null
     */
    private function getSettingsLink(): ?array
    {
        if (($item = $this->defaultMenu->get('Amasty_Base::settings')) && $item->isAllowed()) {
            return [
                ItemsProvider::LABEL => $item->getTitle(),
                ItemsProvider::ID => $item->getId(),
                ItemsProvider::TYPE => ItemsProvider::TYPE_LINK,
                ItemsProvider::URL => $item->getUrl(),
                ItemsProvider::ADD_INFO => 'open_current'
            ];
        }

        return null;
    }

    private function getSupportLink(): ?array
    {
        if (($item = $this->defaultMenu->get('Amasty_Base::get_support')) && $item->isAllowed()) {
            return [
                ItemsProvider::LABEL => $item->getTitle(),
                ItemsProvider::ID => $item->getId(),
                ItemsProvider::TYPE => ItemsProvider::TYPE_LINK,
                ItemsProvider::URL => $item->getUrl(),
                ItemsProvider::ADD_INFO => 'open_current'
            ];
        }

        return null;
    }

    /**
     * @return array|null
     */
    private function getMarketplaceLink(): ?array
    {
        if (($item = $this->defaultMenu->get('Amasty_Base::marketplace')) && $item->isAllowed()) {
            $url = $this->moduleInfoProvider->isOriginMarketplace()
                ? self::MAGENTO_MARKET_URL
                : self::MARKET_URL . Information::SEO_PARAMS . self::MARKET_SEO_CAMPAIGN_NAME;

            return [
                ItemsProvider::LABEL => $item->getTitle(),
                ItemsProvider::ID => $item->getId(),
                ItemsProvider::TYPE => ItemsProvider::TYPE_LINK,
                ItemsProvider::URL => $url
            ];
        }

        return null;
    }
}
