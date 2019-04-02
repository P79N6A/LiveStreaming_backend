<?php
//定时处理观看数据
function crontab_do_guard()
{
    try {
        $ret_array = array();
        $m_config = load_auto_cache("m_config");
        $limit_time = intval($m_config['guard_limit_time']) == 0 ? intval($m_config['guard_limit_time']) : 60; //间隔时间300秒
        $video_c = intval($m_config['guard_video_c']) == 0 ? intval($m_config['guard_video_c']) : 100; //观看下限
        $sql = "select id,view_count from " . DB_PREFIX . "user where is_effect = 1 and view_count > " . $video_c . " ORDER BY view_count DESC";
        //$ret_array['sql1'] = $sql;
        $list = $GLOBALS['db']->getAll($sql);
        $sql = "select user_id from " . DB_PREFIX . "user_guard";
        //$ret_array['sql2'] = $sql;
        $list_guard = $GLOBALS['db']->getAll($sql);
        foreach ($list_guard as $k => $v) {
            $list_guard_arr[$k] = $v['user_id'];
        }
        $i = 0;
        $j = 0;
        if ($list) {
            foreach ($list as $k => $v) {
                $data = array();
                if (in_array($v['id'], $list_guard_arr)) {
                    $sql = "update " . DB_PREFIX . "user_guard set old_view_count = view_count,view_count=" . $v['view_count'] . ",update_time = " . NOW_TIME . " where  view_count!=" . $v['view_count'] . " and user_id = " . $v['id'] . " and update_time < " . (NOW_TIME - $limit_time);
                    $GLOBALS['db']->query($sql);
                    $res = 0;
                    if ($GLOBALS['db']->affected_rows()) {
                        $ret_array['sql3'][$k] = $sql;
                        $res = 1;
                        $i++;
                    }
                } else {
                    $data['user_id'] = $v['id'];
                    $data['create_time'] = NOW_TIME;
                    $data['view_count'] = intval($v['view_count']);
                    $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "user_guard", $data);
                    $j++;
                }
            }
        }
        $ret_array['res'] = print_r($res, 1);
        $ret_array['i'] = $i;
        $ret_array['j'] = $j;
        return $ret_array;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
