<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}
	function creat_child($callback)
	{
		global $child_list;
		global $pid;
		echo "fork one\n";
/*		$pid=pcntl_fork();
		if ($pid == 0)
		{
			$pid=posix_getpid();
			$callback();
			exit();
		}
		else
		{
			$child_list[$pid]=1;
			return $pid;
		}*/	
	}
	
	$child_list=[];
	$redis = new Redis;
	$conn=jry_wb_connect_database();	
	$redis->connect("127.0.0.1",6379);
	$per_time=0.5;
	$ps=10;				
	while($redis->lLen('task'))
		if($one=json_decode($redis->lpop('task'),true))
		{
			$pn=1;
			$buff=$ps;						
			echo '学生'.$one['xjh']."\t";
			$stu=get_student_basic($one['xjh'],getport($one['school']));
			if($stu->code)
			{
				db_insert_student($conn,$stu);echo"姓名:".$stu->name."\t余额:".$stu->amount."\t";
//							db_insert_extern($conn,($ex=get_student_extern($one['xjh'],getport($one['school']))));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday;
				echo "\n";
				while($buff>=$ps)
				{
					$delta_log+=$buff=db_insert_logs($conn,$one['xjh'],($logs=get_student_logs($one['xjh'],getport($one['school']),$ps,$pn)));
					echo jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff.'('.count($logs).')'." logs insert\n",'cyan');	
					$pn++;
				}
			}
			else
				echo "\n";
			$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `qiafan`.`student`.`xjh`=?;");
			$st->bindValue(1,$one['xjh']);
			$st->execute();
			$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_abs`=ROUND(IFNULL((SELECT SUM(ABS(`qiafan`.`logs`.`amount`)) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2) WHERE `qiafan`.`student`.`xjh`=?;");
			$st->bindValue(1,$one['xjh']);
			$st->execute();
			usleep(($per_time*1000-(msectime()-$sstart))*1000);						
		}