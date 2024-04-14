<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml\System\Config\Form\Field;

use Amasty\Base\Model\Serializer;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class LicenseKeys extends AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Base::config/form/field/array.phtml';

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        Context $context,
        Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->serializer = $serializer;
    }

    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'license_key',
            ['label' => __('Instance Registration Key')]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');
    }

    public function getExistingKeysData(): string
    {
        $rowsData = array_map(function ($row) {
            return $row->getData();
        }, $this->getArrayRows());

        return $this->serializer->serialize(array_values($rowsData)) ?: '';
    }

    public function getElementName(): string
    {
        return (string)$this->getElement()->getName();
    }

    public function getColumnNames(): string
    {
        return $this->serializer->serialize(array_keys($this->getColumns()));
    }
}
