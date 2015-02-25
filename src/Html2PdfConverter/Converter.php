<?php
namespace Anam\Html2PdfConverter;

use Exception;

class Converter extends Runner
{
	protected $tempFilePath;

	protected $source;

	public $pages = [];

	public static $scripts = [];

	public $defaultPdfOptions = [
		//Supported formats are: 'A3', 'A4', 'A5', 'Legal', 'Letter', 'Tabloid'
		'format' 		=> 'A4',
		//Orientation: 'portrait', 'landscape'
		'orientation'	=> 'portrait',
		// 1 = 100% zoom
		'zoomfactor'	=> 1,
		'margin'		=> '1cm',
		// If paper width and paper height are provided, 
		// format will be replace with them
		// Supported dimension units are: 'mm', 'cm', 'in', 'px'. No unit means 'px'.
		'paperwidth'	=> null,
		'paperheight'	=> null
	];

	public $defaultImageOptions = [];

	public function __construct($source = null)
	{
		$this->initialize();

		if ($source) {
			$this->setSource($source);
		}
	}


	public function initialize()
	{
		self::$scripts['converter'] = dirname(__FILE__) . '/scripts/phantom_magick.js';
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

	public function toPdf(array $options = array())
	{
		$options = $this->processOptions($this->defaultPdfOptions, $options);

        // Custom paper width and height will replace the default format.
        if($options['paperwidth'] && $options['paperheight']) {
            // ex. 10cm*5cm, 1200px*1000px, 10in*5in, 900*600
            // note: without unit (i.e 900*600) will use px.
            $options['format'] => $options['paperwidth'] . '*' . $options['paperheight'];
        }

        $this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . '.pdf');

		$this->run(self::$scripts['converter'], $this->getSource(), $this->getTempFilePath(), $options);

		return $this;
	}

	public function toPng(array $options = array())
	{

	}

	public function toJpg(array $options = array())
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

	protected processOptions(array $defaultOPtions, array $options)
	{
        foreach ($options as $key => $option) {
            if(isset($defaultOPtions[$key])) {
                $defaultOPtions[$key] = $option;
            }
        }

        return $defaultOPtions;
	}
}

