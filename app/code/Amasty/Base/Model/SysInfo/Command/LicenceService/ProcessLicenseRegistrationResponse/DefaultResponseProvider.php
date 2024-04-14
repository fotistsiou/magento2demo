<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SimpleDataObjectFactory;

class DefaultResponseProvider
{
    /**
     * @var SimpleDataObjectFactory
     */
    private $simpleDataObjectFactory;

    public function __construct(
        SimpleDataObjectFactory $simpleDataObjectFactory
    ) {
        $this->simpleDataObjectFactory = $simpleDataObjectFactory;
    }

    public function getServiceUnavailable(): SimpleDataObject
    {
        $responseArray = [
            'messages' => [
                [
                    'type' => 'error',
                    'content' => 'The service is currently unavailable. '
                        . 'Please try again later or contact our support team.'
                ]
            ]
        ];

        return $this->simpleDataObjectFactory->create(['data' => $responseArray]);
    }

    public function getVerificationProcessError(): SimpleDataObject
    {
        $responseArray = [
            'code' => '418',
            'messages' => [
                [
                    'type' => 'error',
                    'content' => 'We were unable to verify your product licenses for a certain period. '
                        . 'Kindly ensure that the verification process or cron job is enabled.'
                ]
            ]
        ];

        return $this->simpleDataObjectFactory->create(['data' => $responseArray]);
    }
}
