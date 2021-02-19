<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();	
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();
	$st=$conn->prepare("SELECT `xjh`,ROUND(`amount`-`amount_logs`,2) AS cha FROM `qiafan`.`student` WHERE `amount`!=`amount_logs` ORDER BY xjh");
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." student's logs will be fixed\n";
	foreach($data as $one)
	{
		if(!file_exists('run'))
			break;
		echo 'fixing '.$one['xjh']."\n";
		$st=$conn->prepare("INSERT INTO qiafan.logs (`xjh`,`amount`,`time`,`consumtype`,`mercname`,`tmp`) VALUES (?,?,NOW(),'离线饭卡调整','N/A',0);");
		$st->bindValue(1,$one['xjh']);
		$st->bindValue(2,$one['cha']);
		$st->execute();
		$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `xjh`=?;");
		$st->bindValue(1,$one['xjh']);
		$st->execute();
		
	
	}
	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." unfixed logs\n";	
	