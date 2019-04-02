<?php

fanwe_require(APP_ROOT_PATH . 'mapi/lib/user_center.action.php');

class user_centerCModule extends user_centerModule
{
    /**
     * 提现领取记录
     */
    public function extract_record()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = $GLOBALS['user_info']['id'];
        $sql = "select money,pay_time,create_time,is_pay from " . DB_PREFIX . "user_refund  where is_pay in (1,3) and user_id =" . $user_id;
        $data = $GLOBALS['db']->getAll($sql, true, true);
        $total_money = 0;
        $list = [];
        foreach ($data as $v) {
            if ($v['is_pay'] == 3) {
                $total_money += $v['money'] * 100;
            }

            $list[] = [
                'money' => number_format($v['money'], 2),
                'pay_time' => $v['pay_time'] != 0 ? date("Y年m月d日", $v['pay_time']) : '',
                'is_pay' => intval($v['is_pay']),
                'create_time' => date("Y年m月d日", $v['create_time']),
            ];
        }

        api_ajax_return([
            'status' => 1,
            'total_money' => number_format(intval($total_money) / 100, 2),
            'list' => $list,
        ]);
    }
}
