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

##### 2. Using Composer

The easiest way to install PhantomJS is by adding the following depencies to your composer.json

```json
"required": {
  "xyz" : 1.5.*
}
```