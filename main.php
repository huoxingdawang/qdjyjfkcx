<?php
	$sno=$argv[1];
	$time=$argv[2];
	$ch=curl_init('http://27.221.57.108:10009/app/cardInfo');
	curl_setopt($ch,CURLOPT_HEADER, 0);    
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
	curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0)));	
	$get_sorce=curl_exec($ch);	
	$stu=json_decode($get_sorce);
	if($stu->data==NULL)
		echo '学生'.$sno.'没有绑定卡，嘤嘤嘤'."\n\n\n";
	else
	{
		echo '学生:'.$stu->data->name."\t卡号:".$stu->data->cardNo."\t余额:".$stu->data->amount."\n";
		$ch=curl_init('http://27.221.57.108:10009/app/trades');
		curl_setopt($ch,CURLOPT_HEADER, 0);    
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0,'pageNum'=>1,'pageSize'=>1000,'timeType'=>$time)));	
		$get_sorce=curl_exec($ch);	
		$card=json_decode($get_sorce);
		echo $time.'交易记录如下:(共'.count($card->data->trades)."条)\n";
		foreach($card->data->trades as $trade)
			var_dump($trade);
	}