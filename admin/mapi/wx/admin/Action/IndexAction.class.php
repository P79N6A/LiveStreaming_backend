<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class IndexAction extends AuthAction{
	//首页
    public function index(){
    	//管理员的SESSION
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
    	
    	if(intval(app_conf('EXPIRED_TIME'))>0&&$adm_id!=0){
			
			$admin_logined_time = intval($adm_session['admin_logined_time']);
			$max_time = intval(conf('EXPIRED_TIME'))*60;
			if(NOW_TIME-$admin_logined_time>=$max_time)
		{
 				es_session::delete((md5(conf("AUTH_KEY"))));
				$this->display();
			}
		}
		
    	if($adm_id == 0)
		{
			//已登录
			$this->redirect(u("Public/login"));			
		}else{
			$this->display();
		}
		
    }
    

    //框架头
	public function top()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$role_id = intval($adm_session['role_id']);
 		$navs= get_admin_nav($role_id,$adm_session['adm_name']);
		$this->assign("navs",$navs);

		$this->assign("admin",$adm_session);
		$this->display();
	}
	//框架左侧
	public function left()
	{

		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
		$role_id = intval($adm_session['role_id']);
		$navs= get_admin_nav($role_id,$adm_session['adm_name']);
		$nav_key = strim($_REQUEST['key']);
		
 		$nav_group = $navs[$nav_key]['groups'];

 		$this->assign("menus",$nav_group);
		$this->display();
	}
	//默认框架主区域

	public function main()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
        $adm = $GLOBALS['db']->getRow("SELECT a.adm_name,r.name as role_name,a.login_time,a.login_ip,a.login_count FROM ".DB_PREFIX."admin a left join ".DB_PREFIX."role r on a.role_id = r.id where a.id = $adm_id ");
        $adm['login_time'] = to_date($adm['login_time']);
        $this->assign("adm_session",$adm);

		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";
		$this->assign("navs",$navs);

        //分享审核
        $share_count = M("Share")->where("audit_status=0")->count();
        $this->assign("share_count",$share_count);

        //提问老余
        $question_count = M("Question")->where("is_answered=0")->count();
        $this->assign("question_count",$question_count);

        //预约老余
        $date_count = M("UserDate")->where("status=0")->count();
        $this->assign("date_count",$date_count);

        //会员反馈
        $feedback_count = M("Feedback")->count();
        $this->assign("feedback_count",$feedback_count);

        //累计会员数
        $user_count = M("User")->where("is_effect=1")->count();
        $this->assign("user_count",$user_count);

        //累计付费会员数
        $pay_user_count = M("User")->where("is_effect=1 and member_type = 3")->count();
        $this->assign("pay_user_count",$pay_user_count);

        //今日会员登陆数
        $login_user_count = M("User")->where("is_effect=1 and DATE(login_time) = '".to_date(NOW_TIME,'Y-m-d')."'")->count();
        $this->assign("login_user_count",$login_user_count);

        //今日新增会员数/人
        $timezone = intval(conf('TIME_ZONE')) * 3600;
        $new_user_count = M("User")->where("is_effect=1 and FROM_UNIXTIME(create_time+".$timezone.",'%Y-%m-%d') = '".to_date(NOW_TIME,'Y-m-d')."'")->count();
        $this->assign("new_user_count",$new_user_count);

        //最近7天会员登录次数
        $login_sql = "SELECT COUNT(*) as login_count,DATE(login_time) as login_time FROM ".DB_PREFIX."user WHERE is_effect=1 and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(login_time) group by (DATE(login_time))";
		$login_data = $GLOBALS['db']->getAll($login_sql,true,true);
        $login_data = array_combine(array_column($login_data,'login_time'),$login_data);

        //最近7天会员注册数量
        $register_sql = "SELECT COUNT(*) as reg_count,FROM_UNIXTIME(create_time+".$timezone.",'%Y-%m-%d') as create_time FROM `fanwe_user` WHERE is_effect=1 and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(FROM_UNIXTIME(create_time+".$timezone.")) group by (DATE(FROM_UNIXTIME(create_time+".$timezone.")))";
        $register_data = $GLOBALS['db']->getAll($register_sql,true,true);
        $register_data = array_combine(array_column($register_data,'create_time'),$register_data);

        $char_data = array();
        $format = 'Y-m-d';
        for($i=6;$i>=0;$i--){
            $data = array();
            $data['time'] = to_date((NOW_TIME - $i*3600*24),$format);
            $data['login_count'] = 0;
            $data['reg_count'] = 0;
            if(array_key_exists($data['time'],$login_data)){
                $data['login_count'] = $login_data[$data['time']]['login_count'];
            }
            if(array_key_exists($data['time'],$register_data)){
                $data['reg_count'] = $register_data[$data['time']]['reg_count'];;
            }
            $char_data[] = $data;
        }
        $this->assign("char_data",$char_data);
		$this->display();
	}	
	//底部
	public function footer()
	{
		$this->display();
	}
	
	//修改管理员密码
	public function change_password()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_data",$adm_session);
		$this->display();
	}
	public function do_change_password()
	{
		$adm_id = intval($_REQUEST['adm_id']);
		if(!check_empty($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if(!check_empty($_REQUEST['adm_new_password']))
		{
			$this->error(L("ADM_NEW_PASSWORD_EMPTY_TIP"));
		}
		if($_REQUEST['adm_confirm_password']!=$_REQUEST['adm_new_password'])
		{
			$this->error(L("ADM_NEW_PASSWORD_NOT_MATCH_TIP"));
		}		
		if(M("Admin")->where("id=".$adm_id)->getField("adm_password")!=md5($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_ERROR"));
		}
		M("Admin")->where("id=".$adm_id)->setField("adm_password",md5($_REQUEST['adm_new_password']));
		save_log(M("Admin")->where("id=".$adm_id)->getField("adm_name").L("CHANGE_SUCCESS"),1);
		$this->success(L("CHANGE_SUCCESS"));
		
		
	}
	
	public function reset_sending()
	{
		$field = trim($_REQUEST['field']);
		if($field=='DEAL_MSG_LOCK'||$field=='PROMOTE_MSG_LOCK'||$field=='APNS_MSG_LOCK')
		{
			M("Conf")->where("name='".$field."'")->setField("value",'0');
			$this->success(L("RESET_SUCCESS"),1);
		}
		else
		{
			$this->error(L("INVALID_OPERATION"),1);
		}
	}

	/*
	 * 网站数据统计
	*/
	public function statistics(){
		
		$user_count=M("User")->where("is_robot=0")->count();		
		$no_effect=M("User")->where("is_robot=0 and is_effect=0 or is_effect=2")->count(); //未审核
		$is_effect=M("User")->where("is_robot=0 and is_effect=1")->count(); //审核

		//认证
		$user_authentication=M("User")->where("is_authentication = 2 and user_type=0  and is_effect=1 and is_robot = 0")->count();
		$business_authentication=M("User")->where("is_authentication = 2 and user_type=1 and is_effect=1 and is_robot = 0")->count();
		$all_authentication=M("User")->where(" (user_type=0 or user_type=1) and is_authentication =2 and is_effect=1 and is_robot = 0")->count();
		
		//资金进出
		//线上充值
		$online_pay = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where is_paid = 1 and payment_id>0  "));
		$this->assign("online_pay",$online_pay);
		//总计
		$total_usre_money = $online_pay;
		$this->assign("total_usre_money",$total_usre_money);

		
		$this->assign("user_count",$user_count);
		$this->assign("no_effect",$no_effect);
		$this->assign("is_effect",$is_effect);
		$this->assign("user_authentication",$user_authentication);
		$this->assign("business_authentication",$business_authentication);
		$this->assign("all_authentication",$all_authentication);
		$this->display();
	}
}
?>