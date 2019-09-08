<?php
/*
SELECT `qiafan`.`student`.`xjh`,`qiafan`.`student`.`name`,`qiafan`.`logs`.`amount`,`qiafan`.`logs`.`time`,`qiafan`.`logs`.`consumtype`,`qiafan`.`logs`.`mercname` FROM `qiafan`.`logs`,`qiafan`.`student` WHERE `student`.`xjh`=`logs`.`xjh` ORDER BY `time` ASC
SELECT `qiafan`.`student`.`xjh`,`qiafan`.`student`.`name`,`qiafan`.`logs`.`amount`,`qiafan`.`logs`.`time`,`qiafan`.`logs`.`consumtype`,`qiafan`.`logs`.`mercname` FROM `qiafan`.`logs`,`qiafan`.`student` WHERE `student`.`xjh`=`logs`.`xjh` AND CAST(xjh/100000 AS SIGNED)%100=2 ORDER BY `time` ASC
*/
	include_once('main.php');
	include_once('jry_wb_tools/jry_wb_includes.php');
	date_default_timezone_set('Asia/Shanghai');	
	function db_insert($conn,$stu,$rand=false)
	{
		$delta_log=0;
		if($stu->xjh!=''&&$stu->name!=''&&$stu->card_id!=''&&$stu->amount!='')
		{
			$st = $conn->prepare("INSERT INTO qiafan.student (`xjh`,`name`,`card_id`,`amount`,`lasttime`,`lasttime_query`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE lasttime=IF(amount=?,lasttime,?),amount=?,lasttime_query=?;");
			$st->bindValue(1,$stu->xjh);
			$st->bindValue(2,$stu->name);
			$st->bindValue(3,$stu->card_id);
			$st->bindValue(4,$stu->amount);
			$st->bindValue(5,jry_wb_get_time());
			$st->bindValue(6,jry_wb_get_time());
			$st->bindValue(7,$stu->amount);
			$st->bindValue(8,jry_wb_get_time());
			$st->bindValue(9,$stu->amount);
			$st->bindValue(10,jry_wb_get_time());
			$st->execute();
			foreach($stu->logs as $log)
			{
				$st=$conn->prepare("INSERT INTO qiafan.logs (`xjh`,`amount`,`time`,`consumtype`,`mercname`,`lasttime`,`tmp`) VALUES (?,?,?,?,?,?,0);");
				$st->bindValue(1,$stu->xjh);
				$st->bindValue(2,$log->amount);
				$st->bindValue(3,$log->time);
				$st->bindValue(4,$log->consumtype);
				$st->bindValue(5,$log->mercname);
				$st->bindValue(6,jry_wb_get_time());
				$st->execute();
				$delta_log+=$aaaa=$st->rowCount();
				if($aaaa==0&&$rand)
				{
					$st=$conn->prepare("INSERT INTO qiafan.logs (`xjh`,`amount`,`time`,`consumtype`,`mercname`,`lasttime`,`tmp`) VALUES (?,?,?,?,?,?,?) ");
					$st->bindValue(1,$stu->xjh);
					$st->bindValue(2,$log->amount);
					$st->bindValue(3,$log->time);
					$st->bindValue(4,$log->consumtype);
					$st->bindValue(5,$log->mercname);
					$st->bindValue(6,jry_wb_get_time());
					$st->bindValue(7,rand(1,1000));
					$st->execute();					
					$delta_log+=$aaaa=$st->rowCount();
				}
				else if($aaaa==0)
					return $delta_log;					
//				if($aaaa==0)echo "\t".$stu->name."\t".$log->time."\t".$log->consumtype."\t".$log->amount."元\t".$log->mercname."\t".$st->errorInfo()[2]."\n";
			}
		}
		return $delta_log;
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