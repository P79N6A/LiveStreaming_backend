<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterBargainAction extends CommonAction
{
    public function index()
    {
		$bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);
        $user_id=intval($_REQUEST['user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}
		
         $where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
		 
		if($login_name){
     		$where .=" and p.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
		
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = strim($_REQUEST ['_order']);
		} else {
			$order = "l.id";
		}
		
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		$pre = DB_PREFIX;
		
       	$sql_count="select 
						u.bm_pid,
						sum(l.total_ticket) as total_ticket
				   from 
				   		".$pre."video_prop_".$time['begin_time_ym']." as l 
					left join ".DB_PREFIX."user as u on u.id = l.to_user_id  
					left join ".DB_PREFIX."bm_promoter as p on p.user_id = u.bm_pid 
					where 
						".$where." 
					GROUP BY u.bm_pid".$limit;
		
		$list_count_all=$GLOBALS['db']->getAll($sql_count);
		
		$p = $_REQUEST['p'];
		if ($p == '') {
			$p = 1;
		}
		$p         = $p > 0 ? $p : 1;
		$page_size = 10; 
		$limit     = (($p - 1) * $page_size) . "," . $page_size;

		$count     = count($list_count_all);

        if($count > 0){

			$page      = new Page($count, $page_size);
			$page_show = $page->show();
			
			$sql="select 
						l.id,
						l.to_user_id,
						sum(l.total_ticket) as total_ticket,
						p.login_name as p_login_name,
						p.name as p_name,
						p.pid p_pid,
						u.nick_name,
						u.bm_pid,
						u.bm_qrcode_id,
						u.bm_promoter_id as qrcode_promoter_id 
				   from 
				   		".$pre."video_prop_".$time['begin_time_ym']." as l 
					left join ".$pre."user as u on u.id = l.to_user_id 
					left join ".$pre."bm_promoter as p on p.user_id = u.bm_pid 
					where 
						".$where." 
					GROUP BY u.bm_pid 
					order by ".$order." ".$sort." limit ".$limit;
			
			$list = $GLOBALS['db']->getAll($sql);
			
			$bm_config = load_auto_cache("bm_config");
			//代理商普通主播收益分成
			$promoter_average_anchor_revenue = intval($bm_config['promoter_average_anchor_revenue']);
			if ($promoter_average_anchor_revenue < -1 || $promoter_average_anchor_revenue > 100) {
				$promoter_average_anchor_revenue = 70;
			}
			$list_re=array();
			foreach($list as $k=>$v){
				//计算
				$list[$k]['user_ticket'] = $v['total_ticket'] * ($promoter_average_anchor_revenue / 100);
				$list[$k]['promoter_ticket'] = $list[$k]['total_ticket'] - $list[$k]['user_ticket'];
				
				if($v['p_pid'] != '')
					$page_promoter_pids[$v['p_pid']]=$v['p_pid'];
				$list_re[$v['bm_pid']]=$list[$k];
			}
			
			//获取所属鱼乐合伙人编号
        	$p_info=$GLOBALS['db']->getAll("select user_id,login_name from ".$pre."bm_promoter where user_id in(".implode(',',$page_promoter_pids).") ");
			
			$p_info_re=array();
			foreach($p_info as $k=>$v){
				$p_info_re[$v['user_id']]=$v['login_name'];
			}
	
			foreach($list_re as $k=>$v){
				$list_re[$k]['p_parent_login_name']=$p_info_re[$v['p_pid']];
			}
			
			//游戏流水及合计
			$all_promoter_user_ids=array_map('array_shift',$list_count_all);
			$total_re=array();
			foreach($list_count_all as $k=>$v){
				$all_promoter_user_ids[$v['bm_pid']]=$v['bm_pid'];
				$total_re['total_ticket'] +=$v['total_ticket'];//秀票合计
			}
			
			$total_re['user_ticket'] = $total_re['total_ticket'] * ($promoter_average_anchor_revenue / 100);//会员秀票合计
			$total_re['promoter_ticket'] = $total_re['total_ticket'] - $total_re['user_ticket'];//平台（鱼商）秀票合计
			
			if($all_promoter_user_ids){
				$game_log_sql="
					select 
						bm_pid,
						sum(promoter_gain) as promoter_gain,
						sum(platform_gain) as platform_gain,sum(gain) as gain 
					from 
						".$pre."bm_promoter_game_log 
					where 
						bm_pid in(".implode(',',$all_promoter_user_ids).") and sum_win >0 GROUP BY bm_pid";
						
				$game_log=$GLOBALS['db']->getAll($game_log_sql);
				foreach($game_log as $k=>$v){
					$game_log[$k]['gain']=abs($v['gain']);
					$total_re['promoter_gain'] +=$v['promoter_gain'];//鱼乐合伙人游戏流水合计
					$total_re['platform_gain'] +=$v['platform_gain'];//平台戏流水合计
					$total_re['gain'] +=$game_log[$k]['gain'];//鱼商戏流水合计
					if($list_re[$v['bm_pid']]){ 
						$list_re[$v['bm_pid']]['promoter_gain'] =$v['promoter_gain'];
						$list_re[$v['bm_pid']]['platform_gain'] =$v['platform_gain'];
						$list_re[$v['bm_pid']]['gain'] =$game_log[$k]['gain'];
					}
				}
			}
			
		}
		
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		//模板赋值显示
		
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		
        $this->assign('list', $list);
        $this->assign('page', $page_show);
		$this->assign('begin_time', to_date($time['begin_time'], 'Y-m-d'));
		$this->assign('end_time', to_date($time['end_time'], 'Y-m-d'));
		$this->assign('user_id', $to_user_id);
		$this->assign('total_re', $total_re);
		$this->assign('bm_qrcode_id', $bm_qrcode_id);
		$this->assign('login_name', $login_name);
		
        $this->display();
    }
}
