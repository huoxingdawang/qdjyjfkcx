<?php
	include_once('jry_wb_config_default_user.php');
	define('JRY_WB_DATABASE_NAME'			,'data');
	define('JRY_WB_DATABASE_ADDR'			,'127.0.0.1');
	define('JRY_WB_DATABASE_USER_NAME'		,'ying');
	define('JRY_WB_DATABASE_USER_PASSWORD'	,'yingyingying');
	define('JRY_WB_DATABASE_ALL_PREFIX'		,'');
	
	define('JRY_WB_REDIS_ADDR'				,'');		//REDIS数据库位置
	define('JRY_WB_REDIS_PORT'				,'');				//REDIS数据库位置
	define('JRY_WB_REDIS_PASSWORD'			,'');	//REDIS密码
	define('JRY_WB_REDIS_PREFIX'			,'');			//REDIS前缀	
	
	if(!JRY_WB_HOST_SWITCH)
	{
		define('JRY_WB_HOST_DATABASE_NAME'			,'');
		define('JRY_WB_HOST_DATABASE_ADDR'			,'');
		define('JRY_WB_HOST_DATABASE_USER_NAME'		,'');
		define('JRY_WB_HOST_DATABASE_USER_PASSWORD'	,'');
		define('JRY_WB_HOST_DATABASE_ALL_PREFIX'	,'');	
	}
?>