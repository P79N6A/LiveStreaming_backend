<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/app.action.php');
class appCModule extends appModule
{
    protected static function getPluginOrder($user_id)
    {
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = APP_ROOT_PATH . 'mapi/lib/';

        $plugin_order       = Model::build('plugin_order')->field('plugin_id')->select(['user_id' => $user_id]);
        $plugin_order_array = [];
        foreach ($plugin_order as $value) {
            $plugin_order_array[$value['plugin_id']] = 1;
        }
        return $plugin_order_array;
    }
    public function buyPlugin()
    {
        $plugin_id = intval($_REQUEST['plugin_id']);
        $user_id   = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            api_ajax_return(array(
                'status' => 10007,
                'error'  => "请先登录",
            ));
        }
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
        $res        = Model::build('plugin_order')->buyPlugin($user_id, $plugin_id);
        if (is_string($res)) {
            api_ajax_return(array(
                'status' => 0,
                'error'  => $res,
            ));
        }
        api_ajax_return(array(
            'status'           => 1,
            'error'            => '',
            'account_diamonds' => $res,
        ));
    }
    public function plugin_init()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            api_ajax_return(array(
                'status' => 10007,
                'error'  => "请先登录",
            ));
        }
        $m_config = load_auto_cache("m_config"); //初始化手机端配置

        //审核版本
        $ios_check        = 0;
        $dev_type         = strim($_REQUEST['sdk_type']);
        $sdk_version_name = strim($_REQUEST['sdk_version_name']);
        if ($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name && $GLOBALS['user_info']['mobile'] == '13888888888') {
            $ios_check = 1;
        }

        if ($ios_check) {
            ajax_return(array(
                'status'   => 1,
                'list'     => array(),
                'rs_count' => 0,
            ));
        }

        $plugin       = $GLOBALS['db']->getALL("SELECT id,child_id,name,image,type,price,class as class_name FROM " . DB_PREFIX . "plugin WHERE is_effect=1", true, true);
        $plugin_order = self::getPluginOrder($user_id);

        $table = DB_PREFIX . 'video';
        $video = $GLOBALS['db']->getRow("SELECT `id` FROM  $table WHERE user_id=$user_id and live_in=1");
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $fields      = array('live_pay_time', 'live_fee', 'live_pay_type', 'is_live_pay', 'game_log_id');
        $video_info  = $video_redis->getRow_db($video['id'], $fields);
        foreach ($plugin as $key => $value) {
            $plugin[$key]['is_active'] = 0;
            switch ($value['class_name']) {
                case 'game':
                    if (defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE == 1) {
                        if ($value['type'] == 2) {
                            $game_id   = 0;
                            $is_enable = 1;
                            if ($video['id']) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GamesRedisService.php');
                                $redis     = new GamesRedisService();
                                $last_game = $video_info['game_log_id'];
                                if ($last_game) {
                                    $last_game = $redis->get($last_game, 'game_id,create_time,long_time');
                                    if (NOW_TIME < $last_game['create_time'] + $last_game['long_time']) {
                                        $game_id   = $last_game['game_id'];
                                        $is_enable = 0;
                                    }
                                }
                            }
                            $plugin[$key]['is_active'] = intval($value['id'] == $game_id);
                            $plugin[$key]['is_enable'] = $is_enable;
                        }
                    } else {
                        unset($plugin[$key]);
                        continue 2;
                    }
                    break;

                case 'pai':
                    if (defined('OPEN_PAI_MODULE') && OPEN_PAI_MODULE == 1) {
                        if ($value['type'] == 1) {
                            if ($video['pai_id'] != 0) {
                                $pai_goods = $GLOBALS['db']->getRow("SELECT create_time,pai_time FROM " . DB_PREFIX . "pai_goods WHERE id=" . $video['pai_id'] . " ");
                                if ($pai_goods) {
                                    if (NOW_TIME < strtotime($pai_goods['create_time']) + $pai_goods['pai_time'] * 3600) {
                                        $is_enable = 0;
                                    }
                                }
                            }
                            $plugin[$key]['is_enable'] = $is_enable;
                        }
                    } else {
                        unset($plugin[$key]);
                        continue 2;
                    }
                    break;
                case 'shop':
                    if (!defined('SHOPPING_GOODS') || SHOPPING_GOODS == 0) {
                        unset($plugin[$key]);
                        continue 2;
                    } else {
                        $plugin[$key]['is_enable'] = 1;
                    }
                    break;
                case 'podcast_goods':
                    if (!defined('OPEN_PODCAST_GOODS') || OPEN_PODCAST_GOODS == 0) {
                        unset($plugin[$key]);
                        continue 2;
                    } else {
                        $plugin[$key]['is_enable'] = 1;
                    }
                    break;
                case 'live_pay':
                case 'live_pay_scene':
                default:
                    if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1) {
                        $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id, true, true);
                        if ($is_nospeaking) {
                            unset($plugin[$key]);
                            continue 2;
                        }
                        $live_pay_time = $video_info['live_pay_time']; //开始收费时间
                        $live_fee      = intval($video_info['live_fee']); //付费直播 收费多少
                        $is_live_pay   = intval($video_info['is_live_pay']);
                        $live_pay_type = intval($video_info['live_pay_type']);
                        $is_active     = 0;
                        if ($live_pay_time != '' && $live_fee > 0) {
                            if (($is_live_pay == 1 && $live_pay_type == 0 && $value['class_name'] == 'live_pay') || ($is_live_pay == 1 && $live_pay_type == 1 && $value['class_name'] == 'live_pay_scene')) {
                                $is_active = 1;
                                $is_enable = 0;
                            } else {
                                $is_enable = 0;
                            }
                        }
                        $plugin[$key]['is_active'] = $is_active;
                        $plugin[$key]['is_enable'] = $is_enable;
                    } else {
                        unset($plugin[$key]);
                        continue 2;
                    }
                    break;
            }
            $plugin[$key]['image'] = get_spec_image($value['image']);
            $plugin[$key]['price'] = intval($value['price']);
            if (defined('BUY_PLUGIN_ONCE') && BUY_PLUGIN_ONCE) {
                $plugin[$key]['has_plugin'] = intval($value['price']) ? intval(isset($plugin_order[$value['id']])) : 1;
            } else {
                $plugin[$key]['has_plugin'] = 0;
            }
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis    = new UserRedisService();
        $coin          = intval($user_redis->getOne_db($user_id, 'coin'));
        $user_diamonds = intval($user_redis->getOne_db($user_id, 'diamonds'));
        api_ajax_return(array(
            'status'        => 1,
            'list'          => array_values($plugin),
            'rs_count'      => sizeof($plugin),
            'coin'          => $coin,
            'user_diamonds' => $user_diamonds,
            'test'          => __FILE__ . __LINE__,
        ));
    }
    //插件接口状态
    public function plugin_status()
    {
        $root    = array('status' => 1, 'error' => '');
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            ajax_return($root);
        } else {
            $plugin_id = intval($_REQUEST['plugin_id']); //插件id，fanwe_plugin.id

            $table = DB_PREFIX . 'video';
            $video = $GLOBALS['db']->getRow("SELECT `id`,pai_id FROM  $table WHERE user_id=" . $user_id . " and live_in=1");
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $fields      = array('live_pay_time', 'live_fee', 'live_pay_type', 'is_live_pay', 'game_log_id');
            $video_info  = $video_redis->getRow_db($video['id'], $fields);
            $is_enable   = 1;
            $is_active   = 0;
            $plugin      = $GLOBALS['db']->getRow("SELECT id,child_id,name,image,type,price,class as class_name FROM " . DB_PREFIX . "plugin WHERE is_effect=1 and id=" . $plugin_id, true, true);

            if (defined('BUY_PLUGIN_ONCE') && BUY_PLUGIN_ONCE) {
                if ($plugin['price']) {
                    $plugin_order = self::getPluginOrder($user_id);
                    if (!isset($plugin_order[$plugin_id])) {
                        ajax_return(['status' => 0, 'error' => '未购买插件']);
                    }
                }
            } else {
                require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
                Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
                $res        = Model::build('plugin_order')->buyPlugin($user_id, $plugin_id);
                if (is_string($res)) {
                    api_ajax_return([
                        'status'      => 0,
                        'is_recharge' => 1,
                        'error'       => $res,
                    ]);
                }
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis    = new UserRedisService();
                $coin          = intval($user_redis->getOne_db($user_id, 'coin'));
                $user_diamonds = intval($user_redis->getOne_db($user_id, 'diamonds'));
                $root['account_diamonds'] = $user_diamonds;
                $root['coin'] = $coin;
            }

            $plugin['is_active'] = $is_active;
            $plugin['is_enable'] = $is_enable;
            if (defined('OPEN_PLUGIN') && OPEN_PLUGIN) {
                $open_plugin = $GLOBALS['db']->getRow("select open_game,open_pay,open_auction from " . DB_PREFIX . "user where id = $user_id");
                if ($plugin['class_name'] == 'game') {
                    if ($open_plugin['open_game'] == 1) {
                        $root['error']  = "您无法开启游戏，请联系客服";
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }
                if ($plugin['class_name'] == 'pai') {
                    if ($open_plugin['open_auction'] == 1) {
                        $root['error']  = "您无法开启竞拍，请联系客服";
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }
                if ($plugin['class_name'] == 'live_pay' || $plugin['class_name'] == 'live_pay_scene') {
                    if ($open_plugin['open_pay'] == 1) {
                        $root['error']  = "您无法开启付费，请联系客服";
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }

            }
            if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1) {
                $live_pay_time = $video_info['live_pay_time']; //开始收费时间
                $live_fee      = intval($video_info['live_fee']); //付费直播 收费多少
                $is_live_pay   = intval($video_info['is_live_pay']);
                $live_pay_type = intval($video_info['live_pay_type']);
                if ($live_pay_time != '' && $live_fee > 0) {
                    //$is_enable = 0;
                    if (($is_live_pay == 1 && $live_pay_type == 1 && $plugin['class_name'] == 'live_pay_scene') || ($is_live_pay == 1 && $live_pay_type == 0 && $plugin['class_name'] == 'live_pay')) {
                        $is_active = 1;
                    }

                    if ($plugin['class_name'] == 'live_pay') {
                        $is_enable = 0;
                        if ($is_active) {
                            $error = '按时付费直播正在使用中...';
                        } else {
                            $error = '按场付费直播正在使用中...';
                        }
                    } else if ($plugin['class_name'] == 'live_pay_scene') {
                        $is_enable = 0;
                        if ($is_active) {
                            $error = '按场付费直播正在使用中...';
                        } else {
                            $error = '按时付费直播正在使用中...';
                        }
                    }
                }

                $plugin['is_active'] = $is_active;
                $plugin['is_enable'] = $is_enable;
            }

            if (defined('OPEN_PAI_MODULE') && OPEN_PAI_MODULE == 1) {
                if ($plugin['class_name'] == 'pai') {
                    if ($video['pai_id'] != 0) {
                        $pai_goods = $GLOBALS['db']->getRow("SELECT create_time,pai_time,status FROM " . DB_PREFIX . "pai_goods WHERE id=" . $video['pai_id'] . " ");
                        if ($pai_goods) {
                            if (NOW_TIME < $pai_goods['create_time'] + $pai_goods['pai_time'] * 3600 || $pai_goods['status'] < 2) {
                                $is_enable = 0;
                                $error     = '竞拍正在使用中...';
                            }
                        }

                    }

                    if (defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE == 1) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GamesRedisService.php');
                        $redis     = new GamesRedisService();
                        $last_game = $video_info['game_log_id'];
                        if ($last_game) {
                            $last_game = $redis->get($last_game, 'game_id,create_time,long_time');
                            $game_id   = $last_game['game_id'];
                            $is_enable = $game_id ? intval($plugin_id == $game_id) : 1;
                            if ($game_id) {
                                $error = $plugin_id == $game_id ? '游戏正在使用中...' : '请先关闭当前游戏再切换...';
                            }
                        }

                    }
                    $plugin['is_enable'] = $is_enable;
                }
            }

            if (defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE == 1) {
                if ($plugin['type'] == 2) {
                    $game_id = 0;
                    if ($video['id']) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GamesRedisService.php');
                        $redis     = new GamesRedisService();
                        $last_game = $video_info['game_log_id'];
                        if ($last_game) {
                            $last_game = $redis->get($last_game, 'game_id,create_time,long_time');
                            $game_id   = $last_game['game_id'];
                            $is_enable = $game_id ? intval($plugin_id == $game_id) : 1;
                            if ($game_id) {
                                $error = $plugin_id == $game_id ? '游戏正在使用中...' : '请先关闭当前游戏再切换...';
                            }
                        }
                        if (defined('OPEN_PAI_MODULE') && OPEN_PAI_MODULE == 1) {
                            $pai_goods = $GLOBALS['db']->getRow("SELECT create_time,pai_time FROM " . DB_PREFIX . "pai_goods WHERE id=" . $video['pai_id'] . " ");
                            if ($pai_goods) {
                                $is_enable = 0;
                                $error     = '竞拍正在使用中...';
                            }
                        }
                    }

                    $plugin['is_enable'] = $is_enable;
                }
            }
            $root['class_name'] = $plugin['class_name'];
            $root['is_enable']  = $plugin['is_enable'];
            if ($error != '') {
                $root['error'] = $error;
            }

            ajax_return($root);
        }
    }
}
