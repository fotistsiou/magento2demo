<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cron Schedule List for Magento 2 (System) 
 */

namespace Amasty\CronScheduleList\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Amasty\CronScheduleList\Model\ScheduleCollectionFactory as CollectionFactory;
use Amasty\CronScheduleList\Model\DateTimeBuilder;

class Notice extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_CronScheduleList::notice.phtml';

    /**
     * @var CollectionFactory
     */
    private $jobsCollection;

    /**
     * @var DateTimeBuilder
     */
    private $dateTimeBuilder;

    public function __construct(
        CollectionFactory $jobsCollection,
        DateTimeBuilder $dateTimeBuilder,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jobsCollection = $jobsCollection;
        $this->dateTimeBuilder = $dateTimeBuilder;
    }

    public function getLastActivity()
    {
        $collection = $this->jobsCollection->create();

        $item = $collection->getLastActivity();

        if ($item->getId()) {
            return $this->dateTimeBuilder->formatDate($item->getData('finished_at'));
        } else {
            return __('never');
        }
    }
}
