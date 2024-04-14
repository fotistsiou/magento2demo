<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Provider;

class ConnectionProviderAdapter
{
    /**
     * @var ConnectionProviderInterface[]
     */
    private $connectionProviders;

    public function __construct(array $connectionProviders = [])
    {
        foreach ($connectionProviders as $provider) {
            if (!($provider instanceof ConnectionProviderInterface)) {
                throw new \LogicException(
                    sprintf('Connection provider must implement %s', ConnectionProviderInterface::class)
                );
            }
        }

        $this->connectionProviders = $connectionProviders;
    }

    public function get(string $type): ConnectionProviderInterface
    {
        if (!isset($this->connectionProviders[$type])) {
            throw new \LogicException("Connection provider for auth type '{$type}' is not defined.");
        }

        return $this->connectionProviders[$type];
    }
}
