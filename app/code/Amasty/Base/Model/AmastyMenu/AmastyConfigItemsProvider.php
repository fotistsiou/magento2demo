<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu;

use Magento\Config\Model\Config\Structure;

class AmastyConfigItemsProvider
{
    /**
     * @var Structure
     */
    private $configStructure;

    public function __construct(
        Structure $configStructure
    ) {
        $this->configStructure = $configStructure;
    }

    public function getConfigItems(): array
    {
        $result = [];
        $config = $this->getAmastyConfigChildrenNode();

        if ($config) {
            foreach ($config as $item) {
                $data = $item->getData();
                if (isset($data['resource'], $data['id']) && $data['id']) {
                    $result[current(explode('::', $data['resource']))] = $data;
                }
            }
        }

        return $result;
    }

    public function getAmastyConfigChildrenNode(): ?Structure\Element\Iterator
    {
        $configTabs = $this->configStructure->getTabs();
        foreach ($configTabs as $node) {
            if ($node->getId() == 'amasty') {
                return $node->getChildren();
            }
        }

        return null;
    }
}
