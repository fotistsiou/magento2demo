<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo;

use Amasty\Base\Model\FlagRepository;
use Amasty\Base\Model\Serializer;
use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseRegistrationResponse\Converter;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation;

class LicenseValidationRepository
{
    public const FLAG_KEY = 'amasty_base_license_validation_response';

    /**
     * @var FlagRepository
     */
    private $flagRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var LicenseValidation|null
     */
    private $loadedEntity;

    public function __construct(
        FlagRepository $flagRepository,
        Serializer $serializer,
        Converter $converter
    ) {
        $this->flagRepository = $flagRepository;
        $this->serializer = $serializer;
        $this->converter = $converter;
    }

    public function save(LicenseValidation $licenseValidation): void
    {
        $licenseResponseSerialized = $this->serializer->serialize($licenseValidation->toArray());
        $this->flagRepository->save(self::FLAG_KEY, $licenseResponseSerialized);
        $this->loadedEntity = $licenseValidation;
    }

    public function get(bool $reload = false): LicenseValidation
    {
        if ($reload) {
            $this->loadedEntity = null;
        }

        if (!$this->loadedEntity) {
            $licenseResponseSerialized = $this->flagRepository->get(self::FLAG_KEY);
            $licenseResponseArray = $licenseResponseSerialized
                ? $this->serializer->unserialize($licenseResponseSerialized)
                : [];
            $this->loadedEntity = $this->converter->convertArrayToEntity($licenseResponseArray);
        }

        return $this->loadedEntity;
    }
}
