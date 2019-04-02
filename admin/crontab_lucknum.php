<?php
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');

function cancel_sale()
{
    $list = $GLOBALS['db']->getAll('SELECT * FROM `' . DB_PREFIX . 'luck_num` WHERE is_sale = 1');
    $tmp_data = array();
    foreach ($list as &$value) {
        // 判断是否过期
        if (intval((new DateTime(date('Y-m-d')))->diff(new DateTime(date('Y-m-d', $value['sell_time'])))->format('%R%a')) <= -$value['time']) {
            $sql = "UPDATE " . DB_PREFIX . "user u," . DB_PREFIX . "luck_num lu SET u.luck_num = '', lu.is_sale = 0, lu.sell_time = 0 WHERE lu.luck_num = u.luck_num AND lu.id = " . $value['id'];
            $GLOBALS['db']->query($sql);
            if ($GLOBALS['db']->affected_rows()) {
                array_push($tmp_data, 1);
            } else {
                $sql = "UPDATE " . DB_PREFIX . "luck_num SET is_sale = 0, sell_time = 0 WHERE id = " . $value['id'];
                $GLOBALS['db']->query($sql);
                if ($GLOBALS['db']->affected_rows()) {
                    array_push($tmp_data, 1);
                } else {
                    array_push($tmp_data, 0);
                }
            }
        }
    }
    return $tmp_data;
}

echo json_encode(cancel_sale());
