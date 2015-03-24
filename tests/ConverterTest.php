<?php
namespace Anam\PhantomMagick\Test;

use Exception;
use RuntimeException;
use Mockery;
use Anam\PhantomMagick\Converter;
use League\Flysystem\Filesystem;
use Anam\PhantomMagick\Adapter;
use Anam\PhantomMagick\Exception\FileFormatNotSupportedException;
use Aws\S3\S3Client;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    use PrivateAndProtectedMethodsAccessibleTrait;

    protected $converter;

    protected $pdfOptions = [
        'format'        => 'A3',
        'zoomfactor'    => 2,
        'quality'       => '100',
        'orientation'   => 'landscape',
        'margin'        => '2cm'

    ];

    protected $imageOptions = [
        'dimension'     => '1000px',
        'zoomfactor'    => 2,
        'quality'       => '90'
    ];

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
        $this->assertInstanceOf('Anam\PhantomMagick\Converter', Converter::make('http://code-chunk.com'), 'Check Converter::make() method returning the instance of \Anam\PhantomMagick\Converter');
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
        $this->converter->pdfOptions($this->pdfOptions);

        $this->assertEquals($this->pdfOptions, $this->converter->getPdfOptions());
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

    public function testPushContents()
    {
        $pages = ['<html><body><h1>Phantom magick</h1></body></html>'];

        $this->converter->pushContent($pages[0]);

        $this->assertEquals($pages, $this->converter->getPages());
    }

    public function testPageBreak()
    {
        $pages = [
            '<html><body><h1>Phantom magick</h1></body></html>',
            '<div style="page-break-after:always;"><!-- page break --></div>'
        ];

        $this->converter->pushContent($pages[0]);

        $this->converter->pageBreak();

        $this->assertEquals($pages, $this->converter->getPages());
    }

    public function testAddPages()
    {
        $expected = [
            '<html><body><h1>Page 1</h1></body></html>',
            '<div style="page-break-after:always;"><!-- page break --></div>',
            '<html><body><h1>Page 2</h1></body></html>',
        ];

        $pages = [
            '<html><body><h1>Page 1</h1></body></html>',
            '<html><body><h1>Page 2</h1></body></html>'
        ];

        $this->converter->addPages($pages);

        $this->assertEquals($expected, $this->converter->getPages());
    }

    public function testToPdf()
    {
        $this->converter->toPdf($this->pdfOptions);

        // Check pdf options is set properly
        $this->assertEquals($this->pdfOptions, $this->converter->getPdfOptions());

        // Check .pdf extension is set
        $this->assertContains('.pdf', $this->converter->getTempFilePath());

    }

    public function testToPng()
    {
        $this->converter->toPng($this->imageOptions);

        $this->assertEquals($this->imageOptions, $this->converter->getImageOptions());

        // Check .png extension is set
        $this->assertContains('.png', $this->converter->getTempFilePath());

    }

    public function testToJpg()
    {
        $this->converter->toJpg($this->imageOptions);

        $this->assertEquals($this->imageOptions, $this->converter->getImageOptions());

        // Check .png extension is set
        $this->assertContains('.jpg', $this->converter->getTempFilePath());

    }

    public function testToGif()
    {
        $this->converter->toGif($this->imageOptions);

        $this->assertEquals($this->imageOptions, $this->converter->getImageOptions());

        // Check .png extension is set
        $this->assertContains('.gif', $this->converter->getTempFilePath());

    }

}
