<?php
	echo ($xjh=$argv[1])."\n"; 
	for($port=9997;$port<0XFFFF;$port++)
	{
		echo "try ".$port."\n";
		$ch=curl_init('http://27.221.57.108:'.$port.'/app/cardInfo');
		curl_setopt($ch,CURLOPT_HEADER, 0);
		 curl_setopt($ch, CURLOPT_TIMEOUT,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$xjh,'userType'=>0)));	
		$get_sorce=curl_exec($ch);	
		if($get_sorce!='')
		{
			echo $get_sorce;
			$stu=json_decode($get_sorce);
			if($stu->data!=NULL)
				return ;
		}
	}
	