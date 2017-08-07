<?php
namespace Anam\PhantomMagick;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Anam\PhantomMagick\Exception\FileFormatNotSupportedException;
use Anam\PhantomMagick\Adapter;
use League\Flysystem\Filesystem;

class Converter extends Runner
{
    /**
     * The driver of the Filesystem
     *
     * @var string
     */
    protected $driver = 'local';

    /**
     * The visibily of the file
     *
     * @var string
     */
    protected $acl = 'private';

    /**
     * The Filesystem instance.
     *
     * @var \League\Flysystem\Filesystem  $filesystem
     */
    protected $filesystem;

    /**
     * The temporary directory path
     *
     * @var string
     */
    protected $tempFilePath;

    /**
     * The source of the html data.
     * Source might be the physical file path or URL.
     *
     * @var string
     */
    protected $source;

    /**
     * The output file format
     *
     * @var string
     */
    private static $format = 'pdf';

    /**
     * Multiple HTML pages.
     *
     * @var array
     */
    protected $pages = [];

    /**
     * Indicates if the conversion is multi pages or not.
     *
     * @var boolean
     */
    protected static $multiPage = false;

    /**
     * The conversion scripts.
     *
     * @var array
     */
    protected static $scripts = [];

    /**
     * Default PDF settings
     *
     * @var array
     */
    protected static $pdfOptions = [
        //Supported formats are: 'A3', 'A4', 'A5', 'Legal', 'Letter', 'Tabloid'
        'format'        => 'A4',
        // 1 = 100% zoom
        'zoomfactor'    => 1,
        'quality'       => '70',
        //Orientation: 'portrait', 'landscape'
        'orientation'   => 'portrait',
        'margin'        => '1cm'
    ];

    /**
     * Default Image settings
     *
     * @var array
     */
    protected static $imageOptions = [
        // Dimension in pixels.
        // if only width is given full webpage will render
        // if both width and height is given,
        // the image will be clipped to given width and height
        'dimension'     => '1280px',
        // 1 = 100% zoom
        'zoomfactor'    => 1,
        'quality'       => '80'
    ];

    /**
     * Supported image formats
     *
     * @var array
     */
    protected static $imageFormats = [
        'png' => '.png',
        'jpg' => '.jpg',
        'gif' => '.gif'
    ];

    /**
     * Supported Paper sizes.
     * Only use in PDF conversion
     *
     * @var array
     */
    protected static $paperSizes = [
        'A3',
        'A4',
        'A5',
        'Legal',
        'Letter',
        'Tabloid'
    ];

    /**
     * Initialize the Converter
     *
     * @param string  $source  source of the data file
     */
    public function __construct($source = null)
    {
        $this->initialize();

        if ($source) {
            $this->setSource($source);
        }

        parent::__construct();
    }

    /**
     * Initialize the converter settings
     *
     * @return void
     */
    private function initialize()
    {
        self::$scripts['converter'] = dirname(__FILE__) . '/scripts/phantom_magick.js';
    }

    /**
     * Create a new Converter instance.
     *
     * @param  string $source  Source of the data file
     * @return Converter
     */
    public static function make($source)
    {
        return new self($source);
    }

    /**
     * Pick appropriate Flysystem adapter for a client
     *
     * @param  mixed $client
     * @return $this
     */
    public function adapter($client)
    {
        $args = func_get_args();

        array_shift($args);

        $adapter = new Adapter($client, $args);

        $this->filesystem = new Filesystem($adapter->pick());

        $this->driver = $adapter->getDriver();

        return $this;
    }

    /**
     * Set visibility
     *
     * @param  string $acl
     * @return $this
     */
    public function acl($acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Set PhantomJS binary location
     *
     * @param string $binary phantomsjs location
     * @return $this
     **/
    public function setBinary($binary)
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * Get the Executable PhantomJS binary source
     *
     * @return string
     */

    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * Get the driver of the filesystem
     *
     * @return string
     */

    public function getDriver()
    {
        return $this->driver;
    }


    /**
     * Set the temporary file path
     *
     * @param string $filename
     * @return void
     */
    public function setTempFilePath($filename)
    {
        $this->tempFilePath = $filename;
    }

    /**
     * Get the temporary file location
     *
     * @return string
     */
    public function getTempFilePath()
    {
        return $this->tempFilePath;
    }

    /**
     * Get the conversion scripts
     *
     * @return array
     */
    public function getScript()
    {
        return self::$scripts['converter'];
    }

    /**
     * Set the data source
     *
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the data source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Alias of the setSource()
     *
     * @param  string $source
     * @return string
     */
    public function source($source)
    {
        return $this->setSource($source);
    }

    /**
     * Prepare converter for pdf conversion
     *
     * @param  array  $options PDF settings
     * @return $this
     */
    public function toPdf(array $options = [])
    {
        $this->pdfOptions($options);

        $this->setTempFilePath(sys_get_temp_dir() . '/' . uniqid(rand()) . '.pdf');

        return $this;
    }

    /**
     * Prepare converter for PNG conversion
     *
     * @param  array  $options Image settings
     * @return $this
     */
    public function toPng(array $options = [])
    {
        return $this->prepareImage($options, $format = 'png');
    }

    /**
     * Prepare converter for JPG conversion
     *
     * @param  array  $options Image settings
     * @return $this
     */
    public function toJpg(array $options = [])
    {
        return $this->prepareImage($options, $format = 'jpg');
    }

    /**
     * Prepare converter for GIF conversion
     *
     * @param  array  $options Image settings
     * @return $this
     */
    public function toGif(array $options = [])
    {
        return $this->prepareImage($options, $format = 'gif');
    }

    /**
     * Prepare converter for Image conversion
     *
     * @param  array  $options Image settings
     * @param string $format Image format JPG|PNG|GIF
     *
     * @return $this
     */
    public function toImage($options, $format = 'png')
    {
        return $this->prepareImage($options, $format);
    }

    /**
     * Prepare converter for Image conversion
     *
     * @param  array  $options Image settings
     * @param string $format Image format JPG|PNG|GIF
     *
     * @return $this
     */

    public function prepareImage($options, $format = 'png')
    {
        $format = strtolower($format);

        if (! array_key_exists($format, self::$imageFormats)) {
            throw new FileFormatNotSupportedException("{$format} file format not Supported.");
        }

        self::$format = $format;

        $this->imageOptions($options);

        $this->setTempFilePath(sys_get_temp_dir() . '/' . uniqid(rand()) . self::$imageFormats[$format]);

        return $this;
    }

    /**
     * Add HTMl page
     *
     * @param string $page Data file path|URL|Raw html code
     */
    public function addPage($page)
    {
        self::$multiPage = true;

        if (count($this->pages)) {
            $this->pageBreak();
        }

        $this->pushContent($page);

        return $this;
    }

    /**
     * Add multiple pages
     *
     * @param array $pages Data files paths|URLs|Raw HTML code
     * @return $this
     */
    public function addPages(array $pages)
    {
        self::$multiPage = true;

        foreach ($pages as $page) {
            if (count($this->pages)) {
                $this->pageBreak();
            }

            $this->pushContent($page);
        }

        return $this;
    }

    /**
     * Push data to pages
     *
     * @param  string $page file path|URL|Raw HTML
     * @return void
     */
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

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages()
    {
       return $this->pages;
    }

    /**
     * Add page break to pages
     *
     * @return void
     */
    public function pageBreak()
    {
        $content = '<div style="page-break-after:always;"><!-- page break --></div>';

        array_push($this->pages, $content);
    }

    /**
     * Add contents to the tempfile file.
     *
     * @param  string $content
     * @return void
     */
    protected function put($content)
    {
        $this->createTempFile();

        file_put_contents($this->getTempFilePath(), $content, FILE_APPEND);
    }

    /**
     * Create a temporary html file for converting mutipages pdf
     *
     * @return string
     */
    protected function createTempFile()
    {
        $this->setTempFilePath(sys_get_temp_dir() . '/' . uniqid(rand()) . '.html');

        if (! touch($this->getTempFilePath())) {
            throw new RuntimeException('Unable to create file in temp directory: '. sys_get_temp_dir());
        }

        return $this->getTempFilePath();
    }

    /**
     * Force download file when conversion is completed
     *
     * @param  string  $downloadAs filename
     * @param  boolean $inline     Show file in browser or not
     * @return void
     */
    public function download($downloadAs = null, $inline = false)
    {
        // Force "local" driver.
        $this->driver = 'local';

        $filename = $this->getTempFilePath();

        if (self::$multiPage) {
            $this->put(implode('', $this->pages));
            $this->resetPages();

            $filename = dirname($this->getTempFilePath()) . "/" . basename($this->getTempFilePath(), ".html") . ".pdf";
        }

        $result = $this->save($filename);

        // Log warning or errors using PHP system logger
        if (trim($result)) {
            error_log($result);
        }

        $pathParts = pathinfo($filename);

        $downloadAs = $downloadAs? $downloadAs : $pathParts['basename'];
        $contentDisposition = $inline? 'inline' : 'attachment';
        $contentType = $this->contentType($pathParts['extension']);

        if (! file_exists($filename)) {
            error_log("Conversion failed.");
            return "Conversion failed.";
        }

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

    /**
     * Download and display the file in browser
     *
     * @return void
     */
    public function serve()
    {
        $this->download(null, true);
    }

    /**
     * Save PDF|Image to the given file path if driver is local.
     * or Save file in cloud with provided filename
     *
     * @param string $filename full physical path with filename for local driver or just filename for cloud.
     * @return mixed
     **/

    public function save($filename = null)
    {
        if ($this->driver == 'local' && ! $filename) {
            throw new InvalidArgumentException("Filename can not be empty. Please provide a full physical path with filename.");
        }

        if (! $filename) {
            $pathParts = pathinfo($this->getSource());
            $filename = $pathParts['filename'] . '.' . self::$format;

            if (self::$multiPage) {
                $filename = uniqid('phantom_magick') . '.' . self::$format;
            }
        }

        if ($this->driver == 'local') {
            return $this->saveLocal($filename);
        }

        return $this->saveCloud($filename);
    }

    /**
     * Save PDF|Image to the given file path.
     *
     * @param string $filename full physical path with filename
     * @return mixed
     **/
    public function saveLocal($filename)
    {
        if (self::$multiPage) {
            if (count($this->pages)) {
                $this->put(implode('', $this->pages));
            }

            return $this->run(self::$scripts['converter'], $this->getTempFilePath(), $filename, self::$pdfOptions);
        }

        // Single page pdf
        if (self::$format === 'pdf') {
            return $this->run(self::$scripts['converter'], $this->getSource(), $filename, self::$pdfOptions);
        }

        // Image
        return $this->run(self::$scripts['converter'], $this->getSource(), $filename, self::$imageOptions);
    }

    /**
     * Save PDF|Image to the cloud.
     *
     * @param string $filename.
     * @return mixed
     **/
    public function saveCloud($filename)
    {
        $tempFilename = $this->getTempFilePath();

        // Multi page pdf
        if (self::$multiPage) {
            if (count($this->pages)) {
                $this->put(implode('', $this->pages));
            }

            $tempFilename = dirname($this->getTempFilePath()) . "/" . basename($this->getTempFilePath(), ".html") . ".pdf";

            $this->run(self::$scripts['converter'], $this->getTempFilePath(), $tempFilename, self::$pdfOptions);

        } elseif (self::$format === 'pdf') {
            $this->run(self::$scripts['converter'], $this->getSource(), $tempFilename, self::$pdfOptions);
        } else {
            $this->run(self::$scripts['converter'], $this->getSource(), $tempFilename, self::$imageOptions);
        }

        if (file_exists($tempFilename)) {
            $contents = file_get_contents($tempFilename);

            unlink($tempFilename);

            return $this->filesystem->put($filename, $contents, ['visibility' => $this->acl]);
        }
    }

    /**
     * Set the PDF options
     *
     * @param array $options
     * @return $this
     */
    public function setPdfOptions(array $options)
    {
        $this->pdfOptions($options);

        return $this;
    }

    /**
     * Get PDF options
     *
     * @return $array
     */
    public function getPdfOptions()
    {
        return self::$pdfOptions;
    }

    /**
     * Update PDF settings
     *
     * @param  array  $options
     * @return $this
     */
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

    /**
     * Set the Image options
     *
     * @param array $options
     * @return $this
     */
    public function setImageOptions(array $options)
    {
        $this->imageOptions($options);

        return $this;
    }

    /**
     * Get Image settings
     *
     * @return $array
     */
    public function getImageOptions()
    {
        return self::$imageOptions;
    }

    /**
     * Update image settings
     *
     * @param  array  $options
     * @return $this
     */

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

            self::$imageOptions['dimension'] = $options['width'] . 'px' . '*' . $options['height'] . 'px';
        } elseif (isset($options['width'])) {
            if (! ctype_digit($options['width'])) {
                throw new Exception('Width must be a number');
            }

            self::$imageOptions['dimension'] = $options['width'] . 'px';
        }

        return $this;
    }

    /**
     * Set the Paper format for PDF conversion
     * @return $this
     */
    public function format($format)
    {
        if (! in_array($format, self::$paperSizes)) {
            throw new Exception('Paper format not supported.');
        }

        self::$pdfOptions['format'] = $format;

        return $this;
    }

    /**
     * Set the Portrait orientation
     * Only use in PDF conversion
     * @return $this
     */
    public function portrait()
    {
        self::$pdfOptions['orientation'] = 'portrait';

        return $this;
    }

    /**
     * Set the Landscape orientation
     * Only use in PDF conversion
     * @return $this
     */
    public function landscape()
    {
        self::$pdfOptions['orientation'] = 'landscape';

        return $this;
    }

    /**
     * Set the Width
     * Only use in Image conversion
     * @return $this
     */
    public function width($width)
    {
        if (! ctype_digit($width)) {
            throw new Exception('Width must be a number');
        }

        $dimension = explode("*", self::$imageOptions['dimension']);

        $dimension[0] = $width . 'px';

        self::$imageOptions['dimension'] = implode("*", $dimension);

        return $this;
    }

    /**
     * Set the Height
     * Only use in Image conversion
     * @return $this
     */
    public function height($height)
    {
        if (! ctype_digit($height)) {
            throw new Exception('Height must be a number');
        }

        $dimension = explode("*", self::$imageOptions['dimension']);

        $dimension[1] = $height . 'px';

        self::$imageOptions['dimension'] = implode("*", $dimension);

        return $this;
    }

    /**
     * Set the Image quality
     * Only used in Image conversion
     * @return $this
     */
    public function quality($quality = 80)
    {
        if (! ($quality >=1 && $quality <=100)) {
            throw new Exception('Quality must be between 1-100');
        }

        self::$imageOptions['quality'] = $quality;

        return $this;
    }

    /**
     * Determine file mime
     *
     * @param  string $ext
     * @return string
     */
    public function contentType($ext)
    {
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

    /**
     * Reset pages
     * @return void
     */
    public function resetPages()
    {
        $this->pages = [];
    }

    /**
     * Remove temporary files
     *
     * @return void
     */
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
