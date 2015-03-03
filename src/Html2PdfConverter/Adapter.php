<?php
namespace Anam\Html2PdfConverter;

use Anam\Html2PdfConverter\Storage\AmazonS3;
use Anam\Html2PdfConverter\Storage\AmazonS3V3;
use Anam\Html2PdfConverter\Storage\Dropbox;
use Anam\Html2PdfConverter\Storage\LocalFile;
use Anam\Html2PdfConverter\Storage\Rackspace;

class Adapter
{
    public function pick($client, array $arguments = array())
    {

    }

    public function s3()
    {

    }

    public function dropbox()
    {

    }
}
