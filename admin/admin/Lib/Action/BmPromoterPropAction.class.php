<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterPropAction extends CommonAction
{
    public function index()
    {
        $nick_name = trim($_REQUEST['nick_name']);
        $name = trim($_REQUEST['name']);
        $p_name = trim($_REQUEST['p_name']);
        $bm_pid = intval($_REQUEST['bm_pid']);

        $year = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        $y = date('Y');
        $m = date('m');
        if (!($year && $month)) {
            $year = $y;
            $month = $m;
        }
        $years = range($y, $y - 5);
        $months = range(1, 12);

        $user_list = array();
        if ($p_name) {
            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where name like '%{$p_name}%' and is_effect=1 and status=1";
            $promoter_list = $GLOBALS['db']->getAll($sql_p, true, true);

            $user_list = array_map('array_shift', $promoter_list);
        } else if ($name) {
            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where name like '%{$nick_name}%' and is_effect=1 and status=1";
            $promoter_list1 = $GLOBALS['db']->getAll($sql_p, true, true);

            $promoter_list2 = array_map('array_shift', $promoter_list1);

            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where pid in (" . implode(',', $promoter_list2) . ") and name like '%{$nick_name}%' and is_effect=1 and status=1";
            $promoter_list3 = $GLOBALS['db']->getAll($sql_p, true, true);

            $user_list = array_map('array_shift', $promoter_list3);

        }

        $id_list = array();
        $id_list = $user_list; //array_map('array_shift',$user_list);

        $where = "  u.is_authentication=2 ";

        if ($nick_name) {
            $where .= " and u.nick_name like '%{$nick_name}%'";
        }

        if ($bm_pid > 0) {
            $where .= " and u.bm_pid =" . $bm_pid;
        } elseif (count($id_list) > 0) {

            $where .= " and u.bm_pid in (" . implode(',', $id_list) . ") ";
        }

        $time = $y . '' . $m;

        $sql = "SELECT l.id,u.id as user_id,l.to_user_id,sum(l.total_ticket) as total_ticket,u.nick_name,u.bm_special,bp.name as name,bp.user_id as promoter_user_id FROM   "
            . DB_PREFIX . "video_prop_" . $time . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.to_user_id = u.id" . " LEFT JOIN "
            . DB_PREFIX . "bm_promoter AS bp ON bp.user_id = u.bm_pid" . " WHERE "
            . $where . " GROUP BY l.to_user_id";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne($sql, true, true);
        $page = new Page($count, $page_size);
        $page_show = $page->show();

        $list = $GLOBALS['db']->getAll($sql, true, true);

        $this->assign("year", $year);
        $this->assign("month", $month);
        $this->assign("years", $years);
        $this->assign("months", $months);
        $this->assign('list', $list);
        $this->assign('page', $page_show);
        $this->display();
    }

}
