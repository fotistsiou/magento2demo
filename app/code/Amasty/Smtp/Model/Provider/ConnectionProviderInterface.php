<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model\Provider;

interface ConnectionProviderInterface
{
    public function getConnection(\Laminas\Mail\Transport\SmtpOptions $options): \Laminas\Mail\Protocol\Smtp;
}
