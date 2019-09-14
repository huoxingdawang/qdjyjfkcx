<?php
	$port=$argv[1];
	$sno=$argv[2];
	$ch=curl_init('http://27.221.57.108:'.$port.'/app/bankCardInfoFull');
	curl_setopt($ch,CURLOPT_HEADER, 0);    
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
	curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0)));	
	$get_sorce=curl_exec($ch);
	echo $get_sorce;