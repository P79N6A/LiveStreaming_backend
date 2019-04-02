<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmUserMarginAction extends CommonAction
{
    public function index()
    {
		$bm_config = load_auto_cache("bm_config");
		
     	$login_name=trim($_REQUEST['login_name']);
        $time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}
		
        $where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
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
						count(*) as list_count
					  FROM   
					  	". $pre . "user AS u 
					  LEFT JOIN ". $pre . "bm_promoter_game_log as bpg on bpg.user_id = u.id 
					  LEFT JOIN ". $pre . "bm_promoter as bp on u.id = bp.pid 
					  WHERE 
					  	$where  ";
					
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
						bp.login_name,
						sum(bpg.gain) as gain,
						u.id ,
						u.coin
					FROM   ". $pre . "user AS u 
					LEFT JOIN ". $pre . "bm_promoter_game_log as bpg on bpg.user_id = u.id 
					LEFT JOIN ". $pre . "bm_promoter as bp on u.id = bp.pid 
					WHERE 
						".$where." 
					order by ".$order." ".$sort." 
					limit ".$limit;

			$list = $GLOBALS['db']->getAll($sql);
			
			foreach($list as $k=>$v)
            {
            	//鱼乐合伙人编号
            	//$list[$k]['bm_login_name'] = $GLOBALS['db']->getOne("select login_name from ". DB_PREFIX . "bm_promoter where user_id=".$v['id']);
            	//保证金
            	$list[$k]['promoter_deposit'] = $bm_config['promoter_deposit'];
            	//可提现
				$list[$k]['refund_coin'] = $v['coin'] - $bm_config['promoter_deposit'];

				if($list[$k]['refund_coin'] < 0)
				{
					$list[$k]['refund_coin'] = 0 ;
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
		$this->assign('total_diamonds', $list_count_row['total_diamonds']);
		$this->assign('total_coins', $list_count_row['total_coins']);
		$this->assign('begin_time', to_date($time['begin_time'], 'Y-m-d'));
		$this->assign('end_time', to_date($time['end_time'], 'Y-m-d'));
		$this->assign('login_name', $login_name);
		
        $this->display();
    }
}
