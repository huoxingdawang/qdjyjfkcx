<?php
	error_reporting(0);
	function chaxun($sno,$ps=1000,$pn=1)
	{
		$data=(object)[];
		$data->xjh=$sno;
		if		($sno/100000%100==1)	$port=10013	;//一中
		else if	($sno/100000%100==2)	$port=10009	;//二中
		else if	($sno/100000%100==15)	$port=9000	;//十五中
		else if	($sno/100000%100==9)	$port=10021	;//九中
		else							$port=10009	;
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
			$data->logs=[];
			$ch=curl_init('http://27.221.57.108:'.$port.'/app/trades');
			curl_setopt($ch,CURLOPT_HEADER, 0);    
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0,'pageNum'=>$pn,'pageSize'=>$ps)));	
			$get_sorce=curl_exec($ch);	
			$card=json_decode($get_sorce);
			foreach($card->data->trades as $trade)
				$data->logs[]=(object)array('time'=>$trade->time,'consumtype'=>$trade->consumType,'amount'=>(float)($trade->amount),'type'=>$trade->type,'mercname'=>$trade->mercName,'mercaccount'=>$trade->mercAccount);
		}
		return $data;
	}
	function printt($data,$logs=true)
	{
		if($data->code)
		{
			echo '学生'.$data->xjh."\t姓名:".$data->name."\t卡号:".$data->card_id."\t余额:".$data->amount."\n";
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