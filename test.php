<?php
	include_once('main.php');
	$cnt=0;
	$ok=0;
	$okdata=[];
	$datas=[];
	for($i=1;$i<=758;$i++)
	{
		$buf=chaxun('2017370201880230'.str_pad($i,3,"0",STR_PAD_LEFT));
		$cnt++;
		printt($buf);
		$ok+=$buf->code;
		if($buf->code)
			$okdata[]=$buf;
		$datas[]=$buf;
	}
	echo "\n\n\n\n\n\n".'有效数据'.$ok.'条,共'.$cnt.'条,比例:'.($ok/$cnt*100)."%\n";
	foreach($okdata as $buf)
		printt($buf);
	$file=fopen("result.json", "w");
	fwrite($file,json_encode($datas));
	fclose($file);	