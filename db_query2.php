<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();
	$delta_log=1;
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}
	$run=true;
	$per_time=2;
	$big_time=60;
	$start_date=$argv[2]==''?jry_wb_get_time():$argv[2];
	echo 'Limit time '.$start_date."\n";
	$st=$conn->prepare("SELECT count(`xjh`) FROM `qiafan`.`student` WHERE lasttime_query<?");
	$st->bindValue(1,$start_date);
	$st->execute();
	echo jry_wb_php_cli_color($st->fetchAll()[0][0]." stu left\n",'light_blue');		
	//	pcntl_signal(SIGINT,function(){global $run;$run=false;echo "Stop get\n";});
	while($delta_log&&$run)
	{
		$start=msectime();
		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE lasttime_query<? AND `amount`=`amount_logs` ORDER BY rand() ASC LIMIT ?,50");
		$st->bindValue(1,$start_date);
		$st->bindValue(2,$argv[1]??0);
		$st->execute();
		$data=$st->fetchAll();
		echo ($cnt=count($data))." will be updated\n";
		$delta_log=0;
		$ps=10;
		$i=0;
		foreach($data as $one)
		{
			if(!file_exists('run'))
				exit();
			$sstart=msectime();
			$pn=1;
			$buff=$ps;
			echo '学生'.$one['xjh']."\t";
			$stu=get_student_basic($one['xjh'],getport($one['school']));
			$maxtime="1926-08-17 00:00:00";
			if($stu->code)
			{
				db_insert_student($conn,$stu);echo"姓名:".$stu->name."\t余额:".$stu->amount."\t";
	//			db_insert_extern($conn,($ex=get_student_extern($one['xjh'],getport($one['school']))));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday;
				echo "\n";
				while($buff>=$ps)
				{
					$delta_log+=$buff=db_insert_logs($conn,$one['xjh'],($logs=get_student_logs($one['xjh'],getport($one['school']),$ps,$pn)));
					$maxtime=max($maxtime,$logs[0]->time);
					echo jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff.'('.count($logs).')'." logs insert\n",'cyan');	
					$pn++;
				}
				$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `qiafan`.`student`.`xjh`=?;");
				$st->bindValue(1,$one['xjh']);
				$st->execute();
				$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`lasttime`=IF(?>`qiafan`.`student`.`lasttime`,?,`qiafan`.`student`.`lasttime`) WHERE `qiafan`.`student`.`xjh`=?;");
				$st->bindValue(1,$maxtime);
				$st->bindValue(2,$maxtime);
				$st->bindValue(3,$one['xjh']);
				$st->execute();			
				$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`check_point`=IF(`qiafan`.`student`.`amount`=`qiafan`.`student`.`amount_logs`,`qiafan`.`student`.`lasttime`,`qiafan`.`student`.`check_point`) WHERE `qiafan`.`student`.`xjh`=?;");
				$st->bindValue(1,$one['xjh']);
				$st->execute();
				$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_abs`=ROUND(IFNULL((SELECT SUM(ABS(`qiafan`.`logs`.`amount`)) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `qiafan`.`student`.`xjh`=?;");
				$st->bindValue(1,$one['xjh']);
				$st->execute();				
			}
			else
				echo "\n";
			$i++;
			echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\t","green").jry_wb_php_cli_color(((msectime()-$start)/1000)."s\t","red").jry_wb_php_cli_color(((msectime()-$start)/($i/$cnt)*(1-($i/$cnt))/1000)."s left","light_green")."\n";
			usleep(($per_time*1000-(msectime()-$sstart))*1000);
			if(!$run)
				break;
		}
		echo $delta_log." logs insert\n";
		$st=$conn->prepare("SELECT count(`xjh`) FROM `qiafan`.`student` WHERE `amount`!=`amount_logs`");
		$st->execute();
		echo jry_wb_php_cli_color($st->fetchAll()[0][0]." error logs\n",'red');		
		$st=$conn->prepare("SELECT count(`xjh`) FROM `qiafan`.`student` WHERE lasttime_query<? ORDER BY lasttime_query ASC LIMIT 200");
		$st->bindValue(1,$start_date);
		$st->execute();
		echo jry_wb_php_cli_color($st->fetchAll()[0][0]." stu left\n",'light_blue');		
		echo ((msectime()-$start)/1000)." S taken\n";
		echo 'Sleep at '.jry_wb_get_time()."\n";
		echo 'Will weak up after  '.($per_time*$cnt+$big_time-(msectime()-$start)/1000)." S\n";
		sleep(($per_time*$cnt+$big_time-(msectime()-$start)/1000));
	}
	