<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\FlagRepository;

class CacheStorage
{
    public const PREFIX = 'amasty_base_';

    /**
     * @var FlagRepository
     */
    private $flagRepository;

    public function __construct(FlagRepository $flagRepository)
    {
        $this->flagRepository = $flagRepository;
    }

    public function get(string $identifier): ?string
    {
        return $this->flagRepository->get(self::PREFIX . $identifier);
    }

    public function set(string $identifier, string $value): bool
    {
        $this->flagRepository->save(self::PREFIX . $identifier, $value);

        return true;
    }
}
