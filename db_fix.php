<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();	
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();
	$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." student's logs will be fixed\n";
	$delta_log=0;
	$ps=100;
	$i=0;
	foreach($data as $one)
	{
		echo 'fixing '.$one['xjh']."\n";
		$st=$conn->prepare("DELETE  FROM `qiafan`.`logs` WHERE `xjh`=?;");
		$st->bindValue(1,$one['xjh']);
		$st->execute();
		echo $st->rowCount().' logs deleted'."\n";
		$pn=1;
		$buff=$ps;
		while($buff>=$ps)
		{
			$buf=chaxun($one['xjh'],$ps,$pn);
			$delta_log+=$buff=db_insert($conn,$buf,true);
			echo $buf->xjh."\t".$buf->name.jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
			$pn++;
//			if($buff!=$ps)printt($buf);
		}
		$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `xjh`=?;");
		$st->bindValue(1,$one['xjh']);
		$st->execute();
		$i++;
		echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\n","green");
	}
	echo $delta_log." logs insert\n";
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_abs`=ROUND(IFNULL((SELECT SUM(ABS(`qiafan`.`logs`.`amount`)) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();
	$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." unfixed logs\n";	
	