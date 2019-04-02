<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------c
class publishCModule extends baseCModule
{
    //发布信息
    public function do_publish()
    {
        $root = array();
        $data = array();
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $user_id = intval($GLOBALS['user_info']['id']); //用户ID

        //判断是否开启车行定制与小视频功能
        if ((defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) && (defined('OPEN_SVIDEO_MODULE') && OPEN_SVIDEO_MODULE)) {
            $user = $GLOBALS['db']->getRow("select open_svideo,allow_svideo_number from " . DB_PREFIX . "user where id = " . $user_id);
            $svideo = $GLOBALS['db']->getOne("select count(id) from " . DB_PREFIX . "weibo where status=1 and user_id = " . $user_id);

            if ($user['open_svideo'] == 1) {
                api_ajax_return(array("error" => "您无法发布小视频,请联系客服处理 ", "status" => 0));
            } elseif ($user['open_svideo'] == 0 && $user['allow_svideo_number'] > 0 && $svideo >= $user['allow_svideo_number']) {
                api_ajax_return(array("error" => "可发布小视频数量已达上限,请联系客服处理 ", "status" => 0));
            }
        }

        $type_array = array(
            'imagetext', 'video', 'weixin', 'goods', 'red_photo', 'photo'
        );
        $type = strim($_REQUEST['publish_type']);
        if (!in_array($type, $type_array)) {
            $root = array(
                'status' => 0,
                'error' => $type . '上传类型错误：'
            );
            api_ajax_return($root);
        }
        if ($type == 'photo' || $type == 'goods' || $type == 'weixin') {
            if (floatval($_REQUEST['price']) <= 0) {
                $root = array(
                    'status' => 0,
                    'error' => '价格不能为空'
                );
                api_ajax_return($root);
            }
        }

        if ($type == 'weixin') {
            $data['weixin_account'] = $_REQUEST['data'];
            $data['weixin_price'] = floatval($_REQUEST['price']);
            $data['weixin_account_time'] = to_date(get_gmtime());
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $data, 'UPDATE', 'id = ' . $user_id);
        } else {
            $data['content'] = strim($_REQUEST['content']);
            if (empty($data['content'])) {
                $root = array(
                    'status' => 0,
                    'error' => '内容不能为空：'
                );
                api_ajax_return($root);
            }

            if ($type == 'photo' || $type == 'video') {
                if (empty($_REQUEST['photo_image'])) {
                    $root = array(
                        'status' => 0,
                        'error' => '封面不能为空：'
                    );
                    api_ajax_return($root);
                } else {
                    $data['photo_image'] = strim($_REQUEST['photo_image']);
                }
            }

            if ($type == 'video') {
                // $data['photo_image'] = strim($_REQUEST['image_url']);
                $video_url = strim($_REQUEST['video_url']);
                if (!$video_url) {
                    $root = array(
                        'status' => 0,
                        'error' => '视频不能为空!'
                    );
                    api_ajax_return($root);
                }
                $data['data'] = $video_url;

            } else {

                $_REQUEST['data'] = json_decode($_REQUEST['data'], true);

                if (is_array($_REQUEST['data'])) {
                    $image_array = $_REQUEST['data'];
                    if ($type == 'goods' && $image_array[0]['url']) {
                        $data['photo_image'] = $image_array[0]['url'];
                    }
                    if ($type == 'red_photo') {

                        $_REQUEST['price'] = floatval(count($image_array)) * floatval($m_config['weibo_red_price']);

                        if (floatval($_REQUEST['price']) <= 0) {
                            $root = array(
                                'status' => 0,
                                'error' => '红包价格不能小于0！'
                            );
                            api_ajax_return($root);
                        }
                    }
                    $data['data'] = serialize($image_array);

                } else {
                    $root = array(
                        'status' => 0,
                        'error' => '图片结构错误！：'
                    );
                    api_ajax_return($root);
                }
            }
            $data['type'] = strim($type);
            if ($GLOBALS['user_info']['user_level'] >= $m_config['weibo_user_level']) {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }

            $data['price'] = floatval($_REQUEST['price']);

            $data['xpoint'] = strim($_REQUEST['xpoint']);
            $data['ypoint'] = strim($_REQUEST['ypoint']);
            $data['create_time'] = to_date(NOW_TIME);
            $data['user_id'] = $user_id;
            $data['is_audit'] = 1;
            //$ipinfo = get_ip_info();
            // $province = $ipinfo['province'];
            // $city = $ipinfo['city'];
            $data['province'] = strim($_REQUEST['province']);
            $data['city'] = strim($_REQUEST['city']);
            $data['address'] = strim($_REQUEST['address']);
            if ($data['city'] == '不显示' || $data['address'] == '不显示') {
                $data['city'] = '';
                $data['address'] = '';
                $data['province'] = '';
            }

            $GLOBALS['db']->autoExecute(DB_PREFIX . "weibo", $data, 'INSERT');

        }
        if ($GLOBALS['db']->affected_rows()) {
            $root = array(
                'status' => 1,
                'error' => ''
            );
            $weibo_count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "weibo where user_id = " . $user_id);
            $re = $GLOBALS['db']->query("update " . DB_PREFIX . "user set weibo_count = " . intval($weibo_count) . " where id = " . $user_id);
        } else {
            $root = array(
                'status' => 0,
                'error' => '上传失败'
            );
        }
        api_ajax_return($root);
    }
    //微信下架
    public function off_weixin()
    {
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 2;
            api_ajax_return($root);
        }
        $user_id = intval($GLOBALS['user_info']['id']); //用户ID
        $data = array('weixin_account' => '', 'weixin_price' => 0);
        $where = 'id = ' . $user_id;
        $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $data, 'UPDATE', $where);
        if ($res) {
            $root = array(
                'status' => 1,
                'error' => '微信下架成功'
            );
        } else {
            $root = array(
                'status' => 0,
                'error' => '微信下架失败'
            );
        }
        api_ajax_return($root);
    }
    //获取会员权限
    public function check_type()
    {
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $user_info = $GLOBALS['db']->getRow("select is_authentication,weibo_count from " . DB_PREFIX . "user where id = " . $GLOBALS['user_info']['id']);
        $root = array(
            'status' => 1,
            'error' => '',
            'info' => $user_info
        );
        api_ajax_return($root);
    }

}
