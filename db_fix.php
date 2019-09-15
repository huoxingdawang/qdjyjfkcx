<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();	
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();
	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." student's logs will be fixed\n";
	$delta_log=0;
	$ps=100;
	$i=0;
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}	
	$start=msectime();	
	foreach($data as $one)
	{
		echo 'fixing '.$one['xjh']."\n";
		$st=$conn->prepare("DELETE  FROM `qiafan`.`logs` WHERE `xjh`=?;");
		$st->bindValue(1,$one['xjh']);
		$st->execute();
		echo $st->rowCount().' logs deleted'."\n";
		$pn=1;
		$buff=$ps;
		db_insert_student($conn,($stu=get_student_basic($one['xjh'],getport($one['school']))));echo '学生'.$stu->xjh."\t姓名:".$stu->name."\t卡号:".$stu->card_id."\t余额:".$stu->amount."\t";
//		db_insert_extern($conn,($ex=get_student_extern($one['xjh'],getport($one['school']))));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday;
		echo "\n";
		while($buff>=$ps)
		{
			$delta_log+=$buff=db_insert_logs($conn,$one['xjh'],(get_student_logs($one['xjh'],getport($one['school']),$ps,$pn)),true);
			echo $stu->xjh."\t".$stu->name.jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
			$pn++;
		}
		$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `xjh`=?;");
		$st->bindValue(1,$one['xjh']);
		$st->execute();
		$i++;
		echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\t","green").jry_wb_php_cli_color(((msectime()-$start)/1000)."s\t","red").jry_wb_php_cli_color(((msectime()-$start)/($i/$cnt)*(1-($i/$cnt))/1000)."s left\n","light_green");
	}
	echo $delta_log." logs insert\n";
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_abs`=ROUND(IFNULL((SELECT SUM(ABS(`qiafan`.`logs`.`amount`)) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();
	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." unfixed logs\n";	
	