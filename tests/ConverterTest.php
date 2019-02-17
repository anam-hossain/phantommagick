<?php

namespace iBrand\PhantomMagick\Test;

use iBrand\PhantomMagick\Converter;
use iBrand\PhantomMagick\Exception\AdapterException;

class ConverterTest extends BaseTest
{
	use PrivateAndProtectedMethodsAccessibleTrait;

	protected $pdfOptions = [
		'format'      => 'A3',
		'zoomfactor'  => 2,
		'quality'     => '100',
		'orientation' => 'landscape',
		'margin'      => '2cm',
	];

	protected $imageOptions = [
		'dimension'  => '1000px',
		'zoomfactor' => 2,
		'quality'    => '90',
	];

	/** @test */
	public function TestInitialize()
	{
		$this->invokeMethod($this->converter, 'initialize');
		$this->assertContains('phantom_magick.js', $this->converter->getScript(), 'Verify that phantom_magick.js is used');
	}

	/** @test */
	public function TestMake()
	{
		$this->assertInstanceOf('iBrand\PhantomMagick\Converter', Converter::make('http://code-chunk.com'), 'Check Converter::make() method returning the instance of \iBrand\PhantomMagick\Converter');
	}

	/** @test */
	public function TestSetSource()
	{
		$this->converter->setSource('http://code-chunk.com');

		$this->assertEquals('http://code-chunk.com', $this->converter->getSource());
	}

	/** @test */
	public function TestGetSource()
	{
		$this->converter->setSource('http://code-chunk.com');
		$this->assertEquals('http://code-chunk.com', $this->converter->getSource());
	}

	/** @test */
	public function TestSource()
	{
		$this->converter->source('http://code-chunk.com');

		$this->assertEquals('http://code-chunk.com', $this->converter->getSource());
	}

	/** @test */
	public function TestDefaultFileSystemDriverIsLocal()
	{
		$this->assertEquals('local', $this->converter->getDriver());
	}

	/** @test */
	public function TestFileSystemDriverIsS3WhenS3ClientIsUsedAsClient()
	{
		$this->converter->adapter('s3');

		$this->assertEquals('s3', $this->converter->getDriver());
	}

	/** @test */
	public function TestDriverIsQiniu()
	{
		$this->converter->adapter('qiniu');

		$this->assertEquals('qiniu', $this->converter->getDriver());
	}

	/**
	 * @test
	 * @expectedException \iBrand\PhantomMagick\Exception\AdapterException
	 */
	public function TestWillThrowInvalidException()
	{
		$this->converter->adapter('xxxx');
	}

	/** @test */
	public function TestGetTempFilePath()
	{
		$path = '/dummy/file/path';

		$this->converter->setTempFilePath($path);

		$this->assertEquals($path, $this->converter->getTempFilePath());
	}

	/** @test */
	public function TestPdfOptions()
	{
		$this->converter->pdfOptions($this->pdfOptions);

		$this->assertEquals($this->pdfOptions, $this->converter->getPdfOptions());
	}

	/** @test */
	public function TestImageOptions()
	{
		$options = [
			'dimension'  => '900px',
			'zoomfactor' => 1,
		];

		$this->converter->imageOptions($options);

		$this->assertArrayHasKey('quality', $this->converter->getImageOptions());
	}

	/** @test */
	public function TestContentType()
	{
		$mime = $this->converter->contentType('pdf');

		$this->assertEquals('application/pdf', $mime);
	}

	/** @test */
	public function TestPagesIsEmpty()
	{
		$this->assertTrue(empty($this->converter->getPages()));
	}

	/** @test */
	public function TestPagesIsNotEmpty()
	{
		$this->converter->addPage('http://google.com');

		$this->assertFalse(empty($this->converter->getPages()));
	}

	/** @test */
	public function TestPushContents()
	{
		$pages = ['<html><body><h1>Phantom magick</h1></body></html>'];

		$this->converter->pushContent($pages[0]);

		$this->assertEquals($pages, $this->converter->getPages());
	}

	/** @test */
	public function TestPageBreak()
	{
		$pages = [
			'<html><body><h1>Phantom magick</h1></body></html>',
			'<div style="page-break-after:always;"><!-- page break --></div>',
		];

		$this->converter->pushContent($pages[0]);

		$this->converter->pageBreak();

		$this->assertEquals($pages, $this->converter->getPages());
	}

	/** @test */
	public function TestAddPages()
	{
		$expected = [
			'<html><body><h1>Page 1</h1></body></html>',
			'<div style="page-break-after:always;"><!-- page break --></div>',
			'<html><body><h1>Page 2</h1></body></html>',
		];

		$pages = [
			'<html><body><h1>Page 1</h1></body></html>',
			'<html><body><h1>Page 2</h1></body></html>',
		];

		$this->converter->addPages($pages);

		$this->assertEquals($expected, $this->converter->getPages());
	}

	/** @test */
	public function TestToPdf()
	{
		$this->converter->toPdf($this->pdfOptions);

		// Check pdf options is set properly
		$this->assertEquals($this->pdfOptions, $this->converter->getPdfOptions());

		// Check .pdf extension is set
		$this->assertContains('.pdf', $this->converter->getTempFilePath());
	}

	/** @test */
	public function TestToPng()
	{
		$this->converter->toPng($this->imageOptions);

		$this->assertEquals($this->imageOptions, $this->converter->getImageOptions());

		// Check .png extension is set
		$this->assertContains('.png', $this->converter->getTempFilePath());
	}

	/** @test */
	public function TestToJpg()
	{
		$this->converter->toJpg($this->imageOptions);

		$this->assertEquals($this->imageOptions, $this->converter->getImageOptions());

		// Check .png extension is set
		$this->assertContains('.jpg', $this->converter->getTempFilePath());
	}

	/** @test */
	public function TestToGif()
	{
		$this->converter->toGif($this->imageOptions);

		$this->assertEquals($this->imageOptions, $this->converter->getImageOptions());

		// Check .png extension is set
		$this->assertContains('.gif', $this->converter->getTempFilePath());
	}
}
