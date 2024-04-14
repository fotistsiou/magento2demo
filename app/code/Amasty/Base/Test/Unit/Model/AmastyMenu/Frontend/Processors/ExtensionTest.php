<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\AmastyMenu\Frontend\Processors;

use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Extension;
use Amasty\Base\Model\AmastyMenu\MenuItem;
use Amasty\Base\Model\AmastyMenu\MenuItemsProvider;
use Amasty\Base\Model\Feed\ExtensionsProvider;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Item;
use Magento\Backend\Model\Url;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\Base\Model\AmastyMenu\Frontend\Processors\Extension
 */
class ExtensionTest extends TestCase
{
    public const MODULE_CODE = 'Amasty_Test';
    public const MODULE_TITLE = 'Amasty Test Extension';
    public const CONFIG_URL = 'test.url/config';
    public const GUIDE_URL = 'test.url?id=test';
    public const ITEM_TITLE = 'Item Title';
    public const ITEM_URL = 'item.url';
    public const DEFAULT_ITEM_DATA = [
        'label' => self::MODULE_TITLE,
        'id' => self::MODULE_CODE . '::container',
        'type' => 'simple',
        'url' => ''
    ];

    /**
     * @var ExtensionsProvider|MockObject
     */
    private $extensionsProviderMock;

    /**
     * @var Menu|MockObject
     */
    private $defaultMenuMock;

    /**
     * @var MenuItemsProvider|MockObject
     */
    private $menuItemsProviderMock;

    /**
     * @var Url|MockObject
     */
    private $urlMock;

    /**
     * @var Extension
     */
    private $extensionProcessor;

    protected function setUp(): void
    {
        $this->extensionsProviderMock = $this->createMock(ExtensionsProvider::class);
        $this->defaultMenuMock = $this->createMock(Menu::class);
        $this->menuItemsProviderMock = $this->createMock(MenuItemsProvider::class);
        $this->urlMock = $this->createMock(Url::class);

        $menuConfigMock = $this->createMock(Config::class);
        $menuConfigMock->expects($this->once())->method('getMenu')->willReturn($this->defaultMenuMock);

        $this->extensionProcessor = new Extension(
            $this->extensionsProviderMock,
            $menuConfigMock,
            $this->menuItemsProviderMock,
            $this->urlMock
        );
    }

    /**
     * @dataProvider processDataProvider
     *
     * @param array $resources
     * @param bool $withConfig
     * @param bool $withGuide
     * @param array|null $expected
     * @return void
     */
    public function testProcess(
        array $resources,
        bool $withConfig,
        bool $withGuide,
        ?array $expected
    ): void {
        $menuItem = $this->createMock(MenuItem::class);
        $menuItem->expects($this->any())->method('getResources')->willReturn($resources);
        $this->menuItemsProviderMock->expects($this->any())
            ->method('getByModuleCode')
            ->with(self::MODULE_CODE)
            ->willReturn($menuItem);

        $this->defaultMenuMock->expects($this->any())->method('get')->willReturnCallback(function ($id) {
            return $this->createMenuItemMock($id);
        });

        if ($withConfig) {
            $this->initConfigItemMock($menuItem);
        }
        if ($withGuide) {
            $this->initGuideItemMock();
        }

        $result = $this->extensionProcessor->process(self::MODULE_CODE, self::MODULE_TITLE);
        $this->assertEquals($expected, $result);
    }

    public function processDataProvider(): array
    {
        return [
            'no items, no config, no guide' => [[], false, false, null],
            'no items, config, no guide' => [
                [],
                true,
                false,
                self::DEFAULT_ITEM_DATA + ['items' => [
                    [
                        'label' => 'Configuration',
                        'id' => self::MODULE_CODE . '::menuconfig',
                        'type' => 'simple',
                        'url' => self::CONFIG_URL
                    ]
                ]]
            ],
            'no items, config and guide' => [
                [],
                true,
                true,
                self::DEFAULT_ITEM_DATA + ['items' => [
                    [
                        'label' => 'Configuration',
                        'id' => self::MODULE_CODE . '::menuconfig',
                        'type' => 'simple',
                        'url' => self::CONFIG_URL
                    ],
                    [
                        'label' => 'User Guide',
                        'id' => self::MODULE_CODE . '::menuguide',
                        'type' => 'simple',
                        'url' => self::GUIDE_URL
                            . '&utm_source=extension&utm_medium=backend&utm_campaign=main_menu_to_user_guide'
                    ]
                ]]
            ],
            'only items' => [
                [self::MODULE_CODE . '::resource1', self::MODULE_CODE . '::resource2'],
                false,
                false,
                self::DEFAULT_ITEM_DATA + ['items' => [
                    [
                        'label' => self::ITEM_TITLE,
                        'id' => self::MODULE_CODE . '::resource1',
                        'type' => 'simple',
                        'url' => self::ITEM_URL
                    ],
                    [
                        'label' => self::ITEM_TITLE,
                        'id' => self::MODULE_CODE . '::resource2',
                        'type' => 'simple',
                        'url' => self::ITEM_URL
                    ]
                ]]
            ],
            'all data' => [
                [self::MODULE_CODE . '::resource1', self::MODULE_CODE . '::resource2'],
                true,
                true,
                self::DEFAULT_ITEM_DATA + ['items' => [
                    [
                        'label' => self::ITEM_TITLE,
                        'id' => self::MODULE_CODE . '::resource1',
                        'type' => 'simple',
                        'url' => self::ITEM_URL
                    ],
                    [
                        'label' => self::ITEM_TITLE,
                        'id' => self::MODULE_CODE . '::resource2',
                        'type' => 'simple',
                        'url' => self::ITEM_URL
                    ],
                    [
                        'label' => 'Configuration',
                        'id' => self::MODULE_CODE . '::menuconfig',
                        'type' => 'simple',
                        'url' => self::CONFIG_URL
                    ],
                    [
                        'label' => 'User Guide',
                        'id' => self::MODULE_CODE . '::menuguide',
                        'type' => 'simple',
                        'url' => self::GUIDE_URL
                            . '&utm_source=extension&utm_medium=backend&utm_campaign=main_menu_to_user_guide'
                    ]
                ]]
            ]
        ];
    }

    /**
     * @param string $id
     * @return Item
     */
    private function createMenuItemMock(string $id): Item
    {
        return $this->createConfiguredMock(Item::class, [
            'toArray' => [
                'id' => $id,
                'resource' => $id,
                'title' => self::ITEM_TITLE,
                'action' => 'test/action'
            ],
            'getTitle' => self::ITEM_TITLE,
            'getId' => $id,
            'getUrl' => self::ITEM_URL,
            'isAllowed' => true
        ]);
    }

    private function initGuideItemMock(): void
    {
        $this->extensionsProviderMock->expects($this->any())
            ->method('getFeedModuleData')
            ->willReturn(['guide' => self::GUIDE_URL]);
    }

    /**
     * @param MenuItem|MockObject $menuItem
     * @return void
     */
    private function initConfigItemMock(MenuItem $menuItem): void
    {
        $menuItem->expects($this->any())->method('getConfigByKey')
            ->with('id')
            ->willReturn('test_config');

        $this->urlMock->expects($this->atLeastOnce())->method('getUrl')->with(
            'adminhtml/system_config/edit/section/test_config',
            ['_cache_secret_key' => true]
        )->willReturn(self::CONFIG_URL);
    }
}
