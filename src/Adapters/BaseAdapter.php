<?php

namespace Anam\PhantomMagick\Adapters;

use Anam\PhantomMagick\Contracts\AdapterContract;

abstract class BaseAdapter implements AdapterContract
{
	public $driver = 'local';

	const DRIVER = '';

	public function __construct()
	{
		$this->setDriver(static::DRIVER);
	}

	public function setDriver($driver)
	{
		$this->driver = $driver;
	}

	public function getDriver()
	{
		return $this->driver;
	}

	abstract public function pick();
}