<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmAnchorPropAction extends CommonAction
{
    public function index()
    {
		$bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);
        $to_user_id=intval($_REQUEST['to_user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}
		
		$where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
        if($bm_qrcode_id >0){
            $where.=" and u.bm_qrcode_id = ".$bm_qrcode_id."";
        }
        if($to_user_id >0){ 
            $where.=" and l.to_user_id = ".$to_user_id."";
        }
		if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
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
						count(*) as list_count
					from ".$pre."video_prop_".$time['begin_time_ym']." as l 
					left join ".$pre."user as u on u.id = l.from_user_id 
					LEFT JOIN ".$pre."bm_promoter as bp on u.id = bp.user_id
					where 
						".$where." 
					GROUP BY l.to_user_id";
					
		$list_count_row=$GLOBALS['db']->getRow($sql_count);
		
		$p = $_REQUEST['p'];
		if ($p == '') {
			$p = 1;
		}
		$p         = $p > 0 ? $p : 1;
		$page_size = 10;
		$limit     = (($p - 1) * $page_size) . "," . $page_size;

		$count     = intval($list_count_row['list_count']);

        if($count > 0){

			$page      = new Page($count, $page_size);
			$page_show = $page->show();
	
			$sql="select 
					l.id,
					l.to_user_id,
					sum(l.total_diamonds) as total_diamonds,
					sum(l.total_coins) as total_coins,
					sum(l.total_ticket) as total_ticket,
					l.create_time,
					u.nick_name,
					u.bm_pid,
					u.bm_qrcode_id,
					u.bm_promoter_id as qrcode_promoter_id 
				from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l
				left join ".DB_PREFIX."user as u on u.id = l.to_user_id 
				LEFT JOIN ".$pre."bm_promoter as bp on u.id = bp.user_id
				where ".$where." 
				GROUP BY l.to_user_id 
				order by ".$order." ".$sort." 
				limit ".$limit; 
			
			$list = $GLOBALS['db']->getAll($sql);
			
			$bm_config = load_auto_cache("bm_config");
			//代理商签约主播收益分成
			$promoter_sign_anchor_revenue = intval($bm_config['promoter_sign_anchor_revenue']);
			if ($promoter_sign_anchor_revenue < -1 || $promoter_sign_anchor_revenue > 100) {
				$promoter_sign_anchor_revenue = 90;
			}
			//代理商普通主播收益分成
			$promoter_average_anchor_revenue = intval($bm_config['promoter_average_anchor_revenue']);
			if ($promoter_average_anchor_revenue < -1 || $promoter_average_anchor_revenue > 100) {
				$promoter_average_anchor_revenue = 70;
			}
	
			foreach($list as $k=>$v){	
				//计算
				$list[$k]['user_ticket'] = $v['total_ticket'] * ($promoter_average_anchor_revenue / 100);
				$list[$k]['promoter_ticket'] = $list[$k]['total_ticket'] - $list[$k]['user_ticket'];
	
			}
		}
		
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		//模板赋值显示

		$this->assign ( 'list', $redis['list'] );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		
        $this->assign('list', $list);
        $this->assign('page', $page_show);
		$this->assign('begin_time', to_date($time['begin_time'], 'Y-m-d'));
		$this->assign('end_time', to_date($time['end_time'], 'Y-m-d'));
		$this->assign('to_user_id', $to_user_id);
		$this->assign('bm_qrcode_id', $bm_qrcode_id);
		$this->assign('login_name', $login_name);
		
        $this->display();
    }
}
