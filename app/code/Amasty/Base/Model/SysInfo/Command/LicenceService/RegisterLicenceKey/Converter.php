<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey;

use Amasty\Base\Model\SysInfo\Data\RegisteredInstance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\InstanceFactory;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstanceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Converter
{
    /**
     * @var RegisteredInstanceFactory
     */
    private $registeredInstanceFactory;

    /**
     * @var InstanceFactory
     */
    private $instanceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        RegisteredInstanceFactory $registeredInstanceFactory,
        InstanceFactory $instanceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->registeredInstanceFactory = $registeredInstanceFactory;
        $this->instanceFactory = $instanceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    public function convertArrayToRegisteredInstance(array $data): RegisteredInstance
    {
        /** @var RegisteredInstance $registeredInstance */
        $registeredInstance = $this->registeredInstanceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $registeredInstance,
            $data,
            RegisteredInstance::class
        );

        return $registeredInstance;
    }

    public function convertArrayToInstance(array $data): Instance
    {
        /** @var Instance $instance */
        $instance = $this->instanceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $instance,
            $data,
            Instance::class
        );

        return $instance;
    }
}
