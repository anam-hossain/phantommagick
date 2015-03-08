<?php

use Anam\Html2PdfConverter\Converter;

class ConverterTest extends PHPUnit_Framework_TestCase
{
    protected $converter;

    public function setUp()
    {
        $this->converter = new Converter('http://code-chunk.com');
    }

    public function testSetSource()
    {
        $this->converter->setSource('http://google.com');

        $this->assertEquals('http://google.com', $this->converter->getSource());
    }

    public function testGetSource()
    {
        $this->assertEquals('http://code-chunk.com', $this->converter->getSource());
    }

    public function testSource()
    {
        $this->converter->setSource('http://yahoo.com');

        $this->assertEquals('http://yahoo.com', $this->converter->getSource());
    }
}
