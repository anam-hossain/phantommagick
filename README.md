# PhantomMagick

PhantomMagick provides a simple api to ease HTML to PDF or Image conversion. This package is very handy for generating Invoices and capturing website screenshot.

## Features

- Convert HTML to PDF
- Convert HTML to Image (PNG, JPG, GIF)
- Capture a web page as a screenshot
- Save PDF or Image to local disk or in cloud.

## Requirements

- PHP 5.4+
- PhantomJS

## Installation

PhantomMagick is available via Composer:

```bash
$ composer require anam/phantommagick
```

## Dependencies

[PhantomJS](http://phantomjs.org/download.html) is required to use this package.

#### How to install PhantomJS

There are few ways to install PhantomJS:

##### 1. Using PhantomJS binary

you can download PhantomJS binary from the following link:

[http://phantomjs.org/download.html](http://phantomjs.org/download.html).

##### 2. Install with composer

To install with Composer, simply add the requirement to your `composer.json` file:

```json
{
  "require" : {
    "jakoch/phantomjs-installer": "1.9.8"
  }
}
```

More information will be added soon.

## Usage

#### PDF conversion

```php
$conv = new Anam\PhantomMagick\Converter();
$conv->source('http://code-chunk.com')
    ->toPdf()
    ->save('/your/destination/path/codechunk.pdf');
```

## Credits

- [Anam Hossain](https://github.com/anam-hossain)
- [All Contributors](https://github.com/anam-hossain/phantommagick/graphs/contributors)

## License

The MIT License (MIT). Please see [LICENSE](http://opensource.org/licenses/MIT) for more information.