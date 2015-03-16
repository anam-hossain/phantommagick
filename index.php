<?php

include "vendor/autoload.php";

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Anam\Html2PdfConverter\Converter;
use Dropbox\Client;
use League\Flysystem\Dropbox\DropboxAdapter;
use OpenCloud\OpenStack;
use OpenCloud\Rackspace;
use League\Flysystem\Rackspace\RackspaceAdapter;

$config = include(dirname(__FILE__) . '/.env.php');

//Amazon S3

$client = S3Client::factory(array(
    'key'    => $config['AWS_KEY'],
    'secret' => $config['AWS_SECRET'],
    'region' => 'ap-southeast-2'
));

$conv = new Converter();
$conv->adapter($client, 'phantom-magick')
    ->acl('public')
    ->source('http://joinform.com.au')
    ->toPng(['width' => '1200'])
    ->save();

//Local
//https://identity.api.rackspacecloud.com/v2.0/

$conv = new Converter();
$conv->source('/Applications/MAMP/htdocs/html2pdfconverter/tests/test_page.html')
    ->toPng(['width' => '1200'])
    ->save('/var/folders/k2/tp8p_jz5677bfyq904m7twl00000gn/Thello.png');

die(var_dump($conv));

$client = new OpenStack(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => $config['RACKSPACE_USERNAME'],
    'password' => $config['RACKSPACE_PASSWORD']
));

$store = $client->objectStoreService('cloudFiles', 'SYD');
$container = $store->getContainer('phantom-magick');


$conv = new Converter();
$conv->adapter($container)
    ->source('https://google.com')
    ->toPdf()
    ->save();

die(var_dump($container));
$filesystem = new Filesystem(new RackspaceAdapter($container));

die(var_dump($client instanceof Dropbox\Client));


$client = new Client($config['DROPBOX_TOKEN'], $config['DROPBOX_APP']);
//$filesystem = new Filesystem(new DropboxAdapter($client));

$conv = new Converter();
$conv->adapter($client)
    ->source('https://google.com')
    ->toPdf()
    ->save();

die(var_dump($client instanceof Dropbox\Client));

$client = S3Client::factory(array(
    'key'    => $config['AWS_KEY'],
    'secret' => $config['AWS_SECRET'],
    'region' => 'ap-southeast-2'
));


// $conv = new Converter();
// $conv->source(dirname(__FILE__) . '/hello.html')
//     ->toPdf()
//     //->toPdf()
//     ->save(dirname(__FILE__) . '/hello.pdf');

$conv = new Converter();
$conv->adapter($client, 'phantom-magick')
    ->acl('public')
    ->source('http://joinform.com.au')
    ->toPng(['width' => '1200'])
    ->download();

//die(var_dump($conv));

die(var_dump($client instanceof Aws\S3\S3Client));
//die(dump($client));
// $adapter = new AwsS3Adapter($client, $config['AWS_BUCKET'], 'optional-prefix');

//$adapter = new AwsS3Adapter($client, $config['AWS_BUCKET']);
//die(dump($adapter));

//use Anam\Html2PdfConverter\Converter;

//Converter::make('http://google.com')->toPdf();
$conv = new Converter();

$conv->setBinary('phantomjs')
	->source('http://code-chunk.com')
    ->toPng(['width' => 1440])
	//->toPdf(['width' => '900px', 'height' => '700px'])
	//->save(dirname(__FILE__) . '/image2.pdf');
	->download();


die("pdf generated");
