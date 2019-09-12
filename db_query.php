<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();
//	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE lasttime_query<? ORDER BY xjh ASC");
	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE lasttime_query<? ORDER BY lasttime_query ASC");
//	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE CAST(xjh/100000 AS SIGNED)%100=25 AND lasttime_query<? ORDER BY xjh ASC");
//	$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE xjh=2017370201880930204 AND lasttime_query<? ORDER BY xjh ASC");
	$st->bindValue(1,$argv[1]==''?jry_wb_get_time():$argv[1]);
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." will be updated\n";
	$delta_log=0;
	$ps=10;
	$i=0;
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}
	$start=msectime();
	foreach($data as $one)
	{
		$pn=1;
		$buff=$ps;
		while($buff>=$ps)
		{
			$buf=chaxun($one['xjh'],$ps,$pn,getport($one['school']));
			$delta_log+=$buff=db_insert($conn,$buf);
			echo $buf->xjh."\t".$buf->name.jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
			$pn++;
		}
		$i++;
		echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\t","green").jry_wb_php_cli_color(((msectime()-$start)/1000)."s\t","red").jry_wb_php_cli_color(((msectime()-$start)/($i/$cnt)*(1-($i/$cnt))/1000)."s left\n","light_green");
	}
	echo $delta_log." logs insert\n";
	$st=$conn->prepare("UPDATE `qiafan`.`student` SET `qiafan`.`student`.`amount_logs`=ROUND(IFNULL((SELECT SUM(`qiafan`.`logs`.`amount`) FROM `qiafan`.`logs` WHERE `qiafan`.`logs`.`xjh`=`qiafan`.`student`.`xjh`),0),2);");
	$st->execute();