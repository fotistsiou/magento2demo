<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Utils\Http\Response\Entity;

use Amasty\Base\Utils\Http\Response\Entity\Config;
use Amasty\Base\Utils\Http\Response\Entity\ConfigPool;
use Amasty\Base\Utils\Http\Url\UrlComparator;
use Magento\Framework\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class ConfigPoolTest extends TestCase
{
    /**
     * @var ConfigPool|\PHPUnit\Framework\MockObject\MockObject
     */
    private $model;

    /**
     * @var UrlComparator|\PHPUnit\Framework\MockObject\MockObject
     */
    private $urlComparatorMock;

    /**
     * @var string
     */
    private $configMockPath = 'path';

    /**
     * @var Config|\PHPUnit\Framework\MockObject\MockObject
     */
    private $configMock;

    protected function setUp(): void
    {
        $this->urlComparatorMock = $this->createPartialMock(UrlComparator::class, ['isEqual']);
        $this->configMock = $this->createMock(Config::class);

        $this->model = new ConfigPool(
            $this->urlComparatorMock,
            [$this->configMockPath => $this->configMock]
        );
    }

    public function testGet()
    {
        $path = 'path';
        $this->urlComparatorMock
            ->expects($this->once())
            ->method('isEqual')
            ->with($path, $this->configMockPath)
            ->willReturn(true);

        $this->assertEquals($this->configMock, $this->model->get($path));
    }

    public function testGetNotFound()
    {
        $path = 'path1';
        $this->urlComparatorMock
            ->expects($this->once())
            ->method('isEqual')
            ->with($path, $this->configMockPath)
            ->willReturn(false);

        $this->expectException(NotFoundException::class);
        $this->model->get($path);
    }
}
