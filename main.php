<?php
	error_reporting(0);
	include_once('jry_wb_tools/jry_wb_test_china_id_card.php');
//	$daili="218.75.102.198:8000";
//	$daili="149.129.98.81:80";
	$daili="127.0.0.1:1080";
	function getport($school)
	{
		if	($school==1)		return 10013	;//一中
		else if	($school==2)	return 10009	;//二中
		else if	($school==15)	return 9000		;//十五中
		else if	($school==9)	return 10021	;//九中
		else if	($school==97)	return 10005	;//
		else if	($school==39)	return 10017	;//39中
		else if	($school==06)	return 10025	;//六中
		else if	($school==25)	return 10009	;//19中
		else if	($school==19)	return 10029	;//19中
		else					return 0;		
	}
	function getschool($sno)
	{
		if($sno>2017000000000000000)
			return $sno/100000%100;
		else if($sno>=501201900001&&$sno<=501201900999)
			return $sno/1000000000%10;
		else if($sno>=191000101&&$sno<=199000999)
			return 2;
		return 0;
	}
	function getsnostr($sno)
	{
		if($sno>2017000000000000000)
			return (string)$sno;
		else if($sno>=501201900001&&$sno<=501201900999)
			return  str_pad($sno,14,"0", STR_PAD_LEFT); 
		else if($sno>=191000101&&$sno<=199000999)
			return (string)$sno;
		return (string)$sno;
	}
	function get_student_basic($sno,$port=0)
	{
		global $daili;
		$port=$port?$port:getport(getschool($sno));
		$data=(object)[];
		$data->xjh=$sno;
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/cardInfo');
		if($daili!='')
			curl_setopt($ch,CURLOPT_PROXY,$daili);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>getsnostr($sno),'userType'=>0)));	
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
		global $daili;
		$port=$port?$port:getport(getschool($sno));
		$logs=[];
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/trades');
		if($daili!='')
			curl_setopt($ch,CURLOPT_PROXY,$daili);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>getsnostr($sno),'userType'=>0,'pageNum'=>$pn,'pageSize'=>$ps)));	
		$get_sorce=curl_exec($ch);	
		$card=json_decode($get_sorce);
		foreach($card->data->trades as $trade)
			$logs[]=(object)array('time'=>$trade->time,'consumtype'=>$trade->consumType,'amount'=>(float)($trade->amount),'type'=>$trade->type,'mercname'=>$trade->mercName==''?'N/A':$trade->mercName,'mercaccount'=>$trade->mercAccount);
		return $logs;
	}
	function get_student_extern($sno,$port=0)
	{
		global $daili;
		$port=$port?$port:getport(getschool($sno));
		$data=(object)[];
		$data->xjh=$sno;
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/bankCardInfoFull');
		if($daili!='')
			curl_setopt($ch,CURLOPT_PROXY,$daili);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>getsnostr($sno),'userType'=>0)));	
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
		$port=$port?$port:getport(getschool($sno));
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
	if(($_SERVER['PHP_SELF'])==(end(explode('\\',__FILE__)))||$_SERVER['PHP_SELF']==__FILE__)
		printt(chaxun($argv[1],1000,1,getport((int)$argv[2])));