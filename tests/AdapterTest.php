<?php
namespace Anam\PhantomMagick\Test;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Anam\PhantomMagick\Adapter;

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
