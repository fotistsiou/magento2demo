<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class GetSupport implements ArgumentInterface
{
    public function getSubmitTicketUrl(): string
    {
        return 'https://support.amasty.com/portal/en/newticket?departmentId=34453000000007061'
            . '&layoutId=34453000000023011&utm_source=extension&utm_medium=backend&utm_campaign=submit_ticket';
    }

    public function getProSubscribeUrl(): string
    {
        return 'https://amasty.com/contacts/?utm_source=extension&utm_medium=backend&utm_campaign=support_pro';
    }

    public function getPremiumSubscribeUrl(): string
    {
        return 'https://amasty.com/contacts/?utm_source=extension&utm_medium=backend&utm_campaign=support_premium';
    }

    public function getAmastyOneSubscribeUrl(): string
    {
        return 'https://amasty.com/contacts/?utm_source=extension&utm_medium=backend&utm_campaign=support_amastyone';
    }
}
