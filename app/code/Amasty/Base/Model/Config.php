<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model;

use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;

class Config extends ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_base/';

    public const IS_PRODUCTION = 'instance_registration/production_mode';
    public const LICENSE_KEYS = 'instance_registration/license_registration';

    public const NOTIFICATIONS_FREQUENCY = 'notifications/frequency';
    public const NOTIFICATIONS_TYPE = 'notifications/type';
    public const NOTIFICATIONS_ADS_ENABLE = 'notifications/ads_enable';
    public const LICENCE_SERVICE_API_URL = 'licence_service/api_url';

    public const AMASTY_MENU_ENABLE = 'menu/enable';

    public const UPDATE_FREQUENCY = 60 * 60 * 24;
    public const REMOVE_EXPIRED_FREQUENCY = 60 * 60 * 6; //4 times per day

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitableConfig,
        Serializer $serializer = null
    ) {
        parent::__construct($scopeConfig);
        $this->configWriter = $configWriter;
        $this->reinitableConfig = $reinitableConfig;
        $this->serializer = $serializer ?? ObjectManager::getInstance()->get(Serializer::class);
    }

    public function isProduction(): ?bool
    {
        $result = $this->getValue(self::IS_PRODUCTION);

        return $result === null ? $result : (bool)$result;
    }

    public function getLicenseKeys(): array
    {
        $value = (string)$this->getValue(self::LICENSE_KEYS);

        $result = [];
        foreach ((array)$this->serializer->unserialize($value) as $record) {
            $result[] = $record['license_key'] ?? '';
        }

        return array_filter(array_map('trim', $result));
    }

    public function getEnabledNotificationTypes(): array
    {
        $value = $this->getValue(self::NOTIFICATIONS_TYPE);

        return empty($value)
            ? []
            : explode(',', $value);
    }

    public function isAdsEnabled(): bool
    {
        return (bool)$this->getValue(self::NOTIFICATIONS_ADS_ENABLE);
    }

    public function isAmastyMenuEnabled(): bool
    {
        return (bool)$this->getValue(self::AMASTY_MENU_ENABLE);
    }

    public function getLicenceServiceApiUrl(): string
    {
        return (string)$this->getValue(self::LICENCE_SERVICE_API_URL);
    }

    public function getCurrentFrequencyValue(): int
    {
        return $this->getValue(self::NOTIFICATIONS_FREQUENCY);
    }

    public function getFrequencyInSec(): int
    {
        return $this->getCurrentFrequencyValue() * self::UPDATE_FREQUENCY;
    }

    /**
     * Used for updating frequency from the Notification grid
     * @see \Amasty\Base\Plugin\AdminNotification\Block\Grid\Renderer\Actions
     *
     * @param int $value
     *
     * @return void
     */
    public function changeFrequency(int $value): void
    {
        $this->configWriter->save($this->pathPrefix . self::NOTIFICATIONS_FREQUENCY, $value);
        $this->reinitableConfig->reinit();
        $this->clean();
    }
}
