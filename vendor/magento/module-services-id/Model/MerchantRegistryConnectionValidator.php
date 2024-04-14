<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ServicesId\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ServicesConnector\Api\KeyValidationInterface;

/**
 * Validates API keys and connection to merchant registry
 */
class MerchantRegistryConnectionValidator
{
    /**
     * @var ScopeConfigInterface
     * @deprecated 3.2.1
     */
    private $config;

    /**
     * @var ServicesConfigInterface
     * @deprecated 3.2.1
     */
    private $servicesConfig;

    /**
     * @var ServicesClientInterface
     * @deprecated 3.2.1
     */
    private $servicesClient;
    private KeyValidationInterface $keyValidation;

    /**
     * @param ScopeConfigInterface $config
     * @param ServicesConfigInterface $servicesConfig
     * @param ServicesClientInterface $servicesClient
     */
    public function __construct(
        ScopeConfigInterface $config,
        ServicesConfigInterface $servicesConfig,
        ServicesClientInterface $servicesClient,
        KeyValidationInterface $keyValidation
    ) {
        $this->config = $config;
        $this->servicesConfig = $servicesConfig;
        $this->servicesClient = $servicesClient;
        $this->keyValidation = $keyValidation;
    }

    /**
     * Validate API keys against merchant registry
     *
     * @param string $environment
     * @return string
     */
    public function validate(string $environment) : string
    {
        try {
            $message = $this->keyValidation->execute('Magento_ServicesId', $environment)
                ? ServicesConfigMessage::OK
                : ServicesConfigMessage::ERROR_REQUEST_FAILED;
        } catch (\Exception $e) {
            $message = ServicesConfigMessage::ERROR_REQUEST_FAILED;
        }
        return $message;
    }
}
