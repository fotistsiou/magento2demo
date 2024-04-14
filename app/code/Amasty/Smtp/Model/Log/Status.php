<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Log;

use Amasty\Smtp\Model\Log;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => Log::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Log::STATUS_SENT, 'label' => __('Successfully Sent')],
            ['value' => Log::STATUS_FAILED, 'label' => __('Failed')],
            ['value' => Log::STATUS_BLACKLISTED, 'label' => __('Blacklisted')],
        ];
    }
}
