<?php
	include_once('jry_wb_tools/jry_wb_includes.php'); 
	$conn=jry_wb_connect_database();	
	$st = $conn->prepare("SELECT * FROM data.subject ORDER BY student_id ASC");
	$st->execute();				
	$db=$st->fetchAll();
	$filename='result.json';
	if($argv[1]!='')
		$filename=$argv[1];
	$file=fopen($filename, "r") or die("Unable to open file ".$filename);
	$data=json_decode(fread($file,filesize($filename)));
	fclose($file);	
	$n=count($db);$i=0;$lasti=0;
	$tmp=0;
	foreach($data as $one)
	{
		if($one->code==false)
			continue;
		$lasti=$i;
		while($one->name!=$db[$i]['name']&&$i<$n)
		{
			echo $i."\t".$n."\t".$one->name."\t".$db[$i]['name']."\n";
			$i++;
		}
		if($i==$n)
		{
			/*$st = $conn->prepare("INSERT INTO data.subject (`student_id`,`xjh`,`name`) VALUES (?,?,?)");
			$st->bindValue(1,($tmp++));
			$st->bindValue(2,$one->student_id);
			$st->bindValue(3,$one->name);
			$st->execute();*/			
			$i=$lasti;
			echo $one->name."\t lost"."\n";
			continue;
		}
		echo $i."\t".$n."\t".$one->name."\t".$db[$i]['name']."\n";
		$st = $conn->prepare("UPDATE data.subject SET xjh=?,card_id=?,amount=?,lasttime=? WHERE student_id=?");
		$st->bindValue(1,$one->student_id);
		$st->bindValue(2,$one->card_id);
		$st->bindValue(3,$one->amount);
		$st->bindValue(4,jry_wb_get_time());
		$st->bindValue(5,$db[$i]['student_id']);
		$st->execute();
		echo $one->name."\t\t".$db[$i]['student_id']."\t".$one->student_id."\n";
		$i++;
	}
	
	
	