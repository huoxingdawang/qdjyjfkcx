<?php
	include_once("../jry_wb_configs/jry_wb_config_database_user.php");
	include_once("../jry_wb_configs/jry_wb_config_database_system.php");
	function jry_wb_connect_database()
	{
		try{$dbh = new PDO("mysql:host=".JRY_WB_DATABASE_ADDR.";",JRY_WB_DATABASE_USER_NAME,JRY_WB_DATABASE_USER_PASSWORD);} 
		catch (PDOException $e) {die ("Error!: " . $e->getMessage() . "<br/>"); }
		$dbh->query("SET NAMES utf8");
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		return $dbh;
	}
	function jry_wb_connect_database_test($conn)
	{
		return is_object($conn->prepare('SELECT 1'));
	}
	if(!JRY_WB_HOST_SWITCH)
	{
		function jry_wb_connect_host_database()
		{
			try{ $dbh = new PDO("mysql:host=".JRY_WB_HOST_DATABASE_ADDR.";",JRY_WB_HOST_DATABASE_USER_NAME,JRY_WB_HOST_DATABASE_USER_PASSWORD); } 
			catch (PDOException $e) {die ("Error!: " . $e->getMessage() . "<br/>"); }
			$dbh->query("SET NAMES utf8");
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return $dbh;
		}		
	}
?>
