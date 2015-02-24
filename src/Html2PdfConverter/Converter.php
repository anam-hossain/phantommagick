<?php
namespace Anam\Html2PdfConverter;

use Exception;

class Converter extends Runner
{
	protected $tempFilePath;

	protected $source;

	public $pages = [];

	public static $scripts = [];

	public $options = [];

	public function __construct($source = null)
	{
		$this->initialize();

		if ($source) {
			$this->setSource($source);
		}
	}


	public function initialize()
	{
		self::$scripts['rasterize'] = dirname(__FILE__) . '/scripts/rasterize.js';
	}

	/**
	 * Set PhantomJS binary location
	 *
	 * @param string $path
	 * @return void
	 **/
	public function setBinary($binary)
	{
		$this->verifyBinary($binary);

		$this->binary = $binary;
	}

	/**
	 * Get the Executatabe PhantomJS binary source 
	 *
	 * @param string $path
	 * @return string
	 **/

	public function getBinary()
	{
		return $this->binary;
	}

	public function setTempFilePath($filename)
	{
		$this->tempFilePath = $filename;
	}

	public function getTempFilePath()
	{
		return $this->tempFilePath;
	}

	public function setSource($source)
	{
		$this->source = $source;
	}

	public function getSource()
	{
		return $this->source;
	}

	public static function make($source)
	{
		return new self($source);
	}

	public function toPdf()
	{
		$this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . '.pdf');

		$this->run(self::$scripts['rasterize'], $this->getSource(), $this->getTempFilePath());

		return $this;
	}

	public function toPng()
	{

	}

	public function toJpg()
	{

	}

	public function addPage($page)
	{
		if (count($this->pages)) {
			$this->pageBreak();
		}

		$this->pushContent($page);

		return $this;
	}


	public function addPages(array $pages)
	{
		foreach ($pages as $page) {
			if (count($this->pages)) {
				$this->pageBreak();
			}
			
			$this->pushContent($page);
		}
		
		return $this;
	}

	public function download($inline = false)
	{

	}

	/**
	 * Save the PDF to given file path.
	 *
	 * @param string $filename full physical path with filename
	 * @return boolean
	 **/

	public function save($filename)
	{
        $this->put(implode('', $this->pages));

		//return true;
        
        return $this->getTempFilePath();
	}

	public function pushContent($page)
	{
		// file_get_contents will try to load file from physical path or from an URL
		// and will return the content as string
		// If failed, it will return false.

		$content = @file_get_contents($page);
		
		// Perhaps raw HTML content.
		if (! $content) {
			$content = $page;
		}

		array_push($this->pages, $content);

	}

    public function pageBreak()
    {
        $content = '<div style="page-break-after:always;"><!-- page break --></div>';

        array_push($this->pages, $content);
    }

	protected function createTempFile()
	{
		$this->setTempFilePath(sys_get_temp_dir() . uniqid(rand(), true) . '.html');

		if (! touch($this->getTempFilePath())) {
			throw new Exception('Unable to create file in PHP temp directory: '. sys_get_temp_dir());
		}

		return $this->getTempFilePath();
	}


	protected function put($content)
	{
		if (! file_exists($this->getTempFilePath())) {
			$this->createTempFile();
		}

		file_put_contents($this->getTempFilePath(), $content, FILE_APPEND);
	}

	protected function removeTempFile()
	{
		unlink($this->getTempFilePath());
	}
}

