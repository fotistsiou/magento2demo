<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Transport;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class MessageIdGenerator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    public function generate(): string
    {
        $currentTime = time();
        $processId = getmypid();

        try {
            $randomNumber = random_int(0, PHP_INT_MAX);
        } catch (\Exception $e) {
            $randomNumber = 0;
        }

        return hash(
            'sha256',
            $currentTime . $processId . $randomNumber
        ) . '@' . $this->getHostName();
    }

    private function getHostName(): string
    {
        try {
            $hostName = $this->storeManager->getStore()->getBaseUrl();
        } catch (NoSuchEntityException $e) {
            $hostName = $this->urlBuilder->getBaseUrl();
        }

        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        return parse_url($hostName, PHP_URL_HOST);
    }
}
