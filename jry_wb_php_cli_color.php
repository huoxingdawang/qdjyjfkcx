<?php
	function jry_wb_php_cli_color($text,$color)
	{
		$_colors = array( 
			'light_red'=>"[1;31m",
			'light_green'=>"[1;32m",
			'yellow'=>"[1;33m",
			'light_blue'=>"[1;34m",
			'magenta'=>"[1;35m",
			'light_cyan'=>"[1;36m",
			'white'=>"[1;37m",
			'normal'=>"[0m",
			'black'=>"[0;30m",
			'red'=>"[0;31m",
			'green'=>"[0;32m",
			'brown'=>"[0;33m",
			'blue'=>"[0;34m",
			'cyan'=>"[0;36m",
			'bold'=>"[1m",
			'underescore'=>"[4m",
			'reverse'=>"[7m",
		); 
		$out = $_colors[$color]; 
		if($out == "")
			$out="[0m";
		return chr(27).$out.$text.chr(27)."[0m"; 
	}	