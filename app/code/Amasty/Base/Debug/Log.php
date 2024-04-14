<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Debug;

use Amasty\Base\Debug\System\AmastyFormatter;
use Amasty\Base\Debug\System\LogBeautifier;
use Magento\Framework\App\ObjectManager;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * For Remote Debug
 * Output is going to file amasty_debug.log
 * @codeCoverageIgnore
 * @codingStandardsIgnoreFile
 */
class Log
{
    /**
     * @var Logger
     */
    private static $loggerInstance;

    /**
     * @var string
     */
    private static $fileToLog = 'amasty_debug.log';

    public static function execute()
    {
        if (VarDump::isAllowed()) {
            foreach (func_get_args() as $var) {
                self::logToFile(
                    LogBeautifier::getInstance()->beautify(
                        VarDump::dump($var)
                    )
                );
            }
        }
    }

    /**
     * @param int $level
     */
    public static function setObjectDepthLevel($level)
    {
        VarDump::setObjectDepthLevel((int)$level);
    }

    /**
     * @param int $level
     */
    public static function setArrayDepthLevel($level)
    {
        VarDump::setArrayDepthLevel((int)$level);
    }

    /**
     * @param string $filename
     */
    public static function setLogFile($filename)
    {
        if (preg_match('/^[a-z_]+\.log$/i', $filename)) {
            self::$fileToLog = $filename;
        }
    }

    /**
     * Log debug_backtrace
     */
    public static function backtrace()
    {
        if (VarDump::isAllowed()) {
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            foreach ($backtrace as $key => $route) {
                $backtrace[$key] = [
                    'action' => $route['class'] . $route['type'] . $route['function'] . '()',
                    'file' => $route['file'] . ':' . $route['line']
                ];
            }
            self::logToFile(LogBeautifier::getInstance()->beautify(VarDump::dump($backtrace)));
        }
    }

    /**
     * @param string $var
     */
    private static function logToFile($var)
    {
        self::getLogger()->addRecord(200, $var);
    }

    /**
     * @return Logger
     */
    private static function getLogger()
    {
        if (!self::$loggerInstance) {
            self::configureInstance();
        }
        return self::$loggerInstance;
    }

    private static function configureInstance()
    {
        $logDir = ObjectManager::getInstance()
            ->get('\Magento\Framework\Filesystem\DirectoryList')
            ->getPath('log');
        $handler = new RotatingFileHandler($logDir . DIRECTORY_SEPARATOR . self::$fileToLog, 2);

        $output = "\n----------------------------------------------------------------------------\n%datetime%\n
%message%
----------------------------------------------------------------------------\n\n";
        $formatter = new AmastyFormatter($output);

        $handler->setFormatter($formatter);
        self::$loggerInstance = new Logger('amasty_logger', [$handler]);
    }
}
