<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey\Converter;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var RegisteredInstanceRepository $registeredInstancesRepo */
$registeredInstancesRepo = $objectManager->get(RegisteredInstanceRepository::class);
/** @var Converter $converter */
$converter = $objectManager->get(Converter::class);

$instance = $converter->convertArrayToInstance([
    'domain' => 'test.com',
    'system_instance_key' => 'test-key'
]);

$registeredInstance = $registeredInstancesRepo->get();
$registeredInstance->setCurrentInstance($instance);
$registeredInstance->setInstances([$instance]);

$registeredInstancesRepo->save($registeredInstance);
