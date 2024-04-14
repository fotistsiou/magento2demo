<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model;

use Magento\Framework\Flag;
use Magento\Framework\Flag\FlagResource;
use Magento\Framework\FlagFactory;

class FlagRepository
{
    /**
     * @var FlagResource
     */
    private $flagResource;

    /**
     * @var FlagFactory
     */
    private $flagFactory;

    public function __construct(
        FlagResource $flagResource,
        FlagFactory $flagFactory
    ) {
        $this->flagResource = $flagResource;
        $this->flagFactory = $flagFactory;
    }

    public function get(string $code): ?string
    {
        return $this->getFlagObject($code)->getFlagData();
    }

    public function save(string $code, string $value): bool
    {
        $flag = $this->getFlagObject($code);
        $flag->setFlagData($value);
        $this->flagResource->save($flag);

        return true;
    }

    private function getFlagObject(string $code): Flag
    {
        $flagModel = $this->flagFactory->create(['data' => ['flag_code' => $code]]);
        $this->flagResource->load(
            $flagModel,
            $code,
            'flag_code'
        );

        return $flagModel;
    }
}
