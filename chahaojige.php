<?php
	include_once('main.php');
	$data=json_decode($argv[1]);
	foreach($data as $one)
		printt(chaxun($one->sno));