<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Block\Adminhtml;

use Amasty\Base\Model\AmastyMenu\ActiveSolutionsProvider;
use Amasty\Base\Model\AmastyMenu\ModuleTitlesResolver;
use Amasty\Base\Model\Feed\ExtensionsProvider;
use Amasty\Base\Model\ModuleInfoProvider;
use Amasty\Base\Model\ModuleListProcessor;
use Amasty\Base\Model\SysInfo\Command\LicenceService\GetCurrentLicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\Message;
use Amasty\Base\Model\SysInfo\Data\LicenseValidation\Module\VerifyStatus;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Serialize\Serializer\Json;

class Extensions extends Field
{
    public const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=ext_list';

    /**
     * Constants for keys of data array
     */
    public const CODE = 'code';
    public const NAME = 'name';
    public const VERSION = 'version';
    public const LAST_VERSION = 'last_version';
    public const HAS_UPDATE = 'has_update';
    public const UPDATE_URL = 'update_url';
    public const IS_SOLUTION = 'is_solution';
    public const PLAN_LABEL = 'plan_label';
    public const UPGRADE_URL = 'upgrade_url';
    public const VERIFY_STATUS = 'verify_status';
    public const MESSAGES = 'messages';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Base::modules.phtml';

    /**
     * @var ModuleListProcessor
     */
    private $moduleListProcessor;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var ModuleTitlesResolver
     */
    private $moduleTitlesResolver;

    /**
     * @var ActiveSolutionsProvider
     */
    private $activeSolutionsProvider;

    /**
     * @var GetCurrentLicenseValidation
     */
    private $currentLicenseValidation;

    public function __construct(
        Template\Context $context,
        ModuleListProcessor $moduleListProcessor,
        ModuleInfoProvider $moduleInfoProvider,
        ExtensionsProvider $extensionsProvider,
        Json $serializer,
        ModuleTitlesResolver $moduleTitlesResolver,
        ActiveSolutionsProvider $activeSolutionsProvider,
        array $data = [],
        GetCurrentLicenseValidation $currentLicenseValidation = null
    ) {
        parent::__construct($context, $data);
        $this->moduleListProcessor = $moduleListProcessor;
        $this->moduleInfoProvider = $moduleInfoProvider;
        $this->extensionsProvider = $extensionsProvider;
        $this->serializer = $serializer;
        $this->moduleTitlesResolver = $moduleTitlesResolver;
        $this->activeSolutionsProvider = $activeSolutionsProvider;
        $this->currentLicenseValidation = $currentLicenseValidation
            ?: ObjectManager::getInstance()->get(GetCurrentLicenseValidation::class);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }

    public function getModulesDataJson(): string
    {
        return $this->serializer->serialize($this->getModulesData());
    }

    public function isOriginMarketplace(): bool
    {
        return $this->moduleInfoProvider->isOriginMarketplace();
    }

    public function getSeoparams(): string
    {
        return !$this->isOriginMarketplace() ? self::SEO_PARAMS : '';
    }

    private function getModulesData(): array
    {
        $result = [];

        $modulesList = array_merge(...array_values($this->moduleListProcessor->getModuleList()));
        $allSolutions = $this->extensionsProvider->getAllSolutionsData();
        $activeSolutions = $this->activeSolutionsProvider->get();

        foreach ($modulesList as $module) {
            $moduleCode = $module['code'];
            $isSolution = isset($allSolutions[$moduleCode]);
            if ($isSolution && !isset($activeSolutions[$moduleCode])) {
                continue;
            }
            $planLabel = $isSolution ? $activeSolutions[$moduleCode]['solution_version'] : '';
            $item = [];
            $item[self::CODE] = $moduleCode;
            $item[self::NAME] = $this->moduleTitlesResolver->trimTitle(
                htmlspecialchars_decode($module['description']),
                $planLabel
            );
            $item[self::VERSION] = $module['version'];
            $item[self::LAST_VERSION] = $module['lastVersion'];
            $item[self::HAS_UPDATE] = $module['hasUpdate'];
            $item[self::UPDATE_URL] = $this->prepareUpdateUrl($module['url']);
            $item[self::IS_SOLUTION] = $isSolution;
            $item[self::PLAN_LABEL] = $planLabel;
            $item[self::UPGRADE_URL] = $isSolution ? $activeSolutions[$moduleCode]['upgrade_url'] : '';

            $result[] = $item;
        }
        $this->processLicenseValidationModulesStatus($result);
        usort($result, static function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return $result;
    }

    private function prepareUpdateUrl(string $moduleUrl): string
    {
        $seo = !$this->isOriginMarketplace() ? self::SEO_PARAMS : '';
        $anchor = $this->isOriginMarketplace() ? '#product.info.details.release_notes' : '#changelog';

        return $moduleUrl . $seo . $anchor;
    }

    private function processLicenseValidationModulesStatus(array &$modules): void
    {
        $licenseValidation = $this->currentLicenseValidation->get();
        if ($licenseValidation->isNeedCheckLicense() === false) {
            return;
        }

        $licenseValidationModules = $this->getLicenseModulesSortedByCode($licenseValidation);
        foreach ($modules as &$moduleData) {
            $licenseModule = $licenseValidationModules[$moduleData[self::CODE]] ?? null;
            if (!$licenseModule || !$licenseModule->getVerifyStatus()) {
                $moduleData[self::VERIFY_STATUS] = [
                    VerifyStatus::TYPE => 'pending',
                    VerifyStatus::STATUS => 'Pending Verification'
                ];
                $moduleData[self::MESSAGES][] = [
                    Message::TYPE => 'pending',
                    Message::CONTENT => 'Please verify the product'
                ];
            } else {
                $verifyStatus = $licenseModule->getVerifyStatus();
                $moduleData[self::VERIFY_STATUS] = [
                    VerifyStatus::TYPE => $verifyStatus ? $verifyStatus->getType() : 'pending',
                    VerifyStatus::STATUS => $verifyStatus ? $verifyStatus->getStatus() : 'Pending Verification'
                ];
                foreach ($licenseModule->getMessages() as $message) {
                    $moduleData[self::MESSAGES][] = [
                        Message::TYPE => $message->getType(),
                        Message::CONTENT => $message->getContent()
                    ];
                }
            }
        }
    }

    /**
     * @param LicenseValidation $licenseValidation
     * @return Module[]
     */
    private function getLicenseModulesSortedByCode(LicenseValidation $licenseValidation): array
    {
        $result = [];
        foreach ($licenseValidation->getModules() as $module) {
            $result[$module->getCode()] = $module;
        }

        return $result;
    }
}
