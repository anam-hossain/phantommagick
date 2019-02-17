<?php

namespace iBrand\PhantomMagick\Adapters;

class QiniuAdapter extends BaseAdapter
{
	const DRIVER = 'qiniu';

	public function pick()
	{
		$config = config('ibrand.phantommagick.adapters.' . self::DRIVER);

		return new \Overtrue\Flysystem\Qiniu\QiniuAdapter($config['access_key'], $config['secret_key'], $config['bucket'], $config['domain']);
	}
}