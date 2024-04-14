<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\ChangedData;

use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\CacheStorage;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Checker;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Encryption;
use Amasty\Base\Model\SysInfo\Provider\Collector;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;

class Persistor
{
    /**
     * @var Collector
     */
    private $collector;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @var CacheStorage
     */
    private $cacheStorage;

    /**
     * @var Encryption
     */
    private $encryption;

    public function __construct(
        Collector $collector,
        Checker $checker,
        CacheStorage $cacheStorage,
        Encryption $encryption
    ) {
        $this->collector = $collector;
        $this->checker = $checker;
        $this->cacheStorage = $cacheStorage;
        $this->encryption = $encryption;
    }

    public function get(): array
    {
        $data = $this->collector->collect(CollectorPool::LICENCE_SERVICE_GROUP);
        $changedData = [];
        foreach ($data as $sysInfoName => $sysInfo) {
            $cacheValue = $this->cacheStorage->get($sysInfoName);
            $newValue = is_array($sysInfo)
                ? $this->encryption->encryptArray($sysInfo)
                : $this->encryption->encryptString((string)$sysInfo);
            if ($this->checker->isChangedCacheValue($cacheValue, $newValue)) {
                $changedData[$sysInfoName] = $sysInfo;
            }
        }

        return $changedData;
    }

    public function save(array $data): void
    {
        foreach ($data as $sysInfoName => $sysInfo) {
            $encryptionValue = is_array($sysInfo)
                ? $this->encryption->encryptArray($sysInfo)
                : $this->encryption->encryptString((string)$sysInfo);
            $this->cacheStorage->set($sysInfoName, $encryptionValue);
        }
    }
}
