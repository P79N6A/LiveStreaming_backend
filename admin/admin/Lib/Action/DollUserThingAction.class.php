<?php

class DollUserThingAction extends CommonAction{
	public function __construct()
	{	
		parent::__construct();
        require_once APP_ROOT_PATH."/system/libs/user.php";
        require_once APP_ROOT_PATH."/mapi/wawa_server/core/common_wawa.php";
	}

	public function all()
	{
		$date = $_REQUEST;
		if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
        }
        if (!isset($_REQUEST['status'])) {
            $_REQUEST['status'] = -1;
        }
        if(intval($_REQUEST['status'])!=-1)
        {
            $parameter.= "status=" . intval($date['status']). "&";
            $sql_w .= "status=".intval($date['status'])." and ";
        }
        if(intval($date['user_id'])>0)
        {
            $parameter.= "user_id=" . intval($date['user_id']). "&";
            $sql_w .= "user_id=".intval($date['user_id'])." and ";
        }

        $pay_time_1 = $_REQUEST['pay_time_1'];
        $pay_time_2=empty($_REQUEST['pay_time_2'])?to_date(get_gmtime(),'Y-m-d'):strim($_REQUEST['pay_time_2']);
        $pay_time_2=to_timespan($pay_time_2);
        if($pay_time_1!='' )
        {
            $parameter.="pay_time between '". to_timespan($pay_time_1) . "' and '". $pay_time_2 ."'&";
            $sql_w .=" (pay_time between '". to_timespan($pay_time_1). "' and '". $pay_time_2 ."' ) and ";
        }
		
		if(intval($_REQUEST['cate_id'])>0)
        {
            //该cate_id为待找同类的订单的id，通过它获取用户和商品类别
            $cate_detail = $GLOBALS['db']->getRow("select user_id,pay_union_id from ".DB_PREFIX."doll_user_thing where id = {$_REQUEST['cate_id']}");
            //配置过滤条件
            $parameter.= "user_id=" . intval($cate_detail['user_id']). "&";
            $sql_w .= "user_id=".intval($cate_detail['user_id'])." and ";
            $parameter.= "pay_union_id=" . intval($cate_detail['pay_union_id']). "&";
            $sql_w .= "pay_union_id=".intval($cate_detail['pay_union_id'])." and ";
        }
        
        $model = D ();

        $sql_str = "SELECT *," .
        " id,order_sn,user_id,doll_id,doll_name,img,status,grab_time,pay_time,freight" .
        " FROM ".DB_PREFIX."doll_user_thing WHERE 1=1 ";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."doll_user_thing WHERE 1=1 ";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'id',0,$count_sql);
        foreach($voList as $k=>$v){
            $voList[$k]['doll_name'] = emoji_decode($v['doll_name']);
            $voList[$k]['img'] = get_spec_image($v['img']);
        }
        $this->assign ( 'list', $voList );
        $this->display ();
	}
	
	public function delete() {
		//彻底删除指定记录
        $data = $_REQUEST;
        $ajax = intval($data['ajax']);
		$id = $data ['id'];
		if (isset ( $id )) {
            $sql = "delete FROM ".DB_PREFIX."doll_user_thing  where id = {$id}";
            $GLOBALS['db']->query($sql);
			save_log(l("FOREVER_DELETE_SUCCESS"));
			$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);	
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
    public function dispatching(){
        //获取配送信息
        $data = $_REQUEST;
       	$id = intval($_REQUEST['id']);
        $sql = "select user_id,dispatching,logistics from ".DB_PREFIX."doll_user_thing where id = {$id}";
        $dispatching_json =         $GLOBALS['db']->getRow($sql);
        $dispatching = unserialize($dispatching_json['dispatching']);
        $logistics = $dispatching_json['logistics'];
        $this->assign ( 'uid', $dispatching_json['user_id']);
        $this->assign ( 'dispatching', $dispatching );
        $this->assign ( 'logistics', $logistics );
        $this->display ();
    }

    public function arrived_dispatching(){
        //获取配送信息
        $data = $_REQUEST;
        $id = intval($_REQUEST['id']);
        $sql = "select user_id,dispatching,logistics from ".DB_PREFIX."doll_user_thing where id = {$id}";
        $dispatching_json =         $GLOBALS['db']->getRow($sql);
        $dispatching = unserialize($dispatching_json['dispatching']);
        $logistics = $dispatching_json['logistics'];
        $this->assign ( 'uid', $dispatching_json['user_id']);
        $this->assign ( 'dispatching', $dispatching );
        $this->assign ( 'logistics', $logistics );
        $this->display ();
    }

    public function logistics_edit() {
    	//发货填写订单
        $data = $_REQUEST;
        $id = intval($data['id']);
        $sql = "select user_id,logistics from ".DB_PREFIX."doll_user_thing where id = {$id}";
        $data =  $GLOBALS['db']->getRow($sql);
        $this->assign ('uid', $data['user_id']);
        $this->assign ('logistics', $data['logistics']);
        $this->assign ('id', $id);
        $this->display();
    }

    public function send_handful() {
    	//手动发货
        $data = $_REQUEST;
        $ajax = intval($data['ajax']);
        $id = intval($data['id']);
        $logistics = $data['logistics'];

        if (isset ( $id ) && $logistics!='') {
            $pay_union_id = $GLOBALS['db']->getOne("select pay_union_id from ".DB_PREFIX."doll_user_thing where id = {$id} and status = 1");
            if($pay_union_id == 0)
            {
                $this->error (l("pay_union_id is invalid"),$ajax);
            }
            $sql = "update ".DB_PREFIX."doll_user_thing set logistics = '{$logistics}',status = 2 where pay_union_id = {$pay_union_id} and status = 1";
            $GLOBALS['db']->query($sql);
            save_log(l("操作成功"));
            $this->success (l("操作成功"),$ajax); 
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }
  
}
?>