<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Setup\Patch\Data;

use Amasty\Base\Model\Feed\FeedTypes\Extensions;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class RefreshFeedData implements DataPatchInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Extensions
     */
    private $extensionsFeed;

    public function __construct(
        State $appState,
        LoggerInterface $logger,
        Extensions $extensionsFeed
    ) {
        $this->logger = $logger;
        $this->appState = $appState;
        $this->extensionsFeed = $extensionsFeed;
    }

    public function apply()
    {
        $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'refreshFeedData'],
            []
        );

        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function refreshFeedData(): void
    {
        try {
            $this->extensionsFeed->getFeed();
        } catch (\Exception $ex) {
            $this->logger->critical($ex);
        }
    }
}
