<?php
	function jry_wb_aes_encode($data,$key,$vi)
	{
		return base64_encode(openssl_encrypt($data,"AES-128-CBC",$key,OPENSSL_RAW_DATA,$vi));
	}
	
?>
