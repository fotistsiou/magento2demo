<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Di;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerMetaProvider;
use Magento\Framework\ObjectManagerInterface;

class Wrapper
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isShared;

    /**
     * @var bool
     */
    private $isProxy;

    /**
     * @var object
     */
    private $subject;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerMetaProvider
     */
    private $diMetaProvider;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager,
        ObjectManagerMetaProvider $diMetaProvider,
        ?string $name = '',
        ?bool $isShared = false,
        ?bool $isProxy = false
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->diMetaProvider = $diMetaProvider;
        $this->name = $name;
        $this->isShared = $isShared;
        $this->isProxy = $isProxy;
    }

    public function __call(string $method, array $arguments)
    {
        $result = false;

        if ($this->canCreateObject()) {
            $subject = $this->getSubject();
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $result = call_user_func_array([$subject, $method], $arguments);
        }

        return $result;
    }

    private function getSubject(): object
    {
        if ($this->isProxy && $this->subject) {
            return $this->subject;
        }

        if ($this->isShared) {
            $subject = $this->objectManager->get($this->name);
        } else {
            $subject = $this->objectManager->create($this->name);
        }

        if ($this->isProxy) {
            $this->subject = $subject;
        }

        return $subject;
    }

    private function canCreateObject(): bool
    {
        $canAutoload = (class_exists($this->name) || interface_exists($this->name))
            && $this->moduleManager->isEnabled($this->getModuleName());
        $canGetObjectByDI = $this->isVirtualType();

        return $this->name && ($canAutoload || $canGetObjectByDI);
    }

    private function getModuleName(): string
    {
        $class = ltrim($this->name, '\\');
        $parts = preg_split('@[\\\_]@', $class);
        $parts = array_filter($parts);

        if (count($parts) < 2) {
            throw new \InvalidArgumentException(
                (string) __('Provided argument is not in PSR-0 or underscore notation.')
            );
        }

        return sprintf(
            '%1s_%2s',
            ucfirst($parts[0]),
            ucfirst($parts[1])
        );
    }

    private function isVirtualType(): bool
    {
        $instanceType = $this->diMetaProvider->getInstanceType($this->name);

        return $instanceType !== $this->name;
    }
}
