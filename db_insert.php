<?php
/*
SELECT `qiafan`.`student`.`xjh`,`qiafan`.`student`.`name`,`qiafan`.`logs`.`amount`,`qiafan`.`logs`.`time`,`qiafan`.`logs`.`consumtype`,`qiafan`.`logs`.`mercname` FROM `qiafan`.`logs`,`qiafan`.`student` WHERE `student`.`xjh`=`logs`.`xjh` ORDER BY `time` ASC
SELECT `qiafan`.`student`.`xjh`,`qiafan`.`student`.`name`,`qiafan`.`logs`.`amount`,`qiafan`.`logs`.`time`,`qiafan`.`logs`.`consumtype`,`qiafan`.`logs`.`mercname` FROM `qiafan`.`logs`,`qiafan`.`student` WHERE `student`.`xjh`=`logs`.`xjh` AND CAST(xjh/100000 AS SIGNED)%100=2 ORDER BY `time` ASC
*/
	include_once('main.php');
	include_once('jry_wb_tools/jry_wb_includes.php');
	date_default_timezone_set('Asia/Shanghai');	
	function db_insert_student($conn,$stu,$school=0)
	{
		if($stu->xjh!==NULL&&$stu->name!==NULL&&$stu->card_id!==NULL&&$stu->amount!==NULL)
		{
			$st = $conn->prepare("INSERT INTO qiafan.student (`xjh`,`name`,`card_id`,`amount`,`lasttime_query`,`school`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE amount=?,lasttime_query=?;");
			$st->bindValue(1,$stu->xjh);
			$st->bindValue(2,$stu->name);
			$st->bindValue(3,$stu->card_id);
			$st->bindValue(4,$stu->amount);
			$st->bindValue(5,jry_wb_get_time());
			$st->bindValue(6,($school==0?getschool($stu->xjh):$school));
			$st->bindValue(7,$stu->amount);
			$st->bindValue(8,jry_wb_get_time());
			$st->execute();
		}			
	}
	function db_insert_logs($conn,$xjh,$logs,$rand=false,$check_point='1926-08-17 00:00:00')
	{
		$delta_log=0;
		foreach($logs as $log)
		{
			if($log->time<$check_point)
				return $delta_log;
			$st=$conn->prepare("INSERT INTO qiafan.logs (`xjh`,`amount`,`time`,`consumtype`,`mercname`,`tmp`) VALUES (?,?,?,?,?,0);");
			$st->bindValue(1,$xjh);
			$st->bindValue(2,$log->amount);
			$st->bindValue(3,$log->time);
			$st->bindValue(4,$log->consumtype);
			$st->bindValue(5,$log->mercname);
			$st->execute();
			$delta_log+=$aaaa=$st->rowCount();
			if($aaaa==0&&$rand)
			{
				$st=$conn->prepare("INSERT INTO qiafan.logs (`xjh`,`amount`,`time`,`consumtype`,`mercname`,`tmp`) VALUES (?,?,?,?,?,?) ");
				$st->bindValue(1,$xjh);
				$st->bindValue(2,$log->amount);
				$st->bindValue(3,$log->time);
				$st->bindValue(4,$log->consumtype);
				$st->bindValue(5,$log->mercname);
				$st->bindValue(6,rand(1,1000));
				$st->execute();					
				$delta_log+=$aaaa=$st->rowCount();
			}
			else if($aaaa==0)
				return $delta_log;					
		}		
		return $delta_log;					
	}
	function db_insert_extern($conn,$stu)
	{
		if($stu->xjh!==NULL&&$stu->bankcard!==NULL&&$stu->china_id_card!==NULL&&$stu->birthday!==NULL&&$stu->sex!==NULL&&$stu->birthday!==false&&$stu->sex!==false)
		{
			$st = $conn->prepare("UPDATE qiafan.student SET bankcard=?,china_id_card=?,birthday=?,sex=? WHERE xjh=?");
			$st->bindValue(1,$stu->bankcard);
			$st->bindValue(2,$stu->china_id_card);
			$st->bindValue(3,$stu->birthday);
			$st->bindValue(4,$stu->sex,PDO::PARAM_INT);
			$st->bindValue(5,$stu->xjh);
			$st->execute();
			/*if($st->rowCount()==0)
			{
				var_dump($stu);
				var_dump($st->errorInfo());
				exit();
			}*/
		}	
		//else var_dump($stu);
	}	
	function db_insert($conn,$stu,$rand=false)
	{
		db_insert_student($conn,$stu);
		db_insert_extern($conn,$stu);
		return db_insert_logs($conn,$stu->logs,$rand);
	}
	if(($_SERVER['PHP_SELF'])==(end(explode('\\',__FILE__))))
	{
		$conn=jry_wb_connect_database();		
		$filename='result.json';
		if($argv[1]!='')
			$filename=$argv[1];
		$file=fopen($filename, "r") or die("Unable to open file ".$filename);
		$data=json_decode(fread($file,filesize($filename)));
		fclose($file);
		$delta_log=0;
		foreach($data as $buf)
		{
			$delta_log+=$buff=db_insert($conn,$buf);
			echo $buf->name."\t".$buff."\n";
		}
		echo $delta_log." logs insert";
	}