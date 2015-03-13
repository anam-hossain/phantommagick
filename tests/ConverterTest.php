<?php
namespace Anam\Html2PdfConverter\Test;

use Exception;
use RuntimeException;
use Anam\Html2PdfConverter\Converter;
use League\Flysystem\Filesystem;
use Anam\Html2PdfConverter\Adapter;
use Anam\Html2PdfConverter\Exception\FileFormatNotSupportedException;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    use PrivateAndProtectedMethodsAccessibleTrait;

    protected $converter;

    public function setUp()
    {
        $this->converter = new Converter();
    }

    public function testInitialize()
    {
        $this->invokeMethod($this->converter, 'initialize');

        $this->assertContains('phantom_magick.js', $this->converter->getScript(), 'Verify that phantom_magick.js is used');
    }


    public function testMake()
    {
        $this->assertInstanceOf('Anam\Html2PdfConverter\Converter', Converter::make('http://code-chunk.com'), 'Check Converter::make() method returning the instance of \Anam\Html2PdfConverter\Converter');
    }

    public function testSetSource()
    {
        $this->converter->setSource('http://code-chunk.com');

        $this->assertEquals('http://code-chunk.com', $this->converter->getSource());
    }

    public function testGetSource()
    {
        $this->converter->setSource('http://code-chunk.com');
        $this->assertEquals('http://code-chunk.com', $this->converter->getSource());
    }

    public function testSource()
    {
        $this->converter->source('http://code-chunk.com');

        $this->assertEquals('http://code-chunk.com', $this->converter->getSource());
    }
}
