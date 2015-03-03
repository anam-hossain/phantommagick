<?php
namespace Anam\Html2PdfConverter\Storage;

use League\Flysystem\AwsS3v2\AwsS3Adapter;
use Aws\S3\S3Client;

class AmazonS3 extends AwsS3Adapter
{
    public function __construct(S3Client $client, $bucket, $prefix)
    {
        parent::__construct($client, $bucket, $prefix);
    }
}
