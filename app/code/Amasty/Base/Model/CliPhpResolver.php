<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Shell;
use Symfony\Component\Process\PhpExecutableFinder;

class CliPhpResolver
{
    private const ENV_PHP_EXECUTABLE_PATH = 'php_executable_path';
    private const CONFIG_PHP_EXECUTABLE_PATH = 'amasty_base/system/cli_php_path';

    public const VERSION_CHECK_REGEXP = '/PHP [\d\.\+a-z-]+ \(cli\)/';

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var PhpExecutableFinder
     */
    private $executableFinder;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var ScopeConfigInterface
     */
    private $configProvider;

    /**
     * @var string
     */
    private $executablePath;

    public function __construct(
        DeploymentConfig $deploymentConfig,
        ScopeConfigInterface $configProvider,
        PhpExecutableFinder $executableFinder,
        Shell $shell
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->executableFinder = $executableFinder;
        $this->shell = $shell;
        $this->configProvider = $configProvider;
    }

    /**
     * Return Cli PHP executable path.
     * Assumed that this executable will be executed through `exec` function
     *
     * @return string
     */
    public function getExecutablePath(): string
    {
        if (!$this->executablePath) {
            $this->executablePath = $this->resolvePhpExecutable();
        }

        return $this->executablePath;
    }

    private function resolvePhpExecutable()
    {
        $pathCandidates = [
            $this->configProvider->getValue(self::CONFIG_PHP_EXECUTABLE_PATH),
            $this->deploymentConfig->get(self::ENV_PHP_EXECUTABLE_PATH),
            $this->executableFinder->find()
        ];

        foreach ($pathCandidates as $path) {
            if ($path && $this->isExecutable($path)) {
                return $path;
            }
        }

        return 'php';
    }

    private function isExecutable($path): bool
    {
        $disabledFunctions = $this->getDisabledPhpFunctions();
        if (in_array('exec', $disabledFunctions)) {
            throw new \RuntimeException(
                (string)__(
                    'The PHP function exec is disabled.'
                    . ' Please contact your system administrator or your hosting provider.'
                )
            );
        }

        try {
            $versionResult = (string)$this->shell->execute($path . ' %s', ['--version']);
        } catch (\Exception $e) {
            return false;
        }

        return (bool)preg_match(self::VERSION_CHECK_REGEXP, $versionResult);
    }

    private function getDisabledPhpFunctions(): array
    {
        return explode(',', str_replace(' ', ',', ini_get('disable_functions')));
    }
}
