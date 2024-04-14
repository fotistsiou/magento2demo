<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Utils\Http\Url;

use Amasty\Base\Utils\Http\Url\UrlComparator;
use PHPUnit\Framework\TestCase;

class UrlComparatorTest extends TestCase
{
    /**
     * @var UrlComparator
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new UrlComparator();
    }

    /**
     * @param string $url1
     * @param string $url2
     * @param string $mask
     * @param bool $expected
     * @dataProvider isEqualDataProvider
     * @return void
     */
    public function testIsEqual(string $url1, string $url2, string $mask, bool $expected): void
    {
        $this->assertEquals($expected, $this->model->isEqual($url1, $url2));
    }

    public function isEqualDataProvider(): array
    {
        return [
            ['/api/v1/instance/registration', '/api/v1/instance/registration', '{}', true],
            ['/api/v1/instance/registration', '/api/v1/instance_client/ping', '{}', false],
            ['/api/v1/instance_client/{}/collect', '/api/v1/instance_client/test/collect', '{}', true],
            ['/api/v1/instance_client/test/collect', '/api/v1/instance_client/test2/collect', '{}', false]
        ];
    }
}
