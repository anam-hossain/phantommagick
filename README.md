# PhantomMagick

PhantomMagick provides a simple api to ease HTML to PDF or HTML to Image conversion. This package is very handy for generating Invoices or capturing website screenshot.

## Features

- Convert HTML to PDF
- Convert HTML to Image (PNG, JPG, GIF)
- Capture a web page as a screenshot
- Save PDF or Image to local disk or in cloud.
- Framework-agnostic

## Requirements

- PHP 5.4+
- [PhantomJS](http://phantomjs.org)

## Installation

PhantomMagick is available via Composer:

```bash
$ composer require anam/phantommagick
```

or 

```json
{
  "require" : {
    "anam/phantommagick": "dev-master"
  }
}
```

## Dependencies

[PhantomJS](http://phantomjs.org/download.html) is required to install before use the PhantomMagick.

There are few ways to install PhantomJS:

##### Using PhantomJS binary

You can download official PhantomJS binary from the following link:

[http://phantomjs.org/download.html](http://phantomjs.org/download.html).

##### Install with composer

To install with Composer, simply add the following requirement to your `composer.json` file. 

```json
{
  "require" : {
    "anam/phantomjs-linux-x86-binary": "~1.0"
  }
}
```

Note: This composer package will install PhantomJS binary for 64 bit linux systems.

## Usage

### PDF conversion

```php
$conv = new \Anam\PhantomMagick\Converter();
$conv->source('http://google.com')
    ->toPdf()
    ->save('/your/destination/path/google.pdf');
```

##### Multipage pdf

```php
use Anam\PhantomMagick\Converter;

$conv = new Converter();
$conv->addPage('<html><body><h1>Welcome to PhantomMagick</h1></body></html>')
    ->addPage('http://facebook.com')
    ->addPage('/html/file/from/local/drive/example.html')
    ->save('/your/destination/path/multipage.pdf');
```

Multipage Pdf limitation: 
- Only support Absolute paths. Relative paths will be avoided.
- Inline or Internal css is recomended.


### Image conversion

PhantomMagick support HTML to PNG/JPG/GIF conversion.

```php
$conv = new \Anam\PhantomMagick\Converter();
$conv->source('http://google.com')
    ->toPng()
    ->save('/your/destination/path/google.png');
```

##### HTML to PNG

```php
$conv->toPng()
```

##### HTML to JPG

```php
$conv->toJpg()
```

##### HTML to GIF

```php
$conv->toGif()
```

### Download file

```php
use Anam\PhantomMagick\Converter;

Converter::make('http://google.com')
    ->toPdf()
    ->download('google.pdf');

Converter::make('http://yahoo.com')
    ->toPng()
    ->download('yahoo.png');
```

To display in the browser:

```php
$conv->download('yahoo.png', true);
```

## Credits

- [Anam Hossain](https://github.com/anam-hossain)
- [All Contributors](https://github.com/anam-hossain/phantommagick/graphs/contributors)

## License

The MIT License (MIT). Please see [LICENSE](http://opensource.org/licenses/MIT) for more information.