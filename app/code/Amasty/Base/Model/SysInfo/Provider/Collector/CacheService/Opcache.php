<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\CacheService;

use Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Info\CacheInfoInterface;
use Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Info\CacheInfoInterfaceFactory;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;

class Opcache implements CollectorInterface
{
    private const CACHE_NAME = 'Opcache';
    private const OPTION_ENABLED = 'opcache.enable';
    private const OPTION_VALIDATE_TIMESTAMPS = 'opcache.validate_timestamps';
    private const OPTION_REVALIDATE_FREQUENCY = 'opcache.revalidate_freq';
    private const OPTION_FILE_CACHE_ONLY = 'opcache.file_cache_only';
    private const NORMAL_REVALIDATE_FREQUENCY_VALUE = 2;

    /**
     * @var CacheInfoInterfaceFactory
     */
    private $cacheInfoFactory;

    public function __construct(CacheInfoInterfaceFactory $cacheInfoFactory)
    {
        $this->cacheInfoFactory = $cacheInfoFactory;
    }

    public function get(): CacheInfoInterface
    {
        $options = $this->retrieveOptions();

        $cacheInfo = $this->cacheInfoFactory->create();
        $cacheInfo->setName($this->getName());
        $cacheInfo->setStatus($this->resolveStatus($options));
        $cacheInfo->setAdditionalInfo($this->resolveAdditionalInfo($options));

        return $cacheInfo;
    }

    private function retrieveOptions(): array
    {
        $isEnabled = $this->iniGet(self::OPTION_ENABLED)
            && !$this->iniGet(self::OPTION_FILE_CACHE_ONLY);
        $isValidateTimestamps = (bool)$this->iniGet(self::OPTION_VALIDATE_TIMESTAMPS);
        $frequency = $this->iniGet(self::OPTION_REVALIDATE_FREQUENCY);

        if (!$isEnabled || ($isValidateTimestamps && $frequency <= self::NORMAL_REVALIDATE_FREQUENCY_VALUE)) {
            return [];
        }
        $options['Validate timestamps'] = 'Yes';
        $options['Revalidate frequency'] = sprintf('%d seconds', $frequency);

        return $options;
    }

    private function iniGet(string $option): string
    {
        return (string)ini_get($option);
    }

    private function getName(): string
    {
        return self::CACHE_NAME;
    }

    private function resolveStatus(array $options): string
    {
        return !empty($options) ? (string)__('Active') : (string)__('Inactive');
    }

    private function resolveAdditionalInfo(array $options): string
    {
        if (empty($options)) {
            return '';
        }

        $result = [];
        foreach ($options as $optionName => $optionValue) {
            $result[] = sprintf("%s: %s", $optionName, $optionValue);
        }

        return implode(PHP_EOL, $result);
    }
}
