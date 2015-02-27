<?php

include "vendor/autoload.php";

use Anam\Html2PdfConverter\Converter;

//Converter::make('http://google.com')->toPdf();
$conv = new Converter();

$conv->setBinary('phantomjs')
	->source('http://google.com')
	->toPdf(['width' => '900px', 'height' => '700px'])
	->save(dirname(__FILE__) . '/image.pdf');


die("pdf generated");

// die(sys_get_temp_dir() . uniqid(rand(), true) . '.html');
//die(dirname(__FILE__));
$converter = new Converter();

$converter->addPage('/Applications/MAMP/htdocs/html2pdfconverter/site/index-5.html');
// $converter->addPage('http://zurb.com/ink/downloads/templates/sidebar-hero.html');
// $converter->addPage('http://zurb.com/ink/downloads/templates/sidebar-hero.html');

$path = $converter->save('zyz');

$exec = 'phantomjs /Applications/MAMP/htdocs/html2pdfconverter/src/html2pdfconverter/scripts/rasterize.js ' .  $path . ' /Applications/MAMP/htdocs/html2pdfconverter/image.pdf';

var_dump($path, $exec);
shell_exec($exec);

//die(var_dump(implode('', $converter->pages)));

//die(dirname(__FILE__));
//var_dump($phantomjs);
//$file = dirname(__FILE__) . '/examples/rasterize.js';
//var_dump($file);
// if(file_exists($file)) {
// 	echo "File exist";
// 	echo "\<br> $file";
// } else {
// 	echo "File not exist";
// 	echo $file;
// }
//$result = $phantomjs->execute($file, dirname(__FILE__) . "/hello.html", dirname(__FILE__) . "/hello.pdf");
//shell_exec('/home/vagrant/html2pdfconverter/bin/phantomjs /home/vagrant/html2pdfconverter/src/html2pdfconverter/scripts/rasterize.js "http://code-chunk.com" /home/vagrant/html2pdfconverter/image.pdf');
//shell_exec('phantomjs /Applications/MAMP/htdocs/phantomjs/examples/rasterize.js "https://studentvip.com.au" /Applications/MAMP/htdocs/phantomjs/image.pdf');

//echo dirname(__FILE__) . "/hello.html";
//var_dump($result);