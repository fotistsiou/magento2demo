<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Blacklist extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_Smtp::smtp_blacklist';
}
