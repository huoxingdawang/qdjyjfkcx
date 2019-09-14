<?php
	include_once('main.php');
	$filename='result.json';
	if($argv[2]!='')
		$filename=$argv[2];
	$file=fopen($filename, "r") or die("Unable to open file!");
	$data=json_decode(fread($file,filesize($filename)));
	fclose($file);
	foreach($data as $buf)
		if($buf->code&&$buf->name==$argv[1])
			printt($buf);