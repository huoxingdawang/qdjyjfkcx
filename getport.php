<?php
	$data=[];
//	$ports=[80,85,161,1000,4430,7005,8091,9000,9001,9002,9003,10001,10002,10003,10005,10006,10007,10009,10010,10011,10013,10014,10015,10017,10018,10019,10021,10022,10023,10025,10026,10027,10029,10030,10031,22345,51111];
	$ports=[9000,10005,10009,10013,10017,10021,10025,10029];
	for($i=1;$i<=25;$i++)
	{
		echo ($xjh='201737020188'.str_pad($i,2,'0',STR_PAD_LEFT).'30003')."\n"; 
		foreach($ports as $port)
		{
			echo "\ntry ".$port."\n";
			$ch=curl_init('http://27.221.57.108:'.$port.'/app/cardInfo');
			curl_setopt($ch,CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT,10);
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
					$data[]=['port'=>$port,'school'=>$i];
			}
		}
	}
	echo "\n";
	var_dump($data);