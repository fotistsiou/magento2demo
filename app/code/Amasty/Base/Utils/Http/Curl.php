<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Utils\Http;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Utils\Http\Response\ResponseFactory;
use Laminas\Http\Request;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory as FrameworkCurlFactory;

class Curl
{
    /**
     * Connection timeout, seconds
     */
    public const CONNECTION_TIMEOUT = 60;

    /**
     * @var FrameworkCurlFactory
     */
    private $curlFactory;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var array
     */
    private $headers = [];

    public function __construct(
        FrameworkCurlFactory $curlFactory,
        ResponseFactory $responseFactory
    ) {
        $this->curlFactory = $curlFactory;
        $this->responseFactory = $responseFactory;
    }

    public function request(
        string $url,
        $params = '',
        string $method = Request::METHOD_POST
    ): SimpleDataObject {
        $curl = $this->curlFactory->create();
        $curl->setConfig(['timeout' => self::CONNECTION_TIMEOUT, 'header' => false, 'verifypeer' => false]);

        $curl->write(
            $method,
            $url,
            '1.1',
            $this->getHeaders(),
            $params
        );

        $responseData = $curl->read();
        $responseData = json_decode((string)$responseData, true) ?? [];
        $httpCode = $curl->getInfo(CURLINFO_HTTP_CODE);
        $curl->close();
        $response = $this->responseFactory->create($url, $responseData);
        $response->setData('code', $httpCode);

        return $response;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    private function getHeaders(): array
    {
        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers[] = implode(': ', [$name, $value]);
        }

        return $headers;
    }
}
