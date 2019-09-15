<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');	
	$conn=jry_wb_connect_database();	
	$ps=1000;
	for($i=$argv[2];$i<=800;$i++)
	{
		$pn=1;
		$buff=$ps;
		$xjh=$argv[1].'370201889730'.str_pad($i,3,"0",STR_PAD_LEFT);
		echo '学生'.$xjh."\t";
		$stu=get_student_basic($xjh);
		if($stu->code)
		{
			db_insert_student($conn,$stu);echo"姓名:".$stu->name."\t卡号:".$stu->card_id."\t余额:".$stu->amount."\t";
			db_insert_extern($conn,($ex=get_student_extern($xjh)));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday."\n";
			while($buff>=$ps)
			{
				$delta_log+=$buff=db_insert_logs($conn,$xjh,(get_student_logs($xjh,0,$ps,$pn)));
				echo "\t\t".jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
				$pn++;
			}
		}
		echo "\n";
	}