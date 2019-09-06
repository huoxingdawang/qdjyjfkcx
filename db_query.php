<?php
	include_once('main.php');
	include_once('db_insert.php');
	$conn=jry_wb_connect_database();
//	$st=$conn->prepare("SELECT `xjh` FROM `qiafan`.`student`");
	$st=$conn->prepare("SELECT `xjh` FROM `qiafan`.`student` WHERE CAST(xjh/100000 AS SIGNED)%100=2");
	$st->execute();
	$delta_log=0;
	$ps=10;
	foreach($st->fetchAll() as $one)
	{
		$pn=1;
		$buff=$ps;
		while($buff>=$ps)
		{
			$buf=chaxun($one['xjh'],$ps,$pn);
			$delta_log+=$buff=db_insert($conn,$buf);
			echo $buf->name."\tpage:".$pn."\t".$buff." logs insert\n";	
			$pn++;
		}
	}
	echo $delta_log." logs insert";	