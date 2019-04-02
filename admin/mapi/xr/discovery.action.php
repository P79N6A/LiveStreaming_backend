<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
class discoveryCModule extends baseCModule
{
    //推荐
    public function index()
    {
        if (!$GLOBALS['user_info']) {
            $user_id = 0;
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
        }
        $page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

        $root = array(
            'has_next' => 1,
            'page' => $page,
            'status' => 1,
            'error' => ''
        );
        $page_size = 20;
        $list = load_auto_cache("select_weibo_recommond", array('page' => $page, 'page_size' => $page_size, 'user_id' => $user_id));
        if (!empty($list)) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $user_follw_redis = new UserFollwRedisService($user_id);
            $sql = "SELECT weibo_id FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id IN (" . implode(',', array_column($list, 'weibo_id')) . ") AND type = 2 AND user_id = " . $user_id . " GROUP BY weibo_id";
            $like_list = $GLOBALS['db']->getAll($sql);
            if (!empty($like_list)) {
                $like_list = array_column($like_list, 'weibo_id');
            } else {
                $like_list = array();
            }
            foreach ($list as &$value) {
                $value['is_praise'] = in_array($value['weibo_id'], $like_list) ? '1' : '0';
                $value['is_attention'] = $user_follw_redis->is_following($value['user_id']) ? 1 : 0;
            }
        }
        $root['list'] = $list;
        if (count($list) == $page_size) {
            $root['has_next'] = 1;
        } else {
            $root['has_next'] = 0;
        }

        api_ajax_return($root);
    }
    //附近的人
    public function nearby_list()
    {

        $page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $xpoint = floatval($_REQUEST['xpoint']);
        $ypoint = floatval($_REQUEST['ypoint']);
        $type = intval($_REQUEST['type']);
        $page_size = 20;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        $where = '';
        if ($type > 0) {
            $where = ' and sex = ' . $type;
        }
        $sql = "SELECT u.id as user_id,u.head_image,u.is_authentication,u.nick_name,u.sex,u.city,u.focus_count,u.fans_count,u.xpoint,u.ypoint,getDistance($ypoint,$xpoint,u.ypoint,u.xpoint) as distance FROM " . DB_PREFIX . "user u WHERE 1=1  " . $where;
        $sql .= " order by getDistance($ypoint,$xpoint,u.ypoint,u.xpoint),u.id desc  limit " . $limit;
        $list = $GLOBALS['db']->getAll($sql);
        foreach ($list as $k => $v) {
            if ($v) {
                $list[$k]['head_image'] = deal_weio_image($v['head_image']);
            }
        }
        $root = array(
            'list' => $list,
            'has_next' => 1,
            'page' => $page,
            'status' => 1,
            'error' => ''
        );
        if (count($list) == $page_size) {
            $root['has_next'] = 1;
        } else {
            $root['has_next'] = 0;
        }

        api_ajax_return($root);

    }

}
