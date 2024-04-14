<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Adminhtml\Blacklist;

use Amasty\Smtp\Api\Data\BlacklistInterface;
use Amasty\Smtp\Api\BlacklistRepositoryInterface;
use Amasty\Smtp\Controller\Adminhtml\Blacklist;
use Amasty\Smtp\Model\BlacklistFactory;
use Amasty\Smtp\Model\Blacklist as BlacklistModel;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Blacklist
{
    public const DATA_PERSISTOR_KEY = 'amasty_amsmtp_blacklist';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;
    /**
     * @var BlacklistFactory
     */
    private $blacklistFactory;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        BlacklistRepositoryInterface $blacklistRepository,
        BlacklistFactory $blacklistFactory
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->blacklistRepository = $blacklistRepository;
        $this->blacklistFactory = $blacklistFactory;
    }

    public function execute(): ResponseInterface
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $id = (int)$this->getRequest()->getParam(BlacklistInterface::BLACKLIST_ID);

                if ($id) {
                    $blacklist = $this->blacklistRepository->getById($id);
                } else {
                    /** @var BlacklistModel $blacklist */
                    $blacklist = $this->blacklistFactory->create();
                }
                $blacklist->setData($data);
                $this->blacklistRepository->save($blacklist);

                $this->messageManager->addSuccessMessage(__('You saved the blacklist record.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('amasty_smtp/*/edit', ['id' => $blacklist->getId()]);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->saveFormDataAndRedirect($data, $id);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the blacklist data. Please review the error log.')
                );

                return $this->saveFormDataAndRedirect($data, $id);
            }
        }

        return $this->_redirect('amasty_smtp/*/');
    }

    private function saveFormDataAndRedirect(array $data, int $id): ResponseInterface
    {
        $this->dataPersistor->set(self::DATA_PERSISTOR_KEY, $data);
        if (!empty($id)) {
            return $this->_redirect('amasty_smtp/*/edit', ['id' => $id]);
        } else {
            return $this->_redirect('amasty_smtp/*/index');
        }
    }
}
