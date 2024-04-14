<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Integration\Model\LicenseService\Api;

use Amasty\Base\Utils\Http\Curl as BaseCurl;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

abstract class AbstractEndpointTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function mockResponse(string $response, int $code = 200): void
    {
        $mockedCurl = $this->createMock(Curl::class);
        $mockedCurl->method('read')->willReturn($response);
        $mockedCurl->method('getInfo')->willReturn($code);

        $mockedCurlFactory = $this->createMock(CurlFactory::class);
        $mockedCurlFactory = $this->objectManager->get(get_class($mockedCurlFactory)); //to add to shared instances
        $mockedCurlFactory->method('create')->willReturn($mockedCurl);

        $this->objectManager->configure(
            [
                BaseCurl::class => [
                    'arguments' => [
                        'curlFactory' => [
                            'instance' => get_class($mockedCurlFactory),
                            'shared' => true
                        ],
                    ]
                ]
            ]
        );
    }

    protected function readResponseFile(string $fileName): array
    {
        $filepath = __DIR__ . '/../../../_files/responses/' . $fileName;
        $content = (string)file_get_contents($filepath);

        return json_decode($content, true);
    }
}
