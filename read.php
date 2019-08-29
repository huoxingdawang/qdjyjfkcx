<?php
	include_once('main.php');
	$file=fopen("result.json", "r") or die("Unable to open file!");
	$data=json_decode(fread($file,filesize("result.json")));
	fclose($file);
	foreach($data as $buf)
		printt($buf);	