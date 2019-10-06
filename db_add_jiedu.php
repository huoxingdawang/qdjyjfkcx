<?php
	include_once('main.php');
	include_once('db_insert.php');
	include_once('jry_wb_php_cli_color.php');	
	$conn=jry_wb_connect_database();	
	$ps=1000;
	$schools=[1,2,15,9,97,39,19,6];
	for($i=$argv[3];$i<=$argv[4];$i++)
	{
		$pn=1;
		$port=0;
		$stu=NULL;
		$buff=$ps;
		if($argv[1]==2017||$argv[1]==2018)
			$xjh=$argv[1].'37020188'.str_pad($argv[2],2,"0",STR_PAD_LEFT).'30'.str_pad($i,3,"0",STR_PAD_LEFT);
		else if($argv[1]==2019)
			$xjh='005'.str_pad($argv[2],2,"0",STR_PAD_LEFT).$argv[1].'00'.str_pad($i,3,"0",STR_PAD_LEFT);
		echo '学生'.$xjh."\t";
		$st=$conn->prepare("SELECT `xjh`,`school` FROM `qiafan`.`student` WHERE xjh=?");
		$st->bindValue(1,$xjh);
		$st->execute();
		if(count($st->fetchAll())==1)
			echo jry_wb_php_cli_color('has','green');
		else
		{
			foreach($schools as $school)
				if(($stu=get_student_basic($xjh,$port=getport($school)))->code)
					break;
			if($stu->code)
			{
				echo 'from '.getschool($xjh).'to '.$school."\n";
				db_insert_student($conn,$stu,$school);echo"姓名:".$stu->name."\t卡号:".$stu->card_id."\t余额:".$stu->amount."\t";
				db_insert_extern($conn,($ex=get_student_extern($xjh,$port)));echo "银行卡号:".$ex->bankcard."\t身份证号:".$ex->china_id_card."\t性别:".($ex->sex?'男':'女')."\t生日:".$ex->birthday."\n";
				while($buff>=$ps)
				{
					$delta_log+=$buff=db_insert_logs($conn,$xjh,(get_student_logs($xjh,$port,$ps,$pn)));
					echo "\t\t".jry_wb_php_cli_color("\tpage:".$pn,'yellow')."\t".jry_wb_php_cli_color($buff." logs insert\n",'cyan');	
					$pn++;
				}
			}
		}
		echo "\n";
	}