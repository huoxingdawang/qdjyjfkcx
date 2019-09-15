<?php
	error_reporting(0);
	include_once('jry_wb_tools/jry_wb_test_china_id_card.php');
	function getport($school)
	{
		if	($school==1)		return 10013	;//一中
		else if	($school==2)	return 10009	;//二中
		else if	($school==15)	return 9000		;//十五中
		else if	($school==9)	return 10021	;//九中
		else if	($school==97)	return 10005	;//
		else if	($school==39)	return 10017	;//39中
		else if	($school==06)	return 10025	;//六中
		else if	($school==19)	return 10029	;//19中
		else					return 0;		
	}
	function get_student_basic($sno,$port=0)
	{
		$port=$port?$port:getport($sno/100000%100);
		$data=(object)[];
		$data->xjh=$sno;
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/cardInfo');
		curl_setopt($ch,CURLOPT_HEADER, 0);    
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0)));	
		$get_sorce=curl_exec($ch);	
		$stu=json_decode($get_sorce);
		if($stu->data==NULL)
			$data->code=false;
		else
		{
			$data->code=true;
			$data->name=$stu->data->name;
			$data->card_id=$stu->data->cardNo;
			$data->amount=$stu->data->amount;
		}
		return $data;
	}
	function get_student_logs($sno,$port=0,$ps=1000,$pn=1)
	{
		$port=$port?$port:getport($sno/100000%100);
		$logs=[];
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/trades');
		curl_setopt($ch,CURLOPT_HEADER, 0);    
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0,'pageNum'=>$pn,'pageSize'=>$ps)));	
		$get_sorce=curl_exec($ch);	
		$card=json_decode($get_sorce);
		foreach($card->data->trades as $trade)
			$logs[]=(object)array('time'=>$trade->time,'consumtype'=>$trade->consumType,'amount'=>(float)($trade->amount),'type'=>$trade->type,'mercname'=>$trade->mercName==''?'N/A':$trade->mercName,'mercaccount'=>$trade->mercAccount);
		return $logs;
	}
	function get_student_extern($sno,$port=0)
	{
		$port=$port?$port:getport($sno/100000%100);
		$data=(object)[];
		$data->xjh=$sno;
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/bankCardInfoFull');
		curl_setopt($ch,CURLOPT_HEADER, 0);    
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0)));	
		$get_sorce=curl_exec($ch);
		$stu=json_decode($get_sorce);
		if($stu->data==NULL)
			return NULL;
		$data->bankcard=$stu->data->bankCard;
		$data->china_id_card=$stu->data->bankIdNumber;
		$data->sex=jry_wb_get_sex_by_china_id_card($data->china_id_card);
		$data->birthday=jry_wb_get_birthday_by_china_id_card($data->china_id_card);
		return $data;
	}
	function chaxun($sno,$ps=1000,$pn=1,$port=0)
	{
		$port=$port?$port:getport($sno/100000%100);
		$data=get_student_basic($sno,$port);
		$data->logs=get_student_logs($sno,$port,$ps,$pn);
		return (object)array_merge((array)$data,(array)get_student_extern($sno,$port));
	}
	function printt($data,$logs=true)
	{
		if($data->code)
		{
			echo '学生'.$data->xjh."\t姓名:".$data->name."\t卡号:".$data->card_id."\t余额:".$data->amount."\t银行卡号:".$data->bankcard."\t身份证号:".$data->china_id_card."\t性别:".($data->sex?'男':'女')."\t生日:".$data->birthday."\n";
			if(!$logs)
				return;
			echo '交易记录如下:(共'.count($data->logs)."条)\n";
			foreach($data->logs as $log)
				echo "\t".$data->name."\t".$log->time."\t".$log->consumtype."\t".$log->amount."元\t".$log->mercname."\n";
		}
		else
			echo '学生'.$data->xjh."\t没有绑定卡，嘤嘤嘤\n";
		
	}
	if(($_SERVER['PHP_SELF'])==(end(explode('\\',__FILE__))))
		printt(chaxun($argv[1]));