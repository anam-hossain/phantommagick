<?php
namespace Anam\Html2PdfConverter;

class Converter extends Runner
{
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

	public function addPage()
	{
		
	}
}

