<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();
	$delta_log=0;
	$ps=50;
	$i=0;
	$per_time=2;	
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}
	do
	{
		$start=msectime();
//		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE `xjh`=2017370201880630232");
		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE lasttime_query<? AND `ignore`=0 ORDER BY lasttime_query ASC");$st->bindValue(1,$argv[1]==''?jry_wb_get_time():$argv[1]);
//		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE birthday IS NULL");
//		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE lasttime_query<? AND school=9 ORDER BY lasttime_query ASC");
//		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE school=1 ORDER BY lasttime_query");
//		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE xjh=2018370201880230037 AND lasttime_query<? ORDER BY xjh ASC");
		$st->execute();
		$data=$st->fetchAll();
		echo ($cnt=count($data))." will be updated\n";
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
//				db_insert_extern($conn,($ex=get_student_extern($one['xjh'],getport($one['school']))));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'�?:'�?)."\t生日:".$ex->birthday;
				echo "\n";
///*
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
//*/				
			}
			else
				echo "\n";
			$i++;
			echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\t","green").jry_wb_php_cli_color(((msectime()-$start)/1000)."s\t","red").jry_wb_php_cli_color(((msectime()-$start)/($i/$cnt)*(1-($i/$cnt))/1000)."s left","light_green")."\n";
			usleep(($per_time*1000-(msectime()-$sstart))*1000);
		}
		echo $delta_log." logs insert\n";		
	}while(0);