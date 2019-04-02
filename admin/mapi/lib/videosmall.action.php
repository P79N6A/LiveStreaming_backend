<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class videosmallModule extends baseModule
{
    /**
     * [getsign 获取上次签名]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-08T17:37:26+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function getsign()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config");
            $current = time();
            $expired = $current + 600; // 签名有效期：1天
            $arg_list = array(
                "secretId" => $m_config['qcloud_secret_id'],
                "currentTimeStamp" => $current,
                "expireTime" => $expired,
                "random" => rand()
            );
            $orignal = http_build_query($arg_list);
            $signature = base64_encode(hash_hmac('SHA1', $orignal, $m_config['qcloud_secret_key'], true) . $orignal);
            $root = array('status' => 1, 'sign' => $signature);
        }
        ajax_return($root);
    }

    /**
     * [addvideo 上传]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-08T18:24:27+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function addvideo()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $sv_url = isset($_REQUEST['sv_url']) ? strim($_REQUEST['sv_url']) : '';
            $sv_img = isset($_REQUEST['sv_img']) ? strim($_REQUEST['sv_img']) : '';
            $sv_content = isset($_REQUEST['sv_content']) ? strim($_REQUEST['sv_content']) : '';
            if (empty($sv_url)) {
                ajax_return(array('status' => 0, 'error' => "小视频地址不能为空"));
            }
            if (empty($sv_img)) {
                ajax_return(array('status' => 0, 'error' => "小视频封面图不能为空"));
            }
            if (empty($sv_content)) {
                ajax_return(array('status' => 0, 'error' => "小视频描述内容不能为空"));
            }
            $GLOBALS['db']->autoExecute(DB_PREFIX . "weibo", array('user_id' => $user_id, 'type' => 'video', 'data' => $sv_url, 'photo_image' => $sv_img, 'content' => $sv_content, 'create_time' => to_date(NOW_TIME), 'province' => $GLOBALS['user_info']['province'], 'status' => 0, 'is_e'), 'INSERT');
            if ($GLOBALS['db']->affected_rows()) {
                $root['status'] = 1;
            } else {
                $root['status'] = 0;
                $root['error'] = "发布小视频失败";
            }
        }
        ajax_return($root);
    }

    public function svlist()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $to_user_id = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
            $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
            $root = array(
                'has_next' => 1,
                'page' => $page,
                'status' => 1,
                'error' => ''
            );
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $user_follw_redis = new UserFollwRedisService($user_id);
            $page_size = 20;
            $list = load_auto_cache("videosmall_select_weibo_recommend", array('page' => $page, 'page_size' => $page_size, 'user_id' => $to_user_id, 'type' => 'video'));
            $sql = "SELECT weibo_id FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id IN (" . implode(',', array_column($list, 'sv_id')) . ") AND type = 2 AND user_id = " . $user_id . " GROUP BY weibo_id";
            $like_list = $GLOBALS['db']->getAll($sql);
            if (!empty($like_list)) {
                $like_list = array_column($like_list, 'weibo_id');
            } else {
                $like_list = array();
            }
            foreach ($list as &$value) {
                $value['is_praise'] = in_array($value['sv_id'], $like_list) ? '1' : '0';
                $value['is_attention'] = $user_follw_redis->is_following($value['sv_userid']) ? 1 : 0;
            }

            $root['list'] = $list;

            if (count($list) == $page_size) {
                $root['has_next'] = 1;
            } else {
                $root['has_next'] = 0;
            }
            // log_file($root);
            // api_ajax_return($root);
        }
        ajax_return($root);
    }

    public function videodetail()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $user_follw_redis = new UserFollwRedisService($user_id);
            if (empty($sv_id)) {
                ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
            }
            // $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "weibo SET video_count=video_count+1 WHERE id = $sv_id");
            $root['status'] = 1;
            $svideo = load_auto_cache('videosmall_select_weibo_info', array('weibo_id' => $sv_id));
            if (empty($svideo)) {
                ajax_return(array('status' => 0, 'error' => "小视频不存在"));
            }
            $sql = "SELECT COUNT(weibo_id) FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id ={$svideo['sv_id']} AND type = 2 AND user_id = {$user_id}";
            $like = $GLOBALS['db']->getOne($sql);
            $svideo['is_praise'] = $like > 0 ? '1' : '0';
            $svideo['is_attention'] = $user_follw_redis->is_following($svideo['sv_userid']) ? 1 : 0;
            $svideo['share_url'] = SITE_DOMAIN . "/wap/hot/?sv_id={$sv_id}";
            $root['video'][0] = $svideo;
        }
        ajax_return($root);
    }

    public function videodetail_h5()
    {
        $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
        if (empty($sv_id)) {
            ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
        }
        // $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "weibo SET video_count=video_count+1 WHERE id = $sv_id");
        $root['status'] = 1;
        $svideo = load_auto_cache('videosmall_select_weibo_info', array('weibo_id' => $sv_id));
        if (empty($svideo)) {
            ajax_return(array('status' => 0, 'error' => "小视频不存在"));
        }
        $root['video'] = $svideo;
        $root['app_down'] = SITE_DOMAIN . '/mapi/index.php?ctl=app_download';
        ajax_return($root);
    }

    public function commentlist()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
            if (empty($sv_id)) {
                ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
            }
            $svideo = load_auto_cache('videosmall_select_weibo_info', array('weibo_id' => $sv_id));
            if (empty($svideo)) {
                ajax_return(array('status' => 0, 'error' => "小视频不存在"));
            }
            $root['status'] = 1;
            $root['list'] = load_auto_cache('videosmall_select_weibo_info_comments', array('weibo_id' => $sv_id));
        }
        ajax_return($root);
    }

    public function addcomment()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
            $com_content = isset($_REQUEST['com_content']) ? strim($_REQUEST['com_content']) : '';
            $to_comment_id = isset($_REQUEST['to_comment_id']) ? intval($_REQUEST['to_comment_id']) : 0;
            if (empty($sv_id)) {
                ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
            }
            $smallvideo = load_auto_cache('videosmall_select_weibo_info', array('weibo_id' => $sv_id));
            if (empty($smallvideo)) {
                ajax_return(array('status' => 0, 'error' => "小视频不存在"));
            }
            if (empty($com_content)) {
                ajax_return(array('status' => 0, 'error' => "评论内容不能为空"));
            }
            $data = array();
            $data['weibo_id'] = $sv_id;
            if (!empty($to_comment_id)) {
                $to_user_id = $GLOBALS['db']->getOne("select user_id from " . DB_PREFIX . "weibo_comment where comment_id = " . $to_comment_id);
                if (!$to_user_id) {
                    ajax_return(array('status' => 0, 'error' => "被评论ID不存在"));
                }
                if ($to_user_id == $user_id) {
                    ajax_return(array('status' => 0, 'error' => "不能对自己进行回复"));
                }
                $data['to_comment_id'] = $to_comment_id;
                $data['to_user_id'] = $to_user_id;
            }
            $max_length = 255;
            if (mb_strlen($com_content, 'utf-8') > $max_length) {
                ajax_return(array('status' => 0, 'error' => "评论超过{$max_length}个字数限制"));
            }
            $data['user_id'] = $user_id;
            $data['content'] = $com_content;
            $data['type'] = 1;
            $data['create_time'] = to_date(NOW_TIME);
            $data['is_audit'] = 1;
            $data['client_ip'] = get_client_ip();
            $storey = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "weibo_comment where weibo_id = " . $sv_id . "  and type = 1");
            $data['storey'] = intval($storey);
            $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "weibo_comment", $data, 'INSERT');
            if ($res) {
                $root['error'] = "评论发表成功!";
                $root['status'] = 1;
                $comment_count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id = " . $sv_id . "  and type = 1");
                $root['comment_count'] = $comment_count;
                $GLOBALS['db']->query("update " . DB_PREFIX . "weibo set comment_count = $comment_count where id = " . $sv_id);
            } else {
                $root['error'] = "操作失败!";
                $root['status'] = 0;
            }
        }
        ajax_return($root);
    }

    public function delvideo()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
            if (empty($sv_id)) {
                ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
            }
            $svideo = load_auto_cache('videosmall_select_weibo_info', array('weibo_id' => $sv_id));
            if (empty($svideo)) {
                ajax_return(array('status' => 0, 'error' => "小视频不存在"));
            }
            if ($svideo['sv_userid'] != $user_id) {
                ajax_return(array('status' => 0, 'error' => "您只能删除自己的小视频"));
            }
            $sql = "DELETE FROM " . DB_PREFIX . "weibo WHERE id = " . $sv_id . " AND user_id = " . $user_id;
            $res = $GLOBALS['db']->query($sql);
            $sql = "DELETE FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id = " . $sv_id;
            $res = $GLOBALS['db']->query($sql);
            if ($GLOBALS['db']->affected_rows()) {
                $root['status'] = 1;
                $root['error'] = '删除成功';
            } else {
                $root['error'] = '删除失败';
                $root['status'] = 0;
            }
        }
        ajax_return($root);
    }

    public function setpraise()
    {
        //点赞
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
            if (empty($sv_id)) {
                ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
            }
            $data = array();
            $data['weibo_id'] = $sv_id;
            $data['type'] = 2;
            $data['user_id'] = $user_id;
            $data['create_time'] = to_date(NOW_TIME);
            $data['is_audit'] = 1;
            //$data['to_user_id'] = $to_user_id;;
            $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id = " . $sv_id . " AND type = 2 AND user_id = " . $user_id;
            $has_digg = $GLOBALS['db']->getOne($sql);

            if ($has_digg) {
                $sql = "DELETE FROM " . DB_PREFIX . "weibo_comment where weibo_id = " . $sv_id . " AND type = 2 AND user_id = " . $user_id;
                $res = $GLOBALS['db']->query($sql);
            } else {
                $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "weibo_comment", $data, 'INSERT');
                if ($res) {
                    if (defined('OPEN_SVIDEO_MODULE') && OPEN_SVIDEO_MODULE == 1) {
                        $sql = "DELETE FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id = " . $sv_id . " AND type = 3 AND user_id = " . $user_id;
                        $GLOBALS['db']->query($sql);
                    }
                }
            }
            if ($res) {
                if (!$has_digg) {
                    $root['error'] = "点赞成功!";
                    $root['pra_now'] = 1;
                } else {
                    $root['error'] = "取消点赞!";
                    $root['pra_now'] = 0;
                    $GLOBALS['db']->query("update " . DB_PREFIX . "weibo set digg_count = digg_count-1 where id = " . $sv_id);
                }
                if (defined('OPEN_SVIDEO_MODULE') && OPEN_SVIDEO_MODULE == 1) {
                    $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "weibo_comment where weibo_id = " . $sv_id . " AND type = 3 ";
                    $unlike_count = $GLOBALS['db']->getOne($sql);
                    $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "weibo set unlike_count = " . intval($unlike_count) . " WHERE id = " . $sv_id);
                    // $root['has_unlike'] = 0;
                    // $root['unlike_count'] = intval($unlike_count);
                }
                $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "weibo_comment WHERE weibo_id = " . $sv_id . " AND type = 2 ";
                $digg_count = $GLOBALS['db']->getOne($sql);
                $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "weibo SET digg_count = " . intval($digg_count) . " where id = " . $sv_id);
                $root['status'] = 1;
                $root['digg_count'] = intval($digg_count);
            } else {
                $root['error'] = "操作失败!";
                $root['status'] = 0;
            }
        }
        ajax_return($root);
    }

    /**
     * [add_video_count description]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-31T14:58:11+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     */
    public function add_video_count()
    {
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            ajax_return($root);
        }
        $sv_id = isset($_REQUEST['sv_id']) ? intval($_REQUEST['sv_id']) : 0;
        if (empty($sv_id)) {
            ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
        }
        $weibo = $GLOBALS['db']->getRow("select id,video_count from " . DB_PREFIX . "weibo where id = $sv_id and type ='video'");
        if (!$weibo) {
            $root['error'] = "该小视频不存在！";
            $root['status'] = 0;
            ajax_return($root);
        }
        $re = $GLOBALS['db']->query("update " . DB_PREFIX . "weibo set video_count=video_count+1 where id = $sv_id");
        $root['error'] = "";
        $root['status'] = 1;
        $video_count = $GLOBALS['db']->getOne("select video_count from " . DB_PREFIX . "weibo where id = $sv_id ");
        $root['video_count'] = intval($video_count);
        ajax_return($root);

    }
}
