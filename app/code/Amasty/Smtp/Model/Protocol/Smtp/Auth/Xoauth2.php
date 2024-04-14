<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Protocol\Smtp\Auth;

use Laminas\Mail\Protocol\Smtp;
use Magento\Framework\Exception\MailException;

class Xoauth2 extends Smtp
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $username;

    public function auth()
    {
        // Ensure AUTH has not already been initiated.
        parent::auth();

        try {
            $this->_send('AUTH XOAUTH2 ' . $this->getEncodedAuthPart());
            $this->_expect(235);
            $this->auth = true;
        } catch (\Exception $e) {
            $this->_send(Smtp::EOL);
            try {
                $this->_expect(535);
            } catch (\Exception $exception) { // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
                // do nothing, avoid to exception tree because new exception make original as previous
            }

            throw new MailException(__('Unable to authenticate: %1', $e->getMessage()));
        }
    }

    public function getEncodedAuthPart()
    {
        return base64_encode(
            'user=' .
            $this->getUsername() .
            "\001auth=Bearer " .
            $this->getAccessToken() .
            "\001\001"
        );
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }
}
