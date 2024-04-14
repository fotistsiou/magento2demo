<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cron Schedule List for Magento 2 (System) 
 */

namespace Amasty\CronScheduleList\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class AbstractSchedule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_CronScheduleList::schedule_list';
}
