<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse;

use Amasty\Base\Model\SysInfo\Data\LicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidationFactory;
use Magento\Framework\Api\DataObjectHelper;

class Converter
{
    /**
     * @var LicenseValidationFactory
     */
    private $factory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        LicenseValidationFactory $factory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->factory = $factory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    public function convertArrayToEntity(array $data): LicenseValidation
    {
        $licenseValidation = $this->factory->create();
        if (!isset($data['is_need_check_license'])) {
            $data['is_need_check_license'] = true;
        }
        $this->dataObjectHelper->populateWithArray(
            $licenseValidation,
            $data,
            LicenseValidation::class
        );

        return $licenseValidation;
    }
}
