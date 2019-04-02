<?php

class PropWinningAction extends CommonAction
{
    public function index()
    {
        $where = '';
        $prop_id = 0;
        if (!empty($_REQUEST['prop_id'])) {
            // $map['prop_id'] = intval($_REQUEST['prop_id']);
            $where .= ' AND prop_id = ' . intval($_REQUEST['prop_id']);
            $prop_id = intval($_REQUEST['prop_id']);
        }
        $time = NOW_TIME;
        if (!empty($_REQUEST['create_ym'])) {
            // $map['create_ym'] = ($_REQUEST['create_ym']);
            $time = strtotime($_REQUEST['create_ym']);
            $where .= ' AND create_ym = "' . (date('Ym', $time)) . '"';
        } else {
            $where .= ' AND create_ym = "' . date('Ym') . '"';
        }

        // if (method_exists($this, '_filter')) {
        //     $this->_filter($map);
        // }
        // $name = $this->getActionName();
        // 更多的字段
        $award_load = load_auto_cache('award_list');
        $column = array_column($award_load, 'multiple');
        $column_str = implode(',', array_map(function ($v) use (&$prop_id) {
            return $v . '|winning=' . $v . '#$winning["date"]#' . $prop_id . ':' . $v . '倍|8%';
        }, $column));
        $column_value = array_fill(0, count($column), 0);
        $more_column = array_combine($column, $column_value);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
        // 获取指定的月数据
        $award_prop_all = $GLOBALS['db']->getAll('SELECT create_date, SUM( num ) AS num_all, SUM( total_diamonds ) AS total_diamonds_all FROM ' . createPropTable($time) . '  WHERE is_award = 1 ' . $where . ' GROUP BY create_date');
        $award_prop_all = array_combine(array_column($award_prop_all, 'create_date'), $award_prop_all);
        //金额
        $receive_bonus_all_list = $GLOBALS['db']->getAll('SELECT create_date, SUM( receive_bonus ) AS receive_bonus_all FROM ' . DB_PREFIX . 'award_log WHERE 1 ' . $where . ' GROUP BY create_date');
        $receive_bonus_all_list = array_combine(array_column($receive_bonus_all_list, 'create_date'), $receive_bonus_all_list);

        // 次数
        $winning_num_all_list = $GLOBALS['db']->getAll('SELECT create_date, winning_num, COUNT( winning_num ) AS winning_num_all FROM ' . DB_PREFIX . 'award_log WHERE 1 ' . $where . ' GROUP BY create_date,winning_num');
        $grouped = array();
        foreach ($winning_num_all_list as &$value) {
            $grouped[$value['create_date']][$value['winning_num']] = $value['winning_num_all'];
        }
        $list = array();
        $ym = date('Y-m-', $time);
        foreach (range(1, date('t', $time)) as $value) {
            $date = $ym . ($value < 10 ? ('0' . $value) : $value);
            $list[] = array(
                'date' => $date,
                'num_all' => (isset($award_prop_all[$date]) ? $award_prop_all[$date]['num_all'] : 0),
                'total_diamonds_all' => (isset($award_prop_all[$date]) ? $award_prop_all[$date]['total_diamonds_all'] : 0)
            ) + ((isset($grouped[$date])) ? (array_intersect_key($grouped[$date], $more_column) + $more_column) : $more_column) + array(
                'receive_bonus_all' => (isset($receive_bonus_all_list[$date]) ? $receive_bonus_all_list[$date]['receive_bonus_all'] : 0),
                'profit' => (isset($award_prop_all[$date]) ? $award_prop_all[$date]['total_diamonds_all'] : 0) - (isset($receive_bonus_all_list[$date]) ? $receive_bonus_all_list[$date]['receive_bonus_all'] : 0)
            );
        }
        $list[] = array(
            'date' => '总计',
            'num_all' => array_sum(array_column($list, 'num_all')),
            'total_diamonds_all' => array_sum(array_column($list, 'total_diamonds_all'))
        ) + $column_value + array(
            'receive_bonus_all' => array_sum(array_column($list, 'receive_bonus_all')),
            'profit' => array_sum(array_column($list, 'profit'))
        );
        $this->assign('list', $list);
        $this->assign('prop_list', M("Prop")->where(array('is_award' => 1))->findAll());
        $this->assign('column_str', "date:日期|10%,num_all:送出数量|10%,total_diamonds_all:送出价值(秀豆)|10%,{$column_str},receive_bonus_all:中出(秀豆)|10%,profit:收益(秀豆)|10%");
        $this->display();
    }

    public function detail_list()
    {

        $where = '';
        $parameter = '';
        if (empty($_REQUEST['winning_num'])) {
            $this->error("参数错误，倍率不能为空");
        }

        if (!empty($_REQUEST['prop_id'])) {
            // $map['prop_id'] = intval($_REQUEST['prop_id']);
            $where .= ' AND al.prop_id = ' . intval($_REQUEST['prop_id']);
            $parameter .= ' & al.prop_id = ' . intval($_REQUEST['prop_id']);
        }
        // $map['winning_num'] = intval($_REQUEST['winning_num']);
        $where .= ' AND al.winning_num = ' . intval($_REQUEST['winning_num']);
        $parameter .= ' & al.winning_num = ' . intval($_REQUEST['winning_num']);
        $time = NOW_TIME;
        if (!empty($_REQUEST['create_date'])) {
            // $map['create_date'] = ($_REQUEST['create_date']);
            $time = strtotime($_REQUEST['create_date']);
            $where .= ' AND al.create_date = "' . ($_REQUEST['create_date']) . '"';
            $parameter .= ' & al.create_date = "' . ($_REQUEST['create_date']) . '"';
        }

        //列表过滤器，生成时间搜索查询Map对象
        $map = $this->com_search();
        //查看是否有进行时间搜索
        if (!empty($map['start_time']) && !empty($map['end_time'])) {
            $parameter .= " & al.create_time BETWEEN '" . $map['start_time'] . "' AND '" . $map['end_time'] . "'";
            $where .= " AND al.create_time BETWEEN '" . $map['start_time'] . "' AND '" . $map['end_time'] . "'";
        }
        //查看是否有进行使用者或接收者ID搜索
        if (!empty($_REQUEST['from_user_id'])) {
            $parameter .= " & user_id LIKE '%" . strim($_REQUEST['from_user_id']) . "%'";
            $where .= " AND user_id LIKE '%" . strim($_REQUEST['from_user_id']) . "%'";
        }
        if (!empty($_REQUEST['to_user_id'])) {
            $parameter .= " & to_user_id LIKE '%" . strim($_REQUEST['to_user_id']) . "%' ";
            $where .= " AND to_user_id LIKE '%" . strim($_REQUEST['to_user_id']) . "%' ";
        }

        $model = D();

        $sql_str = "SELECT al.create_date, al.user_id, p.name, al.to_user_id, COUNT( 1 ) AS award_num FROM `" . DB_PREFIX . "award_log` AS al LEFT JOIN " . DB_PREFIX . "prop AS p ON p.id = al.prop_id WHERE 1";

        $sql_str .= $where . " GROUP BY al.create_date, al.user_id, al.to_user_id, al.prop_id";

        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'al.id', 0);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        foreach ($voList as &$value) {
            $user_info = $user_redis->getRow_db($value['user_id'], array('nick_name', 'user_level'));
            $to_user_info = $user_redis->getRow_db($value['to_user_id'], array('nick_name', 'user_level'));
            $value['user_nick_name'] = $user_info['nick_name'];
            $value['user_level'] = $user_info['user_level'];
            $value['to_user_nick_name'] = $to_user_info['nick_name'];
            $value['to_user_level'] = $to_user_info['user_level'];
        }
        $this->assign('list', $voList);
        $this->display();
    }
}
