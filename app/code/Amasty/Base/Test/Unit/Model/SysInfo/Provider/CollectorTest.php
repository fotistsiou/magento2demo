<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Provider;

use Amasty\Base\Model\SysInfo\Provider\Collector;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectorTest extends TestCase
{
    /**
     * @var Collector
     */
    private $model;

    /**
     * @var CollectorPool|MockObject
     */
    private $collectorPoolMock;

    protected function setUp(): void
    {
        $this->collectorPoolMock = $this->createMock(CollectorPool::class);

        $this->model = new Collector(
            $this->collectorPoolMock
        );
    }

    public function testCollect(): void
    {
        $groupName = 'name';
        $collectorName = 'collectorName';
        $collectorData = ['val', 'val'];
        $collectorMock = $this->createMock(CollectorInterface::class);
        $collectors = ['collectorName' => $collectorMock];
        $expected = [$collectorName => $collectorData];

        $this->collectorPoolMock
            ->expects($this->once())
            ->method('get')
            ->with($groupName)
            ->willReturn($collectors);
        $collectorMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($collectorData);

        $this->assertEquals($expected, $this->model->collect($groupName));
    }
}
