<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Config\Source;

use Amasty\Smtp\Model\Provider\Config;
use Magento\Framework\Data\OptionSourceInterface;

class Providers implements OptionSourceInterface
{
    public const AUTH_TYPE_NONE    = '';
    public const AUTH_TYPE_LOGIN   = 'login';

    /**
     * @var Config
     */
    protected $providersConfig;

    /**
     * @param Config $providersConfig
     */
    public function __construct(Config $providersConfig)
    {
        $this->providersConfig = $providersConfig;
    }

    public function toOptionArray()
    {
        $providers = $this->providersConfig->get();

        $providersArr = array_column($providers, 'title');
        asort($providersArr);
        $resultArr = [];

        foreach ($providersArr as $key => $val) {
            $resultArr[] = [
                'value' => $key,
                'label' => __($val)
            ];
        }

        return $resultArr;
    }
}
