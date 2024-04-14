<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Test\Unit\Model\SysInfo\Formatter;

use Amasty\Base\Model\SysInfo\Formatter\Xml;
use Magento\Framework\Xml\Generator as XmlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @var Xml
     */
    private $model;

    /**
     * @var XmlGenerator|MockObject
     */
    private $xmlGeneratorMock;

    protected function setUp(): void
    {
        $this->xmlGeneratorMock = $this->createMock(XmlGenerator::class);
    }

    public function testGetContent(): void
    {
        $data = [];
        $rootNodeName = 'node';
        $content = 'content';
        $this->model = new Xml($this->xmlGeneratorMock, $data, $rootNodeName);
        $domDocument = $this->createMock(\DOMDocument::class);

        $this->xmlGeneratorMock
            ->expects($this->once())
            ->method('arrayToXml')
            ->willReturnSelf();
        $this->xmlGeneratorMock
            ->expects($this->once())
            ->method('arrayToXml')
            ->willReturnSelf();
        $this->xmlGeneratorMock
            ->expects($this->once())
            ->method('getDom')
            ->willReturn($domDocument);
        $domDocument
            ->expects($this->once())
            ->method('saveXML')
            ->willReturn($content);

        $this->assertEquals($content, $this->model->getContent());
    }
}
