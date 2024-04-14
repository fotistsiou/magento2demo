<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Controller\Adminhtml\SysInfo;

use Amasty\Base\Model\SysInfo\Command\SysInfoService\Download as DownloadCommand;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;

class Download extends Action
{
    public const FILE_NAME = 'system_information';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var DownloadCommand
     */
    private $downloadCommand;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        FileFactory $fileFactory,
        DownloadCommand $downloadCommand,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->downloadCommand = $downloadCommand;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        try {
            $xml = $this->downloadCommand->execute();

            $tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
            $filePath = self::FILE_NAME . uniqid() . '.' . $xml->getExtension();
            $tmpDir->writeFile($filePath, $xml->getContent());

            return $this->fileFactory->create(
                sprintf('%s.%s', $this->getHost(), $xml->getExtension()),
                [
                    'type' => 'filename',
                    'value' => $filePath,
                    'rm' => true
                ],
                DirectoryList::TMP
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererUrl();

        return $resultRedirect;
    }

    private function getHost(): string
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        return parse_url($this->storeManager->getStore()->getBaseUrl(), PHP_URL_HOST);
    }
}
