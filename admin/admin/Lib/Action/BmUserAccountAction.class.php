<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmUserAccountAction extends CommonAction
{
    public function index()
    {		
		$login_name=strim($_REQUEST['login_name']);
        $user_id=intval($_REQUEST['user_id']);
        $bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);
		$login_name=strim($_REQUEST['login_name']);

        /*$time = $this->check_date();
		if($time['status']==0)
		{
			$this->error($time['error']);
		}*/
		
        $where="u.is_robot =0 and bm_pid>0";

        if($bm_qrcode_id >0){
            $where .=" and u.bm_qrcode_id =".$bm_qrcode_id."";
        } 
        if($user_id >0){
            $where .=" and u.id =".$user_id."";
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
		
        $sql_count="select 
						count(*) as list_count,
						sum(u.diamonds) as total_diamonds,
						sum(u.coin) as total_coins 
					from 
						".$pre."user as u 
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
	
			 $sql="select 
			 			u.id,
						u.nick_name,
						u.diamonds,
						u.coin,
						u.bm_pid,
						u.is_effect,
						u.bm_qrcode_id,
						p.name as p_name,
						p.login_name,
						q.name qrcode_name,
						q.promoter_id,
						u.bm_qrcode_id
					from 
						".$pre."user as u
              		left join ".$pre."bm_promoter as p on p.user_id =u.bm_pid 
					left join ".$pre."bm_qrcode as q on q.id = u.bm_qrcode_id 
					where 
						".$where." order by ".$order." ".$sort." limit ".$limit;
			
			$list = $GLOBALS['db']->getAll($sql);
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		$this->assign ( 'list', $redis['list'] );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
        $this->assign('list', $list);
        $this->assign('page', $page_show);
		$this->assign('total_diamonds', $list_count_row['total_diamonds']);
		$this->assign('total_coins', $list_count_row['total_coins']);
		$this->assign('user_id', $user_id);
		$this->assign('bm_qrcode_id', $bm_qrcode_id);
		$this->assign('login_name', $login_name);
		
        $this->display();
    }
}
