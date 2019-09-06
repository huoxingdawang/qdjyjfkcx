<?php
	include_once('main.php');
	include_once('db_insert.php');
	function jry_wb_php_cli_color($text,$color)
	{
		$_colors = array( 
			'light_red'=>"[1;31m",
			'light_green'=>"[1;32m",
			'yellow'=>"[1;33m",
			'light_blue'=>"[1;34m",
			'magenta'=>"[1;35m",
			'light_cyan'=>"[1;36m",
			'white'=>"[1;37m",
			'normal'=>"[0m",
			'black'=>"[0;30m",
			'red'=>"[0;31m",
			'green'=>"[0;32m",
			'brown'=>"[0;33m",
			'blue'=>"[0;34m",
			'cyan'=>"[0;36m",
			'bold'=>"[1m",
			'underescore'=>"[4m",
			'reverse'=>"[7m",
		); 
		$out = $_colors[$color]; 
		if($out == "")
			$out="[0m";
		return chr(27).$out.$text.chr(27)."[0m"; 
	}	
	
	$conn=jry_wb_connect_database();
	$st=$conn->prepare("SELECT `xjh` FROM `qiafan`.`student` WHERE lasttime_query<? ORDER BY xjh ASC");
//	$st=$conn->prepare("SELECT `xjh` FROM `qiafan`.`student` WHERE CAST(xjh/100000 AS SIGNED)%100=2 AND lasttime_query<? ORDER BY xjh ASC");
	$st->bindValue(1,$argv[1]==''?jry_wb_get_time():$argv[1]);
	$st->execute();
	$data=$st->fetchAll();
	echo ($cnt=count($data))." will be updated\n";
	$delta_log=0;
	$ps=10;
	$i=0;
	foreach($data as $one)
	{
		$pn=1;
		$buff=$ps;
		while($buff>=$ps)
		{
			$buf=chaxun($one['xjh'],$ps,$pn);
			$delta_log+=$buff=db_insert($conn,$buf);
			echo $buf->xjh."\t".$buf->name.jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
			$pn++;
		}
		$i++;
		echo jry_wb_php_cli_color(round(($i/$cnt)*100,4)."%\n","green");
	}
	echo $delta_log." logs insert";	