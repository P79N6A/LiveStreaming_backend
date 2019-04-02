<?php

class videosmall_select_weibo_info_comments_auto_cache extends auto_cache
{
    private $key = "videosmall_select_:weibo_info_comments:";
    public function load($param)
    {
//        $type = intval($param['type']);
        //        $this->key .= $type;
        fanwe_require(APP_ROOT_PATH . 'mapi/xr/core/common.php');
        $this->key .= md5(serialize($param));
        $page = $param['page'] > 0 ? $param['page'] : 1;
        $page_size = $param['page_size'] > 0 ? $param['page_size'] : 20;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        $weibo_id = intval($param['weibo_id']);
        $key_bf = $this->key . '_bf';

        //$list = $GLOBALS['cache']->get($this->key,true);
        // if ($list === false) {
        //     $is_ok = $GLOBALS['cache']->set_lock($this->key);
        //     if (!$is_ok) {
        //         $list = $GLOBALS['cache']->get($key_bf, true);
        //     } else {
        // 评论列表
        $list = $GLOBALS['db']->getAll("SELECT wc.comment_id, wc.weibo_id AS sv_id, wc.user_id AS com_userid,u.nick_name,u.head_image,wc.content AS com_content,wc.to_comment_id,wc.to_user_id AS to_userid,wc.create_time,u.is_authentication from " . DB_PREFIX . "weibo_comment AS wc LEFT JOIN " . DB_PREFIX . "user AS u on wc.user_id = u.id WHERE wc.weibo_id = " . $weibo_id . " and type = 1 ORDER BY wc.comment_id desc limit $limit");
        if ($list) {
            $to_comment_user = array();
            foreach ($list as &$v) {
                if ($v) {
                    $v['head_image'] = get_spec_image($v['head_image']);
                    $v['left_time'] = time_tran($v['create_time']);
                    $v['com_time'] = strtotime($v['create_time']);
                    if ($v['to_comment_id'] && ($v['to_userid'] != $v['com_userid'])) {
                        $v['is_to_comment'] = 1;
                        $to_comment_user[] = $v['to_userid'];
                    } else {
                        $v['is_to_comment'] = 0;
                    }
                    $v['to_user_nickname'] = '';
                    $v['to_user_head_image'] = '';
                }
            }
            if (count($to_comment_user) > 0) {
                $user_list = $GLOBALS['db']->getAll("SELECT id,nick_name,head_image from " . DB_PREFIX . "user where id in (" . implode(',', $to_comment_user) . ")");
                $user_array = array();
                foreach ($user_list as $v_) {
                    $user_array[$v_['id']] = $v_;
                }
                foreach ($list as &$_v) {
                    if ($_v['to_userid']) {
                        $_v['to_user_nickname'] = $user_array[$_v['to_userid']]['nick_name'];
                        $_v['to_user_head_image'] = get_spec_image($user_array[$_v['to_userid']]['head_image']);
                    }
                }
            }
        }
        //         $GLOBALS['cache']->set($this->key, $list, 5, true);
        //         $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
        // echo $this->key;
        //     }
        // }

        if (empty($list)) {
            $list = array();
        }

        return $list;
    }

    public function rm()
    {

        // $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {

        // $GLOBALS['cache']->clear_by_name($this->key);
    }
}
