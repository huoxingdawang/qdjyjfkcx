<?php
	include_once('main.php');
	$sno=$argv[1];
	$port=$argv[2];
	$port=$port?$port:getport($sno/100000%100);
	$interfaceurl=[
		'bind'=>
		[
			'bind'					=>'http://27.221.57.108:'.$port.'/app/user/bind',						//绑定
		],
		//绑卡
//		'cardInfo'					=>'http://27.221.57.108:'.$port.'/app/cardInfo',						//首页接口,
//		'bankCardInfo'				=>'http://27.221.57.108:'.$port.'/app/bankCardInfo',					//绑定银行卡,
//		'bindingBank'				=>'http://27.221.57.108:'.$port.'/app/bindingBank',						//绑定银行卡,
//		'recharge'					=>'http://27.221.57.108:'.$port.'/app/recharge',						//一卡通充值
//		'cardlist'					=>'http://27.221.57.108:'.$port.'/app/list',							//一卡通信息列表
//		'trades'					=>'http://27.221.57.108:'.$port.'/app/trades',							//流水
//		'bankCardInfoFull'			=>'http://27.221.57.108:'.$port.'/app/bankCardInfoFull',				//穫取卡完整信息
//		'unBindingBank'				=>'http://27.221.57.108:'.$port.'/Leave/unBindingBank',					//解绑
		'login'=>
		[
			'login'					=>'http://27.221.57.108:'.$port.'/app/user/login',						//登陆
		],
		'notice'=>
		[
			'noticelist'			=>'http://27.221.57.108:'.$port.'/app/guardian/notice/list',			//通知列表
			'noticedetail'			=>'http://27.221.57.108:'.$port.'/app/guardian/notice/get',				//通知详情
			'feedback'				=>'http://27.221.57.108:'.$port.'/app/guardian/notice/feedback',		//通知反馈
		],
		'students'=>
		[
			'getChildrens'			=>'http://27.221.57.108:'.$port.'/app/guardian/getChildrens',			//学生ID-Name映射关系
			'list'					=>'http://27.221.57.108:'.$port.'/app/guardian/children/list',			//学生详情
		],
		'leave'=>
		[
			'getLeaves'				=>'http://27.221.57.108:'.$port.'/Leave/getLeaves',						//家长老师获取请假列表
			'getLeaveType'			=>'http://27.221.57.108:'.$port.'/Leave/getLeaveType',					//获取请假类型
			'putParentLeave'		=>'http://27.221.57.108:'.$port.'/Leave/putParentLeave',				//家长发起请假
			'getStudent'			=>'http://27.221.57.108:'.$port.'/Leave/getStudent',					//班主任获取学生列表
			'putTeacherLeave'		=>'http://27.221.57.108:'.$port.'/Leave/putTeacherLeave',				//班主任发起请假
			'examinedLeave'			=>'http://27.221.57.108:'.$port.'/Leave/examinedLeave',					//班主任审批请假
			'getStudentNameBySno'	=>'http://27.221.57.108:'.$port.'/Leave/getStudentNameBySno',
			'leavelist'				=>'http://27.221.57.108:'.$port.'/app/guardian/leave/list',				//请假列表
			'add'					=>'http://27.221.57.108:'.$port.'/app/guardian/leave/add',				//提交请假表单
		],
		'pay'=>
		[
			'list'					=>'http://27.221.57.108:'.$port.'/app/guardian/paymentBill/list',		//缴费单列表
			'get'					=>'http://27.221.57.108:'.$port.'/app/guardian/paymentBill/get',		//缴费单详情
			'subpay'				=>'http://27.221.57.108:'.$port.'/app/guardian/paymentBill/pay',		//缴费
		],
		'task'=>
		[
			'list'					=>'http://27.221.57.108:'.$port.'/app/guardian/homework/list',			//作业列表
			'content'				=>'http://27.221.57.108:'.$port.'/app/guardian/homework/get',			//作业列表
		],
		'teacher'=>
		[
			'list'					=>'http://27.221.57.108:'.$port.'/app/guardian/teacher/list',			//老师列表
		],
		'timeat'=>
		[
			'list'					=>'http://27.221.57.108:'.$port.'/app/guardian/course/list',			//课程表
		],
		'questionnaire'=>
		[
			'list'					=>'http://27.221.57.108:'.$port.'/app/guardian/questionnaire/list',		//调查问卷列表
			'info'					=>'http://27.221.57.108:'.$port.'/app/guardian/questionnaire/get',		//调查问卷详情
			'submit'				=>'http://27.221.57.108:'.$port.'/app/guardian/questionnaire/feedback',	//调查问卷提交
		],
	];
	function scan($one,$sno)
	{
		if(is_string($one))
		{
			echo $one."\n";
			$ch=curl_init($one);
			curl_setopt($ch,CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));	
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array('sno'=>$sno,'userType'=>0)));	
			$get_sorce=curl_exec($ch);
			echo $get_sorce."\n\n\n";
		}
		else
			foreach($one as $o)
				scan($o,$sno);
	}
	scan($interfaceurl,$sno);