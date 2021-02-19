<?php
	for($port=1;$port<=65536;$port++)
	{
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/trades');
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_TIMEOUT,1); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>'123','userType'=>0)));		
		echo $port;
		echo "\n";
		echo curl_exec($ch);
		echo "\n";
	}