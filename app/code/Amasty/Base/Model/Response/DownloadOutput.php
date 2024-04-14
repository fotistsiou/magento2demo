<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Response;

use Magento\Downloadable\Helper\Download;
use Magento\Framework\Filesystem\File\ReadInterface;

class DownloadOutput extends Download
{
    /**
     * @var ReadInterface|null
     */
    private $resourceHandler;

    public function setResourceHandler(ReadInterface $readResource): self
    {
        $this->resourceHandler = $readResource;

        return $this;
    }

    protected function _getHandle(): ?ReadInterface
    {
        return $this->resourceHandler;
    }
}
