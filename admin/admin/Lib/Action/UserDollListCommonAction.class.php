<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

//相同操作

class UserDollListCommon extends CommonAction{
    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH."/system/libs/user.php";
    }

    //所有订单
    public function all($date)
    {
     
        if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
        }
        
        if(intval($date['user_id'])>0)
        {
            $parameter.= "user_id=" . intval($date['user_id']). "&";
            $sql_w .= "user_id=".intval($date['user_id'])." and ";
        }

        if (!isset($_REQUEST['status'])) {
            $_REQUEST['status'] = -1;
        }

        if($_REQUEST['status']!=-1) {
            if (isset($date['status'])) {
                $parameter .= "status in (" . $date['status'] . ")&";
                $sql_w .= "status in (" .  $date['status'] . ") and ";
            }
        }

        $grab_time_1 = $_REQUEST['grab_time_1'];
        $grab_time_2=empty($_REQUEST['grab_time_2'])?to_date(get_gmtime(),'Y-m-d'):strim($_REQUEST['grab_time_2']);
        $grab_time_2=to_timespan($grab_time_2);
        if($grab_time_1!='' )
        {
            $parameter.="grab_time between '". to_timespan($grab_time_1) . "' and '". $grab_time_2 ."'&";
            $sql_w .=" (grab_time between '". to_timespan($grab_time_1). "' and '". $grab_time_2 ."' ) and ";
        }

        $model = D ();

        $sql_str = "SELECT *," .
        " id,order_sn,user_id,doll_id,doll_name,img,status,grab_time,pay_time,freight,exchanged_diamonds" .
        " FROM ".DB_PREFIX."user_doll_list WHERE 1=1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."user_doll_list WHERE 1=1";
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

    //发货中订单
	public function index($date)
	{
     
        if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
        }

        if(intval($date['cate_id'])>0)
        {
            //该cate_id为待找同类的订单的id，通过它获取用户和商品类别
            $cate_detail = $GLOBALS['db']->getRow("select user_id,pay_union_id from ".DB_PREFIX."user_doll_list where id = {$date['cate_id']}");
            //配置过滤条件
            $parameter.= "user_id=" . intval($cate_detail['user_id']). "&";
            $sql_w .= "user_id=".intval($cate_detail['user_id'])." and ";
            $parameter.= "pay_union_id=" . intval($cate_detail['pay_union_id']). "&";
            $sql_w .= "pay_union_id=".intval($cate_detail['pay_union_id'])." and ";
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

		$model = D ();

		$sql_str = "SELECT *," .
		" id,order_sn,user_id,doll_id,doll_name,img,status,grab_time,pay_time,freight" .
		" FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = 1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = 1";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'pay_time',0,$count_sql);
		foreach($voList as $k=>$v){
			$voList[$k]['doll_name'] = emoji_decode($v['doll_name']);
			$voList[$k]['img'] = get_spec_image($v['img']);
		}

        $m_config = load_auto_cache('m_config');
        $sendType = $m_config['auto_logistics'];

		$this->assign ( 'list', $voList );
        $this->assign ( 'sendType', $sendType );
		$this->display ();
	}

    //未领取订单
    public function unget($date)
    {
     
        if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
        }
        
        if(intval($date['user_id'])>0)
        {
            $parameter.= "user_id=" . intval($date['user_id']). "&";
            $sql_w .= "user_id=".intval($date['user_id'])." and ";
        }

        $grab_time_1 = $_REQUEST['grab_time_1'];
        $grab_time_2=empty($_REQUEST['grab_time_2'])?to_date(get_gmtime(),'Y-m-d'):strim($_REQUEST['grab_time_2']);
        $grab_time_2=to_timespan($grab_time_2);
        if($grab_time_1!='' )
        {
            $parameter.="grab_time between '". to_timespan($grab_time_1) . "' and '". $grab_time_2 ."'&";
            $sql_w .=" (grab_time between '". to_timespan($grab_time_1). "' and '". $grab_time_2 ."' ) and ";
        }

        $model = D ();

        $sql_str = "SELECT *," .
        " id,order_sn,user_id,doll_id,doll_name,img,status,grab_time,pay_time,freight" .
        " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = 0";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = 0";
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

    //已领取订单
    public function arrived($date)
    {
     
        if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
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

        $model = D ();

        $sql_str = "SELECT *," .
        " id,order_sn,user_id,doll_id,doll_name,img,status,grab_time,pay_time,freight" .
        " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = 2";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = 2";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'pay_time',0,$count_sql);
        foreach($voList as $k=>$v){
            $voList[$k]['doll_name'] = emoji_decode($v['doll_name']);
            $voList[$k]['img'] = get_spec_image($v['img']);
        }
        $this->assign ( 'list', $voList );
        $this->display ();
    }

    //已兑换订单
    public function exchanged($date)
    {
     
        if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
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

        $model = D ();

        $sql_str = "SELECT *," .
        " id,order_sn,user_id,doll_id,doll_name,img,status,grab_time,pay_time,freight,exchanged_diamonds" .
        " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = -2";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = -2";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'pay_time',0,$count_sql);
        foreach($voList as $k=>$v){
            $voList[$k]['doll_name'] = emoji_decode($v['doll_name']);
            $voList[$k]['img'] = get_spec_image($v['img']);
        }
        $this->assign ( 'list', $voList );
        $this->display ();
    }


	//删除
	public function delete($data) {
		//彻底删除指定记录
		$ajax = intval($data['ajax']);
		$id = $data ['id'];
		if (isset ( $id )) {
            $sql = "delete FROM ".DB_PREFIX."user_doll_list  where id = {$id}";
            $GLOBALS['db']->query($sql);
			save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
			$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);	
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}

    //获取配送详情
    public function dispatching($data)
    {
        $id = intval($_REQUEST['id']);
        $sql = "select user_id,dispatching,logistics from ".DB_PREFIX."user_doll_list where id = {$id}";
        $dispatching_json =         $GLOBALS['db']->getRow($sql);
        $dispatching = unserialize($dispatching_json['dispatching']);
        $logistics = $dispatching_json['logistics'];
        $this->assign ( 'uid', $dispatching_json['user_id']);
        $this->assign ( 'dispatching', $dispatching );
        $this->assign ( 'logistics', $logistics );
        $this->display ();
    }

    //获取配送详情
    public function arrived_dispatching($data)
    {
        $id = intval($_REQUEST['id']);
        $sql = "select user_id,dispatching,logistics from ".DB_PREFIX."user_doll_list where id = {$id}";
        $dispatching_json =         $GLOBALS['db']->getRow($sql);
        $dispatching = unserialize($dispatching_json['dispatching']);
        $logistics = $dispatching_json['logistics'];
        $this->assign ( 'uid', $dispatching_json['user_id']);
        $this->assign ( 'dispatching', $dispatching );
        $this->assign ( 'logistics', $logistics );
        $this->display ();
    }

     //发货填写订单页面
    public function logistics_edit($data)
    {
        $id = intval($data['id']);
        $sql = "select user_id,logistics from ".DB_PREFIX."user_doll_list where id = {$id}";
        $data =  $GLOBALS['db']->getRow($sql);
        $this->assign ('uid', $data['user_id']);
        $this->assign ('logistics', $data['logistics']);
        $this->assign ('id', $id);
        $this->display();
    }

    //手动发货，手动输入物流单号以后的发货
    public function send_handful($data)
    {
        $ajax = intval($data['ajax']);
        $id = intval($data['id']);
        $logistics = $data['logistics'];

        if (isset ( $id ) && $logistics!='') {
            $pay_union_id = $GLOBALS['db']->getOne("select pay_union_id from ".DB_PREFIX."user_doll_list where id = {$id} and status = 1");
            if($pay_union_id == 0)
            {
                $this->error (l("pay_union_id is invalid"),$ajax);
            }
            $sql = "update ".DB_PREFIX."user_doll_list set logistics = '{$logistics}',status = 2 where pay_union_id = {$pay_union_id}  and status = 1";
            $GLOBALS['db']->query($sql);
            save_log($info.l("操作成功"),1);
            $this->success (l("操作成功"),$ajax); 
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }

    //发货，直接获取物流单号
    public function send($data)
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common_wawa.php');
        $ajax = intval($data['ajax']);
        $id = intval($data['id']);  //订单ID

        if($id==0)
        admin_ajax_return("you've forgot to request with an id");

        //获取娃娃与配送信息相关参数
        $sql = "select dispatching,result_no,toy_id from ".DB_PREFIX."user_doll_list where id = {$id}";
        $res = $GLOBALS['db']->getRow($sql);
        if(!$res)
        admin_ajax_return("the order doesn't exists");
        if(!$res['dispatching'] || !$res['result_no'] || !$res['toy_id'] )
        {
            admin_ajax_return("your dispatching message is not complete");
        }

        //解析配送信息
        $dispatching = unserialize($res['dispatching']);
        //构造doll参数
        $doll = array( "result_no" => $res['result_no'] ,
                       "toy_id" => $res['toy_id'] );
        //向腾讯云获取物流信息
        $data = set_doll_express($doll,$dispatching);

        if(!$data)
        admin_ajax_return("tencent cloudy doesn't respond a logistics_sn");

        //test
        //admin_ajax_return($data);


        $logistics = $data['Results']['ExpressNo'];
        if(!$logistics)
        {
            save_log($info.l("发货失败"),1);
            $this->error (l("发货失败"),$ajax); 
        }
        //若有成功生成物流单号，则写入
        $pay_union_id = $GLOBALS['db']->getOne("select pay_union_id from ".DB_PREFIX."user_doll_list where id = {$id} and status = 1");
        if($pay_union_id == 0)
            {
                $this->error (l("pay_union_id is invalid"),$ajax);
            }
        $sql = "update ".DB_PREFIX."user_doll_list set logistics = '{$logistics}',status = 2 where pay_union_id = {$pay_union_id}  and status = 1";
        $GLOBALS['db']->query($sql);
        save_log($info.l("发货成功"),1);
        $this->success (l("发货成功"),$ajax); 
    }

    //游戏记录列表
    public function game_record($date)
    {
        if(intval($date['id'])>0)
        {
            $parameter.= "id=" . intval($date['id']). "&";
            $sql_w .= "id=".intval($date['id'])." and ";
        }

        if(intval($date['user_id'])>0)
        {
            $parameter.= "user_id=" . intval($date['user_id']). "&";
            $sql_w .= "user_id=".intval($date['user_id'])." and ";
        }

        if(intval($date['room_id'])>0)
        {
            $parameter.= "room_id=" . intval($date['room_id']). "&";
            $sql_w .= "room_id=".intval($date['room_id'])." and ";
        }

        if (!isset($_REQUEST['status'])) {
            $_REQUEST['status'] = -1;
        }

        if($_REQUEST['status']!=-1) {
            if (isset($date['status'])) {
                $parameter .= "status in (" . $date['status'] . ")&";
                $sql_w .= "status in (" .  $date['status'] . ") and ";
            }
        }

        if (!isset($_REQUEST['results'])) {
            $_REQUEST['results'] = -1;
        }

        if($_REQUEST['results']!=-1) {
            if (isset($date['results'])) {
                $parameter .= "status = 0 and play_result in (" . $date['results'] . ")&";
                $sql_w .= "status = 0 and play_result in (" .  $date['results'] . ") and ";
            }
        }

        $model = D ();

        $sql_str = "SELECT *," .
        " id,user_id,room_id,doll_id,doll_name,status,start_time,end_time,play_result" .
        " FROM ".DB_PREFIX."doll_game_record WHERE 1=1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."doll_game_record WHERE 1=1";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'id',0,$count_sql);
        foreach($voList as $k=>$v){
            $voList[$k]['doll_name'] = emoji_decode($v['doll_name']);
            if ($voList[$k]['status'])
            {
                $voList[$k]['play_result'] = 2;
            }
        }
        $this->assign ( 'list', $voList );
        $this->display ();
    }

    //邀请记录列表
    public function invite_record($date)
    {
     
        if(intval($date['user_id'])>0)
        {
            $parameter.= "user_id=" . intval($date['user_id']). "&";
            $sql_w .= "user_id=".intval($date['user_id'])." and ";
        }

        if(intval($date['invited_id'])>0)
        {
            $parameter.= "invited_id=" . intval($date['invited_id']). "&";
            $sql_w .= "invited_id=".intval($date['invited_id'])." and ";
        }

        $time_1 = $_REQUEST['time_1'];
        $time_2=empty($_REQUEST['time_2'])?to_date(get_gmtime(),'Y-m-d'):strim($_REQUEST['time_2']);
        $time_2=to_timespan($time_2);
        if($time_1!='' )
        {
            $parameter.="time between '". to_timespan($time_1) . "' and '". $time_2 ."'&";
            $sql_w .=" (time between '". to_timespan($time_1). "' and '". $time_2 ."' ) and ";
        }

        $model = D ();

        $sql_str = "SELECT *," .
        " id,user_id,invited_id,time,invite_code,inviter_bonuse,invited_user_bonuse" .
        " FROM ".DB_PREFIX."invite_history WHERE 1=1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."invite_history WHERE 1=1";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'id',0,$count_sql);
        //判断是不是从其他页面跳入
        if(strim($date['jump'])!='')
        {
            $this->assign ( 'jump', strim($date['jump']) );
            $this->assign ( 'user_id', intval($date['user_id']) );
        }
        $this->assign ( 'list', $voList );
        $this->display ();
    }

    //关闭订单页面
    public function close_order($data)
    {
        $id = intval($data['id']);
        $from = strim($data['from']);   //判断是哪个页面跳转过来
        $sql = "select order_sn,user_id,exchanged_diamonds,close_reason from ".DB_PREFIX."user_doll_list where id = {$id}";
        $data =  $GLOBALS['db']->getRow($sql);
        $m_config = load_auto_cache('m_config');
        $this->assign ('uid', $data['user_id']);
        $this->assign ('order_sn', $data['order_sn']);
        $this->assign ('id', $id);
        $this->assign ('from', $from);
        $this->assign ('exchanged_diamonds', intval($data['exchanged_diamonds']));
        $this->assign ('close_reason', $data['close_reason']);
        $this->assign ('diamond_name', $m_config['diamonds_name']);
        $this->display();
    }

    //关闭订单方法
    public function close_confirm($data){
        $ajax = intval($data['ajax']);
        if(!preg_match("/^[0-9]*$/i",$data['exchanged_diamonds']))
        {
            $this->error (l("补偿总额格式不合法"),$ajax);
        }
        $id = intval($data['id']);
        $exchanged_diamonds = intval($data['exchanged_diamonds']);
        $close_reason = strim($data['close_reason']);

        if($exchanged_diamonds < 0)
        {
            $this->error (l("补偿总额不能小于0"),$ajax);
        }

        if($close_reason == '')
        {
            $this->error (l("请填写关闭理由"),$ajax);
        }else if(!preg_match("/^.{1,60}$/",$close_reason)){
            $this->error (l("关闭理由不得超过20个汉字"),$ajax);
        }

        //获取新补偿秀豆与旧值的差值，更正用户实际补偿秀豆
        $exchanged_diamonds_old = $GLOBALS['db']->getRow("select exchanged_diamonds,user_id,status from ".DB_PREFIX."user_doll_list where id = {$id}");
        //判断订单状态是不是已改变，只能对未领取和待发货订单关闭
        if($exchanged_diamonds_old['status'] != 0 && $exchanged_diamonds_old['status'] != 1 )
        {
            $this->error (l("订单状态已变更，关闭失败"),$ajax);
        }
        
        $plus_diamonds = $exchanged_diamonds - intval($exchanged_diamonds_old['exchanged_diamonds']);
        $res = $GLOBALS['db']->query("update ".DB_PREFIX."user set diamonds = diamonds + {$plus_diamonds} where id = {$exchanged_diamonds_old['user_id']}");
        if(!$res)
        {
            $this->error (l("用户补偿失败"),$ajax);
        }
        //同步到redis
        user_deal_to_reids(array($exchanged_diamonds_old['user_id']));
        
        $where = " id = {$id} ";
        $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "user_doll_list", [
            'status' => -3,  
            'pay_time' => NOW_TIME, 
            'pay_union_id' => -$id,
            'close_reason' => $close_reason,
            'exchanged_diamonds' => $exchanged_diamonds
        ],
            'UPDATE', $where);

        if(!$res)
        {
            $this->error (l("数据表更新失败"),$ajax);
        }

        //成功后写入用户日志
        $m_config = load_auto_cache('m_config');
        $log_info['log_info'] = '订单关闭补偿'.$m_config['diamonds_name'];
        $log_info['log_time'] = get_gmtime();
        $log_info['user_id'] = $exchanged_diamonds_old['user_id'];
        $log_info['type'] = 2;
        $log_info['diamonds'] = $exchanged_diamonds;
        $GLOBALS['db']->autoExecute(DB_PREFIX."user_log",$log_info);

        save_log($info.l("关闭成功"),1);
        $this->assign("jumpUrl",u(MODULE_NAME."/".$data['from']));
        $this->success (l("关闭成功"),$ajax); 
    }

    //已关闭订单
    public function closed($date)
    {
     
        if(trim($date['order_sn'])!='')
        {
            $parameter.= "order_sn like " . urlencode ( '%'.trim($date['order_sn']).'%' ) . "&";
            $sql_w .= "order_sn like '%".trim($date['order_sn'])."%' and ";
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

        $model = D ();

        $sql_str = "SELECT *," .
        " id,order_sn,user_id,doll_id,doll_name,img,status,close_reason,pay_time,freight,exchanged_diamonds" .
        " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = -3";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."user_doll_list WHERE 1=1 and status = -3";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'pay_time',0,$count_sql);
        foreach($voList as $k=>$v){
            $voList[$k]['doll_name'] = emoji_decode($v['doll_name']);
            $voList[$k]['img'] = get_spec_image($v['img']);
        }
        $this->assign ( 'list', $voList );
        $this->display ();
    }
}
	