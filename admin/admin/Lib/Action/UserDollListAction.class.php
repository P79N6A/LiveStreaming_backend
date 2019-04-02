<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class UserDollListAction extends CommonAction{
	public function __construct()
	{	
		parent::__construct();
		require_once APP_ROOT_PATH."/admin/Lib/Action/UserDollListCommonAction.class.php";
        require_once APP_ROOT_PATH."/system/libs/user.php";
        require_once APP_ROOT_PATH."/mapi/wawa_server/core/common_wawa.php";
	}

	public function all()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->all($data);
	}

	public function index()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->index($data);
	}

	public function unget()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->unget($data);
	}

	public function arrived()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->arrived($data);
	}

	public function exchanged()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->exchanged($data);
	}
	
	public function delete() {
		//彻底删除指定记录
        $common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->delete($data);
	}
	
    public function dispatching(){
        //获取配送信息
        $common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->dispatching($data);
    }

    public function arrived_dispatching(){
        //获取配送信息
        $common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->arrived_dispatching($data);
    }

    public function logistics_edit() {
    	//发货填写订单
    	$common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->logistics_edit($data);
    }

    public function send() {
    	//自动发货
    	$common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->send($data);
    }

    public function send_handful() {
    	//手动发货
    	$common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->send_handful($data);
    }

    public function game_record()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->game_record($data);
	}

	public function invite_record()
	{
		$common = new UserDollListCommon();
		$data = $_REQUEST;
		$common->invite_record($data);
	}

	public function close_order() {
    	//关闭订单
    	$common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->close_order($data);
    }

    public function close_confirm() {
        //关闭订单方法
        $common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->close_confirm($data);
    }

    public function closed() {
        //已关闭订单
        $common = new UserDollListCommon();
        $data = $_REQUEST;
        $common->closed($data);
    }
	
	public function play()
	{
		$id = intval($_REQUEST['id']);
		$record = M('DollGameRecord')->where(['id' => $id])->find();

		if (empty($record['play_url'])) {
			if (empty($record['channelid'])) {
				$room = M('Video')->where(['id' => $record['room_id']])->find();
				$channelid = $room['channelid'];
				$channelid2 = $room['channelid2'];
			} else {
				$channelid = $record['channelid'];
				$channelid2 = $record['channelid2'];
			}

			$doll = M('Dolls')->where(['id' => $record['doll_id']])->find();
			$round_time = $doll['round_time'] ?: 60;

			$start_time = $record['start_time'];
			$m_config = load_auto_cache('m_config');
        	$count_down = $m_config['doll_count_down'] ?: 30;

			$end_time =  $start_time + ($round_time + $count_down) * 2;

			$record['play_url'] = get_record_videos($channelid, $start_time, $end_time);
			$record['play_url2'] = get_record_videos($channelid2, $start_time, $end_time);

			M('DollGameRecord')->save(['id' => $id, 'play_url' => $record['play_url'], 'play_url2' => $record['play_url2']]);
		}
		
		$this->assign ('record', $record);
        $this->display ();
	}

	
	//导出电子表,根据type区分是哪个页面
	public function export_out($page = 1)
	{

		$type = $_REQUEST['type'];
		if($type == 'noType')
		admin_ajax_return('Exporting contents type not set!');

		$pagesize = 10;
		set_time_limit(0);
		$limit = (($page - 1)*intval($pagesize)).",".(intval($pagesize));

		//列表过滤器，生成查询Map对象
		if($_REQUEST['pay_time_1'] || $_REQUEST['pay_time_2'])
		{
			$map2 = $this->com_search(strim($_REQUEST['pay_time_1']),strim($_REQUEST['pay_time_2']));
		}else if($_REQUEST['grab_time_1'] || $_REQUEST['grab_time_2'])
		{
			$map2 = $this->com_search(strim($_REQUEST['grab_time_1']),strim($_REQUEST['grab_time_2']));
		}
		
		if($type == 'index')
		{
			$sql_w .= ' t1.status = 1 and ';
		}else if($type == 'unget')
		{
			$sql_w .= ' t1.status = 0 and ';
		}
		else if($type == 'arrived')
		{
			$sql_w .= ' t1.status = 2 and ';
		}
		else if ($type == 'exchanged'){
			$sql_w .= ' t1.status = -2 and ';
		}
		else if ($type == 'closed'){
			$sql_w .= ' t1.status = -3 and ';
		}

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="t1.pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="t1.pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			//unset($map2);
		}		

		if(trim($_REQUEST['order_sn'])!='')
        {
            $sql_w .= "t1.order_sn like '%".trim($_REQUEST['order_sn'])."%' and ";
        }
        
        if(intval($_REQUEST['user_id'])>0)
        {
            $sql_w .= "t1.user_id = ".intval($_REQUEST['user_id'])." and ";
        }
		
		$sql_str = "SELECT *," .
        " t1.id,t1.order_sn,t1.user_id,t2.nick_name,t1.doll_id,t1.doll_name,t1.grab_time,t1.pay_time,t1.freight,t1.exchanged_diamonds,t1.close_reason,t1.dispatching,t1.logistics" .
        " FROM ".DB_PREFIX."user_doll_list t1 left join ".DB_PREFIX."user t2 on t1.user_id = t2.id WHERE 1=1 ";
		$sql_str .= " and ".$sql_w." 1=1 order by t1.id desc ";
        $sql =$sql_str." limit ";
        $sql .= $limit;

        /*admin_ajax_return($sql);*/
        $list=$GLOBALS['db']->getAll($sql);
        /*admin_ajax_return($list);*/
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_out'), $page+1);
            $m_config = load_auto_cache('m_config');
            $diamonds_name = $m_config['diamonds_name']!=''?$m_config['diamonds_name']:'秀豆';

			$show_value = array( 'id'=>'""','order_sn'=>'""','user_id'=>'""','nick_name'=>'""','doll_id'=>'""','doll_name'=>'""','grab_time'=>'""','pay_time'=>'""');	
			if($page == 1)
			{
				$type == 'index'? $content_index = ",付款时间,邮费,配送地址,收件人,联系方式" : $content_index =''; 

				$type == 'arrived'? $content_arrived = ",付款时间,邮费,配送地址,收件人,联系方式,物流单号" : $content_arrived =''; 

				$type == 'exchanged'? $content_exchanged = ",兑换时间,兑换".$diamonds_name : $content_exchanged ='';

				$type == 'closed'? $content_closed = ",关闭时间,补偿".$diamonds_name.",关闭理由" : $content_closed ='';

				$content = iconv("utf-8","gbk","订单ID,订单序列号,用户ID,用户昵称,娃娃ID,娃娃名称,抓取时间".$content_index.$content_arrived.$content_exchanged.$content_closed);
				$content = $content . "\n";
			}
			foreach($list as $k=>$v)
			{
				$show_value['id'] = '"' . iconv('utf-8','gbk',$list[$k]['id']) . '"';
				$show_value['order_sn'] = '"' . "'".iconv('utf-8','gbk',strim($list[$k]['order_sn'])) . '"';
				$show_value['user_id'] = '"' . iconv('utf-8','gbk',$list[$k]['user_id']) . '"';
				$show_value['nick_name'] = '"' . iconv('utf-8','gbk',emoji_decode($list[$k]['nick_name'])) . '"';
				$show_value['doll_id'] = '"' . iconv('utf-8','gbk',$list[$k]['doll_id']) . '"';
				$show_value['doll_name'] = '"' . iconv('utf-8','gbk',emoji_decode($list[$k]['doll_name'])) . '"';
				$show_value['grab_time'] = '"' . iconv('utf-8','gbk',to_date($list[$k]['grab_time'],'Y-m-d H:i:s') ). '"';

				if($type == 'index' || $type == 'arrived' || $type == 'exchanged' || $type == 'closed')
				{
					$show_value['pay_time'] = '"' . iconv('utf-8','gbk',to_date($list[$k]['pay_time'],'Y-m-d H:i:s') ). '"';
				}
				if($type == 'index' || $type == 'arrived')
				{
					$show_value['freight'] = '"' . iconv('utf-8','gbk',$list[$k]['freight']) . '"';

					$dispatching = unserialize($list[$k]['dispatching']);
            		$show_value['address'] = '"' . iconv('utf-8','gbk',$dispatching['address']) . '"';
            		$show_value['consignee'] = '"' . iconv('utf-8','gbk',$dispatching['consignee']) . '"';
            		$show_value['mobile'] = '"' . iconv('utf-8','gbk',$dispatching['mobile']) . '"';

            		if($type == 'arrived')
            		$show_value['logistics'] = '"' . iconv('utf-8','gbk',$list[$k]['logistics']) . '"';
				}
				if($type == 'exchanged' || $type == 'closed')
				{
					$show_value['exchanged_diamonds'] = '"' . iconv('utf-8','gbk',$list[$k]['exchanged_diamonds']) . '"';
				}
				if($type == 'closed')
				{
					$show_value['close_reason'] = '"' . iconv('utf-8','gbk',$list[$k]['close_reason']) . '"';
				}
				
				$content .= implode(",", $show_value) . "\n";
			}

			if($type == 'index')
			{
				header("Content-Disposition: attachment; filename=index.csv");
			}
			else if($type == 'unget')
			{
				header("Content-Disposition: attachment; filename=unget.csv");
			}
			else if($type == 'arrived')
			{
				header("Content-Disposition: attachment; filename=arrived.csv");
			}
			else if($type == 'exchanged')
			{
				header("Content-Disposition: attachment; filename=exchanged.csv");
			}
			else if($type == 'closed')
			{
				header("Content-Disposition: attachment; filename=closed.csv");
			}
			echo $content ;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	}

}
?>