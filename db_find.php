<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();
	$string='';
	$start=2;
	if($argv[$start]=='all')
	{
		$st=$conn->prepare("SELECT * FROM `qiafan`.`student`");
		$string='in database';
	}
	else if($argv[$start]=='name')
	{
		$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `name`=?");
		$st->bindValue(1,$argv[$start+1]);
		$string=$argv[$start+1];
	}
	else if($argv[$start]=='xjh')
	{
		$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `xjh`=?");
		$st->bindValue(1,$argv[$start+1]);		
		$string=$argv[$start+1];
	}
	else if($argv[$start]=='birthday')
	{
		$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `birthday`=?");
		$st->bindValue(1,$argv[$start+1]);		
		$string=' people who born on '.$argv[$start+1];
	}	
	else
	{
		echo "ERR\n";
		return ;
	}
	$st->execute();
	$data=$st->fetchAll();
	if($data[0]['xjh']=='')
	{
		echo "No data\n";
		return ;
	}
	echo count($data).' '.$string." found.\n\n";
	foreach($data as $one)
	{
		if(strpos($argv[1],'-data')!==FALSE)
		{
			echo "姓名:".jry_wb_php_cli_color($one['name'],'yellow')."\t学籍号:".jry_wb_php_cli_color($one['xjh'],'light_blue')."\t余额:".jry_wb_php_cli_color($one['amount'],'magenta')."\t交易绝对值:".$one['amount_abs']."\n";
			echo "银行卡号:".$one['bankcard']."\t身份证号:".jry_wb_php_cli_color($one['china_id_card'],'cyan')."\t性别:".jry_wb_php_cli_color(($one['sex']?'男':'女'),'green')."\t生日:".$one['birthday']."\n";
			echo "最后更新时间:".$one['lasttime']."\t最后请求时间:".$one['lasttime_query']."\t在读学校:".$one['school']."\t学籍学校:".($one['xjh']/100000%100)."\n";
		}
		if(strpos($argv[1],'-logs')!==FALSE)
		{
			$st=$conn->prepare("SELECT * FROM `qiafan`.`logs` WHERE `xjh`=? ORDER BY `time` DESC");
			$st->bindValue(1,$one['xjh']);
			$st->execute();
			$logs=$st->fetchAll();		
			echo '交易记录如下:(共'.jry_wb_php_cli_color(count($logs),'light_red')."条)\n";
			foreach($logs as $log)
				echo jry_wb_php_cli_color($one['name'],'yellow')."\t".$log['time']."\t".$log['consumtype']."\t".$log['amount']."元\t".$log['mercname']."\n";
		}
		sleep(1);
		echo "\n";
	}