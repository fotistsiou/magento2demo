<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Adminhtml\Blacklist;

use Amasty\Smtp\Api\BlacklistRepositoryInterface;
use Amasty\Smtp\Controller\Adminhtml\Blacklist;
use Amasty\Smtp\Model\ResourceModel\Blacklist\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Blacklist
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        BlacklistRepositoryInterface $blacklistRepository
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->blacklistRepository = $blacklistRepository;
    }

    public function execute(): ResponseInterface
    {
        try {
            $this->filter->applySelectionOnTargetProvider();
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = $failed = 0;

            foreach ($collection->getItems() as $blacklist) {
                try {
                    $this->blacklistRepository->delete($blacklist);
                    $deleted++;
                } catch (CouldNotDeleteException $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                    $failed++;
                }
            }

            if ($deleted != 0) {
                $this->messageManager->addSuccessMessage(
                    __('%1 blacklist email(s) has been successfully deleted', $deleted)
                );
            }
            if ($failed != 0) {
                $this->messageManager->addErrorMessage(
                    __('%1 blacklist email(s) has been failed to delete', $failed)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while deleting blacklists email(s). Please review the error log.')
            );
        }

        return $this->_redirect('amasty_smtp/*/index');
    }
}
