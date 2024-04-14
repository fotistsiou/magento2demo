<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cron Schedule List for Magento 2 (System) 
 */

namespace Amasty\CronScheduleList\Plugin;

class ScheduleCollectionPlugin
{
    public function afterGetIdFieldName($subject, $result)
    {
        if ($result === null) {
            $result = 'schedule_id';
        }

        return $result;
    }
}
