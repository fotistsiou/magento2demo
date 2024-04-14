<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token\Provider;

use Amasty\Smtp\Model\Token\Generator\AuthTokenGeneratorInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class AccessTokenProvider implements AuthTokenProviderInterface
{
    /**
     * We are using lifetime gap to prevent token invalidation because of computing time gap
     * between receiving access token and saving it to the cache storage.
     */
    public const LIFETIME_GAP = 100;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var AuthTokenGeneratorInterface
     */
    private $accessTokenGenerator;

    /**
     * @var string
     */
    private $connectionProviderName;

    public function __construct(
        CacheInterface $cache,
        EncryptorInterface $encryptor,
        AuthTokenGeneratorInterface $accessTokenGenerator,
        string $connectionProviderName
    ) {
        $this->cache = $cache;
        $this->encryptor = $encryptor;
        $this->accessTokenGenerator = $accessTokenGenerator;
        $this->connectionProviderName = $connectionProviderName;
    }

    public function get(): string
    {
        $cacheName = $this->getCacheName();
        if ($encryptedToken = $this->cache->load($cacheName)) {
            return $this->encryptor->decrypt($encryptedToken);
        }

        $token = $this->accessTokenGenerator->generateToken();
        $this->cache->save(
            $this->encryptor->encrypt($token),
            $cacheName,
            [],
            AuthTokenGeneratorInterface::TOKEN_LIFETIME - self::LIFETIME_GAP
        );

        return $token;
    }

    public function delete(): void
    {
        $this->cache->remove(
            $this->getCacheName()
        );
    }

    private function getCacheName(): string
    {
        return sprintf('smtp_%s_access_token', $this->connectionProviderName);
    }
}
