<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Blacklist;

use Amasty\Smtp\Api\BlacklistRepositoryInterface;
use Amasty\Smtp\Api\Data\BlacklistInterface;
use Amasty\Smtp\Api\Data\BlacklistInterfaceFactory;
use Amasty\Smtp\Model\Blacklist as BlacklistModel;
use Amasty\Smtp\Model\ResourceModel\Blacklist as BlacklistResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class Repository implements BlacklistRepositoryInterface
{
    /**
     * @var BlacklistInterfaceFactory
     */
    private $blacklistFactory;

    /**
     * @var BlacklistResource
     */
    private $blacklistResource;

    public function __construct(
        BlacklistInterfaceFactory $blacklistFactory,
        BlacklistResource $blacklistResource
    ) {
        $this->blacklistFactory = $blacklistFactory;
        $this->blacklistResource = $blacklistResource;
    }

    /**
     * Get data by field value
     *
     * @param mixed $value
     * @param string $field
     *
     * @return BlacklistInterface
     * @throws NotFoundException
     */
    private function getBy($value, string $field = BlacklistInterface::BLACKLIST_ID): BlacklistInterface
    {
        /** @var BlacklistInterface $blacklist */
        $blacklist = $this->blacklistFactory->create();
        $this->blacklistResource->load($blacklist, $value, $field);

        if (!$blacklist->getId()) {
            throw new NotFoundException(
                __('Black list with with specified %1 "%2" not found.', $field, $value)
            );
        }

        return $blacklist;
    }

    public function getById(int $id): BlacklistInterface
    {
        return $this->getBy($id);
    }

    public function getByCustomerEmail(string $customerEmail): BlacklistInterface
    {
        return $this->getBy($customerEmail, BlacklistInterface::CUSTOMER_EMAIL);
    }

    public function save(BlacklistInterface $blacklist): BlacklistInterface
    {
        try {
            $this->blacklistResource->save($blacklist);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save the black list. Error: %1', $e->getMessage())
            );
        }

        return $blacklist;
    }

    public function delete(BlacklistInterface $blacklist): bool
    {
        try {
            $this->blacklistResource->delete($blacklist);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to delete the blacklist email. Error: %1', $e->getMessage())
            );
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->delete($this->getById($id));

        return true;
    }

    public function addByEmail(string $email): bool
    {
        /** @var BlacklistModel $blacklist */
        $blacklist = $this->blacklistFactory->create();
        $blacklist->setData('customer_email', $email);
        $this->save($blacklist);

        return true;
    }
}
