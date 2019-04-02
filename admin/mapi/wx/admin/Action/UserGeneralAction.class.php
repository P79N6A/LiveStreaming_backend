<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class UserGeneralAction extends CommonAction{
	public function __construct()
	{	
		parent::__construct();
		require_once APP_ROOT_PATH."/admin/Lib/Action/UserCommonAction.class.php";
        require_once APP_ROOT_PATH."/system/libs/user_wpk.php";
	}
	public function index()
	{
		$common = new UserCommon();
		$data = $_REQUEST;
//		$data['is_authentication'] = array('in',array(0,1,3));
        //$data['is_robot'] = 0;
		$common->index($data);
	}

	public function edit() {		
		$common = new UserCommon();
		$data = $_REQUEST;
		$common->edit($data);
	}
		

	public function delete() {
		//彻底删除指定记录
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->delete($data);
	}
	
		
	
	public function update() {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->update($data);
		
	}

	public function set_effect()
	{
        $common = new UserCommon();
        $data = $_REQUEST;
        $n_is_effect = $common->set_effect($data);
        $this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1);
	}

    public function set_ban()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $n_is_effect = $common->set_ban($data);
        $this->ajaxReturn($n_is_effect,l("SET_BAN_".$n_is_effect),1);
    }

    //新增关注
    public function add_focus(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->add_focus($data);
    }

    //新增关注
    public function set_follow(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->set_follow($data);
    }
    //关注列表
    public function focus_list(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->focus_list($data);
    }

    //新增粉丝
    public function add_fans(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->add_fans($data);
    }

    //新增粉丝
    public function set_follower(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->set_follower($data);
    }

    //粉丝列表
    public function fans_list(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->fans_list($data);
    }

    //删除关注
    public function del_focus_list(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->del_focus_list($data);
    }

    //删除粉丝
    public function del_fans_list(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->del_fans_list($data);
    }

    //秀票贡献榜
    public function contribution_list(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->contribution_list($data);
    }

    /**
     * 删除秀票贡献榜
     */
    /*public function del_contribution_list()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $common = new UserCommon();
            $data = $_REQUEST;
            $status = $common->del_contribution_list($data);

            if ($status!==false) {
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }*/

    //消息推送
    public function push(){
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->push($data);
    }

    //删除推送消息
    public function del_push(){
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $common = new UserCommon();
            $data = $_REQUEST;
            $status = $common->del_push($data);

            if ($status!==false) {
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }

	//账户管理
	public function account()
	{
        $common = new UserCommon();
        $data = $_REQUEST;
        $status = $common->account($data);
	}
	//账户修改
	public function modify_account()
	{
        $common = new UserCommon();
        $data = $_REQUEST;
        $status = $common->modify_account($data);
        if($status){
        	$this->success(L("UPDATE_SUCCESS"));
        }else{
        	$this->error("累计充值数据有误！");
        }
        
    }

	//账户日志
	public function account_detail()
	{
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->account_detail($data);
	}

    //兑换日志
    public function exchange_log()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->exchange_log($data);
    }
	//删除账户日志
	public function foreverdelete_account_detail()
	{

        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        $data = $_REQUEST;
        if (isset ( $id )) {
            $common = new UserCommon();
            $status = $common->foreverdelete_account_detail($data);
            if ($status!==false) {
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
	}
    //删除兑换日志
    public function foreverdelete_exchange_log()
    {

        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        $data = $_REQUEST;
        if (isset ( $id )) {
            $common = new UserCommon();
            $status = $common->foreverdelete_exchange_log($data);
            if ($status!==false) {
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }
	//检查用户
	public function check_user(){
        $common = new UserCommon();
        $user_id = $_REQUEST['id'];
        ajax_return($common->check_user($user_id));
	}
    //礼物日志
    public function prop()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->prop($data);
    }
    //删除礼物日志
    public function delete_prop()
    {

        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = intval($_REQUEST ['id']);
        $data = $_REQUEST;
        if (isset ( $id )) {
            $common = new UserCommon();
            $status = $common->del_prop($data);
            if ($status!==false) {
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }
}
?>