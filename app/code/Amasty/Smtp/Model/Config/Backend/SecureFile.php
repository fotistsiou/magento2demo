<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Config\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;

class SecureFile extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );
        $this->jsonSerializer = $jsonSerializer;
        $this->encryptor = $encryptor;
    }

    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;

        if ($fileData = $this->getFileData()) {
            $this->_dataSaveAllowed = true;
            $tmpDirectory = $this->_filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
            $jsonCredentials = $tmpDirectory->readFile($fileData['tmp_name']);
            $encrypted = $this->encryptor->encrypt($jsonCredentials);
            $this->setValue($encrypted);
            $tmpDirectory->delete($fileData['tmp_name']);
        }

        return $this;
    }
}
