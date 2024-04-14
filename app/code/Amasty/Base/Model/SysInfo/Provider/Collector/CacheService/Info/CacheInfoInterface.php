<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Provider\Collector\CacheService\Info;

interface CacheInfoInterface
{
    public function getName(): ?string;
    public function setName(string $name): void;
    public function getStatus(): ?string;
    public function setStatus(string $status): void;
    public function getAdditionalInfo(): ?string;
    public function setAdditionalInfo(string $additionalInfo): void;
}
