<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');	
	$conn=jry_wb_connect_database();	
	$ps=1000;
	for($i=$argv[3];$i<=$argv[4];$i++)
	{
		$pn=1;
		$buff=$ps;
		if($argv[1]==2017||$argv[1]==2018)
			$xjh=$argv[1].'37020188'.str_pad($argv[2],2,"0",STR_PAD_LEFT).'30'.str_pad($i,3,"0",STR_PAD_LEFT);
		else if($argv[1]==2019&&$argv[2]==1)
			$xjh='005'.str_pad($argv[2],2,"0",STR_PAD_LEFT).$argv[1].'00'.str_pad($i,3,"0",STR_PAD_LEFT);
		else if($argv[1]==2019&&$argv[2]==2)
			$xjh='19'.str_pad(((int)($i/60/7))+1,1,'0',STR_PAD_LEFT).'000'.str_pad(((int)($i/60%7))+1,1,'0',STR_PAD_LEFT).str_pad($i%60+1,2,'0',STR_PAD_LEFT);
		else if($argv[1]==2020&&$argv[2]==1)
			$xjh='005'.str_pad($argv[2],2,"0",STR_PAD_LEFT).$argv[1].'00'.str_pad($i,3,"0",STR_PAD_LEFT);
		else if($argv[1]==2020&&$argv[2]==2)
			$xjh='20'.str_pad(((int)($i/60/7))+1,1,'0',STR_PAD_LEFT).'000'.str_pad(((int)($i/60%7))+1,1,'0',STR_PAD_LEFT).str_pad($i%60+1,2,'0',STR_PAD_LEFT);

		echo $i.':学生'.$xjh."\t";
		$stu=get_student_basic($xjh,getport($argv[2]));
		if($stu->code)
		{
			echo"姓名:".$stu->name."\t卡号:".$stu->card_id."\t余额:".$stu->amount."\t";
			db_insert_student($conn,$stu);
			db_insert_extern($conn,($ex=get_student_extern($xjh,getport($argv[2]))));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday."\n";
			while($buff>=$ps)
			{
				$delta_log+=$buff=db_insert_logs($conn,$xjh,(get_student_logs($xjh,getport($argv[2]),$ps,$pn)));
				echo "\t\t".jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
				$pn++;
			}
		}
		echo "\n";
	}