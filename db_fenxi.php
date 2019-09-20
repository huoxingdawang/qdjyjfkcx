<?php
	include_once('main.php');
	include_once('jry_wb_tools/jry_wb_includes.php');
	date_default_timezone_set('Asia/Shanghai');	
	function msectime(){list($msec, $sec) = explode(' ', microtime());return (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);}
	function gcd($a,$b){if($b==0) return $a;return gcd($b,$a%$b);}
	
	
	$start=msectime();
	$conn=jry_wb_connect_database();	
	$file=fopen('result/'.date("Y-m-d H-i-s",time()).'.html', "w") or die("Unable to open file!".$filename);
//	$file=fopen('result/test.html', "w") or die("Unable to open file!".$filename);
	fwrite($file,'<!DOCTYPE html PUBLIC >
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="icon" href="logo.ico" type="image/x-icon">
		<link rel="shortcut icon" href="logo.ico" type="image/x-icon">
		<title>'.jry_wb_get_time().'的总结报告</title>
	</head>
	<body ontouchstart="" onmouseover="">');
//睿智开头
	fwrite($file,'<h1>睿智教育局饭卡交♂易记录分析报告</h1>');	
	fwrite($file,'<h2>1:概述</h2>');	
	fwrite($file,'<span>生成时间:'.jry_wb_get_time().'</span><br>');	
	echo ((msectime()-$start)/1000)."s passed(head printed)\n";
//概括分析
	$st=$conn->prepare("SELECT * FROM `qiafan`.`student`");
	$st->execute();
	$stus=$st->fetchAll();
	fwrite($file,'<span>包含'.($stucnt=count($stus)).'个学生,');	
	echo ((msectime()-$start)/1000)."s passed(student loaded)\n";
	$st=$conn->prepare("SELECT COUNT(xjh) FROM `qiafan`.`logs`");
	$st->execute();
	$logscnt=$st->fetchAll()[0][0];	
	fwrite($file,''.$logscnt.'条记录</span>');	
//学生分析
	echo ((msectime()-$start)/1000)."s passed(logs loaded)\n";
	fwrite($file,'<h2>2:学生分析</h2>');
	$stuself=[];
	$stupren=[];
	foreach($stus as $stu)
		if($stu['birthday']!==NULL)
			if(strtotime($stu['birthday'])>strtotime('2000-00-00'))
				$stuself[]=$stu;
			else
				$stupren[]=$stu;
	echo ((msectime()-$start)/1000)."s passed(学生分类完毕)\n";
	fwrite($file,'<span>共'.($stuselfcnt=count($stuself)).'个可用学生(获取到出生日期且在2000-00-00之后),占比'.($stuselfcnt/$stucnt*100).'%</span>');
	fwrite($file,'<h3>2.1:性别分析</h3>');
	$zhuanghan=0;$meizi=0;
	foreach($stuself as $stu)
		if($stu['sex']==0)
			$meizi++;
		else
			$zhuanghan++;
	$gcdd=gcd($zhuanghan,$meizi);
	fwrite($file,'<span>共'.$zhuanghan.'个壮汉,'.$meizi.'个妹子,男女比例'.$zhuanghan/$gcdd.':'.$meizi/$gcdd.'≈'.($zhuanghan/$meizi).'</span>');
	echo ((msectime()-$start)/1000)."s passed(性别分析完毕)\n";
	fwrite($file,'<h3>2.2:生日分析</h3>');
	$birthday=[];
	foreach($stuself as $stu)
		if($birthday[$stu['birthday']]==NULL)
			$birthday[$stu['birthday']]=1;
		else
			$birthday[$stu['birthday']]++;	
	arsort($birthday);
	fwrite($file,'<div style="height:200px;overflow-y: scroll;width: 200px;"><table border="2"><tr><td>日期</td><td>人数</td></tr>');
	foreach($birthday as $bir=>$num)
		fwrite($file,'<tr><td>'.$bir.'</td><td>'.$num.'</td></tr>');
	fwrite($file,'</table></div>');
	$birthday=NULL;$stuself=NULL;
	echo ((msectime()-$start)/1000)."s passed(生日分析完毕)\n";	
//家长分析
	fwrite($file,'<h2>3:家长分析</h2>');
	fwrite($file,'<span>共'.($stuprencnt=count($stupren)).'个可用家长(获取到出生日期且在2000-00-00之前),占比'.($stuprencnt/$stucnt*100).'%</span>');
	fwrite($file,'<h3>3.1:性别分析</h3>');
	$zhuanghan=0;$meizi=0;
	foreach($stupren as $stu)
		if($stu['sex']==0)
			$meizi++;
		else
			$zhuanghan++;
	$gcdd=gcd($zhuanghan,$meizi);
	fwrite($file,'<span>共'.$zhuanghan.'个男家长,'.$meizi.'个女家长,男女比例'.$zhuanghan/$gcdd.':'.$meizi/$gcdd.'≈'.($zhuanghan/$meizi).'</span>');
	echo ((msectime()-$start)/1000)."s passed(性别分析完毕)\n";
	fwrite($file,'<h3>3.2:生日分析</h3>');
	$birthday=[];
	foreach($stupren as $stu)
		if($birthday[$stu['birthday']]==NULL)
			$birthday[$stu['birthday']]=1;
		else
			$birthday[$stu['birthday']]++;	
	arsort($birthday);
	fwrite($file,'<div style="height:200px;overflow-y: scroll;width: 200px;"><table border="2"><tr><td>日期</td><td>人数</td></tr>');
	foreach($birthday as $bir=>$num)
		fwrite($file,'<tr><td>'.$bir.'</td><td>'.$num.'</td></tr>');
	fwrite($file,'</table></div>');
	$birthday=NULL;$stupren=NULL;
	echo ((msectime()-$start)/1000)."s passed(生日分析完毕)\n";	
//借读大军分析
	fwrite($file,'<h2>4:借读大军分析</h2>');
	$jiedu=0;$from=[];$to=[];
	foreach($stus as $stu)
		if($stu['xjh']/100000%100!=$stu['school'])
		{
			$jiedu++;
			if($from[''.$stu['xjh']/100000%100]==NULL)
				$from[''.$stu['xjh']/100000%100]=1;
			else
				$from[''.$stu['xjh']/100000%100]++;
			if($to[''.$stu['school']]==NULL)
				$to[''.$stu['school']]=1;
			else
				$to[''.$stu['school']]++;
		}
	fwrite($file,'<span>共'.$jiedu.'借读生,占比'.($jiedu/$stucnt*100).'%</span>');
	fwrite($file,'<table border="2"><tr><td>借出学校</td><td>人数</td></tr>');
	foreach($from as $sch=>$num)
		fwrite($file,'<tr><td>'.$sch.'</td><td>'.$num.'</td></tr>');
	fwrite($file,'</table>');
	fwrite($file,'<table border="2"><tr><td>借入学校</td><td>人数</td></tr>');
	foreach($to as $sch=>$num)
		fwrite($file,'<tr><td>'.$sch.'</td><td>'.$num.'</td></tr>');
	fwrite($file,'</table>');		
	echo ((msectime()-$start)/1000)."s passed(借读大军分析完毕)\n";	




			
	
	
	
	
	
	fwrite($file,'
	</body>
</html>');
	echo ((msectime()-$start)/1000)."s passed(finish)\n";
?>