<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo;

use Amasty\Base\Model\FlagRepository;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstanceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\SerializerInterface;

class RegisteredInstanceRepository
{
    public const REGISTERED_INSTANCE = 'amasty_base_registered_instance';

    /**
     * @var FlagRepository
     */
    private $flagRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RegisteredInstanceFactory
     */
    private $registeredInstanceFactory;

    public function __construct(
        FlagRepository $flagRepository,
        SerializerInterface $serializer,
        DataObjectHelper $dataObjectHelper,
        RegisteredInstanceFactory $registeredInstanceFactory
    ) {
        $this->flagRepository = $flagRepository;
        $this->serializer = $serializer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->registeredInstanceFactory = $registeredInstanceFactory;
    }

    public function get(): RegisteredInstance
    {
        $registeredInstance = $this->registeredInstanceFactory->create();
        $regInstSerialized = $this->flagRepository->get(self::REGISTERED_INSTANCE);
        $regInstArray = $regInstSerialized ? $this->serializer->unserialize($regInstSerialized) : [];
        $this->dataObjectHelper->populateWithArray(
            $registeredInstance,
            $regInstArray,
            RegisteredInstance::class
        );

        return $registeredInstance;
    }

    public function save(RegisteredInstance $registeredInstance): bool
    {
        $regInstArray = $registeredInstance->toArray();
        $regInstSerialized = $this->serializer->serialize($regInstArray);
        $this->flagRepository->save(self::REGISTERED_INSTANCE, $regInstSerialized);

        return true;
    }
}
