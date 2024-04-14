<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Data\LicenseValidation;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\VerifyStatus;
use Magento\Framework\Api\ExtensibleDataInterface;

class Module extends SimpleDataObject implements ExtensibleDataInterface
{
    public const INSTANCE_KEY = 'instance_key';
    public const CODE = 'code';
    public const VERSION = 'version';
    public const VERIFY_STATUS = 'verify_status';
    public const MESSAGES = 'messages';

    /**
     * @return string
     */
    public function getInstanceKey(): string
    {
        return (string)$this->getData(self::INSTANCE_KEY);
    }

    /**
     * @param string|null $instanceKey
     * @return void
     */
    public function setInstanceKey(?string $instanceKey): void
    {
        $this->setData(self::INSTANCE_KEY, $instanceKey);
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this->getData(self::CODE);
    }

    /**
     * @param string $code
     * @return void
     */
    public function setCode(string $code): void
    {
        $this->setData(self::CODE, $code);
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return (string)$this->getData(self::VERSION);
    }

    /**
     * @param string $version
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->setData(self::VERSION, $version);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\VerifyStatus|null
     */
    public function getVerifyStatus(): ?VerifyStatus
    {
        return $this->getData(self::VERIFY_STATUS);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\VerifyStatus $verifyStatus
     * @return void
     */
    public function setVerifyStatus(VerifyStatus $verifyStatus): void
    {
        $this->setData(self::VERIFY_STATUS, $verifyStatus);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\Message[]
     */
    public function getMessages(): array
    {
        return (array)$this->getData(self::MESSAGES);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\Message[] $messages
     * @return void
     */
    public function setMessages(array $messages): void
    {
        $this->setData(self::MESSAGES, $messages);
    }
}
