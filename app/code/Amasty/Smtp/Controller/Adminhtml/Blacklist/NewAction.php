<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Adminhtml\Blacklist;

use Amasty\Smtp\Controller\Adminhtml\Blacklist;

class NewAction extends Blacklist
{
    public function execute(): void
    {
        $this->_forward('edit');
    }
}
