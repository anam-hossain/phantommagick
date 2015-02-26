<?php
namespace Anam\Html2PdfConverter;

use Exception;

class Converter extends Runner
{
	protected $tempFilePath;

	protected $source;

	protected $pages = [];

	protected static $scripts = [];

	protected $defaultPdfOptions = [
		//Supported formats are: 'A3', 'A4', 'A5', 'Legal', 'Letter', 'Tabloid'
		'format' 		=> 'A4',
		// 1 = 100% zoom
		'zoomfactor'	=> 1,
		'quality'       => '70',
        //Orientation: 'portrait', 'landscape'
        'orientation'   => 'portrait',
		'margin'		=> '1cm',
		// If paper width and paper height are provided, 
		// format will be replace with them
		// Supported dimension units are: 'mm', 'cm', 'in', 'px'. No unit means 'px'.
		'paperwidth'	=> null,
		'paperheight'	=> null
	];

	protected $defaultImageOptions = [
		// Dimension in pixels, 720p.
		'dimension' 	=> '1280px*720px',
		// 1 = 100% zoom
		'zoomfactor'	=> 1,
		'quality'       => '70',
		// If custom width and height is provided, 
		// Dimension will be set with the custom width and height
		// i.e width*height
		'width'	=> null,
		'height'	=> null
	];

	// Supported image formats
	protected static $imageFormats = [
		'png' => '.png',
		'jpg' => '.jpg',
		'gif' => '.gif'
	]; 

	public function __construct($source = null)
	{
		$this->initialize();

		if ($source) {
			$this->setSource($source);
		}
	}

	private function initialize()
	{
		self::$scripts['converter'] = dirname(__FILE__) . '/scripts/phantom_magick.js';
	}

	public static function make($source)
	{
		return new self($source);
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

	public function toPdf(array $options = array())
	{
		$options = $this->processOptions($this->defaultPdfOptions, $options);

        // Custom paper width and height will replace the default format.
        if($options['paperwidth'] && $options['paperheight']) {
            // ex. 10cm*5cm, 1200px*1000px, 10in*5in, 900*600
            // note: without unit (i.e 900*600) will use px.
            $options['format'] => $options['paperwidth'] . '*' . $options['paperheight'];
        }

        unset($options['paperwidth'], $options['paperheight']);

        $this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . '.pdf');

		$this->run(self::$scripts['converter'], $this->getSource(), $this->getTempFilePath(), $options);

		return $this;
	}

	public function toPng(array $options = array())
	{
		return $this->convertImage($options, $format = 'png');
	}

	public function toJpg(array $options = array())
	{
		return $this->convertImage($options, $format = 'jpg');
	}

	public function toGif(array $options = array())
	{
		return $this->convertImage($options, $format = 'gif');
	}

	// Alias of convertImage
	public function toImage($options, $format = 'png')
	{
		return $this->convertImage($options, $format);
	}

	public function convertImage($options, $format = 'png')
	{
		$format = strtolower($format);

		if (! array_key_exists($format, self::$imageFormats)) {
			throw new Exception("\'{$format}\' file format not Supported.");
		}

		$options = $this->processOptions($this->defaultImageOptions, $options);

  		$options = $this->setDimension($options);

        $this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . self::$imageFormats[$format]);

		$this->run(self::$scripts['converter'], $this->getSource(), $this->getTempFilePath(), $options);

		return $this;
	}

	protected function setDimension(array $options)
	{
		if($options['width'] && $options['height']) {

            // Only digits accepted
            if (! ctype_digit($options['width'])) {
				throw new Exception('Width must be a number');
			}

			if (! ctype_digit($options['height'])) {
				throw new Exception('Height must be a number');
			}
            // generate dimension - i.e 1200px*1000px
            $options['dimension'] => $options['width'] . 'px' . '*' . $options['height'] . 'px';
        }

        unset($options['width'], $options['height']);

        return $options;
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

