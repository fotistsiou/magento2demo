<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\AmastyMenu\Frontend;

use Amasty\Base\Model\AmastyMenu\ActiveSolutionsProvider;
use Amasty\Base\Model\AmastyMenu\Frontend\ItemsProvider;
use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Extension;
use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Links;
use Amasty\Base\Model\AmastyMenu\Frontend\Processors\Solution;
use Amasty\Base\Model\AmastyMenu\MenuItemsProvider;
use Amasty\Base\Model\AmastyMenu\ModuleTitlesResolver;
use Amasty\Base\Model\Feed\ExtensionsProvider;
use Magento\Framework\Module\Manager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\Base\Model\AmastyMenu\Frontend\ItemsProvider
 */
class ItemsProviderTest extends TestCase
{
    public const INSTALLED_EXTENSIONS = [
        'Amasty_TestExtensionC' => 'Amasty_TestExtensionC',
        'Amasty_TestExtensionA' => 'Amasty_TestExtensionA',
        'Amasty_TestExtensionB' => 'Amasty_TestExtensionB'
    ];

    /**
     * @var ModuleTitlesResolver|MockObject
     */
    private $moduleTitleResolverMock;

    /**
     * @var MenuItemsProvider|MockObject
     */
    private $menuItemsProviderMock;

    /**
     * @var Extension|MockObject
     */
    private $extensionProcessorMock;

    /**
     * @var Solution|MockObject
     */
    private $solutionProcessorMock;

    /**
     * @var Links|MockObject
     */
    private $linksProcessorMock;

    /**
     * @var Manager|MockObject
     */
    private $activeSolutionsProviderMock;

    /**
     * @var ItemsProvider
     */
    private $itemsProvider;

    protected function setUp(): void
    {
        $this->moduleTitleResolverMock = $this->createMock(ModuleTitlesResolver::class);
        $this->menuItemsProviderMock = $this->createMock(MenuItemsProvider::class);
        $this->extensionProcessorMock = $this->createMock(Extension::class);
        $this->solutionProcessorMock = $this->createMock(Solution::class);
        $this->linksProcessorMock = $this->createMock(Links::class);
        $this->activeSolutionsProviderMock = $this->createMock(ActiveSolutionsProvider::class);

        $this->itemsProvider = new ItemsProvider(
            $this->moduleTitleResolverMock,
            $this->menuItemsProviderMock,
            $this->extensionProcessorMock,
            $this->solutionProcessorMock,
            $this->linksProcessorMock,
            $this->activeSolutionsProviderMock
        );
    }

    /**
     * @dataProvider getItemsDataProvider
     * @param array $solutionsData
     * @param array $expected
     * @return void
     */
    public function testGetItems(array $solutionsData, array $expected)
    {
        $this->moduleTitleResolverMock->expects($this->any())
            ->method('getTitles')
            ->willReturn(self::INSTALLED_EXTENSIONS);
        $this->activeSolutionsProviderMock->expects($this->any())
            ->method('get')
            ->willReturn($solutionsData);
        $this->solutionProcessorMock->expects($this->any())->method('process')->willReturn(
            ...$this->prepareSolutionProcessorOutput($solutionsData)
        );
        $this->extensionProcessorMock->expects($this->any())->method('process')
            ->willReturnCallback(function ($moduleCode, $title) {
                return ['label' => $title, 'type' => 'simple'];
            });

        $this->assertEquals($expected, $this->itemsProvider->getItems());
    }

    public function getItemsDataProvider(): array
    {
        return [
            'no solutions' => [
                [],
                [
                    ['label' => 'Amasty_TestExtensionA', 'type' => 'simple'],
                    ['label' => 'Amasty_TestExtensionB', 'type' => 'simple'],
                    ['label' => 'Amasty_TestExtensionC', 'type' => 'simple']
                ]
            ],
            'all extensions in 1 solution' => [
                [
                    'Amasty_TestSolution' => [
                        'title' => 'Test Solution',
                        'additional_extensions' => [
                            'Amasty_TestExtensionA',
                            'Amasty_TestExtensionB',
                            'Amasty_TestExtensionC'
                        ]
                    ]
                ],
                [
                    [
                        'label' => 'Test Solution',
                        'type' => 'solution',
                        'items' => [
                            ['label' => 'Amasty_TestExtensionA', 'type' => 'simple'],
                            ['label' => 'Amasty_TestExtensionB', 'type' => 'simple'],
                            ['label' => 'Amasty_TestExtensionC', 'type' => 'simple']
                        ]
                    ]
                ]
            ],
            'all extensions from 2 solutions' => [
                [
                    'Amasty_TestSolutionA' => [
                        'title' => 'Test Solution A',
                        'additional_extensions' => [
                            'Amasty_TestExtensionA',
                            'Amasty_TestExtensionB'
                        ]
                    ],
                    'Amasty_TestSolutionB' => [
                        'title' => 'Test Solution B',
                        'additional_extensions' => [
                            'Amasty_TestExtensionA',
                            'Amasty_TestExtensionC'
                        ]
                    ]
                ],
                [
                    [
                        'label' => 'Test Solution A',
                        'type' => 'solution',
                        'items' => [
                            ['label' => 'Amasty_TestExtensionA', 'type' => 'simple'],
                            ['label' => 'Amasty_TestExtensionB', 'type' => 'simple']
                        ]
                    ],
                    [
                        'label' => 'Test Solution B',
                        'type' => 'solution',
                        'items' => [
                            ['label' => 'Amasty_TestExtensionA', 'type' => 'simple'],
                            ['label' => 'Amasty_TestExtensionC', 'type' => 'simple']
                        ]
                    ]
                ]
            ],
            '1 extension outside of solution' => [
                [
                    'Amasty_TestSolution' => [
                        'title' => 'Test Solution',
                        'additional_extensions' => [
                            'Amasty_TestExtensionA',
                            'Amasty_TestExtensionC'
                        ]
                    ]
                ],
                [
                    [
                        'label' => 'Amasty_TestExtensionB',
                        'type' => 'simple'
                    ],
                    [
                        'label' => 'Test Solution',
                        'type' => 'solution',
                        'items' => [
                            ['label' => 'Amasty_TestExtensionA', 'type' => 'simple'],
                            ['label' => 'Amasty_TestExtensionC', 'type' => 'simple']
                        ]
                    ]
                ]
            ]
        ];
    }

    private function prepareSolutionProcessorOutput(array $solutionData): array
    {
        $result = [];

        foreach ($solutionData as $solution) {
            $item = [
                'label' => $solution['title'],
                'type' => 'solution',
            ];
            $subItems = [];
            foreach ($solution['additional_extensions'] as $extension) {
                $subItems[] = ['label' => $extension, 'type' => 'simple'];
            }
            $item['items'] = $subItems;
            $result[] = $item;
        }

        return $result ?: [[]];
    }
}
