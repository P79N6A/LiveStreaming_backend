<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class IndexsAction extends AuthAction
{
    /*
     * 网站数据统计
     */
    public function statistics()
    {

        $user_count = M("User")->where("is_robot=0")->count();
        $no_effect = M("User")->where("is_robot=0 and is_effect=0 or is_effect=2")->count(); //未审核
        $is_effect = M("User")->where("is_robot=0 and is_effect=1")->count(); //审核

        //认证
        $user_authentication = M("User")->where("is_authentication = 2 and user_type=0  and is_effect=1 and is_robot = 0")->count();
        $business_authentication = M("User")->where("is_authentication = 2 and user_type=1 and is_effect=1 and is_robot = 0")->count();
        $all_authentication = M("User")->where(" (user_type=0 or user_type=1) and is_authentication =2 and is_effect=1 and is_robot = 0")->count();

        //树苗订单
        $tree_order_count = M("QkTreeOrder")->count();
        $total_tree_order_money = M("QkTreeOrder")->sum('pay');
        $m_config = load_auto_cache('m_config');
        $diamonds_name = $m_config['diamonds_name'];

        //资金进出
        //线上充值
        $online_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "payment_notice where is_paid = 1 and payment_id>0  "));
        $day_online_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "payment_notice where is_paid = 1 and pay_date = '" . date('Y-m-d') . "'"));
        $all_diamonds = floatval($GLOBALS['db']->getOne("SELECT sum(`diamonds`) FROM " . DB_PREFIX . "user where is_robot = 0"));
        $day_add = ($GLOBALS['db']->getRow("SELECT sum(`diamonds`) AS all_diamonds,SUM(`ticket`) AS all_ticket FROM " . DB_PREFIX . "user_recharge_log WHERE log_time BETWEEN " . strtotime('00:00:00') . " AND " . strtotime('23:59:59')));
        $all_add = ($GLOBALS['db']->getRow("SELECT sum(`diamonds`) AS all_diamonds,SUM(`ticket`) AS all_ticket FROM " . DB_PREFIX . "user_recharge_log"));

        $this->assign("online_pay", $online_pay);
        $this->assign("day_online_pay", $day_online_pay);
        $this->assign("all_diamonds", $all_diamonds);
        $this->assign("day_add", $day_add);
        $this->assign("all_add", $all_add);

        // 提现
        $carry_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "user_refund WHERE is_pay = 0"));
        $all_carry_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "user_refund WHERE is_pay = 3"));
        $day_carry_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "user_refund WHERE is_pay = 0 AND create_time BETWEEN " . strtotime('00:00:00') . " AND " . strtotime('23:59:59')));
        $this->assign("carry_pay", $carry_pay);
        $this->assign("all_carry_pay", $all_carry_pay);
        $this->assign("day_carry_pay", $day_carry_pay);
        //总计
        $total_usre_money = $online_pay;
        $this->assign("total_usre_money", $total_usre_money);

        $this->assign("diamonds_name", $diamonds_name);
        $this->assign("tree_order_count", $tree_order_count);
        $this->assign("total_tree_order_money", $total_tree_order_money);
        $this->assign("user_count", $user_count);
        $this->assign("no_effect", $no_effect);
        $this->assign("is_effect", $is_effect);
        $this->assign("user_authentication", $user_authentication);
        $this->assign("business_authentication", $business_authentication);
        $this->assign("all_authentication", $all_authentication);
        $this->display();
    }

    /*
     * 分销统计
     */
    public function distribution_statistics()
    {
        $list = array();
        $timezone = intval(app_conf('TIME_ZONE'));
        $model = M('User');
        $table = DB_PREFIX . 'user';
        $field = 'p_user_id as id';
        $where = 'p_user_id > 0';
        if (isset($_REQUEST['id'])) {
            $where .= ' and p_user_id like \'%' . addslashes($_REQUEST['id']) . '%\'';
        }
        $where .= " group by p_user_id ";
        $id_list = $model->table($table)->where($where)->field($field)->select(); //获取所有分销上级ID
        $count = 0; //用来存放用户总量
        /*admin_ajax_return($id_list);*/
        if ($id_list) {
            $field = 'id,nick_name,create_time';
            $where = '';
            foreach ($id_list as $k => $v) {
                $where .= " id = '{$v['id']}' ||";
            }
            $where = rtrim($where, '||');
            if (isset($_REQUEST['nick_name'])) {
                $where .= ' and nick_name like \'%' . addslashes($_REQUEST['nick_name']) . '%\''; //搜索条件
            }
            if ($_REQUEST['begin_time']) {
                $where .= ' and create_time>=' . (strtotime($_REQUEST['begin_time']) - $timezone * 3600);
            }
            if ($_REQUEST['end_time']) {
                $where .= ' and create_time<=' . (strtotime($_REQUEST['end_time']) - $timezone * 3600);
            }
            $count = $model->table($table)->where($where)->field($field)->order("id desc")->count(); //获取用户总量
            $p = new Page($count, $listRows = 10); //实例化分页

            $list = $model->table($table)->where($where)->field($field)->order("id desc")->limit($p->firstRow . ',' . $p->listRows)->select(); //分页获取所有用户信息数据

            for ($i = 0; $i < count($list); $i++) //时间戳转为日期格式
            {
                $list[$i]['create_time'] = to_date($list[$i]['create_time']);
            }
            /* admin_ajax_return($list);*/
            if (!empty($list)) //若有用户
            {
                //统计下级用户数
                $field = 'p_user_id,count(p_user_id) as sub_nums';
                $where = '';
                foreach ($list as $k => $v) //拼接下级用户数统计条件
                {
                    $where .= " p_user_id = '{$v['id']}' ||";
                }
                $where = rtrim($where, '||');
                $where .= "group by p_user_id";
                $sub_nums = $model->table($table)->where($where)->field($field)->order("p_user_id desc")->select(); //获取所有用户的下级用户数
                /*admin_ajax_return($sub_nums);*/
                for ($i = 0; $i < count($list); $i++) //整理进list
                {
                    $list[$i]['sub_nums'] = $sub_nums[$i]['sub_nums'];
                }
                /*admin_ajax_return($list);*/
                //获取所有下级用户ID，用来后面的充值总额统计
                $field = 'id,p_user_id';
                $where = rtrim($where, 'p_user_id'); //修正分组条件
                $where .= "id";
                $user_subs = $model->table($table)->where($where)->field($field)->select(); //获取所有下线用户ID
                /*admin_ajax_return($user_subs);*/
                //统计所有用户分销收益总秀票数
                $table = DB_PREFIX . 'user_log';
                $field = " user_id,sum(ticket) as ticket_income ";
                $where = " type = 9 and (";
                foreach ($list as $k => $v) {
                    $where .= " user_id = '{$v['id']}' ||";
                }
                $where = rtrim($where, '||');
                $where .= ") group by user_id";
                $ticket_income = $model->table($table)->where($where)->field($field)->order("user_id desc")->select(); //获取所有用户分销秀票收益
                /*admin_ajax_return($ticket_income);*/
                for ($i = 0; $i < count($list); $i++) //整理进list
                {
                    for ($j = 0; $j < count($ticket_income); $j++) {
                        if ($list[$i]['id'] == $ticket_income[$j]['user_id']) {
                            $list[$i]['ticket_income'] = $ticket_income[$j]['ticket_income'];
                            break;
                        }
                    }
                    if ($j == count($ticket_income)) {
                        $list[$i]['ticket_income'] = 0;
                    }
                }
                /*admin_ajax_return($list);*/
                //统计下级充值总额和充值人数
                $table = DB_PREFIX . 'payment_notice';
                $field = " user_id,sum(money) as recharge_count ";
                $where = "is_paid = 1 and (";
                foreach ($user_subs as $k => $v) {
                    $where .= " user_id = '{$v['id']}' ||";
                }
                $where = rtrim($where, '||');
                $where .= ") group by user_id";
                $recharge_count_all = $model->table($table)->where($where)->field($field)->select(); //获取所有充值数据
                $recharge_count = array(); //存放充值总额和上级ID对应的数据
                foreach ($user_subs as $k => $v) {
                    $recharge_count[$k] = $v;
                    $count1 = 0; //统计单个用户充值量
                    foreach ($recharge_count_all as $key => $val) {
                        if ($recharge_count[$k]['id'] == $val['user_id']) {
                            $count1 += floatval($val['recharge_count']);
                        }
                    }
                    $recharge_count[$k]['recharge_count'] = $count1; //获取用户ID，下家ID，该下家充值总额的数组
                }
                $recharge_total = array();
                foreach ($recharge_count as $k => $v) {
                    //整理成ID与所有下家总额的数组
                    for ($i = 0; $i < count($recharge_total); $i++) {
                        if ($recharge_total[$i]['p_user_id'] == $v['p_user_id']) {
                            break;
                        }
                    }
                    if ($i >= count($recharge_total)) {
                        $recharge_total[$i]['p_user_id'] = $v['p_user_id']; //上级ID
                        $recharge_total[$i]['recharge_count'] = 0; //下级充值总额
                        $recharge_total[$i]['recharge_nums'] = 0; //下级充值人数
                        $nums = array(); //用来统计下级充值人数
                        for ($j = 0; $j < count($recharge_count); $j++) {
                            if ($recharge_total[$i]['p_user_id'] == $recharge_count[$j]['p_user_id']) {
                                $recharge_total[$i]['recharge_count'] += $recharge_count[$j]['recharge_count'];
                                if (!in_array($recharge_count[$j]['id'], $nums) && $recharge_count[$j]['recharge_count'] > 0) {
                                    $nums[] = $recharge_count[$j]['id'];
                                }
                            }
                        }
                        $recharge_total[$i]['recharge_nums'] = count($nums); //统计人数
                    }
                }
                /*admin_ajax_return($recharge_total);*/
                //整理进list
                for ($i = 0; $i < count($list); $i++) //整理进list
                {
                    $list[$i]['recharge_count'] = $recharge_total[count($list) - 1 - $i]['recharge_count'];
                    $list[$i]['recharge_nums'] = $recharge_total[count($list) - 1 - $i]['recharge_nums'];
                }
            }
            $this->assign("page", $p->show());
        }
        $this->assign("count", $count);
        $this->assign("list", $list);
        $this->display();
    }

}
