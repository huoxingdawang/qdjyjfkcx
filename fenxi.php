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
		$ok+=$buf->code;
		$cnt++;
		if($buf->code)
		{
			$jiaoyi+=count($buf->logs);
			$amount+=$buf->amount;
		}
	}
	echo "汇总数据:\n".'有效数据'.$ok.'条,共'.$cnt.'条,比例:'.($ok/$cnt*100)."%\n";
	echo '交易'.$jiaoyi.'条,共'.$amount.'元,平均:'.($amount/$jiaoyi).'元/次,'.($amount/$ok)."元/人";
	
	