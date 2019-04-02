<?php

class videosmall_select_weibo_info_auto_cache extends auto_cache
{
    private $key = "videosmall_select:weibo_info:";
    public function load($param)
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/xr/core/common.php');
        $this->key .= md5(serialize($param));
        $one = isset($param['one']);
        $reload = isset($param['reload']);
        $weibo_id = intval($param['weibo_id']);
        $key_bf = $this->key . '_bf';

        // $list = $GLOBALS['cache']->get($this->key, true);
        // if (($list === false) || $reload) {
        //     $is_ok = $GLOBALS['cache']->set_lock($this->key);
        //     if (!$is_ok && !$reload) {
        //         $list = $GLOBALS['cache']->get($key_bf, true);
        //     } else {
        $weibo_info = $GLOBALS['db']->getRow("SELECT w.id as sv_id,w.user_id AS sv_userid,w.content AS sv_content,w.video_count AS click,u.head_image,u.is_authentication,w.red_count,w.digg_count AS count_praise,w.comment_count AS count_comment,w.gift_count AS count_gift,w.data,u.nick_name,w.sort_num,w.photo_image AS sv_img,u.city,w.is_top,w.price,w.type,w.create_time from " . DB_PREFIX . "weibo as w left join " . DB_PREFIX . "user AS u ON w.user_id = u.id WHERE w.id = " . $weibo_id . " LIMIT 1");
        if (!empty($weibo_info)) {
            $weibo_info['sv_time'] = strtotime($weibo_info['create_time']);
            $weibo_info['left_time'] = time_tran($weibo_info['create_time']);
            if ($weibo_info['head_image']) {
                $weibo_info['head_image'] = deal_weio_image($weibo_info['head_image']);
            }
            if ($weibo_info['sv_img']) {
                $weibo_info['sv_img'] = deal_weio_image($weibo_info['sv_img'], $weibo_info['type'] . '_info');
            }
            $weibo_info['images_count'] = 0;
            if ($weibo_info['type'] == 'video') {
                $weibo_info['images'] = array();
                $url = $weibo_info['data'];
                $weibo_info['sv_url'] = get_file_oss_url($url);
            } else {
                $images = unserialize($weibo_info['data']);
                // if(in_array($weibo_info['weibo_id'],$order_list_array)){
                //                            $is_pay =1;
                //                        }else{
                //                            $is_pay =0;
                //                        }
                if (count($images) > 0) {
                    $weibo_info['images'] = $images;
                    $weibo_info['images_count'] = count($images);
                } else {
                    $weibo_info['images'] = array();
                }
                $weibo_info['sv_url'] = '';
            }
        }

        if (empty($weibo_info)) {
            $weibo_info = array();
        }

        return $weibo_info;
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
