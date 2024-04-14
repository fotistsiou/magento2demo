<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Api;

use Amasty\Smtp\Api\Data\BlacklistInterface;

/**
 * Email blacklist CRUD interface
 * @api
 */
interface BlacklistRepositoryInterface
{
    /**
     * Get blacklist email by ID.
     *
     * @param int $id
     *
     * @return \Amasty\Smtp\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getById(int $id): BlacklistInterface;

    /**
     * Get blacklist email by customer email.
     *
     * @param string $customerEmail
     *
     * @return \Amasty\Smtp\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getByCustomerEmail(string $customerEmail): BlacklistInterface;

    /**
     * Save blacklist email.
     *
     * @param \Amasty\Smtp\Api\Data\BlacklistInterface $blacklist
     *
     * @return \Amasty\Smtp\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(BlacklistInterface $blacklist): BlacklistInterface;

    /**
     * Delete blacklist email.
     *
     * @param \Amasty\Smtp\Api\Data\BlacklistInterface $blacklist
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(BlacklistInterface $blacklist): bool;

    /**
     * Delete blacklist email by ID.
     *
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;

    /**
     * @param string $email
     * @return bool
     */
    public function addByEmail(string $email): bool;
}
