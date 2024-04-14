<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider;

use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Magento\Framework\Exception\NotFoundException;

class CollectorPool
{
    public const LICENCE_SERVICE_GROUP = 'licenceService';
    public const SYS_INFO_SERVICE_GROUP = 'sysInfoService';
    public const CACHE_INFO_SERVICE_GROUP = 'cacheInfoService';

    /**
     * @var CollectorInterface[]
     */
    private $collectors;

    public function __construct(
        array $collectors
    ) {
        $this->checkProviderInstance($collectors);
        $this->collectors = $collectors;
    }

    /**
     * @param string $groupName
     * @return CollectorInterface[]
     * @throws NotFoundException
     */
    public function get(string $groupName): array
    {
        if (!isset($this->collectors[$groupName])) {
            throw new NotFoundException(
                __('The "%1" group name isn\'t defined. Verify the executor and try again.', $groupName)
            );
        }

        return $this->collectors[$groupName];
    }

    /**
     * @param array $collectors
     * @throws \InvalidArgumentException
     * @return void
     */
    private function checkProviderInstance(array $collectors): void
    {
        foreach ($collectors as $groupName => $groupCollectors) {
            foreach ($groupCollectors as $collectorName => $collector) {
                if (!$collector instanceof CollectorInterface) {
                    throw new \InvalidArgumentException(
                        'The collector instance "' . $collectorName . '" from group "'
                        . $groupName . '" must implements ' . CollectorInterface::class
                    );
                }
            }
        }
    }
}
