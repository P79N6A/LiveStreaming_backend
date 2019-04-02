<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmUserPoundageAction extends CommonAction
{
    public function index()
    {		
		$id=intval($_REQUEST['id']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=trim($_REQUEST['bm_qrcode_id']);
        $time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}
		
     	$where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
		
		if($id > 0){
     		$where .= " and u.id = ".$id." ";
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
			$order = "u.id";
		}
		
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		$pre = DB_PREFIX; 
     	
		
		
        $sql_count = "SELECT 
						count(*)  as list_count 
					  FROM ". $pre . "bm_promoter_game_log as bpg 
					  LEFT JOIN ". $pre . "user as u on bpg.user_id = u.id 
					  LEFT JOIN ". $pre . "bm_promoter as bp on u.bm_pid = bp.user_id 
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
	
			$sql = "SELECT 
					u.id,
					u.nick_name,
					(bpg.platform_gain + bpg.promoter_gain) as user_gain,
					bpg.platform_gain,
					bpg.promoter_gain,
					u.bm_pid,
					u.bm_qrcode_id,
					u.bm_promoter_id 
				FROM   
					" . $pre . "bm_promoter_game_log as bpg 
				LEFT JOIN ". $pre . "user as u on bpg.user_id = u.id 
				LEFT JOIN ". $pre . "bm_promoter as bp on u.bm_pid = bp.user_id 
				WHERE 
					$where 
				order by ".$order." ".$sort." 
				limit ".$limit;
			
			$list = $GLOBALS['db']->getAll($sql);
		}
		
		 $total = $GLOBALS['db']->getRow( 
		 		"SELECT 
					sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain,
					sum(bpg.platform_gain ) as total_platform_gain,
					sum(bpg.promoter_gain) as total_promoter_gain 
				FROM   
					". $pre . "bm_promoter_game_log as bpg 
				LEFT JOIN ". $pre . "user as u on bpg.user_id = u.id 
				LEFT JOIN ". $pre . "bm_promoter as bp on u.bm_pid = bp.user_id 
				WHERE 
					$where ");
		
		
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
		$this->assign('id', $id);
		$this->assign('total', $total);
		$this->assign('begin_time', to_date($time['begin_time'], 'Y-m-d'));
		$this->assign('end_time', to_date($time['end_time'], 'Y-m-d'));
		$this->assign('bm_qrcode_id', $bm_qrcode_id);
		$this->assign('login_name', $login_name);
		
        $this->display();
    }
}
