<?php
	function jry_wb_get_browser($db=false,$user_agent=NULL)
	{
		if($user_agent===NULL)
			$user_agent=$_SERVER["HTTP_USER_AGENT"];
		if(strpos($user_agent,"MSIE 6.0")!==false)
			$type=3;
		else if(strpos($user_agent,"MSIE 7.0")!==false)
			$type=2;
		else if(strpos($user_agent,"MSIE 8.0")!==false)
			$type=1;
		else if(strpos($user_agent,"MSIE 9.0")!==false)
			$type=18;
		else if(strpos($user_agent,"MSIE 10.0")!==false)
			$type=17;
		else if(strpos($user_agent,"Trident/7.0; rv:11.0")!==false)
			$type=16;
		else if(strpos($user_agent,"Edge"))
			$type=9;
		else if(strpos($user_agent,"MQQBrowser")!==false)
			$type=12;
		else if(strpos($user_agent,"QQBrowser")!==false)
			$type=15;	
		else if(strpos($user_agent,"FxiOS")!==false)
			$type=13;
		else if(strpos($user_agent,"CriOS")!==false)
			$type=14;				
		else if(strpos($user_agent,"Opera")||strpos($user_agent,"OPR")!==false)
			$type=8;	
		else if(strpos($user_agent,"Firefox17")!==false)
			$type=4;
		else if(strpos($user_agent,"Firefox16")!==false)
			$type=5;
		else if(strpos($user_agent,"Firefox")!==false)
			$type=10;			
		else if(strpos($user_agent,"Chrome")!==false)
			$type=6;
		else if(strpos($user_agent,"Safari")!==false)
			$type=7;
		else if(strpos($user_agent,"MQBHD")!==false)
			$type=11;			
		else
			$type=0;
		if($db)
			return $type;
		else 
			return jry_wb_get_browser_from_database($type);
	}
	function jry_wb_get_browser_from_database($data)
	{
		if($data==1)
			return "IE8";
		else if($data==2)
			return "IE7";
		else if($data==3)
			return "IE6";
		else if($data==4)
			return "Firefox17";
		else if($data==5)
			return "Firefox16";
		else if($data==6)
			return "Chrome";
		else if($data==7)
			return "Safari";
		else if($data==8)
			return "Opera";	
		else if($data==9)
			return "Edge";
		else if($data==10)
			return "Firefox";			
		else if($data==11)
			return 'Mobile QQ browser HD';
		else if($data==12)
			return 'Mobile QQ browser';
		else if($data==13)
			return 'Firefox for iOS';
		else if($data==14)
			return 'Chrome for iOS';
		else if($data==15)
			return 'QQ browser';		
		else if($data==16)
			return "IE11";
		else if($data==17)
			return "IE10";
		else if($data==18)
			return "IE9";	
		else
			return 'unknow';		
	}
	
	function jry_wb_get_device($db=false,$user_agent=NULL)
	{
		if($user_agent===NULL)
			$user_agent=$_SERVER["HTTP_USER_AGENT"];		
		if($db)
		{
			if(strpos($user_agent,"iPad"))
				return 1;
			else if(strpos($user_agent,"iPhone"))
				return 2;	
			else if(strpos($user_agent,"Android"))
				return 3;	
			else
				return 0;	
		}
		else
		{
			if(strpos($user_agent,"iPad"))
				return 'ipad';
			else if(strpos($user_agent,"iPhone"))
				return 'iphone';	
			else if(strpos($user_agent,"Android"))
				return 'android';	
			else
				return 'pc';			
		}
	}
	function jry_wb_get_device_from_database($db)
	{
		if($db==1)
			return "iPad";
		else if($db==2)
			return 'iPhone';	
		else if($db==3)
			return "Android";	
		else
			return 'pc';
	}
	function jry_wb_test_is_mobile() 
	{ 
		if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) 
			return true;
		if(isset($_SERVER['HTTP_VIA'])) 
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		if (isset($_SERVER['HTTP_USER_AGENT'])) 
			if (preg_match("/(" . implode('|',array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile')) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) 
				return true;
		if (isset ($_SERVER['HTTP_ACCEPT'])) 
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
				return true;
		return false;
	}
	function jry_wb_test_is_weixin() 
	{ 
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) 
			return true; 
		return false; 
	}
?>