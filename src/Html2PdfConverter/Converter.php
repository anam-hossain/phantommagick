<?php
namespace Anam\Html2PdfConverter;

class Converter extends Runner
{
	protected $tempFile;

	protected $pages = [];

	public function __construct()
	{

	}

	/**
	 * Set PhantomJS binary location
	 *
	 * @param string $path
	 * @return void
	 **/
	public function setBinary($path)
	{
		$this->binary = $path;
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

	public function addPage($page)
	{
		$this->pushContent($page);

		return $this;
	}


	public function addPages(array $pages)
	{
		foreach ($pages as $page) {
			$this->pushContent($page);
		}
		
		return $this;
	}

	public function pushContent($page)
	{
		// file_get_contents will try to load file from physical path or from an URL
		// and will return the content as string
		// If failed, it will return false.
		$content = file_get_contents($page);
		
		// Perhaps raw HTML content.
		if (! $content) {
			$content = $page;
		}

		array_push($this->pages, $content);

	}

	protected function createTempFile()
	{
		$this->tempFile = tempnam(sys_get_temp_dir(), "PDF");

		if (! $this->tempFile) {
			throw new Exception('Unable to create file in PHP temp directory: '. sys_get_temp_dir());
		}

		return $this->tempFile;
	}


	protected function append($content)
	{
		if (! file_exists($this->tempFile)) {
			$this->createTempFile();
		}

		file_put_contents($this->tempFile, $content, FILE_APPEND);
	}

	protected function removeTempFile()
	{
		unlink($this->tempFile);
	}
}

