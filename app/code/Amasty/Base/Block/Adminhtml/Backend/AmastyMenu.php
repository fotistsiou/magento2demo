<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml\Backend;

use Amasty\Base\Model\AmastyMenu\Frontend\ItemsProvider;
use Amasty\Base\Model\Serializer;
use Magento\Backend\Block\Template;

class AmastyMenu extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Base::menu/submenu.phtml';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ItemsProvider
     */
    private $itemsProvider;

    public function __construct(
        Template\Context $context,
        Serializer $serializer,
        ItemsProvider $itemsProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->itemsProvider = $itemsProvider;
    }

    public function getMenuItemsJson(): string
    {
        $items = $this->itemsProvider->getItems();

        return $this->serializer->serialize($items);
    }
}
