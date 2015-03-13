<?php
namespace Anam\Html2PdfConverter\Test;

use Exception;
use RuntimeException;
use Mockery;
use Anam\Html2PdfConverter\Converter;
use League\Flysystem\Filesystem;
use Anam\Html2PdfConverter\Adapter;
use Anam\Html2PdfConverter\Exception\FileFormatNotSupportedException;
use Aws\S3\S3Client;

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

    public function testDefaultFileSystemDriverIsLocal()
    {
        $this->assertEquals('local', $this->converter->getDriver());
    }

    public function testFileSystemDriverIsS3WhenS3ClientIsUsedAsClient()
    {
        $client = S3Client::factory(array(
            'key'    => 'dummy-key-123',
            'secret' => 'dummy-secret-123'
        ));

        $this->converter->adapter($client, 'dummy-bucket');

        $this->assertEquals('s3', $this->converter->getDriver());
    }

    public function testGetTempFilePath()
    {
        $path = '/dummy/file/path';

        $this->converter->setTempFilePath($path);

        $this->assertEquals($path, $this->converter->getTempFilePath());
    }

    public function testPdfOptions()
    {
        $options = [
            'format'        => 'A3',
            'zoomfactor'    => 2,
            'quality'       => '100',
            'orientation'   => 'landscape',
            'margin'        => '2cm'

        ];

        $this->converter->pdfOptions($options);

        $this->assertEquals($options, $this->converter->getPdfOptions());
    }

    public function testImageOptions()
    {
        $options = [
            'dimension'     => '900px',
            'zoomfactor'    => 1,
        ];

        $this->converter->imageOptions($options);

        $this->assertArrayHasKey('quality', $this->converter->getImageOptions());
    }

    public function testContentType()
    {
        $mime = $this->converter->contentType('pdf');

        $this->assertEquals('application/pdf', $mime);
    }

    public function testPagesIsEmpty()
    {
        $this->assertTrue(empty($this->converter->getPages()));
    }

    public function testPagesIsNotEmpty()
    {
        $this->converter->addPage('http://google.com');

        $this->assertFalse(empty($this->converter->getPages()));
    }

}
