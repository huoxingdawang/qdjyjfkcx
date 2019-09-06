<?php
	include_once('main.php');
	$filename='result.json';
	if($argv[1]!='')
		$filename=$argv[1];
	$file=fopen($filename, "r") or die("Unable to open file ".$filename);
	$data=json_decode(fread($file,filesize($filename)));
	fclose($file);
	foreach($data as $buf)
		printt($buf);