<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\SysInfo\Formatter;

use Magento\Framework\Xml\Generator as XmlGenerator;

class Xml implements FormatterInterface
{
    public const FILE_EXTENSION = 'xml';

    /**
     * @var XmlGenerator
     */
    private $xmlGenerator;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $rootNodeName;

    public function __construct(
        XmlGenerator $xmlGenerator,
        array $data,
        string $rootNodeName
    ) {
        $this->xmlGenerator = $xmlGenerator;
        $this->data = $data;
        $this->rootNodeName = $rootNodeName;
    }

    public function getContent(): string
    {
        $content = $this->xmlGenerator
            ->arrayToXml([$this->rootNodeName => $this->data])
            ->getDom()
            ->saveXML();

        return $content;
    }

    public function getExtension(): string
    {
        return self::FILE_EXTENSION;
    }
}
