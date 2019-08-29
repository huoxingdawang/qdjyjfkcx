<?php
	include_once('main.php');
	$data=json_decode($argv[1]);
	var_dump($argv[1]);
	foreach($data as $one)
		chaxun($one->sno,$one->time);