<?php
	include_once('main.php');
	for($stu=101201;$stu<=1000000000000;$stu+=100)
	{
		$data=chaxun($stu,10,1,getport(2));
		if($data->code)
			printt($data);
	}