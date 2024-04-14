<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\LicenceService\Api;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Model\LicenceService\Request\Url\Builder;
use Amasty\Base\Model\LicenceService\Response\Data\RegisteredInstance;
use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Utils\Http\CurlFactory;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\LocalizedException;

class RequestManager
{
    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Builder
     */
    private $urlBuilder;

    public function __construct(
        SimpleDataObjectConverter $simpleDataObjectConverter,
        CurlFactory $curlFactory,
        Builder $urlBuilder
    ) {
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->curlFactory = $curlFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param string $domain
     * @return RegisteredInstance|SimpleDataObject
     * @throws LocalizedException
     */
    public function registerInstance(string $domain): RegisteredInstance
    {
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build('/api/v1/instance/registration');
        $postParams = json_encode(['domain' => $domain]);

        $response = $curl->request($url, $postParams);
        if (!in_array($response->getData('code'), [200, 204])) {
            throw new LocalizedException(__('Invalid request.'));
        }

        return $response;
    }

    /**
     * @param InstanceInfo $instanceInfo
     * @return void
     * @throws LocalizedException
     */
    public function updateInstanceInfo(InstanceInfo $instanceInfo): void
    {
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build(
            '/api/v1/instance_client/'. $instanceInfo->getSystemInstanceKey() . '/collect'
        );
        $postParams = $this->simpleDataObjectConverter->convertKeysToCamelCase($instanceInfo->toArray());
        $postParams = json_encode($postParams);

        $response = $curl->request($url, $postParams);
        if (!in_array($response->getData('code'), [200, 204])) {
            throw new LocalizedException(__('Invalid request.'));
        }
    }

    /**
     * @deprecated since 1.15.1
     * @see RequestManager::pingRequest
     * @param string $systemInstanceKey
     * @return void
     * @throws LocalizedException
     */
    public function ping(string $systemInstanceKey): void
    {
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build('/api/v1/instance_client/'. $systemInstanceKey . '/ping');

        $curl->request($url);
    }

    /**
     * @param InstanceInfo $instanceInfo
     * @return SimpleDataObject
     */
    public function pingRequest(InstanceInfo $instanceInfo): SimpleDataObject
    {
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build('/api/v1/instance_client/'. $instanceInfo->getSystemInstanceKey() . '/ping');

        $postParams = $this->simpleDataObjectConverter->convertKeysToCamelCase([
            'is_production' => $instanceInfo->getIsProduction(),
            'customer_instance_key' => $instanceInfo->getCustomerInstanceKey()
        ]);
        $postParams = json_encode($postParams);

        return $curl->request($url, $postParams);
    }

    /**
     * @param InstanceInfo $instanceInfo
     * @return SimpleDataObject
     */
    public function verify(InstanceInfo $instanceInfo): SimpleDataObject
    {
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build('/api/v1/instance_client/'. $instanceInfo->getSystemInstanceKey() . '/verify');

        $postParams = $this->simpleDataObjectConverter->convertKeysToCamelCase($instanceInfo->toArray());
        $postParams = json_encode($postParams);

        return $curl->request($url, $postParams);
    }
}
