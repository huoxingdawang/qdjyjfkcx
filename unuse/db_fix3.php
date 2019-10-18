<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();	
	$redis = new Redis;
	$redis->connect("127.0.0.1",6379);	
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();
	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE `amount`!=`amount_logs` ORDER BY rand()");
	$st->execute();
	$data=$st->fetchAll();
	foreach($data as $one)
		$redis->rpush('task',json_encode($one));
	