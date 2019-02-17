<?php

namespace iBrand\PhantomMagick\Test;

use iBrand\PhantomMagick\Converter;
use Orchestra\Testbench\TestCase;

abstract class BaseTest extends TestCase
{
	protected $converter;

	/**
	 * set up test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->converter = new Converter();
	}

	/**
	 * @param \Illuminate\Foundation\Application $app
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('ibrand.phantommagick', require __DIR__ . '/../src/config.php');
	}

	protected function getPackageProviders($app)
	{
		return [
			\iBrand\PhantomMagick\ConverterServiceProvider::class,
		];
	}
}