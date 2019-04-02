<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterPropFromAction extends CommonAction
{
    public function index()
    {
        $nick_name = trim($_REQUEST['nick_name']); //推广会员名称
        $name = trim($_REQUEST['name']); //上线推广商
        $p_name = trim($_REQUEST['p_name']); //上线推广中心
        $bm_pid = intval($_REQUEST['bm_pid']);

        $year = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        if ($month > 0 && $month < 10) {
            $month = "0" . $month;
        }
        $y = date('Y');
        $m = date('m');
        if (!($year && $month)) {
            $year = $y;
            $month = $m;
        }
        $years = range($y, $y - 5);
        $months = range(1, 12);

        $time = $year . '' . $month;
        $table = DB_PREFIX . 'video_prop_' . $time;
        $res = $GLOBALS['db']->getRow("SHOW TABLES LIKE'$table'");
        if (!$res) {
            $this->error("当前月份未开始统计，请选择正常运营月份！");
        }

        $user_list = array();
        if ($p_name) {
            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where name like '%{$p_name}%' and is_effect=1 and status=1";
            $promoter_list = $GLOBALS['db']->getAll($sql_p, true, true);
            $promoter_list2 = array_map('array_shift', $promoter_list);

            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where pid in (" . implode(',', $promoter_list2) . ") and name like '%{$name}%' and is_effect=1 and status=1";
            $promoter_list3 = $GLOBALS['db']->getAll($sql_p, true, true);
            $user_list = array_map('array_shift', $promoter_list3);
        } else if ($name) {
            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where name like '%{$name}%' and is_effect=1 and status=1";
            $promoter_list1 = $GLOBALS['db']->getAll($sql_p, true, true);

            $user_list = array_map('array_shift', $promoter_list1);

        }

        $id_list = array();
        $id_list = $user_list; //array_map('array_shift',$user_list);

        //$where="  u.is_authentication=2 ";
        $where = "  u.id>0 and l.is_coin=0 ";

        if ($nick_name) {
            $where .= " and u.nick_name like '%{$nick_name}%'";
        }

        if ($bm_pid > 0) {
            $where .= " and u.bm_pid =" . $bm_pid;
        } elseif (count($id_list) > 0) {

            $where .= " and u.bm_pid in (" . implode(',', $id_list) . ") ";
        }

        $sql = "SELECT  l.id,u.id as user_id,l.to_user_id,sum(l.total_ticket) as total_ticket,sum(l.total_diamonds) as total_diamonds,u.nick_name,u.bm_special,bp.name as name,bp.user_id as promoter_user_id,bp.pid as promoter_p_id FROM   "
            . DB_PREFIX . "video_prop_" . $time . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " LEFT JOIN "
            . DB_PREFIX . "bm_promoter AS bp ON bp.user_id = u.bm_pid" . " WHERE "
            . $where . " GROUP BY l.from_user_id";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count_list = $GLOBALS['db']->getAll($sql, true, true);
        $count = count($count_list);
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $list = $GLOBALS['db']->getAll($sql, true, true);

        $all_total_ticket = 0; //总数
        $all_total_diamonds = 0; //送出总数
        $podcast_total_ticket = 0; //代理商获取总数
        $pormoter_total_ticket = 0; //代理商获取总数
        $bm_config = load_auto_cache("bm_config");
        $promoter_sign_anchor_revenue = $bm_config['promoter_sign_anchor_revenue'];
        $promoter_average_anchor_revenue = $bm_config['promoter_average_anchor_revenue'];

        foreach ($count_list as $key => $value) {
            /*if ($value['bm_special']==1) {
            $count_list[$key]['podcast_total_ticket']=$value['total_ticket']*$promoter_average_anchor_revenue/100;
            $count_list[$key]['pormoter_total_ticket']=$value['total_ticket']-$list[$key]['podcast_total_ticket'];
            }else{
            $count_list[$key]['podcast_total_ticket']=$value['total_ticket']*$promoter_sign_anchor_revenue/100;
            $count_list[$key]['pormoter_total_ticket']=$value['total_ticket']-$list[$key]['podcast_total_ticket'];
            }*/
            $all_total_ticket += $value['total_ticket'];
            $all_total_diamonds += $value['total_diamonds'];
            //$podcast_total_ticket+=$count_list[$key]['podcast_total_ticket'];
            //$pormoter_total_ticket+=$count_list[$key]['pormoter_total_ticket'];
        }

        /*foreach ($list as $key => $value) {
        if ($value['bm_special']==1) {
        $list[$key]['podcast_total_ticket']=$value['total_ticket']*$promoter_average_anchor_revenue/100;
        $list[$key]['pormoter_total_ticket']=$value['total_ticket']-$list[$key]['podcast_total_ticket'];
        }else{
        $list[$key]['podcast_total_ticket']=$value['total_ticket']*$promoter_sign_anchor_revenue/100;
        $list[$key]['pormoter_total_ticket']=$value['total_ticket']-$list[$key]['podcast_total_ticket'];
        }
        $all_total_ticket+=$value['total_ticket'];
        $podcast_total_ticket+=$list[$key]['podcast_total_ticket'];
        $pormoter_total_ticket+=$list[$key]['pormoter_total_ticket'];
        }*/

        $this->assign("all_total_ticket", $all_total_ticket);
        $this->assign("all_total_diamonds", $all_total_diamonds);
        $this->assign("podcast_total_ticket", $podcast_total_ticket);
        $this->assign("pormoter_total_ticket", $pormoter_total_ticket);
        $this->assign("year", $year);
        $this->assign("month", $month);
        $this->assign("years", $years);
        $this->assign("months", $months);
        $this->assign('list', $list);
        $this->assign('page', $page_show);
        $this->display();
    }

}
