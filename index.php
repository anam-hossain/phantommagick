<?php

include "vendor/autoload.php";

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Anam\Html2PdfConverter\Converter;

$config = include(dirname(__FILE__) . '/.env.php');

$client = S3Client::factory(array(
    'key'    => $config['AWS_KEY'],
    'secret' => $config['AWS_SECRET'],
    'region' => 'ap-southeast-2'
));

$conv = new Converter();
$conv->adapter($client);

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
