<?php
namespace Anam\Html2PdfConverter;

use Exception;

class Converter extends Runner
{
	protected $tempFilePath;

	protected $source;

	private static $format = 'pdf';

	protected $pages = [];

	protected static $scripts = [];

	protected static $pdfOptions = [
		//Supported formats are: 'A3', 'A4', 'A5', 'Legal', 'Letter', 'Tabloid'
		'format' 		=> 'A4',
		// 1 = 100% zoom
		'zoomfactor'	=> 1,
		'quality'       => '70',
        //Orientation: 'portrait', 'landscape'
        'orientation'   => 'portrait',
		'margin'		=> '1cm'
	];

	protected static $imageOptions = [
		// Dimension in pixels, 720p.
		'dimension' 	=> '1280px*720px',
		// 1 = 100% zoom
		'zoomfactor'	=> 1,
		'quality'       => '70'
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

		return $this;
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

		return $this;
	}

	public function getSource()
	{
		return $this->source;
	}

	// Alias of setSource($source)
	public function source($source) {
		return $this->setSource($source);
	}

	public function toPdf(array $options = array())
	{
		$this->pdfOptions($options);

        $this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . '.pdf');

		return $this;
	}

	public function toPng(array $options = array())
	{
		return $this->prepareImage($options, $format = 'png');
	}

	public function toJpg(array $options = array())
	{
		return $this->prepareImage($options, $format = 'jpg');
	}

	public function toGif(array $options = array())
	{
		return $this->prepareImage($options, $format = 'gif');
	}

	// Alias of prepareImage()
	public function toImage($options, $format = 'png')
	{
		return $this->prepareImage($options, $format);

	}

	public function prepareImage($options, $format = 'png')
	{
		$format = strtolower($format);

		if (! array_key_exists($format, self::$imageFormats)) {
			throw new Exception("\'{$format}\' file format not Supported.");
		}

		self::$format = $format;

		$this->imageOptions($options);

        $this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . self::$imageFormats[$format]);

		return $this;
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

	public function download($downloadAs = null, $inline = false)
	{
		if (count($this->pages)) {
            $this->put(implode('', $this->pages));

			$filename = dirname($this->getTempFilePath()) . "/" . basename($this->getTempFilePath(), ".html") . ".pdf";
        } else {
			$filename = $this->getTempFilePath();
		} 

		$result = $this->save($filename);
		
		// Error.
		if (trim($result)) {
			return $result;
		}

		$path_parts = pathinfo($filename);

		$downloadAs = $downloadAs? $downloadAs : $path_parts['basename'];
		$contentDisposition = $inline? 'inline' : 'attachment';
		$contentType = $this->contentType($path_parts['extension']);

		if (file_exists($filename)) {
            header('Content-Description: File Transfer');
            header("Content-Type: {$contentType}");
            header("Content-Disposition: {$contentDisposition}; filename={$downloadAs}");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);

            unlink($filename);
            $this->clearTempFiles();
            
            exit;
        }
	}

	/**
	 * Save the PDF to given file path.
	 *
	 * @param string $filename full physical path with filename
	 * @return boolean
	 **/

	public function save($destination)
	{
        // Multi pages pdf
        if (count($this->pages)) {
        	$this->put(implode('', $this->pages));
                 
        	return $this->run(self::$scripts['converter'], $this->getTempFilePath(), $destination, self::$pdfOptions);
        }

        // Sigle page pdf
        if (self::$format === 'pdf') {

        	return $this->run(self::$scripts['converter'], $this->getSource(), $destination, self::$pdfOptions);
        }
        
        // Image
        return $this->run(self::$scripts['converter'], $this->getSource(), $destination, self::$imageOptions);
	}

	public function pushContent($page)
	{
		// @file_get_contents will not throw any exception due to @ symbol
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

    // Only multipages required to create a html file
	protected function createTempFile()
	{
		$this->setTempFilePath(sys_get_temp_dir() . uniqid(rand()) . '.html');

		if (! touch($this->getTempFilePath())) {
			throw new Exception('Unable to create file in PHP temp directory: '. sys_get_temp_dir());
		}

		return $this->getTempFilePath();
	}


	protected function put($content)
	{
		$this->createTempFile();

		file_put_contents($this->getTempFilePath(), $content, FILE_APPEND);
	}

	protected function removeTempFile()
	{
		unlink($this->getTempFilePath());
	}

	public function pdfOptions(array $options)
	{
		foreach ($options as $key => $option) {
            if (isset(self::$pdfOptions[$key])) {
                self::$pdfOptions[$key] = $option;
            }
        }

        // Custom paper width and height will replace the default format.
        if (isset($options['width']) && isset($options['height'])) {
            // ex. 10cm*5cm, 1200px*1000px, 10in*5in, 900*600
            // note: without unit (i.e 900*600) will use px.
            self::$pdfOptions['format'] = $options['width'] . '*' . $options['height'];
        }

		return $this;
	}

	public function imageOptions(array $options)
	{
        foreach ($options as $key => $option) {
            if (isset(self::$imageOptions[$key])) {
                self::$imageOptions[$key] = $option;
            }
        }

        if (isset($options['width']) && isset($options['height'])) {
            // Only digits accepted
            if (! ctype_digit($options['width'])) {
				throw new Exception('Width must be a number');
			}

			if (! ctype_digit($options['height'])) {
				throw new Exception('Height must be a number');
			}
            // generate dimension - i.e 1200px*1000px
            self::$imageOptions['dimension'] = $options['width'] . 'px' . '*' . $options['height'] . 'px';
        }

        return $this;
	}

    protected function contentType($ext) {
        switch ($ext) {
            case 'pdf':
                return 'application/pdf'; 

            case 'jpg':
                return 'image/jpeg';

            case 'png':
                return 'image/png';

            case 'gif':
                return 'image/gif';
            
            default:
                return 'application/pdf';
        }
    }

    public function clearTempFiles()
    {
        if (file_exists($this->getTempFilePath())) {
            unlink($this->getTempFilePath());
        }
    }

    public function __destruct()
    {
        $this->clearTempFiles();
    }
}

