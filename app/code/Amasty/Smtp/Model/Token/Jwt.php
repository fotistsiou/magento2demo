<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Token;

use Amasty\Smtp\Model\Serialize\Serializer\Base64UrlEncoder;
use Magento\Framework\Serialize\Serializer\Json;

class Jwt
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var Base64UrlEncoder
     */
    private $base64UrlEncoder;

    /**
     * @var array
     */
    private $header;

    /**
     * @var array
     */
    private $payload;

    /**
     * @var Closure|null
     */
    private $signClosure;

    public function __construct(
        Json $jsonSerializer,
        Base64UrlEncoder $base64UrlEncoder,
        array $header = [],
        array $payload = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->base64UrlEncoder = $base64UrlEncoder;
        $this->header = $header;
        $this->payload = $payload;
    }

    public function toString(): string
    {
        $base64UrlSignature = '';
        $base64UrlHeader = $this->base64UrlEncoder->execute(
            $this->jsonSerializer->serialize($this->getHeader())
        );
        $base64UrlPayload = $this->base64UrlEncoder->execute(
            $this->jsonSerializer->serialize($this->getPayload())
        );

        if (is_callable($this->signClosure)) {
            $signature = ($this->signClosure)($base64UrlHeader . '.' . $base64UrlPayload);
            $base64UrlSignature = $this->base64UrlEncoder->execute($signature);
        }

        return sprintf('%s.%s.%s', $base64UrlHeader, $base64UrlPayload, $base64UrlSignature);
    }

    public function setSignClosure(callable $closure)
    {
        $this->signClosure = $closure;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(array $header)
    {
        $this->header = $header;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }
}
