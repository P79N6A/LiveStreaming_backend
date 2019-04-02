<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class dateCModule  extends baseModule
{
    /**
     * 预约老余首页
     */
	public function index(){
        $root = array('status'=>1,'date_list'=>array(),'reservation_config'=>array(),'has_wanna'=>0);
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $sql = "select id from ".DB_PREFIX."date_wanna where user_id = ".$user_id;
            $date_wanna = $GLOBALS['db']->getOne($sql,true,true);
            if(intval($date_wanna)>0){
                $root['has_wanna'] = 1;
            }
            //预约配置信息
            $reservation_config = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."conf where `name`='RESERVATION_CONFIG'");
            if($reservation_config){
                $reservation_config = unserialize($reservation_config['value']);
                if(in_array('全国',$reservation_config['region'])){
                    $reservation_config['region'] = '区域不限';
                }else{
                    $reservation_config['region'] = implode(',',$reservation_config['region']);
                }
                $reservation_config['head_image'] = get_spec_image($reservation_config['head_image']);
                $root['reservation_config'] = $reservation_config;
            }
            //可预约的部分项目
            $date_sql = "select * from ".DB_PREFIX."date order by create_time desc";
            $date_list =  $GLOBALS['db']->getAll($date_sql,true,true);
            if($date_list){
                $root['date_list'] = $date_list;
            }
        }
        $root['page_title'] = '预约余老师';
        api_ajax_return($root);
    }

    /**
     * 想预约(取消想预约)
     */
    public function wanna(){
        $root = array('status'=>1,'error'=>'');
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $sql = "select id from ".DB_PREFIX."date_wanna where user_id = ".$user_id;
            $date_wanna = $GLOBALS['db']->getRow($sql,true,true);
            $reservation_config = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."conf where `name`='RESERVATION_CONFIG'");
            $value = unserialize($reservation_config['value']);
            $root['wanna_count'] = $value['wanna_count'];
            if($date_wanna){
                $GLOBALS['db']->query("delete from ".DB_PREFIX."date_wanna where id = ".intval($date_wanna['id']));
                if($GLOBALS['db']->affected_rows()){
                    //改变想预约老余的人数
                    $value['wanna_count'] = $value['wanna_count'] - 1;
                    $wanna_count = $value['wanna_count'];
                    $value =  serialize($value);
                    $GLOBALS['db']->query("update ".DB_PREFIX."conf set value='".$value."' where `name`='RESERVATION_CONFIG'");
                    if($GLOBALS['db']->affected_rows()){
                        $root['wanna_count'] = $wanna_count;
                    }
                }
            }else{
                $data['user_id'] = $user_id;
                $GLOBALS['db']->autoExecute(DB_PREFIX."date_wanna", $data,"INSERT");
                if($GLOBALS['db']->insert_id()>0){
                    //改变想预约老余的人数
                    $value['wanna_count'] = $value['wanna_count'] + 1;
                    $wanna_count = $value['wanna_count'];
                    $value =  serialize($value);
                    $GLOBALS['db']->query("update ".DB_PREFIX."conf set value='".$value."' where `name`='RESERVATION_CONFIG'");
                    if($GLOBALS['db']->affected_rows()){
                        $root['wanna_count'] = $wanna_count;
                    }
                }
            }
            $date_wanna = $GLOBALS['db']->getRow($sql,true,true);
            if($date_wanna){
                $root['error'] = '想预约';
                $root['has_wanna'] = 1;
            }else{
                $root['error'] = '取消想预约';
                $root['has_wanna'] = 0;
            }
        }
        api_ajax_return($root);
    }

    /**
     * 预约
     */
    public function date(){
        $root = array('status'=>1,'error'=>'');
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $date_id = intval($_REQUEST['date_id']);
            $name = strim($_REQUEST['name']);
            $verify = strim($_REQUEST['verify']);
            $mobile = strim($_REQUEST['mobile']);
            if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."date WHERE id=".$date_id)==0){
                $root['error'] = "预约项目不存在";
                $root['status'] = 0;
                ajax_return($root);
            }
            $user_date = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_date WHERE status = 0 and date_id=".$date_id." AND user_id=".$user_id);
            if($user_date>0){
                $root['error'] = "该项目已预约过";
                $root['status'] = 0;
                ajax_return($root);
            }
            if($name == ''){
                $root['error'] = "请输入姓名";
                $root['status'] = 0;
                ajax_return($root);
            }
            if($verify == ''){
                $root['error'] = "请输入验证码";
                $root['status'] = 0;
                ajax_return($root);
            }
            if($mobile!=''){
                if(!check_mobile($mobile)){
                    $root['error'] = "手机号码格式错误";
                    $root['status'] = 0;
                    ajax_return($root);
                }
                if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$mobile."' AND verify_code='".$verify."'")==0){
                    $root['error'] = "手机验证码出错";
                    $root['status'] = 0;
                    ajax_return($root);
                }
            }else{
                $root['error'] = "请输入手机号码";
                $root['status'] = 0;
                ajax_return($root);
            }


            $data['user_id'] = $user_id;
            $data['name'] = $name;
            $data['date_id'] = $date_id;
            $data['mobile'] = $mobile;
            $data['create_time'] = NOW_TIME;

            $GLOBALS['db']->autoExecute(DB_PREFIX."user_date", $data,"INSERT");
            $id = $GLOBALS['db']->insert_id();
            if($id>0){
                $GLOBALS['db']->query("update ".DB_PREFIX."date set count=count+1 where id = ".$data['date_id']);
                $root['error'] = '预约成功';
                $root['id'] = $id;
            }else{
                $root['error'] = '预约失败';
                $root['status'] = 0;
            }
        }
        api_ajax_return($root);
    }

    /**
     * 预约详情页面
     */
    public function detail(){
        $root = array('status'=>1,'error'=>'','data'=>array(),'has_date'=>1);
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $id = intval($_REQUEST['id']);
            $sql = "select * from ".DB_PREFIX."date where id = ".$id;
            $date = $GLOBALS['db']->getRow($sql,true,true);
            if($date){
                $root['data'] = $date;
                $user_date = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_date WHERE status = 0 and date_id=".$id." AND user_id=".$user_id);
                if($user_date>0){
                    $root['has_date'] = 0;//不可预约
                    $root['error'] = '已预约';
                }
            }else{
                $root['status'] = 0;
                $root['error'] = "预约项目不存在";
            }
        }
        $root['page_title'] = '预约项目';
        api_ajax_return($root);
    }

    //发送手机验证码
    function send_mobile_verify(){
        $mobile = addslashes(htmlspecialchars(trim($_REQUEST['mobile'])));

        if(app_conf("SMS_ON")==0)
        {
            $root['status'] = 0;
            $root['error'] = "短信未开启";
            ajax_return($root);
        }

        if($mobile == '')
        {
            $root['status'] = 0;
            $root['error'] = "请输入你的手机号";
            ajax_return($root);
        }

        if(!check_mobile($mobile))
        {
            $root['status'] = 0;
            $root['error'] = "请填写正确的手机号码";
            ajax_return($root);
        }

        //添加：手机发送 防护
        $root = check_sms_send($mobile);
        if ($root['status'] == 0){
        	ajax_return($root);
        }
        
        $result = array("status"=>1,"info"=>'');


        if(!check_ipop_limit(get_client_ip(),"mobile_verify",60,0))
        {
            $root['status'] = 0;
            $root['error'] = "发送速度太快了";
            ajax_return($root);
        }

        if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and client_ip='".get_client_ip()."' and create_time>=".(get_gmtime()-60)." ORDER BY id DESC") > 0)
        {
            $root['status'] = 0;
            $root['error'] = "发送速度太快了";
            ajax_return($root);
        }
        $n_time=get_gmtime()-300;
        //删除超过5分钟的验证码
        $GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".$n_time);
        //开始生成手机验证
        
        $code = rand(1000,9999);
        $GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$code,"mobile"=>$mobile,"create_time"=>get_gmtime(),"client_ip"=>get_client_ip()),"INSERT");

        send_verify_sms($mobile,$code);
        $status = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_msg_list where dest = '".$mobile."' and code='".$code."'");

        if($status['is_success']){
            $root['status'] = 1;
            $root['time'] = 60;
            $root['error'] = $status['title'].$status['result'];
        }else{
            $root['status'] = 0;
            $root['time'] = 0;
            $root['error'] = "短信验证码发送失败";
        }

        api_ajax_return($root);
    }
}

?>
