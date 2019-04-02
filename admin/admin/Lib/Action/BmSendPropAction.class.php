<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmSendPropAction extends CommonAction
{
    public function index()
    {
		$prop_id=intval($_REQUEST['prop_id']);
        $to_user_id=intval($_REQUEST['to_user_id']);
        $from_user_id=intval($_REQUEST['from_user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}
		
        $where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
        if($prop_id >0){
            $where.=" and l.prop_id =".$prop_id." ";
        }

        if($to_user_id >0){
            $where.=" and l.to_user_id =".$to_user_id." ";
        }

        if($from_user_id >0){
            $where.=" and l.from_user_id =".$from_user_id." ";
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
			$order = "l.id";
		}
		
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		$pre = DB_PREFIX;
		
        $sql_count = "
				select 
					count(*) as list_count,
					sum(l.total_diamonds) as total_diamonds,
					sum(l.total_coins) as total_coins 
				from 
					".$pre."video_prop_".$time['begin_time_ym']." as l 
				left join ".$pre."user as u on u.id = l.from_user_id 
				LEFT JOIN ".$pre."bm_promoter as bp on u.id = bp.user_id
				where 
					".$where;
					
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
	
			$sql = "select
						l.id,l.to_user_id,
						l.from_user_id,
						l.prop_name,
						l.prop_id,
						l.total_diamonds,
						l.total_coins,
						l.create_time,
						l.is_coin,
						u.bm_pid,
						u.bm_qrcode_id,
						u.bm_promoter_id 
					from ".$pre."video_prop_".$time['begin_time_ym']." as l 
					left join ".$pre."user as u on u.id = l.from_user_id 
					LEFT JOIN ".$pre."bm_promoter as bp on u.id = bp.user_id
					where 
						".$where." order by ".$order." ".$sort." limit ".$limit;
			
			$list = $GLOBALS['db']->getAll($sql);
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
		$this->assign('total_diamonds', $list_count_row['total_diamonds']);
		$this->assign('total_coins', $list_count_row['total_coins']);
		$this->assign('begin_time', to_date($time['begin_time'], 'Y-m-d'));
		$this->assign('end_time', to_date($time['end_time'], 'Y-m-d'));
		$this->assign('to_user_id', $to_user_id);
		$this->assign('from_user_id', $from_user_id);
		$this->assign('prop_id', $prop_id);
		$this->assign('login_name', $login_name);
		
        $this->display();
    }
}
