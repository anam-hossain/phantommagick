<?php

include "vendor/autoload.php";

$tmpfname = tempnam(sys_get_temp_dir(), "PDF");

$handle = fopen($tmpfname, "w");
fwrite($handle, "writing to tempfile");
//fclose($handle);

// do here something
var_dump($tmpfname, basename($tmpfname));
unlink($tmpfname);

die("Exit");
//echo "Home page";

class Runner {
	/**
	 * @var string Path to phantomjs binary
	 **/
	private $bin = '/home/vagrant/phantomjs/bin/phantomjs';
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
	public function __construct($bin = null, $debug = null) {
		if($bin !== null) $this->bin = $bin;
		if($debug !== null) $this->debug = $debug;
	} // end func: __construct
	
	public function execute($script) {
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
	} // end func: execute
} // end class: Runner


$phantomjs = new Runner;
//die(dirname(__FILE__));
//var_dump($phantomjs);
$file = dirname(__FILE__) . '/examples/rasterize.js';
var_dump($file);
// if(file_exists($file)) {
// 	echo "File exist";
// 	echo "\<br> $file";
// } else {
// 	echo "File not exist";
// 	echo $file;
// }
//$result = $phantomjs->execute($file, dirname(__FILE__) . "/hello.html", dirname(__FILE__) . "/hello.pdf");
shell_exec('/home/vagrant/html2pdfconverter/bin/phantomjs /home/vagrant/html2pdfconverter/src/html2pdfconverter/scripts/rasterize.js "http://code-chunk.com" /home/vagrant/html2pdfconverter/image.pdf');
//shell_exec('phantomjs /Applications/MAMP/htdocs/phantomjs/examples/rasterize.js "https://studentvip.com.au" /Applications/MAMP/htdocs/phantomjs/image.pdf');

//echo dirname(__FILE__) . "/hello.html";
//var_dump($result);