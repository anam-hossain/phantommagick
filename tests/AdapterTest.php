<?php
namespace Anam\Html2PdfConverter\Test;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Anam\Html2PdfConverter\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;

    public function setUp()
    {

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAmazonS3AdapterWillThrowInvalidArgumentException()
    {
        throw new InvalidArgumentException("Bucket is required");
    }
}
