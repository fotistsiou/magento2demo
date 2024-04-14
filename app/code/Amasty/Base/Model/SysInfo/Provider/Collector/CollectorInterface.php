<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector;

interface CollectorInterface
{
    /**
     * Uses to get information about system;
     * mixed because that data must be processed
     * in class that called group
     *
     * @return mixed
     */
    public function get();
}
