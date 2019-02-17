<?php

return [
	'defaults' => [
		'adapter' => env('DEFAULT_ADAPTER', null),
	],
	'adapters' => [
		's3'        => [
			'key'        => env('S3_KEY', ''),
			'secret'     => env('S3_SECRET', ''),
			'region'     => env('S3_REGION', 'cn-north-1'),
			'bucket'     => env('S3_BUCKET', ''),
			'pathPrefix' => env('S3_PATH_PREFIX', ''),
		],
		'rackspace' => [
			'username'  => env('RACKSPACE_USERNAME', ''),
			'password'  => env('RACKSPACE_PASSWORD', ''),
			'container' => env('RACKSPACE_CONTAINER', ''),
			'region'    => env('RACKSPACE_REGION', 'HKG'),
		],
		'qiniu'     => [
			'access_key' => env('QINIU_ACCESS_KEY', ''),
			'secret_key' => env('QINIU_SECRET_KEY', ''),
			'bucket'     => env('QINIU_BUCKET', ''),
			'domain'     => env('QINIU_DOMAIN', ''),
		],
	],
];