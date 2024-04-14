<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\LicenceService;

use Amasty\Base\Model\Config;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;

class CustomerInstanceKey implements CollectorInterface
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config->getLicenseKeys();
    }
}
