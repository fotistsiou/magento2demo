<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Response\File;

use Amasty\Base\Model\MagentoVersion;
use Amasty\Base\Model\Response\AbstractOctetResponse;
use Amasty\Base\Model\Response\DownloadOutput;
use Magento\Framework\App;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\File\ReadInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Stdlib;

class FileOctetResponse extends AbstractOctetResponse
{
    /**
     * @var ReadFactory
     */
    private $fileReadFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ReadFactory $fileReadFactory,
        DownloadOutput $downloadHelper,
        MagentoVersion $magentoVersion,
        App\Request\Http $request,
        Stdlib\CookieManagerInterface $cookieManager,
        Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        App\Http\Context $context,
        Stdlib\DateTime $dateTime,
        ConfigInterface $sessionConfig = null,
        Filesystem $filesystem = null
    ) {
        $this->fileReadFactory = $fileReadFactory;
        $this->filesystem = $filesystem ?? ObjectManager::getInstance()->get(Filesystem::class);

        parent::__construct(
            $downloadHelper,
            $magentoVersion,
            $request,
            $cookieManager,
            $cookieMetadataFactory,
            $context,
            $dateTime,
            $sessionConfig
        );
    }

    public function getReadResourceByPath(string $readResourcePath): ReadInterface
    {
        $driver = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getDriver();

        return $this->fileReadFactory->create($readResourcePath, $driver);
    }
}
