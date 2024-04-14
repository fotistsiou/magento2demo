<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Controller\Adminhtml\Notification;

use Amasty\Base\Model\Config;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;

class Frequency extends Action
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Amasty\Base\Model\Source\Frequency
     */
    private $frequency;

    public function __construct(
        Action\Context $context,
        Config $config,
        \Amasty\Base\Model\Source\Frequency $frequency
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->frequency = $frequency;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $action = $this->getRequest()->getParam('action');

        switch ($action) {
            case 'less':
                $this->increaseFrequency();
                break;
            case 'more':
                $this->decreaseFrequency();
                break;
            default:
                $this->messageManager->addErrorMessage(
                    __(
                        'An error occurred while changing the frequency.'
                    )
                );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setRefererUrl();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Amasty_Base::config'
        );
    }

    protected function decreaseFrequency()
    {
        $currentValue = $this->config->getCurrentFrequencyValue();
        $allValues = $this->frequency->toOptionArray();
        $resultValue = null;
        foreach ($allValues as $option) {
            if ($option['value'] != $currentValue) {
                $resultValue = $option['value'];
            } else {
                if ($resultValue) {
                    $this->config->changeFrequency((int)$resultValue);
                }

                break;
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                'You will get more messages of this type. Notification frequency has been updated.'
            )
        );
    }

    protected function increaseFrequency()
    {
        $currentValue = $this->config->getCurrentFrequencyValue();
        $allValues = $this->frequency->toOptionArray();
        $resultValue = null;
        foreach ($allValues as $option) {
            if ($option['value'] == $currentValue) {
                $resultValue = $option['value'];
            }

            if ($resultValue && $option['value'] != $resultValue) {
                $this->config->changeFrequency((int)$option['value']);//save next option
                break;
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                'You will get less messages of this type. Notification frequency has been updated.'
            )
        );
    }
}
