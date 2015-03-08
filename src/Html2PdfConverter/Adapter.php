<?php
namespace Anam\Html2PdfConverter;

use Exception;

class Adapter
{
    /**
     * The driver of the Filesystem
     *
     * @var string
     */
    protected $driver = 'local';

    /**
     * Filesystem client. S3|Dropbox|Rackspace.
     *
     * @var mixed
     */
    protected $client;

    /**
     * Client related options
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor
     *
     * @param mixed $client
     * @param array  $args
     */
    public function __construct($client, array $args = array())
    {
        $this->client = $client;
        $this->args = $args;
    }

    /**
     * Set the Filesystem driver
     *
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the driver
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Determine which Flysystem adapter required
     *
     * @return mixed
     */
    public function pick()
    {
        // Amazon S3
        if ($this->client instanceof \Aws\S3\S3Client) {
            $this->setDriver('s3');

            return $this->s3();
        }

        // Dropbox
        if ($this->client instanceof \Dropbox\Client) {
            $this->setDriver('dropbox');

            return $this->dropbox();
        }

        // Rackspace cloudFiles
        if ($this->client instanceof \OpenCloud\ObjectStore\Resource\Container) {
            $this->setDriver('rackspace');

            return $this->rackspace();
        }
    }

    /**
     * Create a new AwsS3Adapter instance.
     *
     * @return mixed
     */
    public function s3()
    {
        $options = [
            // Amazon S3 api version
            'version'   => 2,
            'prefix'    => null
        ];

        if (! isset($this->args[0])) {
            throw new Exception('S3 Bucket name is required');
        }

        if (isset($this->args[1])) {
            if (! is_array($this->args[1])) {
                throw new Exception('Options must be an array');
            }

            $options = array_merge($options, $this->args[1]);
        }

        $bucket = $this->args[0];

        if ($options['version'] >= 3 || $options['version'] == 'latest') {
            return new \League\Flysystem\AwsS3v3\AwsS3Adapter($this->client, $bucket, $options['prefix']);
        }

        return new \League\Flysystem\AwsS3v2\AwsS3Adapter($this->client, $bucket, $options['prefix']);
    }

    /**
     * Create a new DropboxAdapter instance.
     *
     * @return \League\Flysystem\Dropbox\DropboxAdapter
     */
    public function dropbox()
    {
        $prefix = null;

        if (isset($this->args[0])) {
            $prefix = $this->args[0];
        }

        return new \League\Flysystem\Dropbox\DropboxAdapter($this->client, $prefix);
    }

    /**
     * Create a new RackspaceAdapter instance.
     *
     * @return \League\Flysystem\Rackspace\RackspaceAdapter
     */
    public function rackspace()
    {
        return new \League\Flysystem\Rackspace\RackspaceAdapter($this->client);
    }
}
