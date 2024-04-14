<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Plugin\Config\Block\System\Config\Edit;

use Amasty\Base\Block\Adminhtml\InstanceRegistrationMessages;
use Magento\Config\Block\System\Config\Edit;
use Magento\Framework\View\LayoutInterface;

class RemoveSaveButton
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetLayout(Edit $subject, Edit $result, LayoutInterface $layout): Edit
    {
        if ($subject->getRequest()->getParam('section') === InstanceRegistrationMessages::SECTION_NAME) {
            $subject->getToolbar()->unsetChild('save_button');
        }

        return $result;
    }
}
