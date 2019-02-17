<?php

namespace iBrand\PhantomMagick\Adapters;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class AmazonS3Adapter extends BaseAdapter
{
	const DRIVER = 's3';

	public function pick()
	{
		$config = config('ibrand.phantommagick.adapters.' . self::DRIVER);

		$credentials = new Credentials($config['key'], $config['secret']);
		$client      = new S3Client([
			'credentials' => $credentials,
			'region'      => $config['region'],
			'version'     => 'latest',
		]);

		return new \League\Flysystem\AwsS3v3\AwsS3Adapter($client, $config['bucket'], $config['pathPrefix']);
	}
}