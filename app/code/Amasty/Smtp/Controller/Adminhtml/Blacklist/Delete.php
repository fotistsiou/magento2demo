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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Blacklist
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

    public function execute(): ResponseInterface
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->blacklistRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Blacklist email was deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete the blacklist email right now. Please review the log and try again.')
                );

                return $this->_redirect('amasty_smtp/*/edit', ['id' => $id]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a blacklist email to delete.'));
        }

        return $this->_redirect('amasty_smtp/*/');
    }
}
