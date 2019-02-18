<?php

namespace iBrand\PhantomMagick;

use iBrand\PhantomMagick\Adapters\AmazonS3Adapter;
use iBrand\PhantomMagick\Adapters\QiniuAdapter;
use Illuminate\Support\ServiceProvider;

class ConverterServiceProvider extends ServiceProvider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([__DIR__ . '/config.php' => config_path('ibrand/phantommagick.php')], 'config');
		}
	}

	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/config.php', 'ibrand.phantommagick'
		);

		$this->app->bind('converter', function () {
			return new \iBrand\PhantomMagick\Converter;
		});

		$this->app->alias(AmazonS3Adapter::class, AmazonS3Adapter::DRIVER);

		$this->app->alias(QiniuAdapter::class, QiniuAdapter::DRIVER);
	}
}
