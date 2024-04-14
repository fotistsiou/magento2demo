<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Adminhtml\Debug;

use Magento\Backend\App\Action;

class Clear extends Action
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Smtp::reports_debug');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_objectManager->get(\Amasty\Smtp\Model\ResourceModel\Debug::class)->truncate();
            $this->messageManager->addSuccess(__('Debug log has been cleared.'));

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());

            return $resultRedirect->setPath('*/*/');
        }
    }
}