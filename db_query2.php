<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();
	$delta_log=1;
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}
	$run=true;
//	pcntl_signal(SIGINT,function(){global $run;$run=false;echo "Stop get\n";});
	while($delta_log&&$run)
	{
		$start=msectime();
		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE lasttime_query<? ORDER BY lasttime_query ASC LIMIT 200");
		$st->bindValue(1,$argv[1]==''?jry_wb_get_time():$argv[1]);
		$st->execute();
		$data=$st->fetchAll();
		echo ($cnt=count($data))." will be updated\n";
		$delta_log=0;
		$ps=10;
		$i=0;
		foreach($data as $one)
		{
			$sstart=msectime();
			$pn=1;
			$buff=$ps;
			echo '学生'.$one['xjh']."\t";
			$stu=get_student_basic($one['xjh'],getport($one['school']));
			if($stu->code)
			{
				db_insert_student($conn,$stu);echo"姓名:".$stu->name."\t卡号:".$stu->card_id."\t余额:".$stu->amount."\t";
	//			db_insert_extern($conn,($ex=get_student_extern($one['xjh'],getport($one['school']))));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday;
				echo "\n";
				while($buff>=$ps)
				{
					$delta_log+=$buff=db_insert_logs($conn,$one['xjh'],($logs=get_student_logs($one['xjh'],getport($one['school']),$ps,$pn)));
					echo $stu->xjh."\t".$stu->name.jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff.'('.count($logs).')'." logs insert\n",'cyan');	
					$pn++;
				}
			}
			else
				echo "\n";			
			$i++;
			$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `qiafan`.`student`.`xjh`=?;");
			$st->bindValue(1,$one['xjh']);
			$st->execute();
			$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_abs`=ROUND(IFNULL((SELECT SUM(ABS(`qiafan`.`logs`.`amount`)) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `qiafan`.`student`.`xjh`=?;");
			$st->bindValue(1,$one['xjh']);
			$st->execute();
			echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\t","green").jry_wb_php_cli_color(((msectime()-$start)/1000)."s\t","red").jry_wb_php_cli_color(((msectime()-$start)/($i/$cnt)*(1-($i/$cnt))/1000)."s left","light_green");
			echo "\n";
			usleep((1000-(msectime()-$sstart))*1000);
			if(!$run)
				break;
		}
		echo $delta_log." logs insert\n";
		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
		$st->execute();
		echo jry_wb_php_cli_color(count($st->fetchAll())." error logs\n",'red');		
		echo ((msectime()-$start)/1000)." S taken\n";
		echo 'Sleep at '.jry_wb_get_time()."\n";
		echo 'Will weak up after  '.(0.5*$cnt+60-(msectime()-$start)/1000)." S\n";
		sleep((0.5*$cnt+60-(msectime()-$start)/1000));
	}
	