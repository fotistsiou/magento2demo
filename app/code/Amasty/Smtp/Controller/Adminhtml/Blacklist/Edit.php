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
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

class Edit extends Blacklist
{
    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    public function __construct(
        Action\Context $context,
        BlacklistRepositoryInterface $blacklistRepository
    ) {
        parent::__construct($context);
        $this->blacklistRepository = $blacklistRepository;
    }

    public function execute(): ResultInterface
    {
        $title = __('New Blacklist Email');

        if ($blacklistId = (int)$this->getRequest()->getParam('id')) {
            try {
                $blacklist = $this->blacklistRepository->getById($blacklistId);
                $title = __('Editing Blacklist Email %1', $blacklist->getName());
            } catch (NotFoundException $e) {
                $this->messageManager->addErrorMessage(__('This blacklist email no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('amasty_smtp/*/index');

                return $resultRedirect;
            }
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Smtp::smtp_blacklist');
        $resultPage->setActiveMenu('Amasty_Smtp::smtp');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
