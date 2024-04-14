<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\Feed;

use Amasty\Base\Model\Feed\ExtensionsProvider;
use Amasty\Base\Model\Feed\FeedTypes\Extensions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\Base\Model\Feed\ExtensionsProvider
 */
class ExtensionsProviderTest extends TestCase
{
    /**
     * @var Extensions|MockObject
     */
    private $extensionsFeedMock;

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    protected function setUp(): void
    {
        $this->extensionsFeedMock = $this->createMock(Extensions::class);
        $this->extensionsProvider = new ExtensionsProvider($this->extensionsFeedMock);
    }

    /**
     * @dataProvider getFeedModuleDataDataProvider
     * @param array $modules
     * @param array $expected
     * @return void
     */
    public function testGetFeedModuleData(array $modules, array $expected): void
    {
        $this->extensionsFeedMock->expects($this->any())->method('execute')->willReturn($modules);

        $this->assertEquals($expected, $this->extensionsProvider->getFeedModuleData('Amasty_Test1'));
    }

    public function getFeedModuleDataDataProvider(): array
    {
        return [
            'no feed data' => [[], []],
            'get by key' => [
                [
                    'Amasty_Test1' => ['feed name 1' => ['name' => 'Test1']],
                    'Amasty_Test2' => ['feed name 2' => ['name' => 'Test2']]
                ],
                ['name' => 'Test1']
            ]
        ];
    }

    /**
     * @dataProvider getAllSolutionsDataDataProvider
     * @param array $modules
     * @param array $expected
     * @return void
     */
    public function testGetAllSolutionsData(array $modules, array $expected): void
    {
        $this->extensionsFeedMock->expects($this->any())->method('execute')->willReturn($modules);

        $this->assertEquals($expected, $this->extensionsProvider->getAllSolutionsData());
    }

    public function getAllSolutionsDataDataProvider(): array
    {
        return [
            'no feed data' => [[], []],
            'no solutions in feed' => [
                [
                    'Amasty_Extension1' => ['feed name 1' => ['name' => 'Test1', 'is_solution' => '']],
                    'Amasty_Extension2' => ['feed name 2' => ['name' => 'Test2', 'is_solution' => '']]
                ],
                []
            ],
            'solution with additional extensions' => [
                [
                    'Amasty_Solution' => [
                        'solution feed name' => [
                            'name' => 'Test1',
                            'is_solution' => 'Yes',
                            'additional_extensions' => 'Amasty_TestB,Amasty_TestA'
                        ]
                    ],
                    'Amasty_Extension1' => ['feed name 1' => ['name' => 'Test1', 'is_solution' => '']]
                ],
                [
                    'Amasty_Solution' => [
                        'name' => 'Test1',
                        'is_solution' => 'Yes',
                        'additional_extensions' => [
                            'Amasty_TestA',
                            'Amasty_TestB'
                        ]
                    ]
                ]
            ],
            'solution without extensions' => [
                [
                    'Amasty_Solution' => [
                        'solution feed name' => [
                            'name' => 'Test1',
                            'is_solution' => 'Yes',
                        ]
                    ],
                    'Amasty_Extension1' => ['feed name 1' => ['name' => 'Test1', 'is_solution' => '']]
                ],
                [
                    'Amasty_Solution' => [
                        'name' => 'Test1',
                        'is_solution' => 'Yes',
                        'additional_extensions' => []
                    ]
                ]
            ]
        ];
    }
}
