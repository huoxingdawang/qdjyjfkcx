<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');
	$conn=jry_wb_connect_database();
	$string='';
	if($argv[1]=='name')
	{
		$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `name`=?");
		$st->bindValue(1,$argv[2]);
		$string=$argv[2];
	}
	else if($argv[1]=='xjh')
	{
		$st=$conn->prepare("SELECT * FROM `qiafan`.`student` WHERE `xjh`=?");
		$st->bindValue(1,$argv[2]);		
		$string=$argv[2];
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
		echo "姓名:".$one['name']."\t学籍号:".$one['xjh']."\t余额:".$one['amount']."\t交易绝对值:".$one['amount_abs']."\n";
		echo "银行卡号:".$one['bankcard']."\t身份证号:".$one['china_id_card']."\t性别:".($one['sex']?'男':'女')."\t生日:".$one['birthday']."\n";
		echo "最后更新时间:".$one['lasttime']."\t最后请求时间:".$one['lasttime_query']."\t在读学校:".$one['school']."\t学籍学校:".($one['xjh']/100000%100)."\n";
		$st=$conn->prepare("SELECT * FROM `qiafan`.`logs` WHERE `xjh`=? ORDER BY `time` DESC");
		$st->bindValue(1,$one['xjh']);
		$st->execute();
		$logs=$st->fetchAll();		
		echo '交易记录如下:(共'.count($logs)."条)\n";
		foreach($logs as $log)
			echo $log['time']."\t".$log['consumtype']."\t".$log['amount']."元\t".$log['mercname']."\n";
		echo "\n";
	}