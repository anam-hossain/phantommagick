<?php
namespace Anam\Html2PdfConverter\Storage;

use League\Flysystem\Adapter\Local as Adapter;
use Aws\S3\S3Client;

class LocalFile extends AwsS3Adapter
{
    public function __construct(S3Client $s3)
    {
        parent::__construct($s3);
    }
}
