# PhantomMagick

### For PhantomMagick version 1, please use the [1.0.2 branch](https://github.com/anam-hossain/phantommagick/tree/1.0.2)!

PhantomMagick provides a simple API to ease the process of converting HTML to PDF or images. It's especially handy for things like generating invoices or capturing screenshots of websites. It's framework agnostic but it does provide a facade for simple use in Laravel 4/5.

## Features

- Convert HTML to a PDF
- Convert HTML to an image (PNG, JPG or GIF)
- Support multipage PDFs
- Capture a web page as a screenshot
- Save PDF or image to local disk or to the cloud (S3, Dropbox or Rackspace)
- Framework agnostic, with optional Laravel integration

## Requirements

- PHP 5.5+
- [PhantomJS](http://phantomjs.org)

## Installation

PhantomMagick is available via Composer:

```bash
$ composer require anam/phantommagick
```

## Dependencies

[PhantomJS](http://phantomjs.org/download.html) must be installed to use PhantomMagick.

There are few ways to install PhantomJS:

##### Install binary manually

You can download the official PhantomJS binary from the following link:

[http://phantomjs.org/download.html](http://phantomjs.org/download.html).

##### Install binary through Composer

Simply pull in the `anam/phantomjs-linux-x86-binary` package to get the up-to-date PhantomJS binary for 64-bit Linux systems.

```bash
composer require anam/phantomjs-linux-x86-binary
```

## Integrations

##### Laravel 4 and Laravel 5 integrations
Although `PhantomMagick` is framework agnostic, it does support Laravel out of the box and comes with a Service provider and Facade for easy integration.

After you have installed the PhantomMagick, open the `config/app.php` file which is included with Laravel and add the following lines.

In the `$providers` array add the following service provider.

```php
'Anam\PhantomMagick\ConverterServiceProvider'
```

Add the facade of this package to the `$aliases` array.

```php
'Converter' => 'Anam\PhantomMagick\Facades\Converter'
```

You can now use this facade in place of instantiating the converter yourself in the following examples.

## Usage

### PDF conversion

```php
$conv = new \Anam\PhantomMagick\Converter();
$conv->source('http://google.com')
    ->toPdf()
    ->save('/your/destination/path/google.pdf');
```

##### Multipage PDFs

```php
use Anam\PhantomMagick\Converter;

$conv = new Converter();
$conv->addPage('<html><body><h1>Welcome to PhantomMagick</h1></body></html>')
    ->addPage('http://facebook.com')
    ->addPage('/html/file/from/local/drive/example.html')
    ->save('/your/destination/path/multipage.pdf');
```

Please note with multipage PDFs:
- Only absolute paths are supported, so avoid relative paths
- Inline styles or inline style stylesheets are recommended

### Image conversion

PhantomMagick supports HTML to PNG/JPG/GIF conversion.

```php
$conv = new \Anam\PhantomMagick\Converter();
$conv->source('http://google.com')
    ->toPng()
    ->save('/your/destination/path/google.png');
```

###### HTML to PNG

```php
$conv->toPng()
```

###### HTML to JPG

```php
$conv->toJpg()
```

###### HTML to GIF

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

To display in the browser instead of forcing the file to be download, you can pass a second parameter to the method.

```php
$conv->download('google.pdf', true);
```
or just simply call:

```php
$conv->serve();
```

## Save to cloud

PhantomMagick leverages [Flysystem](http://flysystem.thephpleague.com) to save converted files in the cloud. 

PhantomMagick currently supports:
- Amazon S3
- Dropbox
- Rackspace

##### Amazon S3

First install the required S3 dependencies through Composer.

```bash
composer require aws/aws-sdk-php
composer require league/flysystem-aws-s3-v3
```

```php
use Anam\PhantomMagick\Converter;
use Aws\S3\S3Client;

$client = S3Client::factory([
    'credentials' => [
        'key'    => 'AWS_KEY',
        'secret' => 'AWS_SECRET',
    ],
    'region' => 'your-region',
    'version' => 'latest',
]);

$conv = new Converter();
$conv->adapter($client, 'bucket-name', 'optional/path/prefix')
    ->acl('public')
    ->source('http://google.com')
    ->toPdf()
    ->save('google.pdf');
```

##### Dropbox
First install the required Dropbox dependencies through Composer.

```bash
composer require dropox/dropbox-sdk
composer require flysystem-dropbox
```

```php
use Anam\PhantomMagick\Converter;
use Dropbox\Client;

$client = new Client('DROPBOX_TOKEN', 'DROPBOX_APP');

$conv = new Converter();
$conv->adapter($client)
    ->source('https://google.com')
    ->toPdf()
    ->save('dropbox_example.pdf');
```

##### Rackspace
First install the required Rackspace dependencies through Composer.

```bash
composer require rackspace/php-opencloud
composer require league/flysystem-rackspace
```

```php
use Anam\PhantomMagick\Converter;
use OpenCloud\OpenStack;
use OpenCloud\Rackspace;

$client = new OpenStack(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => 'RACKSPACE_USERNAME',
    'password' => 'RACKSPACE_PASSWORD'
));

$store = $client->objectStoreService('cloudFiles', 'SYD');
$container = $store->getContainer('phantom-magick');

$conv = new Converter();
$conv->adapter($container)
    ->source('https://google.com')
    ->toPdf()
    ->save('rackspace_example.pdf');
```

### Settings

#### Global options
###### Binary
You can set the path of the `phantomjs` binary if you've installed it yourself manually, or the `phantomjs` command is not available in your shell. If you installed it through Composer (with the `anam/phantomjs-linux-x86-binary` package) PhantomMagick will be smart enough to find the file automatically.

```php
$conv->setBinary('/phantomjs/binary/path/phantomjs');
```
###### Data Source
PhantomMagick only supports HTML and data can be provided via an URL or from the local disk. If you need to use raw HTML data, you can use multipage PDF conversion. However raw data does have some limitations; it does not support relative paths and it only supports inline styles and internal CSS.

```php
new Converter('/Path/to/file/example.html');
// or
Converter::make('/Path/to/file/example.html');
//or
$conv->source('/Path/to/file/example.html');
// or
$conv->source('http://google.com');
```

For raw HTML:

```php
$conv->addPage('<html><body><h1>Raw HTML</h1></body></html>');
```

#### PDF options

###### Format
Format is optional. Supported formats are: 'A3', 'A4', 'A5', 'Legal', 'Letter', 'Tabloid'.
```php
$conv->format('A4');
```
###### Margin
Margin is optional and defaults to 1cm.

```php
array('margin' => '1cm')
```

###### Orientation
Orientation ('portrait', 'landscape') is optional and defaults to 'portrait'.

```php
$conv->portrait();
$conv->landscape();
```
###### zoomFactor
zoomFactor is optional and defaults to 1 (where 1 is 100% zoom).

```php
array('zoomfactor' => 1)
```

###### Custom width and height
Custom dimension is optional. Supported formats are `cm`, `px` and `in`.

```php
array('width' => '900px', height => '700px')
```

##### Example

```php
$options = [
  'format' => 'A4',
  'zoomfactor' => 1,
  'orientation' => 'portrait',
  'margin' => '1cm'
];

$conv->setPdfOptions($options);
// or
$conv->pdfOptions($options);
// or
$conv->toPdf($options);

```
#### Image options

###### Width
Width is optional and defaults to 1280px (720p) and only intergers are accepted.

```php
$conv->width(1280);
```

###### Height
Height is optional and only integers are accepted.

```php
$conv->height(1280);
```

**Note:** If only width is given full webpage will be rendered. However, if both width and height is given the image will be clipped to the given width and height.

###### Quality
Quality is optional and defaults to 80. The quality must be between 1-100.

```php
$conv->quality(90);
```

#####Example

```php
$options = [
  'width' => 1280,
  'quality' => 90
];

$conv->setImageOptions($options);
// or
$conv->imageOptions($options);
// or
$conv->toPng($options);
// or
$conv->toJpg($options);
// or
$conv->toGif($options);
```

## Credits

- [Anam Hossain](https://github.com/anam-hossain)
- [All Contributors](https://github.com/anam-hossain/phantommagick/graphs/contributors)

## License

The MIT License (MIT). Please see [LICENSE](http://opensource.org/licenses/MIT) for more information.
