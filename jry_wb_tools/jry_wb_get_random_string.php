<?php
	function jry_wb_get_random_string($length)
	{
		$srcstr = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		$code='';
		mt_srand();
		for ($i=0;$i<$length; $i++) 
			$code.=$srcstr[mt_rand(0,strlen($srcstr)-1)];
		return $code;
	}
?>