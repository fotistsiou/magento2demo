<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\AmastyMenu;

use Amasty\Base\Model\Feed\ExtensionsProvider;
use Magento\Framework\Module\Manager;

class ActiveSolutionsProvider
{
    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $solutionsVersions;

    /**
     * @var array|null
     */
    private $activeSolutions = null;

    public function __construct(
        ExtensionsProvider $extensionsProvider,
        Manager $moduleManager,
        array $solutionsVersions = []
    ) {
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleManager = $moduleManager;
        ksort($solutionsVersions);
        $this->solutionsVersions = $solutionsVersions;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        if ($this->activeSolutions === null) {
            $result = [];

            $feedSolutions = $this->extensionsProvider->getAllSolutionsData();
            foreach ($feedSolutions as $solutionCode => $solutionData) {
                if ($this->moduleManager->isEnabled($solutionCode)) {
                    $result[$solutionCode] = $solutionData;
                }
            }
            $this->filterLowerVersions($result);
            $this->activeSolutions = $result;
        }

        return $this->activeSolutions;
    }

    /**
     * Temporary solution until all solutions are adjusted
     * to disable their lower version packages on their own.
     *
     * @param array $solutions
     * @return void
     */
    private function filterLowerVersions(array &$solutions): void
    {
        foreach ($solutions as $name => $data) {
            $solutionVersion = $data['solution_version'];
            $pos = strrpos($name, $solutionVersion);
            $solutionCode = $pos === false ? $name : substr($name, 0, $pos);

            if (($versionIndex = array_search($solutionVersion, array_values($this->solutionsVersions))) === false) {
                continue;
            }
            $versionCandidates = array_slice(
                $this->solutionsVersions,
                0,
                $versionIndex
            );
            foreach ($versionCandidates as $candidate) {
                $candidateSolutionData = $solutions[$solutionCode . $candidate] ?? [];
                if (isset($candidateSolutionData['solution_version'])
                    && $candidateSolutionData['solution_version'] === $candidate
                ) {
                    unset($solutions[$solutionCode . $candidate]);
                }
            }
        }
    }
}
