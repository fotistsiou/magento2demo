<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block;

use Amasty\Base\Block\Adminhtml\System\Config\SysInfo\CacheInfo;
use Amasty\Base\Block\Adminhtml\System\Config\SysInfo\DownloadButton;
use Amasty\Base\Model\ModuleInfoProvider;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Helper\Js;

class Info extends Fieldset
{
    public const CRON_HELP_URL = 'https://amasty.com/knowledge-base/magento-cron.html';

    /**
     * @var CollectionFactory
     */
    private $cronFactory;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Field|null
     */
    protected $fieldRenderer;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        CollectionFactory $cronFactory,
        DirectoryList $directoryList,
        Reader $reader,
        ResourceConnection $resourceConnection,
        ModuleInfoProvider $moduleInfoProvider,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->cronFactory = $cronFactory;
        $this->directoryList = $directoryList;
        $this->resourceConnection = $resourceConnection;
        $this->reader = $reader;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    public function render(AbstractElement $element): string
    {
        return $this->_getHeaderHtml($element)
            . $this->getMagentoMode($element)
            . $this->getMagentoPathInfo($element)
            . $this->getOwnerInfo($element)
            . $this->getDbTime($element)
            . $this->getMagentoTime($element)
            . $this->getCronInfo($element)
            . $this->getCacheInfo($element)
            . $this->getDownloadButtonHtml($element)
            . $this->_getFooterHtml($element);
    }

    /**
     * @return Field|BlockInterface
     */
    private function getFieldRenderer()
    {
        if (empty($this->fieldRenderer)) {
            $this->fieldRenderer = $this->_layout->createBlock(
                Field::class
            );
        }

        return $this->fieldRenderer;
    }

    private function getMagentoMode(AbstractElement $fieldset): string
    {
        $label = __('Magento Mode');

        $env = $this->reader->load();
        $mode = $env[State::PARAM_MODE] ?? '';

        return $this->getFieldHtml($fieldset, 'magento_mode', $label, ucfirst($mode));
    }

    private function getMagentoPathInfo(AbstractElement $fieldset): string
    {
        $label = __('Magento Path');
        $path = $this->directoryList->getRoot();

        return $this->getFieldHtml($fieldset, 'magento_path', $label, $path);
    }

    private function getOwnerInfo(AbstractElement $fieldset): string
    {
        $serverUser = function_exists('get_current_user')
            ? get_current_user()
            : __('Unknown');

        return $this->getFieldHtml($fieldset, 'magento_user', __('Server User'), $serverUser);
    }

    private function getDbTime(AbstractElement $fieldset): string
    {
        $time = $this->resourceConnection->getConnection()->fetchOne('select now()');

        return $this->getFieldHtml(
            $fieldset,
            'mysql_current_date_time',
            __('Current Database Time'),
            $time
        );
    }

    private function getMagentoTime(AbstractElement $fieldset): string
    {
        return $this->getFieldHtml(
            $fieldset,
            'magento_current_date_time',
            __('Current Magento Time'),
            $this->getLocalizedTime()
        );
    }

    private function getCronInfo(AbstractElement $fieldset): string
    {
        $crontabCollection = $this->cronFactory->create();
        $crontabCollection->setOrder('schedule_id')->setPageSize(5);

        $value = '';
        foreach ($crontabCollection as $crontabRow) {
            $value .=
                '<tr>' .
                '<td>' . $crontabRow['job_code'] . '</td>' .
                '<td>' . $crontabRow['status'] . '</td>' .
                '<td>' . $crontabRow['created_at'] . ' (DB Time)<br>'
                . $this->getLocalizedTime($crontabRow['created_at']) . '</td>'
                . '</tr>';
        }

        if ($value) {
            $cronInfoHead =
                '<thead>
                    <tr>
                        <th style="text-align: left">' . __('Job code') . '</th>
                        <th style="text-align: left">' . __('Status') . '</th>
                        <th style="text-align: left">' . __('Created At') . '</th>
                    </tr>
                </thead>
                <tbody>';
            $value = '<table>' . $cronInfoHead . $value . '</table>';
        } else {
            $value = '<div class="red">' . __('No cron jobs found') . '</div>';
            if (!$this->moduleInfoProvider->isOriginMarketplace()) {
                $value .= '<a target="_blank" href="' . self::CRON_HELP_URL . '">' . __('Learn more') . '</a>';
            }
        }

        return $this->getFieldHtml($fieldset, 'cron_configuration', __('Cron (Last 5)'), $value);
    }

    private function getDownloadButtonHtml(AbstractElement $fieldset): string
    {
        $button = $this->getLayout()->createBlock(DownloadButton::class);

        return $this->getFieldHtml(
            $fieldset,
            DownloadButton::ELEMENT_ID,
            __('System Data'),
            $button->toHtml()
        );
    }

    private function getCacheInfo(AbstractElement $fieldset): string
    {
        $cacheInfoBlock = $this->getLayout()->createBlock(CacheInfo::class);

        return $this->getFieldHtml(
            $fieldset,
            'cache_info',
            __('Cache Info'),
            $cacheInfoBlock->toHtml()
        );
    }

    private function getLocalizedTime(?string $inputTime = null): string
    {
        return $this->_localeDate->date($inputTime)->format('Y-m-d H:i:s \U\T\CP');
    }

    protected function getFieldHtml(
        AbstractElement $fieldset,
        string $fieldName,
        Phrase $label,
        string $value = ''
    ): string {
        $field = $fieldset->addField($fieldName, 'label', [
            'name'  => 'dummy',
            'label' => $label,
            'after_element_html' => $value,
        ])->setRenderer($this->getFieldRenderer());

        return (string)$field->toHtml();
    }
}
