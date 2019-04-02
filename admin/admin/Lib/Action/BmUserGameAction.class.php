<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmUserGameAction extends CommonAction
{
    public function index()
    {
     	$id=intval($_REQUEST['id']);
     	$game_name=trim($_REQUEST['game_name']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=trim($_REQUEST['bm_qrcode_id']);
     	//时间
        $time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}
		
		$where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	if($id > 0){
     		$where .= " and  u.id = ".$id." ";
     	}
     	if($game_name != ''){
     		$where .=" and gs.name like '%".$game_name."%' ";
     	}
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
		
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = strim($_REQUEST ['_order']);
		} else {
			$order = "bpg.create_time";
		}
		
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		$pre = DB_PREFIX;
		
		
        $sql_count ="SELECT 
						count(*) as list_count
					FROM  "
						. $pre . "bm_promoter_game_log as bpg 
					LEFT JOIN ". $pre . "user as u on bpg.user_id = u.id 
					LEFT JOIN ". $pre . "bm_promoter as bp on u.id = bp.user_id 
					LEFT JOIN ". $pre . "games as gs on bpg.game_id = gs.id  
					WHERE 
						$where ";
		
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
	
	
			$sql = 
				"SELECT 
					bpg.create_time,
					gs.name as game_name,
					bpg.sum_bet,if(bpg.sum_win > 0, abs(bpg.sum_win),0) as sum_win,
					if(bpg.sum_win < 0, abs(bpg.sum_win),0) as sum_fail,
					(bpg.platform_gain + bpg.promoter_gain) as user_gain ,
					u.id,u.nick_name,
					u.bm_pid,
					u.bm_qrcode_id,
					u.bm_promoter_id 
				FROM   
					". $pre . "bm_promoter_game_log as bpg 
				LEFT JOIN ". $pre . "user as u on bpg.user_id = u.id 
				LEFT JOIN ". $pre . "bm_promoter as bp on u.id = bp.user_id 
				LEFT JOIN ". $pre . "games as gs on bpg.game_id = gs.id  
				WHERE 
					$where "
				." order by ".$order." ".$sort."
				limit " . $limit;
	        
			
			$list = $GLOBALS['db']->getAll($sql);

		}
		
		$total = $GLOBALS['db']->getRow( 
			"SELECT 
				sum(if(bpg.sum_win > 0, bpg.sum_win,0)) as sum_win,
				sum(if(bpg.sum_win < 0, abs(bpg.sum_win),0)) as sum_fail,
				sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain 
			FROM   
				". $pre . "bm_promoter_game_log as bpg 
			LEFT JOIN ". $pre . "user as u on bpg.user_id = u.id 
			LEFT JOIN ". $pre . "bm_promoter as bp on u.id = bp.user_id 
			LEFT JOIN ". $pre . "games as gs on bpg.game_id = gs.id  
			WHERE $where ");
		
		
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
		$this->assign('total_diamonds', $list_count_row['total_diamonds']);
		$this->assign('total_coins', $list_count_row['total_coins']);
		$this->assign('begin_time', to_date($time['begin_time'], 'Y-m-d'));
		$this->assign('end_time', to_date($time['end_time'], 'Y-m-d'));
		$this->assign('total', $total);
		
        $this->display();
    }
}
