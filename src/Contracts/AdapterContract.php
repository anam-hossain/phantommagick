<?php

namespace iBrand\PhantomMagick\Contracts;

interface AdapterContract
{
	public function pick();

	public function getDriver();

	public function setDriver($driver);
}