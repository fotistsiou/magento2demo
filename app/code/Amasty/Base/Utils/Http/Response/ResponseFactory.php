<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Utils\Http\Response;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SimpleDataObjectFactory;
use Amasty\Base\Utils\DataConverter;
use Amasty\Base\Utils\Http\Response\Entity\ConfigPool;
use Amasty\Base\Utils\Http\Response\Entity\Converter;
use Magento\Framework\Exception\NotFoundException;

class ResponseFactory
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ConfigPool
     */
    private $configPool;

    /**
     * @var SimpleDataObjectFactory
     */
    private $simpleDataObjectFactory;

    /**
     * @var DataConverter
     */
    private $dataConverter;

    public function __construct(
        Converter $converter,
        ConfigPool $configPool,
        SimpleDataObjectFactory $simpleDataObjectFactory,
        DataConverter $dataConverter
    ) {
        $this->converter = $converter;
        $this->configPool = $configPool;
        $this->simpleDataObjectFactory = $simpleDataObjectFactory;
        $this->dataConverter = $dataConverter;
    }

    public function create(string $url, array $response): SimpleDataObject
    {
        $response = $this->dataConverter->convertArrayToSnakeCase($response);
        try {
            // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
            $path = parse_url($url, PHP_URL_PATH);
            $entityConfig = $this->configPool->get($path);
            if ($entityConfig->getType() == 'array') {
                $object = [];
                foreach ($response as $row) {
                    $object[] = $this->converter->convertToObject($row, $entityConfig);
                }
            } else {
                $object = $this->converter->convertToObject($response, $entityConfig);
            }
        } catch (NotFoundException $e) {
            $object = $this->simpleDataObjectFactory->create(['data' => $response ?? []]);
        }

        return $object;
    }
}
