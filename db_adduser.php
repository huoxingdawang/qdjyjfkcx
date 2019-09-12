<?php
	include_once('main.php');
	include_once('db_insert.php');
	$conn=jry_wb_connect_database();	
	$ps=1000;
	for($i=1;$i<=800;$i++)
	{
		$pn=1;
		$buff=$ps;
		echo ($xjh='2018370201881930'.str_pad($i,3,"0",STR_PAD_LEFT))."\n";
		while($buff>=$ps)
		{
			$buf=chaxun($xjh,$ps,$pn);
			printt($buf);
			$buff=db_insert($conn,$buf);
			$pn++;
		}
	}