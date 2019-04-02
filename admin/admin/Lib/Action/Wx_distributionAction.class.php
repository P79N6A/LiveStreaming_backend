<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class Wx_distributionAction extends AuthAction
{
    //首页
    public function index()
    {
        if (trim($_REQUEST['nick_name']) != '') {
            $where = " and u1.nick_name like '%" . trim($_REQUEST['nick_name']) . "%'";
        }
        if (trim($_REQUEST['mobile']) != '') {

            $where .= "and u1.mobile like '%" . trim($_REQUEST['mobile']) . "%'";
        }

        if (intval($_REQUEST['id']) != '') {

            $where .= "and u1.id =" . intval($_REQUEST['id']) . "";
        }
        $pre = DB_PREFIX;
        $sql = "SELECT
                    u1.id,
                    u1.nick_name,
                    u1.mobile,
                    ifnull(a.num, 0) AS num,
                    b.count
                FROM
                    {$pre}user AS u1
                LEFT JOIN (
                    SELECT
                        topid,
                        count(1) AS count
                    FROM
                        {$pre}weixin_distribution
                    GROUP BY
                        topid
                ) AS b ON b.topid = u1.id,
                 {$pre}weixin_distribution AS u2
                LEFT JOIN (
                    SELECT
                        d.topid,
                        SUM(
                            l.first_distreibution_money + l.second_distreibution_money
                        ) AS num
                    FROM
                        {$pre}weixin_distribution d,
                        {$pre}weixin_distribution_log l
                    WHERE
                        l.user_id = d.user_id
                    GROUP BY
                        d.topid
                ) AS a ON a.topid = u2.user_id
                WHERE
                    u1.id = u2.user_id
                AND u2.user_id = u2.topid $where";
        $user_list = $GLOBALS['db']->getAll($sql);

        $this->assign('user_list', $user_list);
        $this->display();
    }

    //游戏分销
    public function yx_distribution()
    {
        $root = $this->getRoot();
        $this->assign('sum_money', intval($root['sum_money']));
        $this->assign('sum_distribution', intval($root['sum_distribution']));
        $this->display();
    }

    //游戏消费
    public function yx_consumption()
    {
        $root = $this->getRoot();
        $this->assign('sum_bet', intval($root['sum_bet']));
        $this->assign('sum_gain', intval($root['sum_gain']));
        $this->display();
    }

    //礼物分销
    public function lw_distribution()
    {
        $root = $this->getRoot();
        $this->assign('sum_money', intval($root['sum_money']));
        $this->assign('sum_distribution', intval($root['sum_distribution']));
        $this->display();
    }

    //礼物消费
    public function lw_consumption()
    {
        $root = $this->getRoot();

        $this->assign('total_diamonds', intval($root['total_diamonds']));
        $this->display();
    }
    public function getRoot()
    {
        $id = $_REQUEST['id'];
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        NewModel::$lib = APP_ROOT_PATH . 'mapi/lib/';

        $type        = intval($_REQUEST['type']); //0游戏分销，1礼物分销，2游戏消费，3礼物消费
        $year        = intval($_REQUEST['year']);
        $month       = intval($_REQUEST['month']);
        $user_id     = intval($_REQUEST['user_id']);
        $game_log_id = intval($_REQUEST['game_log_id']); //游戏ID
        if (!isset($_REQUEST['is_group'])) {
            $_REQUEST['is_group'] = 1;
        }

        $where = [];

        $y = date('Y');
        $m = date('m');
        if (!($year && $month)) {
            $year  = $y;
            $month = $m;
        }
        $start                  = strtotime("{$year}-{$month}-1 00:00:00");
        $end                    = strtotime('+1 month', $start);
        $where['l.create_time'] = ['between', [$start, $end]];
        if ($user_id) {
            $where['d.user_id'] = $user_id;
        }
        if ($game_log_id) {
            $where['l.game_log_id'] = $game_log_id;
        }
        $d = NewModel::build('weixin_distribution')->selectOne(['user_id' => $id]);
        if ($d['topid']) {
            $model = NewModel::build('weixin_distribution_log');
            switch ($type) {
                case 1:
                    $root = $model->childPropDistribution($id, $where, intval($_REQUEST['is_group']));
                    break;
                case 2:
                    $root = $model->childGame($id, $where, intval($_REQUEST['is_group']));
                    break;
                case 3:
                    unset($where['l.create_time']);
                    unset($where['l.game_log_id']);
                    $root = $model->childProp($id, $where, $start, intval($_REQUEST['is_group']));
                    break;
                default:
                    $root = $model->childGameDistribution($id, $where, intval($_REQUEST['is_group']));
                    break;
            }
        }
        $root['type']        = $type;
        $root['page_title']  = "个人中心-微信分销";
        $root['year']        = $year;
        $root['month']       = $month;
        $root['user_id']     = $user_id;
        $root['game_log_id'] = $game_log_id;
        $root['years']       = range($y, $y - 5);
        $root['months']      = range(1, 12);
        $root['act']         = 'weixin_distribution';

        $this->assign("is_group", $_REQUEST['is_group']);
        $this->assign("year", $root['year']);
        $this->assign("month", $root['month']);
        $this->assign("years", $root['years']);
        $this->assign("months", $root['months']);
        $this->assign('list', $root['list']);
        $this->assign('sum_first', intval($root['sum_first']));
        $this->assign('sum_second', intval($root['sum_second']));
        $this->assign('sum_child', intval($root['sum_child']));
        return $root;
    }

}
