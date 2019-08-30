<?php
	//文件包含
	$jry_wb_start_time=microtime(true);	
	class jry_wb_exception extends Exception{};		
	include_once(dirname(dirname(__FILE__)).'/jry_wb_configs/jry_wb_config_includes.php');	
	include_once('jry_wb_database.php');
	include_once('jry_wb_get_time.php');
	include_once('jry_wb_get_device.php');
	include_once('jry_wb_get_random_string.php');
	include_once('jry_wb_aes.php');
?>
