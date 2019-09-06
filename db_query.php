<?php
	include_once('main.php');
	include_once('db_insert.php');
	$conn=jry_wb_connect_database();
	$st=$conn->prepare("SELECT `xjh` FROM `qiafan`.`student` WHERE lasttime_query<? ORDER BY xjh ASC");
//	$st=$conn->prepare("SELECT `xjh` FROM `qiafan`.`student` WHERE CAST(xjh/100000 AS SIGNED)%100=2 AND lasttime_query<? ORDER BY xjh ASC");
	$st->bindValue(1,$argv[1]==''?jry_wb_get_time():$argv[1]);
	$st->execute();
	$data=$st->fetchAll();
	echo count($data)." will be updated\n";
	$delta_log=0;
	$ps=10;
	foreach($data as $one)
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