<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\AmastyMenu;

use Amasty\Base\Model\AmastyMenu\AmastyConfigItemsProvider;
use Amasty\Base\Model\AmastyMenu\MenuItemFactory;
use Amasty\Base\Model\AmastyMenu\MenuItemsProvider;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Filter\Iterator;
use Magento\Backend\Model\Menu\Filter\IteratorFactory;
use Magento\Backend\Model\Menu\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\Base\Model\AmastyMenu\MenuItemsProvider
 */
class MenuItemsProviderTest extends TestCase
{
    public const MENU_ITEM_ACTION = 'test';

    /**
     * @var MenuItemsProvider
     */
    private $menuItemsProvider;

    /**
     * @var Iterator|MockObject
     */
    private $iteratorMock;

    /**
     * @var MenuItemFactory|MockObject
     */
    private $menuItemFactoryMock;

    /**
     * @var Menu|MockObject
     */
    private $defaultMenuMock;

    /**
     * @var AmastyConfigItemsProvider|MockObject
     */
    private $configItemsProviderMock;

    protected function setUp(): void
    {
        //partial mock because can't access iterator functionality with foreach through full mock
        $this->iteratorMock = $this->createPartialMock(Iterator::class, ['rewind', 'valid', 'current', 'next']);
        $this->configItemsProviderMock = $this->createMock(AmastyConfigItemsProvider::class);
        $this->menuItemFactoryMock = $this->createMock(MenuItemFactory::class);
        $this->defaultMenuMock = $this->createMock(Menu::class);

        $iteratorFactoryMock = $this->createMock(IteratorFactory::class);
        $iteratorFactoryMock->expects($this->any())->method('create')->willReturn($this->iteratorMock);

        $menuConfigMock = $this->createMock(Config::class);
        $menuConfigMock->expects($this->once())->method('getMenu')->willReturn($this->defaultMenuMock);

        $this->menuItemsProvider = new MenuItemsProvider(
            $iteratorFactoryMock,
            $this->configItemsProviderMock,
            $this->menuItemFactoryMock,
            $menuConfigMock
        );
    }

    /**
     * @dataProvider getDataProvider
     *
     * @param Item $menuItemMock
     * @param array $configData
     * @param array $expectedItemsData
     * @return void
     */
    public function testGet(Item $menuItemMock, array $configData, array $expectedItemsData): void
    {
        $this->iteratorMock->expects($this->any())->method('valid')->willReturn(true, false);
        $this->iteratorMock->expects($this->any())->method('current')->willReturn($menuItemMock);
        $this->configItemsProviderMock->expects($this->any())->method('getConfigItems')->willReturn($configData);

        $index = 0;
        foreach ($expectedItemsData as $expectedItemData) {
            $this->menuItemFactoryMock->expects($this->at($index++))->method('create')->with(
                ['data' => $expectedItemData]
            );
        }

        $this->menuItemsProvider->get();
    }

    public function testGetNoAmastyModules(): void
    {
        $this->assertEmpty($this->menuItemsProvider->get());
    }

    public function testGetCalled2ndTime(): void
    {
        $storedData = ['test'];

        $reflection = new \ReflectionClass(get_class($this->menuItemsProvider));
        $property = $reflection->getProperty('amastyItems');
        $property->setAccessible(true);
        $property->setValue($this->menuItemsProvider, $storedData);

        $result = $this->menuItemsProvider->get();
        $this->assertEquals($storedData, $result);
    }

    public function getDataProvider(): array
    {
        return [
            'both config and menu from same module' => [
                $this->createConfiguredMock(
                    Item::class,
                    ['getId' => 'Amasty_Test::test', 'getAction' => 'test']
                ),
                [
                    'Amasty_Test' => ['test_data']
                ],
                [
                    'Amasty_Test' => [
                        'resources' => [
                            'Amasty_Test::test'
                        ],
                        'config' => [
                            'test_data'
                        ]
                    ]
                ]
            ],
            'config and menu from different modules' => [
                $this->createConfiguredMock(
                    Item::class,
                    ['getId' => 'Amasty_Test::test', 'getAction' => 'test']
                ),
                [
                    'Amasty_Test2' => ['test_data']
                ],
                [
                    'Amasty_Test' => [
                        'resources' => [
                            'Amasty_Test::test'
                        ],
                    ],
                    'Amasty_Test2' => [
                        'config' => [
                            'test_data'
                        ]
                    ]
                ]
            ],
            'only menu data' => [
                $this->createConfiguredMock(
                    Item::class,
                    ['getId' => 'Amasty_Test::test', 'getAction' => 'test']
                ),
                [],
                [
                    'Amasty_Test' => [
                        'resources' => [
                            'Amasty_Test::test'
                        ],
                    ]
                ]
            ]
        ];
    }
}
