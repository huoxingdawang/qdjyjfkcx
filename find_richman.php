<?php
	include_once('main.php');
	$filename='result.json';
	if($argv[1]!='')
		$filename=$argv[1];
	$file=fopen($filename, "r") or die("Unable to open file!".$filename);
	$data=json_decode(fread($file,filesize($filename)));
	fclose($file);
	$jiaoyi=0;$amount=0;$ok=0;$cnt=0;
	foreach($data as $buf)
	{
		$cnt++;
		if($buf->code&&$buf->amount>0)
		{
			$ok++;
			$jiaoyi+=count($buf->logs);
			$amount+=$buf->amount;
			printt($buf);
		}
	}
	echo "汇总数据:\n".'有钱人'.$ok.'个,共'.$cnt.'条,比例:'.($ok/$cnt*100)."%\n";
		
	