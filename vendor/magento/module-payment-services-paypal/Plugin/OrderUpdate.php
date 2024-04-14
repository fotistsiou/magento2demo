<?php

/**
 * ADOBE CONFIDENTIAL
 *
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 */

declare(strict_types=1);

namespace Magento\PaymentServicesPaypal\Plugin;

use Magento\PaymentServicesPaypal\Helper\OrderHelper;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\PaymentServicesPaypal\Model\OrderService;
use Magento\PaymentServicesBase\Model\HttpException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Cancels an order and an authorization transaction.
 */
class OrderUpdate
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * @var array
     */
    private array $orderUpdateLocations;

    /**
     * @var OrderHelper
     */
    private $orderHelper;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param OrderService $orderService
     * @param OrderHelper $orderHelper
     * @param array $orderUpdateLocations
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        OrderService            $orderService,
        OrderHelper             $orderHelper,
        array                   $orderUpdateLocations = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->orderService = $orderService;
        $this->orderUpdateLocations = $orderUpdateLocations;
        $this->orderHelper = $orderHelper;
    }

    /**
     * Updates an order during the order creation.
     *
     * @param CartManagementInterface $subject
     * @param int $cartId
     * @param PaymentInterface|null $payment
     * @return void
     * @throws LocalizedException
     * @throws HttpException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePlaceOrder(
        CartManagementInterface $subject,
        int $cartId,
        PaymentInterface $payment = null
    ) : void {
        $quote = $this->quoteRepository->get($cartId);
        $location = $quote->getPayment()->getAdditionalInformation('location');
        if (in_array($location, $this->orderUpdateLocations)) {
            $quote->getBillingAddress()->setShouldIgnoreValidation(true);
            $quote->getShippingAddress()->setShouldIgnoreValidation(true);
            $paypalOrderId = $quote->getPayment()->getAdditionalInformation('paypal_order_id');
            try {
                $this->orderService->update(
                    $paypalOrderId,
                    [
                        'amount' => $this->orderHelper->formatAmount((float)$quote->getBaseGrandTotal()),
                        'currency_code' => $quote->getCurrency()->getBaseCurrencyCode()
                    ]
                );
            } catch (HttpException $e) {
                throw new LocalizedException(__('Your payment was not successful. Try again.'));
            }
        }
    }
}
