<?php

namespace iBrand\PhantomMagick\Adapters;

use OpenCloud\OpenStack;
use OpenCloud\Rackspace;

class RackspaceAdapter extends BaseAdapter
{
	const DRIVER = 'rackspace';

	public function pick()
	{
		$config = config('ibrand.phantommagick.adapters.' . self::DRIVER);

		$client = new OpenStack(Rackspace::US_IDENTITY_ENDPOINT, [
			'username' => $config['username'],
			'password' => $config['password'],
		]);

		$store     = $client->objectStoreService('cloudFiles', $config['region']);
		$container = $store->getContainer($config['container']);

		return new \League\Flysystem\Rackspace\RackspaceAdapter($container);
	}
}