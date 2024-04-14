<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cron Schedule List for Magento 2 (System) 
 */

namespace Amasty\CronScheduleList\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Escaper;
use Magento\Cron\Model\Schedule;

class Status implements OptionSourceInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    public function toOptionArray()
    {
        return [
            [
                'value' => Schedule::STATUS_SUCCESS, 'label' => '<span class="grid-severity-notice">'
                . $this->escaper->escapeHtml(__("Success"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_PENDING, 'label' => '<span class="grid-severity-minor">'
                . $this->escaper->escapeHtml(__("Pending"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_RUNNING, 'label' => '<span class="grid-severity-minor">'
                . $this->escaper->escapeHtml(__("Running"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_ERROR, 'label' => '<span class="grid-severity-critical">'
                . $this->escaper->escapeHtml(__("Error"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_MISSED, 'label' => '<span class="grid-severity-critical">'
                . $this->escaper->escapeHtml(__("Missed"))
                . '</span>'
            ]
        ];
    }
}
