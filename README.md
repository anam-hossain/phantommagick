# PhantomMagick

PhantomMagick provides a simple API to ease HTML to PDF or HTML to Image conversion. This package is very handy for generating Invoices or capturing website screenshot.

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

Multipage Pdf limitations: 
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

To display in the browser:

```php
$conv->download('google.pdf', true);
```

###Save to cloud

PhantomMagick leverage [Flysystem](http://flysystem.thephpleague.com) to save file in cloud. 
PhantomMagick currently support:
- Amazon S3
- Dropbox
- Rackspace

#####Amazon S3

```php
use Anam\PhantomMagick\Converter;
use Aws\S3\S3Client;

$client = S3Client::factory(array(
    'key'    => 'AWS_KEY',
    'secret' => 'AWS_SECRET',
    'region' => 'ap-southeast-2'
));

$conv = new Converter();
$conv->adapter($client, 'bucket-name')
    ->acl('public')
    ->source('http://google.com')
    ->toPdf()
    ->save('google.pdf');
```

#####Dropbox
Add the following requirements to your composer.json file.
```json
{
  "require" : {
    "dropbox/dropbox-sdk": "1.1.*",
    "league/flysystem-dropbox": "~1.0"
  }
}
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

#####Rackspace
Add the following requirements to your composer.json file.
```json
{
  "require" : {
    "rackspace/php-opencloud": "1.12.1",
    "league/flysystem-rackspace": "~1.0"
  }
}
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

###Settings

####Global options
######Binary
Path or filename of the `phantomjs` shell command. Default is `phantomjs`. However, if you installed the `Phantomjs` binary using `anam/phantomjs-linux-x86-binary` package, you do not need to provide any `binary` path as PhantomMagick is smart enough to detect the binary path for you. If you installed the PhantomJS using different composer package and PhantomJS is not executable by using `phantomjs` shell command, You have to set the binary path.

```php
$conv->setBinary('/phantomjs/binary/path/phantomjs');
```

####PDF options

######Format

Format is optional. Supported formats are: 'A3', 'A4', 'A5', 'Legal', 'Letter', 'Tabloid'.
```php
$conv->format('A4');
```
######Margin
Margin is optional and defaults to 1cm.
```php
array('margin' => '1cm')
```
######Orientation
Orientation ('portrait', 'landscape') is optional and defaults to 'portrait'.
```php
$conv->portrait();
$conv->landscape();
```
######zoomFactor
zoomFactor is optional and defaults to 1. i.e. 100% zoom.
```php
array('zoomfactor' => 1)
```
######Custom width and height
Custom dimension is optional. Supported formats are cm, px, in.
```php
array('width' => '900px', height => '700px')
```
#####Example

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
####Image options

######Width
Width is optional and defaults to 1280px (720p) and only number is accepted.
```php
$conv->width(1280);
```
######Height
Height is optional and only number is accepted.
```php
$conv->height(1280);
```
**Note:** If only width is given full webpage will render. However, if both width and height is given, the image will be clipped to given width and height

######Quality
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