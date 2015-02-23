<?php
namespace Anam\Html2PdfConverter;

class Runner 
{
	/**
	 * @var string Path to phantomjs binary.
	 **/
	protected $binary = 'phantomjs';
	
	/**
	 * @var bool If true, all Command output is returned verbatim
	 **/
	private $debug = true;

	/**
	 * Constructor
	 *
	 * @param string Path to phantomjs binary
	 * @param bool Debug mode
	 * @return void
	 **/
	public function __construct($binary = null, $debug = null) 
	{
		if($binary !== null) $this->bin = $binary;
		if($debug !== null) $this->debug = $debug;
	} // end func: __construct
	
	public function run($script) 
	{
		// Escape
		$args = func_get_args();
		$cmd = escapeshellcmd("{$this->bin} " . implode(' ', $args));
		if($this->debug) $cmd .= ' 2>&1';
		// Execute
		$result = shell_exec($cmd);
		if($this->debug) return $result;
		if($result === null) return false;
		// Return
		if(substr($result, 0, 1) !== '{') return $result; // not JSON
		$json = json_decode($result, $as_array = true);
		if($json === null) return false;
		return $json;
	}
}