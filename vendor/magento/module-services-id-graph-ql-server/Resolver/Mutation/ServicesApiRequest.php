<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ServicesIdGraphQlServer\Resolver\Mutation;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\ServicesId\Model\ServicesClientInterface;

/**
 * Resolver for mutation servicesApiRequest
 */
class ServicesApiRequest implements ResolverInterface
{
    /**
     * @var ServicesClientInterface
     */
    private $servicesClient;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param ServicesClientInterface $servicesClient
     * @param Json $serializer
     */
    public function __construct(
        ServicesClientInterface $servicesClient,
        Json $serializer
    ) {
        $this->servicesClient = $servicesClient;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $request = $args['servicesApiRequest'];
        $result = $this->servicesClient->request(
            $request['method'],
            $request['uri'],
            $request['payload'],
            isset($request['headers']) ? $this->getHeaders($request['headers']) : []
        );
        $response = $this->serializer->serialize($result);
        return ['response' => $response];
    }

    /**
     * Parse headers from request
     *
     * @param array $requestHeaders
     * @return array
     */
    private function getHeaders(array $requestHeaders) : array
    {
        $headers = [];
        foreach ($requestHeaders as $header) {
            $headers[$header['key']] = $header['value'];
        }
        return $headers;
    }
}
