<?php
/**
 *
 * @param unknown_type $user_id  查看人
 * @param unknown_type $podcast_id  主播
 * @param unknown_type $to_user_id  被查看的人
 * @return Ambigous <mixed, multitype:number unknown mixed >
 */
function getuserinfo($user_id, $podcast_id, $to_user_id, $request_data)
{
    $root = array();
    $root['show_tipoff'] = 0; //举报按钮 1:显示;0:不显示
    $root['show_admin'] = 0; //管理按钮 1,2:显示;0:不显示 （1 管理员：举报，禁言，取消; 2 主播：设置为管理员/取消管理员,管理员列表，禁言，取消）
    $root['has_focus'] = 0; //0:未关注;1:已关注
    $root['has_admin'] = 0; //0:非管理员;1:是管理员
    $root['is_forbid'] = 0; //0:未被禁言;1:被禁言
    if ($to_user_id == 0) {
        $to_user_id = $user_id;
    }
    $room_id = intval($request_data['room_id']);
    if ($room_id) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $video_info = $video_redis->getRow_db($room_id, array('id', 'group_id'));
        $forbid_info = $video_redis->has_forbid_msg($video_info['group_id'], $to_user_id); //判断某个用户是否被禁言(被禁言返回：true; 未被禁言返回：false)
        if ($forbid_info && intval($forbid_info) > NOW_TIME) {
            $root['is_forbid'] = 1;
        } else {
            $video_redis->unset_forbid_msg($video_info['group_id'], $to_user_id);
        }
    }
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
    $user_redis = new UserRedisService();
    $m_config = load_auto_cache("m_config"); //初始化手机端配置
    //$userfollw_redis = new UserFollwRedisService($to_user_id);
    //$fans_count = $userfollw_redis->follower_count();
    //$focus_count = $userfollw_redis->follow_count();
    //查看自己
    if ($to_user_id == $user_id) {
        $fields = array('id', 'fans_count', 'focus_count', 'is_agree', 'video_count', 'is_authentication', 'authentication_type', 'authentication_name', 'nick_name', 'signature', 'sex', 'province', 'city', 'head_image', 'ticket', 'no_ticket', 'refund_ticket', 'use_diamonds', 'diamonds', 'user_level', 'v_type', 'v_explain', 'v_icon', 'is_remind', 'birthday', 'emotional_state', 'job', 'family_id', 'family_chieftain', 'society_id', 'society_chieftain', 'society_settlement_type', 'is_robot', 'room_title', 'luck_num', 'coin', 'is_nospeaking', 'weibo_count', 'score', 'classified_id', 'is_admin', 'default_mount');
        // if (OPEN_GAME_MODULE == 1) {
        //     $fields[] = 'coin';
        // }
        $fields = array_merge($fields, array('is_vip', 'vip_expire_time'));
        if (defined('OPEN_VIP')) {
            $open_vip = intval(OPEN_VIP);
        }
        $userinfo = $user_redis->getRow_db($to_user_id, $fields);
        $userinfo['signature'] = htmlspecialchars_decode($userinfo['signature']);
        $userinfo['nick_name'] = htmlspecialchars_decode($userinfo['nick_name']);
        $userinfo['signature'] = ($userinfo['signature']);
        $userinfo['nick_name'] = ($userinfo['nick_name']);
        $userinfo['user_id'] = $to_user_id;
        $userinfo['refund_ticket'] = intval(floor($userinfo['refund_ticket']));
        if (defined("robot_gifts") && robot_gifts == 1) {
            $userinfo['ticket'] = intval(floor($userinfo['ticket'] + $userinfo['no_ticket']));
        } else {
            $userinfo['ticket'] = intval(floor($userinfo['ticket']));
        }
        $userinfo['use_diamonds'] = intval(floor($userinfo['use_diamonds']));
        $userinfo['diamonds'] = intval(floor($userinfo['diamonds']));
        if ($userinfo['is_authentication'] == 2) {
            if ($userinfo['v_explain'] == '') {
                $userinfo['v_explain'] = trim($userinfo['authentication_type']);
            }
        }
        if ($userinfo['signature'] == '' || $userinfo['signature'] == false) {
            $userinfo['signature'] = 'TA好像忘记签名了';
        }
        /*
        $sql = "select id as user_id,is_agree,video_count,is_authentication,nick_name,signature,sex,province,city,focus_count,head_image,fans_count,ticket,use_diamonds,diamonds,user_level,v_type,v_explain,v_icon,is_remind from ".DB_PREFIX."user where id = '".$to_user_id."'";
        $userinfo = $GLOBALS['db']->getRow($sql,true,true);
         */
        if ($userinfo['is_nospeaking'] === false) {
            $userinfo['is_nospeaking'] = 0;
        }
        if ($userinfo['coin'] === false) {
            $userinfo['coin'] = 0;
        }
        if ($userinfo['birthday'] == false) {
            $userinfo['birthday'] = '';
        }
        if ($userinfo['room_title'] === false) {
            $userinfo['room_title'] = '';
        }
        if ($userinfo['luck_num'] == false) {
            $userinfo['luck_num'] = '';
        }
        if ($userinfo['family_id'] == false) {
            $userinfo['family_id'] = 0;
        }
        if ($userinfo['family_chieftain'] == false) {
            $userinfo['family_chieftain'] = 0;
        }
        if ($userinfo['society_id'] == false) {
            $userinfo['society_id'] = 0;
        }
        if ($userinfo['society_chieftain'] == false) {
            $userinfo['society_chieftain'] = 0;
        }
        if ($userinfo['society_settlement_type'] == false) {
            $userinfo['society_settlement_type'] = 0;
        }
        if ($userinfo['emotional_state'] === false) {
            $userinfo['emotional_state'] = '';
        }
        if ($userinfo['is_robot'] === false) {
            $userinfo['is_robot'] = 0;
        }
        if ($userinfo['job'] === false) {
            $userinfo['job'] = '';
        }
        if ($userinfo['alipay_user_id'] === false) {
            $userinfo['alipay_user_id'] = '';
        }
        if ($userinfo['alipay_name'] === false) {
            $userinfo['alipay_name'] = '';
        }
        if ($userinfo['alipay_authent_token'] === false) {
            $userinfo['alipay_authent_token'] = '';
        }
        if ($userinfo['id'] === false) {
            $userinfo['id'] = $user_id;
        }
        if ($userinfo['fans_count'] === false) {
            $userinfo['fans_count'] = 0;
        }
        if ($userinfo['focus_count'] === false) {
            $userinfo['focus_count'] = 0;
        }
        if ($userinfo['is_agree'] === false) {
            $userinfo['is_agree'] = 0;
        }
        if ($userinfo['is_remind'] === false) {
            $userinfo['is_remind'] = 0;
        }
        if ($userinfo['video_count'] === false) {
            $userinfo['video_count'] = 0;
        }
        if ($userinfo['is_authentication'] === false) {
            $userinfo['is_authentication'] = 0;
        }
        if ($userinfo['authentication_type'] === false) {
            $userinfo['authentication_type'] = 0;
        }
        if ($userinfo['sex'] === false) {
            $userinfo['sex'] = 0;
        }
        if ($userinfo['province'] === false) {
            $userinfo['province'] = '';
        }
        if ($userinfo['city'] === false) {
            $userinfo['city'] = '';
        }
        if ($userinfo['head_image'] === false) {
            $userinfo['head_image'] = '';
        }
        if ($userinfo['use_diamonds'] === false) {
            $userinfo['use_diamonds'] = 0;
        }
        if ($userinfo['user_level'] === false) {
            $userinfo['user_level'] = 1;
        }
        if ($userinfo['v_type'] === false) {
            $userinfo['v_type'] = 0;
        }
        if ($userinfo['v_explain'] === false) {
            $userinfo['v_explain'] = '';
        }
        if ($userinfo['v_icon'] === false) {
            $userinfo['v_icon'] = '';
        }
        $userinfo['open_vip'] = 0;
        if ($open_vip) {
            $userinfo['open_vip'] = $open_vip;
        }
        if ($userinfo['is_vip'] === false) {
            $userinfo['is_vip'] = 0;
        }
        if (!$userinfo['weibo_count']) {
            $userinfo['weibo_count'] = 0;
        }
        $userinfo['is_admin'] = !empty($userinfo['is_admin']) ? intval($userinfo['is_admin']) : 0;
        $userinfo['moments'] = $userinfo['weibo_count'];
        if (defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) {
            $userinfo['coin'] = $userinfo['use_diamonds'];
        }
        $vip_expire_time = intval($userinfo['vip_expire_time']);
        if ($vip_expire_time > 0) {
            $userinfo['vip_expire_time'] = to_date($vip_expire_time, 'Y-m-d H:i');
            if ($vip_expire_time < NOW_TIME) {
                $userinfo['is_vip'] = 0;
                $userinfo['vip_expire_time'] = '已过期';
                $sql = "update " . DB_PREFIX . "user set is_vip = 0 where id = " . $user_id;
                $GLOBALS['db']->query($sql);
                user_deal_to_reids(array($user_id));
            } else {
                if (intval($userinfo['is_vip']) == 0) {
                    $userinfo['vip_expire_time'] = '未开通';
                }
            }
        } else {
            $userinfo['vip_expire_time'] = '未开通';
            if (intval($userinfo['is_vip']) == 1) {
                $userinfo['vip_expire_time'] = '永久';
            }
        }
        //未开启家族不显示
        if (!defined('OPEN_FAMILY_MODULE') || OPEN_FAMILY_MODULE != 1) {
            unset($userinfo['family_id']);
            unset($userinfo['family_chieftain']);
        }
        //未开启公会不显示
        if (!defined('OPEN_SOCIETY_MODULE') || OPEN_SOCIETY_MODULE != 1) {
            unset($userinfo['society_id']);
            unset($userinfo['society_chieftain']);
            unset($userinfo['society_settlement_type']);
        }
        $root['user'] = $userinfo;
        $root['user']['useable_ticket'] = intval($userinfo['ticket'] - $userinfo['refund_ticket']);
        //直播间显示主播实际可提现秀票（客户定制，标准版保留此功能）
        if (0) {
            $root['user']['ticket'] = $root['user']['useable_ticket'];
        }
        $root['user']['head_image'] = get_spec_image($userinfo['head_image']);
        $root['user']['is_robot'] = intval($userinfo['is_robot']);
        $root['user']['medals'] = array_map(function ($v) {
            return $v['icon'];
        }, load_auto_cache('user_medals', array('user_id' => $to_user_id, 'no_expired' => true)));
        //$root['user']['focus_count'] = $focus_count;
        //$root['user']['fans_count'] = $fans_count;
        $u_user_mobile = $user_redis->getRow_db($user_id, array('mobile'));
        //新增字段 测试数据
        if ((OPEN_PAI_MODULE == 1 || SHOPPING_GOODS == 1) && $u_user_mobile['mobile'] != '13888888888' && $u_user_mobile['mobile'] != '13999999999') {
            $root['user']['show_user_order'] = intval(SHOW_USER_ORDER);
            if (SHOW_USER_ORDER == 1) {
                $sql = "select count(*) from " . DB_PREFIX . "goods_order where order_type='shop' and order_status in (1,2,3) and (pid = 0 OR is_p=1 ) and viewer_id = " . $user_id;
                $root['user']['user_order'] = intval($GLOBALS['db']->getOne($sql, true, true));
            } else {
                $root['user']['user_order'] = 0;
            }
            $root['user']['show_user_pai'] = intval(SHOW_USER_PAI);
            if (SHOW_USER_PAI == 1) {
                $sql = "select count(*) from " . DB_PREFIX . "goods_order where order_type<>'shop' and order_status in (1,2,3) and viewer_id =" . $user_id;
                $root['user']['user_pai'] = intval($GLOBALS['db']->getOne($sql, true, true));
            } else {
                $root['user']['user_pai'] = 0;
            }
            /*$root['user']['show_podcast_order'] = 0;
            $root['user']['podcast_order'] = 0;
            $root['user']['show_podcast_pai'] = 0;
            $root['user']['podcast_pai'] = 0;
            $root['user']['show_podcast_goods'] = 0;
            $root['user']['podcast_goods'] = 0;*/
            $root['user']['show_podcast_order'] = intval(SHOW_PODCAST_ORDER);
            if (SHOW_PODCAST_ORDER == 1) {
                $sql = "select count(*) from " . DB_PREFIX . "goods_order where podcast_id = " . $user_id;
                $root['user']['podcast_order'] = intval($GLOBALS['db']->getOne($sql, true, true));
            }
            $root['user']['show_podcast_pai'] = intval(SHOW_PODCAST_PAI);
            if (SHOW_PODCAST_PAI == 1) {
                $sql = "select count(*) from " . DB_PREFIX . "pai_goods where podcast_id = " . $user_id . " and is_delete=0 and status in (0,1,4) and order_status in (1,2,3)";
                $root['user']['podcast_pai'] = intval($GLOBALS['db']->getOne($sql, true, true));
            }
            $root['user']['show_podcast_goods'] = intval(SHOW_PODCAST_GOODS);
            if (SHOW_PODCAST_GOODS == 1) {
                //商品
                //$sql = "select is_shop from ".DB_PREFIX."user where id = ".$user_id." ";
                $sql = "select count(*) from " . DB_PREFIX . "user_goods as ug," . DB_PREFIX . "goods as gs where gs.id=ug.goods_id and ug.user_id= " . $user_id . " and gs.is_effect=1 and gs.inventory > 0";
                if (OPEN_GOODS == 1) {
                    $root['user']['podcast_goods'] = intval(good_number($user_id));
                } else {
                    $root['user']['podcast_goods'] = intval($GLOBALS['db']->getOne($sql, true, true));
                }
            }
            $root['user']['show_shopping_cart'] = intval(SHOP_SHOPPING_CART);
            if (SHOP_SHOPPING_CART == 1) {
                $sql = "select count(*) from " . DB_PREFIX . "shopping_cart where user_id = " . $user_id . " and is_effect=1";
                $root['user']['shopping_cart'] = intval($GLOBALS['db']->getOne($sql, true, true));
            }
            $root['user']['open_podcast_goods'] = intval(OPEN_PODCAST_GOODS);
            if (OPEN_PODCAST_GOODS == 1) {
                $sql = "select count(*) from " . DB_PREFIX . "podcast_goods where user_id = " . $user_id . " and is_effect=1";
                $root['user']['shop_goods'] = intval($GLOBALS['db']->getOne($sql, true, true));
            }
            $shopping_goods = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "plugin WHERE class='shop' and is_effect=1");
            $pai = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "plugin WHERE class='pai' and is_effect=1");
            $podcast_goods = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "plugin WHERE class='podcast_goods' and is_effect=1");
            if (!$shopping_goods) {
                $root['user']['shopping_goods'] = 0;
                $root['user']['show_shopping_cart'] = 0;
                $root['user']['show_user_order'] = 0;
                $root['user']['show_podcast_order'] = 0;
            }
            if (!$pai) {
                $root['user']['show_user_pai'] = 0;
                $root['user']['show_podcast_pai'] = 0;
            }
            if (!$pai && !$shopping_goods) {
                $root['user']['show_podcast_goods'] = 0;
            }
            if (!$podcast_goods) {
                $root['user']['open_podcast_goods'] = 0;
            }
        } else {
            $root['user']['show_user_order'] = 0;
            $root['user']['user_order'] = 0;
            $root['user']['show_user_pai'] = 0;
            $root['user']['user_pai'] = 0;
            $root['user']['show_podcast_order'] = 0;
            $root['user']['show_podcast_pai'] = 0;
            $root['user']['show_podcast_goods'] = 0;
            $root['user']['podcast_goods'] = 0;
            $root['user']['show_shopping_cart'] = 0;
            $root['user']['shopping_cart'] = 0;
            $root['user']['open_podcast_goods'] = 0;
        }
        $root['user']['show_svideo'] = defined("OPEN_SVIDEO_MODULE") && OPEN_SVIDEO_MODULE == 1 ? 1 : 0;
        $root['user']['show_svideo_favor'] = defined("OPEN_SVIDEO_MODULE") && OPEN_SVIDEO_MODULE == 1 ? 1 : 0;
        if ($root['user']['show_svideo']) {
            $sql = "select count(*) from " . DB_PREFIX . "weibo where user_id = " . $user_id . " and status=1";
            $root['user']['svideo_count'] = intval($GLOBALS['db']->getOne($sql, true, true));
        } else {
            $root['user']['svideo_count'] = 0;
        }
        if ($podcast_id == $user_id) {
            $root['show_admin'] = 0; //主播查看 主播：设置为管理员/取消管理员,管理员列表，禁言，取消
            /*if (OPEN_PAI_MODULE==1) {
        $root['user']['show_podcast_order'] = SHOW_PODCAST_ORDER;
        if (SHOW_PODCAST_ORDER==1) {
        $sql = "select count(*) from ".DB_PREFIX."goods_order where podcast_id = ".$user_id;
        $root['user']['podcast_order'] = intval($GLOBALS['db']->getOne($sql,true,true));
        }
        $root['user']['show_podcast_pai'] = SHOW_PODCAST_PAI;
        if (SHOW_PODCAST_PAI==1) {
        $sql = "select count(*) from ".DB_PREFIX."pai_goods where podcast_id = ".$user_id." and is_delete=0";
        $root['user']['podcast_pai'] = intval($GLOBALS['db']->getOne($sql,true,true));
        }
        $root['user']['show_podcast_goods'] = SHOW_PODCAST_GOODS;
        if (SHOW_PODCAST_GOODS==1) {
        //商品暂无
        $root['user']['podcast_goods'] = 0;
        }
        }*/
        }
        //H5链接
        $h5_url = array(
            'url_my_grades' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=user_center&act=grade',
            'url_about_we' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=settings&act=article_cate',
            'url_help_feedback' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=settings&act=help',
            'url_auction_record' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=pailogs',
            'url_user_order' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=shop&act=shop_order&page=1', //商城h5订单链接（观众）
            'url_user_pai' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=goods',
            'url_podcast_order' => '', // 星级订单（暂无）
            'url_podcast_pai' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_podcast&act=goods',
            'url_podcast_goods' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=shop&act=podcasr_goods_management&state=1&page=1', //商城h5链接（主播）
            'url_auction_agreement' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=settings&act=article_show&cate_id=18', //竞拍协议
            'url_pai_income' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=user_center&act=income', //竞拍收益h5
            'url_goods_income' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=user_center&act=goods_income_details', //商品收益h5
            'url_user_goods' => '', //进入第三方商城h5链接(观众)
            'url_shopping_cart' => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=shop&act=shop_shopping_cart&page=1' //购物车
        );
        if (defined('OPEN_EDU_MODULE') && OPEN_EDU_MODULE == 1 && $userinfo['authentication_type'] == '机构') {
            $store_url = $GLOBALS['db']->getOne("select store_url from " . DB_PREFIX . "edu_org where user_id = " . $user_id);
            $h5_url['url_user_store'] = $store_url;
        }
        if ((defined('PAI_REAL_BTN') && PAI_REAL_BTN == 1) && (defined('PAI_VIRTUAL_BTN') && PAI_VIRTUAL_BTN == 0)) {
            $h5_url['url_user_pai'] = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=goods&is_true=1';
            $h5_url['url_podcast_pai'] = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_podcast&act=goods&is_true=1';
        }
        $root['h5_url'] = $h5_url;
    } else {
        $fields = array('id', 'fans_count', 'focus_count', 'is_agree', 'video_count', 'is_authentication', 'nick_name', 'signature', 'sex', 'province', 'city', 'head_image', 'ticket', 'no_ticket', 'use_diamonds', 'user_level', 'v_type', 'v_explain', 'v_icon', 'is_remind', 'birthday', 'emotional_state', 'job', 'family_id', 'family_chieftain', 'is_robot', 'room_title', 'luck_num', 'weibo_count', 'classified_id', 'is_admin', 'default_mount');
        $fields = array_merge($fields, array('is_vip', 'vip_expire_time'));
        $userinfo = $user_redis->getRow_db($to_user_id, $fields);
        $userinfo['user_id'] = $to_user_id;
        $userinfo['signature'] = htmlspecialchars_decode($userinfo['signature']);
        $userinfo['nick_name'] = htmlspecialchars_decode($userinfo['nick_name']);
        $userinfo['signature'] = ($userinfo['signature']);
        $userinfo['nick_name'] = ($userinfo['nick_name']);
        if (defined("robot_gifts") && robot_gifts == 1) {
            $userinfo['ticket'] = intval(floor($userinfo['ticket'] + $userinfo['no_ticket']));
        } else {
            $userinfo['ticket'] = intval(floor($userinfo['ticket']));
        }
        //===================slf add by 20160927 ============
        if ($userinfo['signature'] == '' || $userinfo['signature'] == false) {
            $userinfo['signature'] = 'TA好像忘记签名了';
        }
        //===================================================
        /*
        $sql = "select id as user_id,video_count,is_authentication,nick_name,signature,sex,province,city,focus_count,head_image,fans_count,ticket,use_diamonds,user_level,v_type,v_explain,v_icon,emotional_state,job,city,birthday from ".DB_PREFIX."user where id = '".$to_user_id."'";
        $userinfo = $GLOBALS['db']->getRow($sql,true,true);
         */
        if ($userinfo['birthday'] == false) {
            $userinfo['birthday'] = '';
        }
        if ($userinfo['room_title'] === false) {
            $userinfo['room_title'] = '';
        }
        if ($userinfo['luck_num'] === false) {
            $userinfo['luck_num'] = '';
        }
        if ($userinfo['emotional_state'] === false) {
            $userinfo['emotional_state'] = '';
        }
        if ($userinfo['family_id'] == false) {
            $userinfo['family_id'] = 0;
        }
        if ($userinfo['family_chieftain'] == false) {
            $userinfo['family_chieftain'] = 0;
        }
        if ($userinfo['is_robot'] === false) {
            $userinfo['is_robot'] = 0;
        }
        if ($userinfo['job'] === false) {
            $userinfo['job'] = '';
        }
        if ($userinfo['id'] === false) {
            $userinfo['id'] = $user_id;
        }
        if ($userinfo['fans_count'] === false) {
            $userinfo['fans_count'] = 0;
        }
        if ($userinfo['focus_count'] === false) {
            $userinfo['focus_count'] = 0;
        }
        if ($userinfo['is_agree'] === false) {
            $userinfo['is_agree'] = 0;
        }
        if ($userinfo['is_remind'] === false) {
            $userinfo['is_remind'] = 0;
        }
        if ($userinfo['video_count'] === false) {
            $userinfo['video_count'] = 0;
        }
        if ($userinfo['is_authentication'] === false) {
            $userinfo['is_authentication'] = 0;
        }
        if ($userinfo['sex'] === false) {
            $userinfo['sex'] = 0;
        }
        if ($userinfo['province'] === false) {
            $userinfo['province'] = '';
        }
        if ($userinfo['city'] === false) {
            $userinfo['city'] = '';
        }
        if ($userinfo['head_image'] === false) {
            $userinfo['head_image'] = '';
        }
        if ($userinfo['use_diamonds'] === false) {
            $userinfo['use_diamonds'] = 0;
        }
        if ($userinfo['user_level'] === false) {
            $userinfo['user_level'] = 1;
        }
        if ($userinfo['v_type'] === false) {
            $userinfo['v_type'] = 0;
        }
        if ($userinfo['v_explain'] === false) {
            $userinfo['v_explain'] = '';
        }
        if ($userinfo['v_icon'] === false) {
            $userinfo['v_icon'] = '';
        }
        if ($userinfo['is_vip'] === false) {
            $userinfo['is_vip'] = 0;
        }
        if ($userinfo['weibo_count'] === false) {
            $userinfo['weibo_count'] = 0;
        }
        $userinfo['is_admin'] = !empty($userinfo['is_admin']) ? intval($userinfo['is_admin']) : 0;
        $userinfo['moments'] = $userinfo['weibo_count'];
        $vip_expire_time = intval($userinfo['vip_expire_time']);
        if ($vip_expire_time > 0) {
            $userinfo['vip_expire_time'] = to_date($vip_expire_time, 'Y-m-d H:i');
            if ($vip_expire_time < NOW_TIME) {
                $userinfo['is_vip'] = 0;
                $userinfo['vip_expire_time'] = '已过期';
                $sql = "update " . DB_PREFIX . "user set is_vip = 0 where id = " . $to_user_id;
                $GLOBALS['db']->query($sql);
                user_deal_to_reids(array($to_user_id));
            } else {
                if (intval($userinfo['is_vip']) == 0) {
                    $userinfo['vip_expire_time'] = '未开通';
                }
            }
        } else {
            $userinfo['vip_expire_time'] = '未开通';
            if (intval($userinfo['is_vip']) == 1) {
                $userinfo['vip_expire_time'] = '永久';
            }
        }
        $root['user'] = $userinfo;
        $root['user']['head_image'] = get_spec_image($userinfo['head_image']);
        $root['user']['medals'] = array_map(function ($v) {
            return $v['icon'];
        }, load_auto_cache('user_medals', array('user_id' => $to_user_id, 'no_expired' => true)));
        //被查看的用户：个人主页地址
        //$root['user']['home_url'] = SITE_DOMAIN.APP_ROOT.'/index.php?isapp=1&c=home&podcast_id='.$to_user_id;
        //$root['user']['focus_count'] = $focus_count;
        //$root['user']['fans_count'] = $fans_count;
        if ($podcast_id > 0) {
            //主播查看
            if ($podcast_id == $user_id) {
                $root['show_admin'] = 2; //主播查看 主播：设置为管理员/取消管理员,管理员列表，禁言，取消
                $sql = "select id from " . DB_PREFIX . "user_admin where podcast_id = " . $user_id . " and user_id = " . $to_user_id;
                //$root['sql'] = $sql;
                if ($GLOBALS['db']->getOne($sql, true, true) > 1) {
                    $root['has_admin'] = 1; //0:非管理员;1:是管理员
                } else {
                    $root['has_admin'] = 0;
                }
            } else {
                $sql = "select count(id) as num from " . DB_PREFIX . "user_admin where podcast_id = '" . $podcast_id . "' and user_id = " . $user_id;
                if ($GLOBALS['db']->getOne($sql, true, true) > 0) {
                    $root['show_admin'] = 1;
                    $root['has_admin'] = 1; //0:非管理员;1:是管理员
                    //$root['sql'] = $sql;
                }
            }
            //管理员查看：主播; 不能：禁言 主播
            if ($root['show_admin'] == 1 && $podcast_id == $to_user_id) {
                $root['show_admin'] = 0;
            }
            if ($root['show_admin'] > 0) {
                $root['show_tipoff'] = 0;
            } else {
                $root['show_tipoff'] = 1;
            }
        }
        //关注
        //fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
        $userfollw_redis = new UserFollwRedisService($user_id);
        if ($userfollw_redis->is_following($to_user_id)) {
            $root['has_focus'] = 1; //0:未关注;1:已关注
        }
        /*
    //关注
    $sql = "select count(id) as num from ".DB_PREFIX."focus where podcast_id = ".$to_user_id." and user_id = ".$user_id;
    if ($GLOBALS['db']->getOne($sql,true,true) > 0){
    $root['has_focus'] = 1;//0:未关注;1:已关注
    }*/
    }
    if ($podcast_id == 0) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
        $video_con = new VideoContributionRedisService();
        $con_list = $video_con->get_podcast_contribute($to_user_id, 0, 3, 1);
        //秀票贡献前3名
        $root['cuser_list'] = $con_list;
        foreach ($root['cuser_list'] as $k => $v) {
            $root['cuser_list'][$k]['nick_name'] = ($v['nick_name']);
        }
        //拖黑
        $sql = "select count(id) as num from " . DB_PREFIX . "black where black_user_id = " . $to_user_id . " and user_id = " . $user_id;
        if ($GLOBALS['db']->getOne($sql, true, true) > 0) {
            $root['has_black'] = 1; //0:未拖黑;1:已拖黑
        } else {
            $root['has_black'] = 0; //0:未拖黑;1:已拖黑 $podcast_id == 0 时有效
        }
        $item = array();
        if ($root['user']['birthday'] == 0) {
            $root['user']['age'] = $item['年龄'] = '你猜';
        } else {
            $root['user']['age'] = $item['年龄'] = ceil((NOW_TIME - $root['user']['birthday']) / 31536000) . "岁";
        }
        $item['情感状态'] = $root['user']['emotional_state'];
        $item['家乡'] = $root['user']['province'] . " " . $root['user']['city'];
        $item['职业'] = $root['user']['job'];
        //如果有靓号，显示的用户ID为靓号luck_num
        if (intval($root['user']['luck_num']) > 0) {
            $item[$m_config['short_name'] . '号'] = $userinfo['luck_num'];
        } else {
            $item[$m_config['short_name'] . '号'] = $root['user']['user_id'];
        }
        // $item[$m_config['short_name'].'号'] = $root['user']['user_id'];
        $item['个性签名'] = $root['user']['signature'];
        //清空空值
        foreach ($item as $k => $v) {
            if (trim($v) == '') {
                unset($item[$k]);
            } else {
                $item[$k] = htmlspecialchars_decode($v);
            }
        }
        $root['user']['item'] = $item;
    } else {
        //秀票贡献第一名
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
        $video_con = new VideoContributionRedisService();
        $con_root = $video_con->get_podcast_contribute($to_user_id, 0, 1, 1);
        if ($con_root[0]) {
            $root['cuser'] = $con_root[0];
        }
    }
    if (!$GLOBALS['db']->getOne("select id from " . DB_PREFIX . "video where live_in = 1 and user_id=" . $user_id, true, true)) {
        // if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
        //     $video = $GLOBALS['db']->getRow("select id as room_id,group_id,live_in,user_id,video_type,head_image,create_type,live_image,room_type from " . DB_PREFIX . "video where user_id=" . $to_user_id . " and live_in in (1,3) order by sort_num desc,sort desc", true, true);
        // } else {
        $video = $GLOBALS['db']->getRow("select id as room_id,group_id,live_in,user_id,video_type,head_image,create_type,live_image from " . DB_PREFIX . "video where user_id=" . $to_user_id . " and live_in in (1,3) and room_type = 3 order by sort_num desc,sort desc", true, true);
        // }
        if (intval($video['room_id'])) {
            $video['head_image'] = get_spec_image($video['head_image']);
            $video['live_image'] = get_spec_image($video['live_image']);
            if (defined('OPEN_EDU_MODULE') && OPEN_EDU_MODULE == 1) {
                $video['is_verify'] = intval($GLOBALS['db']->getOne("select is_verify from " . DB_PREFIX . "edu_video_info where video_id = " . $video['room_id']));
            }
            if (empty($video['group_id']) && defined('CHILD_ROOM') && CHILD_ROOM) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                $child_room = new child_room();
                $parent_id = $child_room->parent_id($video['room_id']);
                $video['group_id'] = $GLOBALS['db']->getOne("SELECT group_id FROM " . DB_PREFIX . "video WHERE id =" . $parent_id);
            }
            $root['video'] = $video;
        }
    }
    return $root;
}
/**
 * 设置关注/取消关注
 * @param unknown_type $user_id 关注用户ID
 * @param unknown_type $to_user_id 被关注的用户ID
 * $force_remove: 强制取消关注操作
 * @return multitype:number string
 */
function redis_set_follow($user_id, $to_user_id, $force_remove = false, $room_id = 0)
{
    $root = array();
    $root['status'] = 1;
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
    $user_follw_redis = new UserFollwRedisService($user_id);
    if ($user_follw_redis->is_following($to_user_id) || $force_remove) {
        //已关注,取消关注操作
        //if ($user_redis->is_following($to_user_id)){
        //取消关注;
        $user_follw_redis->unfollow($to_user_id, $room_id);
        /*
        $user_redis->follow_count();//关注数 减少1
        $user2_redis = new UserNodeService($to_user_id);
        $user2_redis->follower_count();//粉丝数 减少1
         */
        //}
    } else {
        //未关注,添加关注操作
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $follow_max = $m_config['follow_max'];
        if ($user_follw_redis->follow_count() >= $follow_max) {
            $root['error'] = '关注用户不能超过' . $follow_max . '个';
            $root['status'] = 0;
            return $root;
        } else {
            //关注操作
            $user_follw_redis->follow($to_user_id, $room_id);
            /*
            $user_redis->follow_count();//关注数 减少1
            $user2_redis = new UserNodeService($to_user_id);
            $user2_redis->follower_count();//粉丝数 减少1
             */
            //若在黑名单，解除拉黑
            set_black($user_id, $to_user_id, true);
        }
    }
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
    $user_redis = new UserRedisService();
    $user_data = array();
    $user_data['fans_count'] = $user_follw_redis->follower_count(); //粉丝数
    $user_data['focus_count'] = $user_follw_redis->follow_count(); //关注数
    $user_redis->update_db($user_id, $user_data);
    $sql = "update " . DB_PREFIX . "user set fans_count = " . $user_data['fans_count'] . ",focus_count = " . $user_data['focus_count'] . " where id = " . $user_id;
    $GLOBALS['db']->query($sql);
    $user2_follw_redis = new UserFollwRedisService($to_user_id);
    $user_data = array();
    $user_data['fans_count'] = $user2_follw_redis->follower_count(); //粉丝数
    $user_data['focus_count'] = $user2_follw_redis->follow_count(); //关注数
    $user_redis->update_db($to_user_id, $user_data);
    $sql = "update " . DB_PREFIX . "user set fans_count = " . $user_data['fans_count'] . ",focus_count = " . $user_data['focus_count'] . " where id = " . $to_user_id;
    $GLOBALS['db']->query($sql);
    if ($user_follw_redis->is_following($to_user_id)) {
        $root['has_focus'] = 1; //0:未关注;1:已关注
    } else {
        $root['has_focus'] = 0;
    }
    $root['fans_count'] = $user2_follw_redis->follower_count();
    $root['focus_count'] = $user_follw_redis->follow_count(); //关注数
    return $root;
}

/**
 * 结束直播
 * @param unknown_type $video(id,user_id,group_id,watch_number,begin_time,end_time,room_type)
 * @param string $video_vid
 * @param string $is_aborted 1:被服务器异常终止结束(主要是心跳超时)
 */
function do_end_video($video, $video_vid, $is_aborted = 0, $cate_id = 0)
{
    $user_id = $video['user_id'];
    $room_id = $video['id'];
    $group_id = $video['group_id'];
    //$watch_number = $video['watch_number'];
    $video_vid = strim($video_vid);
    if ($video_vid == 'null') {
        $video_vid = '';
    }
    /*if ($video_vid == '')
    $is_del_vod = 1;
    else
    $is_del_vod = 0;*/
    $is_del_vod = 1;
    $m_config = load_auto_cache("m_config");
    $short_video_time = $m_config['short_video_time'] ? $m_config['short_video_time'] : 300;
    //私有聊天或小于5分钟的视频，不保存 is_delete = IF(room_type =1 or ((".NOW_TIME." - begin_time) <300),1,is_delete)
    $sql = "update " . DB_PREFIX . "video set live_in = 0, in_livepk = 0, online_status=1,is_aborted = '" . $is_aborted . "',end_time = " . NOW_TIME . ",end_date = '" . to_date(NOW_TIME, 'Y-m-d') . "',video_vid = '" . $video_vid . "',is_del_vod = " . $is_del_vod . ",is_delete = IF(room_type =1 or ((" . NOW_TIME . " - begin_time) <" . $short_video_time . "),1,is_delete) where (live_in =1 or live_in = 2 or live_in = 0) and id = " . $room_id;
    $GLOBALS['db']->query($sql);
    if ($GLOBALS['db']->affected_rows()) {
        if ($cate_id > 0) {
            $sql = "update " . DB_PREFIX . "video_cate a set a.num = (select count(*) from " . DB_PREFIX . "video b where b.cate_id = a.id and b.live_in in (1,3)";
            if ((defined('OPEN_ROOM_HIDE') && OPEN_ROOM_HIDE == 1) && intval($m_config['open_room_hide']) == 1) {
                $sql .= " and b.province <> '火星' and b.province <>''";
            }
            $sql .= ") where a.id = " . $cate_id;
            $GLOBALS['db']->query($sql);
        }
        //直播结束,连麦也打上结束标识
        $sql = 'update ' . DB_PREFIX . "video_lianmai set stop_time ='" . NOW_TIME . "' where stop_time = 0 and video_id =" . $room_id;
        $GLOBALS['db']->query($sql);

        //开启公会功能ljz
        if (defined('OPEN_SOCIETY_MODULE') && intval(OPEN_SOCIETY_MODULE) == 1) {
            $video_id = intval($video['id']);
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $user_info = $GLOBALS['db']->getRow("select society_id,society_settlement_type from " . DB_PREFIX . "user where id=" . $user_id);
            //获取公会审核状态
            $society_status = $GLOBALS['db']->getOne("select status from " . DB_PREFIX . "society where id=" . $user_info['society_id']);
            //判断是否有公会
            if ($user_info['society_id'] > 0 && $society_status == 1) {
                switch ($m_config['society_pattern']) {
                    case 0:
                        // 关闭公会模式
                        platform_receipts($user_id, $m_config, $video, $video_id);
                        break;
                    case 1:
                        //有抽成模式
                        society_lv_jf($m_config, $user_id, $video_id, $video, $user_info);
                        break;
                    case 2:
                        //无抽成模式
                        society_receipts($video);
                        break;
                    case 3:
                        // 独立模式，只加会长的数据，主播的保存不变
                        independent_society_receipts($video);
                        break;
                }

            } else {
                platform_receipts($user_id, $m_config, $video, $video_id);
            }
        }
        //开启家族功能
        if (defined('OPEN_FAMILY_MODULE') && intval(OPEN_FAMILY_MODULE) == 1 && intval(OPEN_SOCIETY_MODULE) == 0) {
            family_receipts1($room_id, $user_id, $video);
        }
        //开启公会功能，直播结束后将受益写入公会收益表
        /* if(OPEN_SOCIETY_MODULE == 1){
        society_receipts($video);
        }  */
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
        $api = createTimAPI();
        $video_redis = new VideoRedisService();
        //直播结束时,将redis中计算的数据,同步一份到mysql;
        $fields = array('room_type', 'video_type', 'share_count', 'like_count', 'fans_count', 'sort_num', 'vote_number', 'robot_num', 'watch_number', 'virtual_watch_number', 'max_watch_number', 'channelid', 'prop_table', 'pk_ticket', 'in_livepk');
        $video_data = $video_redis->getRow_db($room_id, $fields);
        $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $video_data, "UPDATE", "id=" . $room_id);
        if ($video_data['room_type'] == 1) {
            $drop_video = true;
            if (defined('OPEN_EDU_MODULE') && OPEN_EDU_MODULE == 1) {
                // 教育众筹和约课不删除视频
                $sql = "select deal_id,booking_class_id from " . DB_PREFIX . "edu_video_info where video_id = " . $room_id;
                $edu_video_info = $GLOBALS['db']->getRow($sql);
                if ($edu_video_info && ($edu_video_info['deal_id'] > 0 || $edu_video_info['booking_class_id'] > 0)) {
                    $sql = "update " . DB_PREFIX . "video set is_delete = IF(((" . NOW_TIME . " - begin_time) <" . $short_video_time . "),1,0) where id = " . $room_id;
                    $GLOBALS['db']->query($sql);
                    $drop_video = false;
                }
            }
            //私密直播,
            if ($drop_video) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
                $video_private_redis = new VideoPrivateRedisService();
                $video_private_redis->drop_video($room_id);
            }
        }
        $parent_id = 0;
        if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
            $child_room = new child_room();
            $parent_id = $child_room->parent_id($room_id);
            if ($parent_id != $room_id) {
                $p_video_data = $video_redis->getRow_db($parent_id, $fields);
                $video_data['max_watch_number'] = $p_video_data['max_watch_number'];
            }
        }
        if ($video_data['video_type'] == 1 && $parent_id == $room_id) {
            //0:腾讯云互动直播;1:腾讯云直播
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
            $video_factory = new VideoFactory();
            $video_factory->StopLVBChannel($video_data['channelid']);
        }
        //获取直播间，红包 发放记录,主要用于,直播结束后,处理还未被领取的红包
        $red_list = $video_redis->get_reds($room_id);
        if (count($red_list) > 0) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedRedisService.php');
            $videoRed_redis = new VideoRedRedisService();
            foreach ($red_list as $red_id) {
                if ($videoRed_redis->red_exists($red_id)) {
                    $prop_table = $video_data['prop_table'];
                    if ($prop_table != '') {
                        $sql = "SELECT from_user_id FROM " . $prop_table . " WHERE id=" . $red_id;
                        $from_user_id = $GLOBALS['db']->getOne($sql, true, true);
                    }
                    do {
                        $money = $videoRed_redis->pop_red($red_id);
                        if ($money > 0) {
                            //随机获得一机器人
                            $robot_keys = $videoRed_redis->redis->srandmember($videoRed_redis->user_robot_db, 1);
                            $robot_id = intval($robot_keys[0]);
                            //如果是私密直播间，红包退回发送者
                            if ($video_data['room_type'] == 1) {
                                if (intval($from_user_id)) {
                                    $robot_id = $from_user_id;
                                }
                            }
                            allot_red_to_user($red_id, $robot_id, $money);
                        }
                    } while ($money > 0);
                }
            }
        }
        if (OPEN_PAI_MODULE == 1 && intval($video['pai_id']) > 0) {
            //关闭竞拍
            $data = array();
            $data['podcast_id'] = $video['user_id'];
            $data['pai_id'] = $video['pai_id'];
            $data['video_id'] = $room_id;
            $rs = FanweServiceCall("pai_podcast", "stop_pai", $data);
        }
        if ((defined('LIVE_END_TO_SCENE') && LIVE_END_TO_SCENE == 1) && intval($video['is_live_pay']) == 1) {
            //按时付费直播结束后转按场付费
            change_pay_type($video);
        }
        //将mysql数据,同步一份到redis中
        sync_video_to_redis($room_id, '*', false);
        if ($group_id != '') {
            //广播：直播结束
            $ext = array();
            $ext['type'] = 7; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
            $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
            $ext['show_num'] = $video_data['max_watch_number']; //观看人数
            $ext['fonts_color'] = ''; //字体颜色
            $ext['desc'] = '直播结束'; //弹幕消息;
            $ext['desc2'] = '直播结束'; //弹幕消息;
            //消息发送者
            //$sender = array();
            //$ext['sender'] = $sender;
            #构造高级接口所需参数
            $msg_content = array();
            //创建array 所需元素
            $msg_content_elem = array(
                'MsgType' => 'TIMCustomElem', //自定义类型
                'MsgContent' => array(
                    'Data' => json_encode($ext),
                    'Desc' => ''
                    //  'Ext' => $ext,
                    //  'Sound' => '',
                )
            );
            //将创建的元素$msg_content_elem, 加入array $msg_content
            array_push($msg_content, $msg_content_elem);
            //发送广播：直播结束
            // fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
            // $api = createTimAPI();
            $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                $child_room = new child_room();
                $child_ids = $child_room->child_id($room_id);
                foreach ($child_ids as $child_id) {
                    $ext['room_id'] = $child_id;
                    $ret = $api->group_send_group_msg2($user_id, $group_id, array(
                        array(
                            'MsgType' => 'TIMCustomElem', //自定义类型
                            'MsgContent' => array(
                                'Data' => json_encode($ext),
                                'Desc' => ''
                            )
                        )
                    ));
                }
            }
            //=========================================================
            //广播：直播结束
            $ext = array();
            $ext['type'] = 18; //18：直播结束（全体推送的，用于更新用户列表状态）
            $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
            //18：直播结束（全体推送的，用于更新用户列表状态）
            $api->group_send_group_system_notification($m_config['on_line_group_id'], json_encode($ext), null);
            //=========================================================
            return $ret;
        } else {
            return true;
        }
    } else {
        return false;
    }
}
/**
 * 直播结束 后相关数据处理（在后台定时执行）
 *
 *$video id,user_id,group_id,room_type,begin_time,end_time,video_vid,is_delete,is_del_vod
 *
 * 1、处理 fanwe_video_viewer 异常数据;没有begin_time时间的,用fanwe_video.begin_time代替; 没有end_time 用fanwe_video.end_time代替
 * 2、统计用户在线时长，fanwe_video_viewer
 * 3、移除fanwe_video_viewer,fanwe_video_contribution,fanwe_video_monitor,fanwe_video_share,fanwe_video,fanwe_video_lianmai数据到历史表中
 * @param unknown_type $video(id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time)
 */
function do_end_video_2(&$video_redis, &$api, $video_id)
{
    $pInTrans = $GLOBALS['db']->StartTrans();
    try
    {
        $sql = "update " . DB_PREFIX . "video set live_in = -1 where live_in = 0 and id = " . $video_id;
        //echo $sql."<br>";
        $GLOBALS['db']->query($sql);
        if ($GLOBALS['db']->affected_rows()) {
            $sql = "select * from " . DB_PREFIX . "video where id = " . $video_id;
            //$video = $video_redis->getRow_db($video_id);
            $video = $GLOBALS['db']->getRow($sql);
            $group_id = $video['group_id'];
            $begin_time = $video['begin_time'];
            $end_time = $video['end_time'];
            $user_id = $video['user_id'];
            $is_del_vod = $video['is_del_vod'];
            $is_delete = $video['is_delete'];
            $room_type = $video['room_type']; //房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
            /*
            //处理fanwe_video_viewer 异常数据
            $sql = "update ".DB_PREFIX."video_viewer set is_exception = 1, begin_time = '".$begin_time."' where begin_time = 0 and group_id = '".$group_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql = "update ".DB_PREFIX."video_viewer set is_exception = 1,end_time = '".$end_time."' where end_time = 0 and group_id = '".$group_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            //fanwe_video_viewer
            //统计用户在线时长
            $sql = "update ".DB_PREFIX."user u,
            (select user_id, sum(end_time - begin_time) as time_len from ".DB_PREFIX."video_viewer where group_id = '".$group_id."' group by user_id) t
            set u.online_time = u.online_time + t.time_len
            where t.user_id = u.id";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            //将本次直播观众移到历史表中
            $sql = "insert into ".DB_PREFIX."video_viewer_history(video_id,group_id,user_id,begin_time,end_time,is_robot,is_exception) select ".$video_id." as video_id,group_id,user_id,begin_time,end_time,is_robot,is_exception from ".DB_PREFIX."video_viewer where group_id='".$group_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql= "delete from ".DB_PREFIX."video_viewer where group_id='".$group_id."'";
            $GLOBALS['db']->query($sql);
            ///echo $sql."<br>";
            //将本次直播贡献排行移到历史表中
            $sql = "insert into ".DB_PREFIX."video_contribution_history(video_id,user_id,num) select video_id,user_id,num from ".DB_PREFIX."video_contribution where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql= "delete from ".DB_PREFIX."video_contribution where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
             */
            //将本次直播 心跳监听 移到历史表中
            $sql = "insert into " . DB_PREFIX . "video_monitor_history(video_id,user_id,vote_number,watch_number,lianmai_num,monitor_time,statistic_time,appCPURate,sysCPURate,sendKBps,recvKBps,sendLossRate,fps,device) select video_id,user_id,vote_number,watch_number,lianmai_num,monitor_time,statistic_time,appCPURate,sysCPURate,sendKBps,recvKBps,sendLossRate,fps,device from " . DB_PREFIX . "video_monitor where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql = "delete from " . DB_PREFIX . "video_monitor where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            //将本次直播 用户分享记录 移到历史表中
            $sql = "insert into " . DB_PREFIX . "video_share_history(video_id,user_id,type,create_time) select video_id,user_id,type,create_time from " . DB_PREFIX . "video_share where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql = "delete from " . DB_PREFIX . "video_share where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            //修正异常连麦结束数据
            $sql = 'update ' . DB_PREFIX . "video_lianmai set stop_time ='" . $end_time . "' where stop_time = 0 and video_id ='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //将本次直播 用户连麦记录 移到历史表中
            $sql = "insert into " . DB_PREFIX . "video_lianmai_history(video_id,user_id,start_time,stop_time) select video_id,user_id,start_time,stop_time from " . DB_PREFIX . "video_lianmai where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql = "delete from " . DB_PREFIX . "video_lianmai where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //修正异常PK
            $sql = 'update ' . DB_PREFIX . "video_livepk set stop_time ='" . $end_time . "' where stop_time = 0 and video_id ='" . $video_id . "' and  AND start_time<=" . NOW_TIME;
            $GLOBALS['db']->query($sql);
            //将本次直播PK记录 移到历史表中
            $sql = "insert into " . DB_PREFIX . "video_livepk_history(`user_id`, `video_id`, `to_user_id`, `to_video_id`, `pk_time`, `is_win`, `pk_theme`, `start_time`, `stop_time`) select `user_id`, `video_id`, `to_user_id`, `to_video_id`, `pk_time`, `is_win`, `pk_theme`, `start_time`, `stop_time` from " . DB_PREFIX . "video_livepk where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql = "delete from " . DB_PREFIX . "video_livepk where video_id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            /*
            //将本次直播 礼物记录 移到历史表中
            $sql = "insert into ".DB_PREFIX."video_prop_history(id,prop_id,prop_name,total_score,total_diamonds,use_diamonds,total_ticket,from_user_id,to_user_id,create_time,num,video_id,group_id,is_red_envelope,create_date,ActionStatus,ErrorInfo,ErrorCode) select id,prop_id,prop_name,total_score,total_diamonds,use_diamonds,total_ticket,from_user_id,to_user_id,create_time,num,video_id,group_id,is_red_envelope,create_date,ActionStatus,ErrorInfo,ErrorCode from ".DB_PREFIX."video_prop where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql= "delete from ".DB_PREFIX."video_prop where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            //将本次直播 红包记录 移到历史表中
            $sql = "insert into ".DB_PREFIX."video_red_envelope_history(id,video_id,video_prop_id,user_id,nick_name,diamonds,sex,head_image,create_time) select id,video_id,video_prop_id,user_id,nick_name,diamonds,sex,head_image,create_time from ".DB_PREFIX."video_red_envelope where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql= "delete from ".DB_PREFIX."video_red_envelope where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
             */
            /* fanwe_video_private 放在redis中处理
            if ($room_type == 1){
            //将本次私聊直播 被护肩 的用户 移到历史表中
            $sql = "insert into ".DB_PREFIX."video_private_history(video_id,user_id) select video_id,user_id from ".DB_PREFIX."video_private where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
            $sql= "delete from ".DB_PREFIX."video_private where video_id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            }
             */
            //echo $sql."<br>";
            /*
            //把fanwe_video 也移历史表中 end_date = ".to_date(NOW_TIME,'Y-m-d')
            $fields = "id,title,user_id,live_in,end_date,watch_number,virtual_watch_number,vote_number,cate_id,province,city,create_time,begin_time,end_time,group_id,destroy_group_status,long_polling_key,is_hot,is_new,max_watch_number,room_type,is_del_vod,video_vid,monitor_time,is_delete,robot_num,robot_time,channelid,is_aborted,tipoff_count";
            $sql = "insert into ".DB_PREFIX."video_history(".$fields.") select ".$fields." from ".DB_PREFIX."video where id='".$video_id."'";
            $GLOBALS['db']->query($sql);
            //echo $sql."<br>";
             */
            $video['live_in'] = 0; //改成：直播结束 live_in 0:结束;1:正在直播;2:创建中;3:回看
            $end_time = intval($video['end_time']);
            if ($end_time == 0) {
                $end_time = to_timespan($video['monitor_time']);
            }
            $video['len_time'] = $video['len_time'] + ($end_time - $video['begin_time']);
            $GLOBALS['db']->autoExecute(DB_PREFIX . "video_history", $video, "INSERT");
            $sql = "delete from " . DB_PREFIX . "video where id='" . $video_id . "'";
            $GLOBALS['db']->query($sql);
            //将付费直播记录移到历史表
            if (intval($video['is_live_pay']) == 1) {
                syn_live_pay_to_history($video_id, $video['user_id']);
            }
            //echo $sql."<br>";
            /*
            //删除禁言数据
            $sql = "delete from ".DB_PREFIX."video_forbid_send_msg where group_id='".$group_id."'";
            $GLOBALS['db']->query($sql);
             */
            //video_count
            //$sql = "select id,title,begin_time,max_watch_number from ".DB_PREFIX."video_history where is_delete = 0 and is_del_vod = 0 and user_id = '".$to_user_id."' order by ".$sort_field." limit ".$limit;
            $sql = "select count(*) as num from " . DB_PREFIX . "video_history where is_delete = 0 and is_del_vod = 0 and user_id = '" . $user_id . "'";
            $video_count = $GLOBALS['db']->getOne($sql);
            $sql = "update " . DB_PREFIX . "user set video_count = " . $video_count . " where id = " . $user_id;
            $GLOBALS['db']->query($sql);
            //将直播间,用户领取的红包记录,同步一份到mysql
            syn_red_to_mysql($video_id);
            $GLOBALS['db']->Commit($pInTrans);
            $pInTrans = false;
            $data = array();
            /*改成在 删除视频时 解散群组
            //如果是删除状态,则解散群组
            if ($is_delete == 1 && $video['destroy_group_status'] == 1){
            if ($video['group_id'] != ''){
            $ret = $api->group_destroy_group($video['group_id']);
            $data['destroy_group_status'] = $ret['ErrorCode'];
            $video_redis->del_video_group_db($video['group_id']);//只有在：解散 聊天组时，才删除
            }else{
            $data['destroy_group_status'] = 0;
            }
            }
            $video_redis->update_db($video_id, $data);
             */
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_data = array();
            $user_data['video_count'] = $video_count;
            $user_redis->update_db($user_id, $user_data);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $data = [
                'game_log_id' => 0,
                'banker_id' => 0,
                'banker_status' => 0,
                "banker_log_id" => 0,
                "banker_name" => '',
                "banker_img" => '',
                'coin' => 0
            ];
            $video_redis->update_db($video_id, $data);
            if ($is_del_vod == 0 && $is_delete == 1) {
                $m_config = load_auto_cache("m_config");
                $del_short_video = $m_config['del_short_video'] ? $m_config['del_short_video'] : 1;
                if ($del_short_video) {
                    return del_vodset($video, true); //直播删除短视频
                } else {
                    return $video_id;
                }
            } else {
                return $video_id;
            }
        } else {
            $GLOBALS['db']->Rollback($pInTrans);
            return 0;
        }
    } catch (Exception $e) {
        //异常回滚
        $GLOBALS['db']->Rollback($pInTrans);
        return 0;
    }
}
/**
 * 设置 黑名单/取消 黑名单
 * @param unknown_type $user_id 关注用户ID
 * @param unknown_type $to_user_id 被关注的用户ID
 * $force_remove: 强制取消黑名单
 * @return multitype:number string
 */
function set_black($user_id, $to_user_id, $force_remove = false)
{
    $root = array();
    $root['status'] = 1;
    //
    $pInTrans = $GLOBALS['db']->StartTrans();
    try
    {
        $sql = "select id from " . DB_PREFIX . "black where black_user_id = " . $to_user_id . " and user_id = " . $user_id;
        $black_id = $GLOBALS['db']->getOne($sql);
        if ($black_id > 0 || $force_remove) {
            if ($black_id > 0) {
                //echo "black_a_id:".$black_id;
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->sns_black_delete((string) $user_id, (string) $to_user_id);
                if ($ret['ActionStatus'] == 'OK') {
                    //取消黑名单操作;
                    $sql = "delete from " . DB_PREFIX . "black where id = " . $black_id;
                    $GLOBALS['db']->query($sql);
                } else {
                    $root['status'] = 0;
                    $root['error'] = $ret['ErrorCode'] . $ret['ErrorInfo'];
                }
            }
        } else {
            //echo "black_b_id:".$black_id;
            fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
            $api = createTimAPI();
            $ret = $api->sns_black_import((string) $user_id, (string) $to_user_id);
            //print_r($ret);
            if ($ret['ActionStatus'] == 'OK') {
                //未关注,需要关注操作;
                $black = array();
                $black['black_user_id'] = $to_user_id;
                $black['user_id'] = $user_id;
                $black['create_time'] = NOW_TIME;
                $GLOBALS['db']->autoExecute(DB_PREFIX . "black", $black, "INSERT");
                //取消关注
                //set_follow($user_id,$to_user_id,true);
                redis_set_follow($user_id, $to_user_id, true);
            } else {
                $root['status'] = 0;
                $root['error'] = $ret['ErrorCode'] . $ret['ErrorInfo'];
            }
        }
        $sql = "select count(id) as num from " . DB_PREFIX . "black where black_user_id = " . $to_user_id . " and user_id = " . $user_id;
        //$root['sql'] = $sql;
        if ($GLOBALS['db']->getOne($sql) > 0) {
            $root['has_black'] = 1; //0:未黑名单;1:黑名单
        } else {
            $root['has_black'] = 0;
        }
        if ($root['status'] == 1) {
            $GLOBALS['db']->Commit($pInTrans);
        } else {
            $GLOBALS['db']->Rollback($pInTrans);
        }
    } catch (Exception $e) {
        //异常回滚
        $root['error'] = $e->getMessage();
        $root['status'] = 0;
        $GLOBALS['db']->Rollback($pInTrans);
    }
    return $root;
}
/**
 * 获得点播资料
 * @param unknown_type $video_vid
 * @return multitype:multitype: number string
 * https://www.qcloud.com/doc/api/257/2331
 */
function get_vodset($video_vid)
{
    $root = array();
    $root['status'] = 1;
    //$root['vodset'] = array();
    //var_dump($video_vid);
    $video_vid = htmlspecialchars_decode($video_vid);
    //var_dump($video_vid);
    $video_vid = str_replace("&quot;", "", $video_vid);
    $video_vid = str_replace("[", "", $video_vid);
    $video_vid = str_replace("]", "", $video_vid);
    $video_vid = str_replace('"', '', $video_vid);
    $video_vid = explode(",", $video_vid);
    //var_dump($video_vid2);
    //$video_vid3 = json_decode($video_vid);
    //var_dump($video_vid3);
    //print_r($video_vid);
    //exit;
    if (count($video_vid) > 0) {
        fanwe_require(APP_ROOT_PATH . 'system/QcloudApi/QcloudApi.php');
        $m_config = load_auto_cache("m_config");
        $config = array('SecretId' => $m_config['qcloud_secret_id'],
            'SecretKey' => $m_config['qcloud_secret_key'],
            'RequestMethod' => 'GET',
            'DefaultRegion' => 'gz');
        //print_r($config);exit;
        $service = QcloudApi::load(QcloudApi::MODULE_VOD, $config);
        foreach ($video_vid as $k => $v) {
            $package = array('vid' => $v);
            $ret = $service->DescribeRecordPlayInfo($package);
            //var_dump($package);
            //var_dump($ret);
            //exit;
            if ($ret === false) {
                $error = $service->getError();
                $root['status'] = 0;
                $root['error'] = $v . ";" . $error->getCode() . ";" . $error->getMessage();
            } else {
                $root['vodset'][] = $ret;
                /*
            //code错误码, 0: 成功, 其他值: 失败; message: 错误信息;
            if ($ret['code'] == 0){
            foreach ( $ret['fileSet'] as $k => $v )
            {
            }
            }
             */
            }
        }
    } else {
        $root['status'] = 0;
        $root['error'] = '无效的直播录制文件';
    }
    return $root;
}
/**
 * 删除录制视频
 * @param unknown_type $room_id
 */
function del_vodset($video, $is_del_group = false)
{
    if (!is_array($video)) {
        $video = array('id' => $video);
    }
    $room_id = $video['id'];
    $root = array();
    $root['status'] = 1;
    $root['room_id'] = $room_id;
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
    $video_factory = new VideoFactory();
    if ($video['video_type'] == 1 && $video['channelid'] && strpos($video['channelid'], '_')) {
        //修复连麦视频无法删除问题
        $m_config = load_auto_cache('m_config');
        $bizId = $m_config['qcloud_bizid'];
        $filename = $bizId . "_" . $room_id;
        $root['delvodset'] = $video_factory->DeleteVodFilesByFileName($filename);
    } else {
        $filename = $room_id;
        if ($video['video_type'] == 1) {
            $filename = 'live' . $room_id;
        }
        $root['delvodset'] = $video_factory->DeleteVodFilesByFileName($filename);
    }
    //$sql = "select id as room_id,video_vid,is_del_vod,group2_id,group2_status from ".DB_PREFIX."video_history where id = ".$room_id;
    //$video = $GLOBALS['db']->getRow($sql);
    $isdelall = true;
    foreach ($root['delvodset'] as $k => $v) {
        if ($v != 1 && isset($v['code']) && $v['code'] != 0) {
            $isdelall = false;
            $root['error'] = $root['error'] . ";key:" . $k . ";code:" . $v['code'] . ";message:" . $v['message'];
        }
    }
    if ($isdelall) {
        $sql = "update " . DB_PREFIX . "video_history set is_del_vod = 1 where id = " . $room_id;
        $GLOBALS['db']->query($sql);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $data = array();
        $data['is_del_vod'] = 1;
        $video_redis->update_db($room_id, $data);
        //解散聊天组
        if ($is_del_group) {
            $sql = "select destroy_group_status,group_id from " . DB_PREFIX . "video_history where id = " . $room_id;
            $video_data = $GLOBALS['db']->getRow($sql);
            //如果是删除状态,则解散群组
            if ($video_data['destroy_group_status'] == 1) {
                if ($video_data['group_id'] != '') {
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();
                    $ret = $api->group_destroy_group($video_data['group_id']);
                    $destroy_group_status = $ret['ErrorCode'];
                } else {
                    $destroy_group_status = 0;
                }
                $sql = "update " . DB_PREFIX . "video_history set destroy_group_status = " . $destroy_group_status . " where id = " . $room_id;
                $GLOBALS['db']->query($sql);
                $data = array();
                $data['destroy_group_status'] = $destroy_group_status;
                $video_redis->update_db($room_id, $data);
            }
        }
        //将直播间,用户领取的红包记录,同步一份到mysql
        syn_red_to_mysql($room_id);
        $sql = "select destroy_group_status,is_del_vod from " . DB_PREFIX . "video_history where id = " . $room_id;
        $video_data = $GLOBALS['db']->getRow($sql);
        if ($video_data['destroy_group_status'] == 0 && $video_data['is_del_vod'] == 1) {
            $video_redis->del_db($room_id); //清空redis上视频相关数量【fanwe_video,禁言,点赞,观众列表,group_id与 video_id对应数据】
        }
    }
    //print_r($root);
    return $root;
}
//防盗连接sign = md5(【腾讯号 + 用户ID + 房间号】)
function check_sign($sign, $sign_version, $tim_sdkappid, $user_id, $room_id)
{
    //防盗连接问题 sign = md5(【腾讯号 + 用户ID + 房间号】)
    if ($sign_version == '1.0') {
        $sign2 = md5($tim_sdkappid . $user_id . $room_id);
    } else {
        $sign2 = md5($tim_sdkappid . $user_id . $room_id);
    }
    return (($sign == $sign2) && $sign != '');
}
//获取直播信息2
function get_video_info2($room_id, $user_id, $type, $param, $require_type = 0)
{
    $has_scroll = strim($param['has_scroll']); //1: 自动会多返回一个podcast2(room_id,head_image)参数,用于上下滚动切换时，预加载使用; 当预加载直播失效时，自动返回下一个有效的直播房间
    $private_key = strim($param['private_key']); //私密直播key
    $is_vod = intval($param['is_vod']); //0:观看直播;1:点播
    $sex = intval($param['sex']); //性别 0:全部, 1-男，2-女，默认为：0
    $cate_id = intval($param['cate_id']); //话题id，默认为：0
    $city = strim($param['city']); //城市(空为:热门)，默认为：空
    $type = intval($param['type']);
    if ($city == '热门' || $city == 'null') {
        $city = '';
    }
    $require_type = intval($require_type); //1：PC端；0：app端
    $root = array();
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    if ($room_id > 0) {
        //获得当前用户和主播的手机资料
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $u_user_mobile = $user_redis->getRow_db($user_id, array('mobile', 'is_effect'));
        if ($u_user_mobile['is_effect'] == 0) {
            $root = array();
            $root['error'] = "无效帐户";
            $root['status'] = 0;
            if (!$require_type) {
                ajax_return($root);
            }
        }
        //累计观看次数;累计观看次数明显大于其它用户观看次数时，及有可能是盗连接用户，需要禁用它
        $sql = "UPDATE  " . DB_PREFIX . "user SET view_count = view_count + 1 where id = " . $user_id;
        $GLOBALS['db']->query($sql);
        $fields = array('id', 'channelid', 'create_type', 'head_image', 'cate_id', 'title', 'thumb_head_image', 'xpoint', 'ypoint', 'sort_num', 'push_rtmp', 'play_url', 'play_mp4', 'play_flv', 'play_rtmp', 'play_hls', 'room_type', 'user_id', 'live_in', 'monitor_time', 'max_watch_number', 'online_status', 'video_vid', 'is_del_vod', 'is_delete', 'video_type', 'group_id', 'room_type', 'private_key', 'share_type', 'province', 'city', 'pai_id', 'begin_time', 'create_time', 'live_pay_time', 'is_live_pay', 'live_pay_type', 'live_fee', 'live_is_mention', 'room_title', 'pay_room_id', 'tourist_astrict', 'in_livepk');
        if (OPEN_GAME_MODULE == 1) {
            $fields[] = 'game_log_id';
        }
        if (OPEN_BM == 1) {
            $fields[] = 'is_bm';
            $fields[] = 'is_push';
        }
        if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
            $fields[] = 'private_code';
            $fields[] = 'prop_table';
        }
        $m_config = load_auto_cache("m_config"); //手机端配置
        $root = $video_redis->getRow_db($room_id, $fields);
        if ($root['pay_room_id'] === false) {
            $root['pay_room_id'] = 0;
        }
        $monitor_overtime = $m_config['monitor_overtime'];
        if ($monitor_overtime <= 0) {
            $monitor_overtime = 40;
        }
        if ($root['live_in'] == 1 && $root['monitor_time'] > 0 && $root['monitor_time'] < NOW_TIME - $monitor_overtime) {
            //心跳超时
            crontab_do_end_video();
            $root = $video_redis->getRow_db($room_id, $fields);
        }
        //兼容IM推送失败列表不刷新，存在已关闭的直播显示问题
        if (intval($root['live_in']) == 0 && $is_vod == 0) {
            $root = array();
            $root['error'] = "直播已被关闭！";
            $root['status'] = 0;
            if ($require_type == 1) {
                return $root;
            } else {
                ajax_return($root);
            }
        }
        $root['head_image'] = get_spec_image($root['head_image']);
        $root['thumb_head_image'] = get_spec_image($root['thumb_head_image']);
        //非百魅模式
        if (OPEN_BM == 1 && !$root['is_bm']) {
            $root['is_push'] = 1;
        }
        $root['open_daily_task'] = 0; //每日任务
        if (defined('OPEN_MISSION') && OPEN_MISSION == 1 && $m_config['mission_switch']) {
            require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
            Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
            if (sizeof(Model::build('mission')->getMissionList($user_id)) > 0) {
                $root['open_daily_task'] = 1;
            }
        }
        //-------------------------------------
        //sdk_type 0:使用腾讯SDK、1：使用金山SDK
        //映射关系类型  腾讯云直播, 金山云，星域，千秀云 ，阿里云
        //video_type     1          2        3      4       5
        //sdk_type       0          1        -      -       -
        $root['sdk_type'] = get_sdk_info(intval($m_config['video_type']));
        $p_user_mobile = $user_redis->getRow_db($root['user_id'], array('mobile'));
        $dev_type = strim($_REQUEST['sdk_type']);
        $sdk_version_name = strim($_REQUEST['sdk_version_name']);
        /*$is_refuse = 1;
        if($p_user_mobile['mobile']=='13999999999'&&($u_user_mobile['mobile']=='13888888888'||$u_user_mobile['mobile']=='13999999999')) $is_refuse = 0;
        if($p_user_mobile['mobile']=='13888888888'&&($u_user_mobile['mobile']=='13888888888'||$u_user_mobile['mobile']=='13999999999')) $is_refuse = 0;
        if($m_config['ios_check_version'] != ''&&$is_refuse){
        $root = array();
        $root['error'] = "直播间".$room_id."达到人数上限,无法进入！";
        $root['status'] = 0;
        ajax_return($root);
        }
        if ($root['pay_room_id'] === false){
        $root['pay_room_id'] = 0;
        }*/
        $filter_false = array('create_type' => 0, 'video_vid' => '', 'is_del_vod' => 0, 'is_delete' => 0, 'private_key' => '', 'group_id' => '', 'video_type' => 0, 'pai_id' => 0, 'max_watch_number' => 0, 'head_image' => '', 'thumb_head_image' => '', 'xpoint' => 0, 'ypoint' => 0, 'sort_num' => 0, 'play_flv' => '', 'play_rtmp' => '', 'play_hls' => '', 'play_mp4' => '', 'play_url' => '', 'push_rtmp' => '', 'cate_id' => 0, 'title' => '', 'is_live_pay' => 0, 'live_pay_type' => 0, 'live_pay_time' => '', 'live_fee' => 0, 'live_is_mention' => 0, 'pay_room_id' => 0);
        if (defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE == 1) {
            $filter_false['game_log_id'] = 0;
        }
        if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
            $filter_false['private_code'] = '';
            $filter_false['prop_table'] = '';
        }
        filter_false($root, $filter_false);
        //限制主播PC端发起直播，APP端重复登录
        $dev_type = strim($_REQUEST['sdk_type']);
        if ($root['user_id'] == $user_id && $root['create_type'] == 1 && $is_vod != 1 && ($dev_type == 'ios' || $dev_type == 'android')) {
            $root = array();
            $root['error'] = "PC端发起的直播，APP不能重复登录";
            $root['status'] = 0;
            ajax_return($root);
        }
        //vip
        if ($user_id != $root['user_id'] && (defined('OPEN_VIP') && OPEN_VIP == 1) && intval($m_config['open_vip']) == 1) {
            $sql = "select id,is_vip,vip_expire_time from " . DB_PREFIX . "user where id = " . $user_id;
            $user = $GLOBALS['db']->getRow($sql, true, true);
            $vip_expire_time = intval($user['vip_expire_time']);
            $is_vip = intval($user['is_vip']);
            if ($is_vip != 1 || ($is_vip == 1 && $vip_expire_time > 0 && $vip_expire_time < NOW_TIME)) {
                $root = array();
                $root['error'] = "非VIP会员或VIP会员已过期，请先购买。";
                $root['status'] = 0;
                $root['is_vip'] = 0;
                $root['play_url'] = '';
                $root['is_live_pay'] = 0;
                if ($require_type == 1) {
                    return $root;
                } else {
                    ajax_return($root);
                }
            }
        } else {
            $is_vip = 1;
        }
        $group_id = $root['group_id'];
        $p_user_id = $root['user_id'];
        $is_order = 0;
        if (defined('ORDER_ZC') && ORDER_ZC == 1) {
            $is_order = is_ordered($room_id, $user_id);
        }
        //付费直播
        if (intval(OPEN_LIVE_PAY) == 1 && !$is_order) {
            $result = get_pay_video_info($root);
            if ($result['status'] == 0 && $root['user_id'] != $user_id) {
                //去掉这个参数; 0腾讯云互动直播,1腾讯云直播  &&intval($m_config['video_type'])==1
                $root_result = array();
                $root_result = $result;
                $root_result['status'] = 1;
                if (intval($m_config['is_only_play_video'])) {
                    $root_result['is_only_play_voice'] = 0;
                } else {
                    $root_result['is_only_play_voice'] = 1;
                }
                $sign = strim($param['sign']); //防盗连接问题 sign = md5(【腾讯号 + 用户ID + 房间号】)
                $sign_version = strim($param['sign_version']); //1.0
                $tim_sdkappid = $m_config['tim_sdkappid'];
                //只有签名对，才返回：视频预览地址 防盗连接
                if (check_sign($sign, $sign_version, $m_config['tim_sdkappid'], $user_id, $room_id)) {
                    $key = "preview:video:{$room_id}:{$user_id}";
                    $preview_num = intval($GLOBALS['cache']->get($key, true));
                    $preview_num = $preview_num + 1;
                    $GLOBALS['cache']->set($key, $preview_num, 600, true);
                    $preview_play_url = '';
                    if ($preview_num > 2) {
                        $root_result['is_only_play_voice'] = 1; //超过2次预览后，只显示声音
                    }
                    //视频预览地址,后面添加较验
                    $preview_play_url = $root['play_url'];
                    //直播状态,默认播放flv格式;
                    if (($root['live_in'] == 1 || $root['live_in'] == 2) && $preview_play_url == '') {
                        $preview_play_url = $root['play_rtmp'] ?: ($root['play_flv'] ?: ($root['play_hls'] ?: $root['play_mp4']));
                    }
                    if ($root['live_in'] == 3 && $preview_play_url == '') {
                        $file_info = load_auto_cache('video_file', array(
                            'id' => $root['room_id'],
                            'video_type' => $root['video_type'],
                            'channelid' => $root['channelid'],
                            'begin_time' => $root['begin_time'],
                            'create_time' => $root['create_time']
                        ));
                        if ($file_info['play_url']) {
                            $preview_play_url = $file_info['play_url'];
                        }
                    }
                }
                if (intval($m_config['countdown']) == 0) {
                    $preview_play_url = '';
                }
                $root_result['id'] = $room_id;
                $root_result['room_id'] = $room_id;
                $root_result['group_id'] = $group_id;
                $root_result['user_id'] = $p_user_id;
                $root_result['countdown'] = intval($m_config['countdown']); //(秒) 付费直播间预览倒计时，默认为10，0为关闭倒计时预览
                $root_result['preview_play_url'] = $preview_play_url ? $preview_play_url : '';
                $root_result['online_status'] = $root['online_status'];
                if (($root['live_in'] == 3 || $root['live_in'] == 0) && $is_vod == 1) {
                    $root_result['has_video_control'] = 1; //点播时，视频控制操作
                }
                if ($require_type == 1) {
                    $root_result['status'] = 0;
                    return $root_result;
                } else {
                    ajax_return($root_result);
                }
            } else {
                $is_live_pay = intval($result['is_live_pay']);
                $live_pay_type = intval($result['live_pay_type']);
                $live_fee = intval($result['live_fee']);
                $is_pay_over = intval($result['is_pay_over']);
                if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
                    $is_live_pay = 1;
                }
            }
        }
        /* 非主播的 $is_live_pay 都要==0？修改 20170203
         * 写错了？$root['user_id']==$user_id ?
         * 主播 进入自己直播间 都不需要付费 $root['user_id']==$user_id ！！！
         * */
        /*if($root['user_id']==$user_id&&$is_live_pay==1){
        $is_live_pay = 0;
        }*/
        //直播间标题
        //非主播,不下发推流地址
        if ($root['user_id'] != $user_id) {
            $root['push_rtmp'] = '';
        }
        //直播状态,默认播放flv格式;
        if (($root['live_in'] == 1 || $root['live_in'] == 2) && $root['play_url'] == '') {
            $root['play_url'] = $root['play_rtmp'] ?: ($root['play_flv'] ?: ($root['play_hls'] ?: $root['play_mp4']));
        }
        if ($root['room_type'] == 1 && $root['user_id'] != $user_id) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
            $video_private_redis = new VideoPrivateRedisService();
            //
            if ($private_key != '' && $private_key != 'null') {
                if ($private_key == $root['private_key']) {
                    //检查用户是否被踢除,被踢除后，不能再加入;除非再次邀请
                    if ($video_private_redis->check_user_drop($room_id, $user_id)) {
                        $root = array();
                        $root['error'] = "您已经被踢出,不能再加入";
                        $root['status'] = 0;
                        if ($require_type == 1) {
                            return $root;
                        } else {
                            ajax_return($root);
                        }
                    } else {
                        //将用户加入私密直播,可重复操作
                        $video_private_redis->push_user($room_id, $user_id);
                    }
                } else {
                    $root = array();
                    $root['error'] = "无效的私密钥匙:" . $private_key;
                    $root['status'] = 0;
                    if ($require_type = 1) {
                        return $root;
                    } else {
                        ajax_return($root);
                    }
                }
            } else {
                //私聊,判断用户是否在被邀请的名单中,被踢除后，也不能重新加入
                if (OPEN_BM == 1) {
                    $is_bm = $root['is_bm'];
                } else {
                    $is_bm = false;
                }
                if ($video_private_redis->check_user_push($room_id, $user_id) == false && !$is_bm) {
                    $root = array();
                    $root['error'] = "私聊群,用户不在邀请名单中";
                    $root['status'] = 0;
                    if ($require_type == 1) {
                        return $root;
                    } else {
                        ajax_return($root);
                    }
                }
            }
            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                $private_code = intval($param['private_code']); //私密直播code
                if ($private_code != 0) {
                    if ($private_code == $root['private_code']) {
                        //检查用户是否被踢除,被踢除后，不能再加入;除非再次邀请
                        if ($video_private_redis->check_user_drop($room_id, $user_id)) {
                            $root = array();
                            $root['error'] = "您已经被踢出,不能再加入";
                            $root['status'] = 0;
                            if ($require_type == 1) {
                                return $root;
                            } else {
                                ajax_return($root);
                            }
                        } else {
                            //将用户加入私密直播,可重复操作
                            $video_private_redis->push_user($room_id, $user_id);
                        }
                    } else {
                        $root = array();
                        $root['error'] = "邀请验证码错误:" . $private_code;
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                } else {
                    //私聊,判断用户是否在被邀请的名单中,被踢除后，也不能重新加入
                    if ($video_private_redis->check_user_push($room_id, $user_id) == false) {
                        $root = array();
                        $root['error'] = "私聊群,用户不允许进入";
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }
            }
        }
        if ($root) {
//$has_scroll == 1 &&
            $param = array(
                'sex' => $sex,
                'city' => $city,
                'cate_id' => $cate_id,
                'is_vod' => $is_vod,
                'video_type' => $root['video_type'],
                'sort_num' => $root['sort_num']
            );
            $root['room_id'] = $room_id;
            //$video_list = get_rand_video($type,2,$user_id);
            $video_list = get_rand_video($room_id, $user_id, $type, $param);
            $video_previous = $video_list[0];
            if (!$video_previous) {
                $video_previous = $root;
            }
            $video_next = $video_list[1];
            if (!$video_next) {
                $video_next = $root;
            }
        }
    }
    if ($root['id']) {
        $root['init_version'] = intval($m_config['init_version']); //手机端配置版本号
        //获得当前用户资料
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_info = $user_redis->getRow_db($user_id, array('id', 'nick_name', 'v_icon', 'head_image', 'user_level', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type', 'default_mount'));
        //live_in 0:结束;1:正在直播;2:创建中;3:回看
        if (($root['live_in'] == 1 && $is_vod == 0) || ($user_id == $root['user_id'] && $is_vod == 0) || (($root['live_in'] == 3 || $root['live_in'] == 0) && $is_vod == 1)) {
            //'video_vid','is_del_vod','is_delete','video_type','play_url','group_id','room_type','private_key'
            $root['room_id'] = $room_id;
            $root['podcast'] = getuserinfo($user_id, $root['user_id'], $root['user_id']);
            $root['pk_time'] = 0;
            if ($root['in_livepk'] == 1) {
                $sql = "SELECT * FROM " . DB_PREFIX . "video_livepk WHERE video_id =" . $room_id . " AND user_id =" . $root['user_id'] . " ORDER BY id DESC LIMIT 1";
                $pkinfo = $GLOBALS['db']->getRow($sql, true, true);
                if (empty($pkinfo)) {
                    $root['in_livepk'] == 0;
                    $video_redis->update_db($room_id, array('in_livepk' => 0));
                } else {
                    $root['pk_time'] = $pkinfo['pk_time'];
                    $root['pk_theme'] = $pkinfo['pk_theme'];
                    $info = getuserinfo($user_id, $pkinfo['to_user_id'], $pkinfo['to_user_id']);
                    $_user = $root['podcast']['user'];
                    $_user['pk_ticket'] = (int) $video_redis->getOne_db($pkinfo['video_id'], 'pk_ticket');
                    $_to_user = $info['user'];
                    $_to_user['pk_ticket'] = (int) $video_redis->getOne_db($pkinfo['to_video_id'], 'pk_ticket');
                    $_to_user['room_id'] = (int) $pkinfo['to_video_id'];
                    $root['pk_info'] = array(
                        'user' => $_user,
                        'to_user' => $_to_user
                    );
                }
            }
            // $root['guard'] = array();
            $guard = user_guard_syn_redis($user_id, $root['user_id']);
            if (!empty($guard)) {
                $root['guard'] = $guard;
                $root['guard']['to_user_id'] = $root['user_id'];
                $root['guard']['sender'] = array(
                    'user_id' => $user_id,
                    'nick_name' => $user_info['nick_name'],
                    'head_image' => get_spec_image($user_info['head_image']),
                    'user_level' => $user_info['user_level'],
                    'v_icon' => $user_info['v_icon']
                );
                $root['guard']['level_info'] = load_auto_cache('guard_level', array('level' => $guard['level']));
                $root['guard']['guard_animated'] = load_auto_cache("guard_id", array('id' => $guard['guard_id']));
                // if (!empty($root['guard']['guard_animated']['content'])) {
                //     $root['guard']['guard_animated']['content'] = sprintf($root['guard']['guard_animated']['content'], $user_info['nick_name']);
                //     // $root['guard']['guard_animated']['top_title'] = sprintf($root['guard']['guard_animated']['content'], $user_info['nick_name']);
                // }
            } else {
                $mount = get_mount($user_id, $user_info['default_mount']);
                if (!empty($mount)) {
                    $root['mount'] = $mount;
                    // if (!empty($root['mount']['desc'])) {
                    //     $root['mount']['desc'] = sprintf($root['mount']['desc'], $user_info['nick_name']);
                    // }
                    $root['mount']['to_user_id'] = $root['user_id'];
                    $root['mount']['sender'] = array(
                        'user_id' => $user_id,
                        'nick_name' => $user_info['nick_name'],
                        'head_image' => get_spec_image($user_info['head_image']),
                        'user_level' => $user_info['user_level'],
                        'v_icon' => $user_info['v_icon']
                    );
                }
            }
            // fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GuardianRecordRedisService.php');
            // $guard = new GuardianRecordRedisService();
            // $root['guard_num'] = (int) $guard->get_guarded_len($root['user_id']);
            $root['guard_num'] = (int) count(load_auto_cache('guardian_record_list', array('anchor_id' => $root['user_id'])));
            $root['luck_num'] = $root['podcast']['user']['luck_num'];
            if (empty($root['room_title'])) {
                $root['room_title'] = $root['podcast']['user']['room_title'];
            }
            if ($video_previous) {
                $podcast_previous = array();
                $podcast_previous['room_id'] = $video_previous['room_id'];
                $video_previous['thumb_head_image'] = $video_previous['thumb_head_image'] != '' ? $video_previous['thumb_head_image'] : $video_previous['head_image'];
                $podcast_previous['head_image'] = get_spec_image($video_previous['thumb_head_image']);
                $root['podcast_previous'] = $podcast_previous;
            }
            //车行定制 ljz 身份等级颜色区分
            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                fanwe_require(APP_ROOT_PATH . "mapi/car/core/common_car.php");
                $res = video_status_effect($user_info, $root['user_id'], $root['tourist_astrict']);
                foreach ($res as $key => $val) {
                    $root['podcast'][$key] = $val;
                }
                //当前直播间的热度排名
                $root['heat_rank'] = get_user_rank(intval($root['user_id']));
                //存在红包 重新推送一次红包信息 //获取直播间，红包 发放记录,主要用于,直播结束后,处理还未被领取的红包
                $red_list = $video_redis->get_reds($room_id);
                $red_prop_id = end($red_list);
                $prop_table = $root['prop_table'];
                if ($prop_table != '' && $red_prop_id) {
                    $sql = "SELECT * FROM " . $prop_table . " WHERE id=" . $red_prop_id;
                    $red_prop_info = $GLOBALS['db']->getRow($sql, true, true);
                    //构造红包消息
                    $ext_prop = array();
                    $ext_prop['prop_id'] = intval($red_prop_info['prop_id']); //礼物id
                    $ext_prop['is_red_envelope'] = 1; //是否是：红包；1:红包
                    $ext_prop['user_prop_id'] = intval($red_prop_id); //红包时用到，抢红包的id
                    $ext_prop['to_user_id'] = $red_prop_info['to_user_id']; //礼物接收人（主播）
                    $ext_prop['from_user_id'] = $red_prop_info['from_user_id']; //礼物接收人（主播）
                    $create_time = intval(NOW_TIME - $red_prop_info['create_time']); //礼物发送时间
                    $ext_prop['create_time'] = $create_time < 40 ? 40 - $create_time : 0;
                    $sender = $user_redis->getRow_db($ext_prop['from_user_id'], array('id', 'nick_name', 'head_image'));
                    $sender['head_image'] = get_spec_image($sender['head_image'], 150, 150);
                    $ext_prop['sender'] = $sender; //发红包人
                    if ($ext_prop['create_time']) {
                        $root['red_envelope'] = $ext_prop;
                    }
                }
            }
            if ($video_next) {
                $podcast_next = array();
                $podcast_next['room_id'] = $video_next['room_id'];
                $video_next['thumb_head_image'] = $video_next['thumb_head_image'] != '' ? $video_next['thumb_head_image'] : $video_next['head_image'];
                $podcast_next['head_image'] = get_spec_image($video_next['thumb_head_image']);
                $root['podcast_next'] = $podcast_next;
            }
            $user_level = intval($user_info['user_level']);
            if ($user_level == 0) {
                $user_level = 1;
            }
            $sort_num = $user_level * $user_redis->gz_level_weight;
            if ($user_info['is_robot'] == 0) {
                $sort_num += $user_redis->gz_real_weight;
            }
            if ($user_info['is_authentication'] == 2) {
                $sort_num += $user_redis->gz_rz_weight;
            }
            //观众列表的排序权重
            $root['sort_num'] = $sort_num;
            //live_in 0:结束;1:正在直播;2:创建中;3:回看
            if (($root['live_in'] == 3 || $root['live_in'] == 0) && $is_vod == 1) {
                //录制地址不能为空,且录制文件没有被删除
                if ($root['is_del_vod'] == 0) {
                    $file_info = load_auto_cache('video_file', array(
                        'id' => $root['room_id'],
                        'video_type' => $root['video_type'],
                        'channelid' => $root['channelid'],
                        'begin_time' => $root['begin_time'],
                        'create_time' => $root['create_time']
                    ));
                    if ($file_info['play_url']) {
                        $root['play_url'] = $file_info['play_url'];
                    } else {
                        $root['play_url'] = get_spec_image($root['play_url']);
                    }
                    $root['urls'] = $file_info['urls'];
                }
                if ($root['user_id'] == $user_id) {
                    $root['is_live_pay'] = 0;
                }
                //$root['video_type'] = 1;
                //$root['play_url'] = 'rtmp://2811.liveplay.myqcloud.com/live/2811_b540dc105a3311e6a2cba4dcbef5e35a';
                //http://2811.liveplay.myqcloud.com/live/2811_b540dc105a3311e6a2cba4dcbef5e35a.flv
                $root['has_video_control'] = 1; //点播时，视频控制操作
                $root['has_lianmai'] = 0; //1:显示连麦按钮;0:不显示连麦按钮
            } else {
                if (($root['video_type'] == 1 && $m_config['qcloud_security_key'] == '') || $root['video_type'] == 2 || $root['video_type'] == 5) {
                    //非直播码方式，不让连麦
                    $root['has_lianmai'] = 0; //1:显示连麦按钮;0:不显示连麦按钮
                } else {
                    //会员允许等级
                    if ($m_config['has_lianmai_lv'] <= $user_level) {
                        $root['has_lianmai'] = 1; //1:显示连麦按钮;0:不显示连麦按钮
                    } else {
                        $root['has_lianmai'] = 0; //1:显示连麦按钮;0:不显示连麦按钮
                    }
                }
            }
            //$root['video_type'] = 0;//0:腾讯云互动直播;1:腾讯云直播
            //$root['play_url'] = '';//video_type=1;1:腾讯云直播播放地址(rmtp,flv)
            if ($user_id != $root['user_id']) {
                //非主播，不返回：推流地址
                $root['push_url'] = ''; //video_type=1;1:腾讯云直播推流地址
            }
            //print_r($root['podcast']);exit;
            //秀票贡献榜 http://ilvb.fanwe.net/index.php?ctl=user&act=contribution&user_id=1
            //$root['cont_url'] = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=user&act=contribution&user_id='.$root['user_id'];
            $share = array();
            $share['share_title'] = strim($m_config['share_title']); //'你丑你先睡,我美我直播!';
            $share['share_imageUrl'] = $root['podcast']['user']['head_image'];
            $share['share_key'] = $root['room_id'];
            $share['share_url'] = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=share&act=live&user_id=' . $root['user_id'] . '&video_id=' . $root['room_id'] . '&share_id=' . $user_id;
            if (intval(OPEN_CAR_MODULE)) {
                if ($root['user_id'] == 168037) {
                    $share['share_url'] = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=share&act=live&user_id=' . $root['user_id'] . '&video_user=1&itype=car';
                }
            }
            $share['share_content'] = $share['share_title'] . $root['podcast']['user']['nick_name'] . '正在直播,快来一起看~';
            $root['share'] = $share;
            $root['status'] = 1;
            //$sql = "select count(*) from ".DB_PREFIX."video_viewer where group_id = '".$root['group_id']."'";
            //$sql = "select count(*) from ".DB_PREFIX."user";
            //当前房间人数 = 当前实时观看人数（实际,不含虚拟人数,不包含机器人) + 当前虚拟观看人数 + 机器人
            //$root['viewer_num'] = $root['watch_number'] + $root['virtual_watch_number'] + $root['robot_num'];
            //观众列表
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
            $video_viewer_redis = new VideoViewerRedisService();
            $viewer = $video_viewer_redis->get_viewer_list2($room_id, 1, 10);
            $root['viewer'] = $viewer;
            $root['viewer_num'] = $viewer['watch_number'];
            //判断是否开启车行定制
            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                //直播间最大人数限制
                $allow_max_number = $GLOBALS['db']->getOne("select allow_max_number from " . DB_PREFIX . "user where id=" . $root['user_id']);
                //直播间真实人数
                $watch_number = $GLOBALS['db']->getOne("select watch_number from " . DB_PREFIX . "video where id =" . $root['room_id']);
                if (intval($allow_max_number) > 0 && $watch_number >= $allow_max_number && $root['user_id'] != $user_id) {
                    $root = array();
                    $root['error'] = "该直播间人数到达上限，无法加入！";
                    $root['status'] = 0;
                    return $root;
                }
            }
            //$root['viewer_num'] =  $video_redis->get_video_watch_num($room_id);
            if ($root['room_type'] == 1) {
                $root['is_private'] = 1;
                if ($root['is_bm'] == 1) {
                    $root['is_private'] = 0;
                }
                /*
                $sql = "select user_id from ".DB_PREFIX."user_admin where podcast_id = ".$root['user_id'];
                $user_admin_list = $GLOBALS['db']->getAll($sql,true,true);
                $user_admin_list = array_column($user_admin_list,'user_id');
                //主播自己或管理员  // || $root['podcast']['show_admin'] == 2
                if (in_array($user_id,$user_admin_list) || ($user_id == $root['user_id'])){
                $private_share = "复制整段信息，打开［".strim($m_config['app_name'])."］可直接看直播,".$root['podcast']['user']['nick_name']."正在".$root['city']."直播";
                $private_share = $private_share.base64_decode("8J+UkQ==").$root['private_key'].base64_decode("8J+UkQ==")."还没安装".strim($m_config['app_name'])."？点此安装，".SITE_DOMAIN."/appdown.php";
                $root['private_share'] = $private_share;//print_r($root['podcast'],1);
                }
                 */
                $sql = "select id from " . DB_PREFIX . "user_admin where podcast_id = " . $root['user_id'] . " and user_id=" . $user_id;
                $user_admin_id = intval($GLOBALS['db']->getOne($sql, true, true));
                //主播自己或管理员  // || $root['podcast']['show_admin'] == 2
                if ($user_admin_id > 0 || ($user_id == $root['user_id'])) {
                    $private_share = "复制整段信息，打开［" . strim($m_config['app_name']) . "］可直接看直播," . $root['podcast']['user']['nick_name'] . "正在" . $root['city'] . "直播";
                    $private_share = $private_share . base64_decode("8J+UkQ==") . $root['private_key'] . base64_decode("8J+UkQ==") . "还没安装" . strim($m_config['app_name']) . "？点此安装，" . SITE_DOMAIN_DOWNLOAD . "/appdown.php";
                    $root['private_share'] = $private_share; //print_r($root['podcast'],1);
                }
                if ($root['is_bm'] == 1) {
                } else {
                    $root['private_key'] = '';
                }
            } else {
                $root['is_private'] = 0;
            }
            //进入房间提示 1、提示进入房间  0、 不提示
            $join_room_remind_limit = intval($m_config['join_room_remind_limit']);
            $root['join_room_prompt'] = 1;
            if ($join_room_remind_limit > 0 && $root['viewer_num'] > $join_room_remind_limit) {
                $root['join_room_prompt'] = 0; //
            }
            $root['is_live_pay'] = intval($is_live_pay); //
            $root['live_pay_type'] = intval($live_pay_type); //
            $root['live_fee'] = intval($live_fee); //
            $root['is_pay_over'] = intval($is_pay_over); //
            $root['is_vip'] = intval($is_vip); //是否VIP会员
            //进入房间人数上限  join_room_limit
            $join_room_limit = intval($m_config['join_room_limit']);
            if ($join_room_limit > 0 && $root['viewer_num'] >= $join_room_limit && $root['user_id'] != $user_id) {
                $root = array();
                $root['error'] = "直播间达到人数上限,无法进入！";
                $root['status'] = 0;
            }
            $root['video_title'] = '直播Live';
            if ($root['live_in'] == 3 && intval($root['is_gather']) != 1) {
                $root['video_title'] = '精彩回放';
            } else if ($root['live_in'] == 3 && intval($root['is_gather']) == 1) {
                $root['video_title'] = '直播Live';
            }
        } else {
            if ($is_vod == 0) {
                $show_num = $root['max_watch_number']; //观看人数
                $podcast_id = $root['user_id'];
                $live_in = $root['live_in'];
                $root = array();
                //关注
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                $userfollw_redis = new UserFollwRedisService($user_id);
                if ($userfollw_redis->is_following($podcast_id)) {
                    $root['has_focus'] = 1; //0:未关注;1:已关注
                } else {
                    $root['has_focus'] = 0;
                }
                $root['live_in'] = $live_in;
                $root['show_num'] = $show_num;
                $root['status'] = 2; //提示直播结束;
                //=========================================================
                //广播：直播结束
                //发送广播：直播结束
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ext = array();
                $ext['type'] = 18; //18：直播结束（全体推送的，用于更新用户列表状态）
                $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                //18：直播结束（全体推送的，用于更新用户列表状态）
                $api->group_send_group_system_notification($m_config['on_line_group_id'], json_encode($ext), null);
                //=========================================================
                //$video_redis->update_db($room_id,array('live_in'=>1));
                //较验数据
                crontab_do_end_video();
                crontab_do_end_video_2();
                //
            } else {
                $root = array();
                $root['error'] = "直播已结束" . $room_id;
                $root['status'] = 0;
            }
        }
    } else {
        $root = array();
        $root['error'] = "未发现有效的直播房间" . $room_id;
        $root['status'] = 0;
    }
    return $root;
}
//检查主播是否直播中
function is_live($data, $live_list)
{
    foreach ($data as $k => $v) {
        foreach ($live_list as $kk => $vv) {
            if ($vv['user_id'] == $v['user_id']) {
                $data[$k]['live_in'] = $vv['live_in'];
                $data[$k]['video_url'] = get_video_url($vv['room_id'], $vv['live_in']);
                break;
            }
        }
        if (empty($data[$k]['video_url'])) {
            $data[$k]['video_url'] = url('live#show', array('podcast_id' => $v['user_id']));
        }
        $data[$k]['user_level_ico'] = get_spec_image("./public/images/rank/rank_" . $v['user_level'] . ".png");
        $data[$k]['nick_name'] = ($v['nick_name']);
    }
    return $data;
}
//获取直播列表
function get_live()
{
    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type, (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.head_image,v.thumb_head_image, v.xpoint,v.ypoint,v.live_image, u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) order by v.live_in, v.sort_num desc,v.sort desc";
    $live_list = $GLOBALS['db']->getAll($sql, true, true);
    return $live_list;
}
/**
 * //随机从type类型的直播列表中，取$num个直播ID; type: 0:热门;1:最新;2:关注
 * 不会列出私聊房间
 * @param int $type
 * @param int $num
 * @param string $user_id 当$type=2时生效;
 */
/*function get_rand_video($type,$num,$user_id){
if ($type == 1){
//1:最新 半小时内
$sql = "select id as room_id,user_id,group_id,live_in,watch_number,robot_num,virtual_watch_number,room_type from ".DB_PREFIX."video WHERE room_type = 3 and live_in = 1 and is_new = 1 and begin_time > ".(NOW_TIME - 1800)." order by rand() LIMIT ".$num;
}else if ($type == 2){
//2:关注
$sql = "select v.id as room_id,v.user_id,v.group_id,v.live_in,v.watch_number,v.robot_num,v.virtual_watch_number,v.room_type from ".DB_PREFIX."video v "
."LEFT JOIN ".DB_PREFIX."focus f on f.podcast_id = v.user_id "
."WHERE v.room_type = 3 and v.live_in = 1 and f.user_id = '".$user_id."' order by rand() LIMIT ".$num;
}else{
//热门
$sql = "select id as room_id,user_id,group_id,live_in,watch_number,robot_num,virtual_watch_number,room_type from ".DB_PREFIX."video WHERE room_type = 3 and live_in = 1 and is_hot = 1 order by rand() LIMIT ".$num;
}
//随机获得2个（一个当前使用的，一个上下滚动时备用);
$video_list = $GLOBALS['db']->getAll($sql);
return $video_list;
}*/
function get_rand_video($room_id, $user_id, $type, $param, $num = 2)
{
    /*
    $type = intval($_REQUEST['type']);//type: 0:热门;1:最新;2:关注 [当room_id=0时有效，随机返回一个type类型下的直播]
    $param = array(
    'sex'=>$sex,
    'city'=>$city,
    'cate_id'=>$cate_id,
    'type'=>$type,
    'room_id'=>$room_id,
    'is_vod'=>$is_vod,
    'video_type'=>$root['video_type'],
    'sort_num'=>$root['sort_num']
    );
     */
    $ret_list = array();
    $is_vod = intval($param['is_vod']);
    if ($is_vod == 0) {
        if ($type == 2) {
            //关注
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $userfollw_redis = new UserFollwRedisService($user_id);
            $user_list = $userfollw_redis->following();
            //私密直播  video_private,私密直播结束后， 本表会清空
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
            $video_private_redis = new VideoPrivateRedisService();
            $private_list = $video_private_redis->get_video_list($user_id);
            if (sizeof($private_list) || sizeof($user_list)) {
                $list_all = load_auto_cache("select_video", array('has_private' => 1));
                foreach ($list_all as $k => $v) {
                    if (($v['live_in'] == 1) && ($v['room_id'] != $room_id) && (($v['room_type'] == 1 && in_array($v['room_id'], $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'], $user_list)))) {
                        $list[] = $v;
                    }
                }
            }
        } else {
            $list_all = load_auto_cache("select_video", array('sex_type' => intval($param['sex']), 'area_type' => $param['city'], 'cate_id' => intval($param['cate_id']), 'has_private' => 0));
            foreach ($list_all as $k => $v) {
                if (($v['live_in'] == 1) && ($v['room_id'] != $room_id) && $v['user_id'] != $user_id) {
                    $list[] = $v;
                }
            }
        }
        if (count($list) <= $num) {
            $ret_list = $list;
        } else {
            foreach (array_rand($list, $num) as $key) {
                $ret_list[] = $list[$key];
            }
        }
    }
    return $ret_list;
}
/*
function get_rand_video($room_id,$num,$param){
$list = array();
$type = $param['type'];
$sql = "SELECT v.id, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type,
(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number
FROM ".DB_PREFIX."video v where v.live_in=1 and v.id<>".$room_id;
if ($param['cate_name']) {
$cate_name = $param['cate_name'];
$sql .= " and v.room_type=3 and v.title='" . $cate_name . "' ";
$sql.= " order by rand() LIMIT ".$num;
$list = $GLOBALS['db']->getAll($sql);
} elseif ($type == 0) {
$sex = intval($param['sex']);
$city = strim($param['city']);
if($city=='热门' || $city=='null'){
$city = '';
}
if ($sex == 1 || $sex == 2){
$sql .= ' and v.sex = '.$sex;
}
if ($city != ''){
$sql .= " and v.province = '".$city."'";
}
$sql.=" and v.room_type=3 ";
$sql.= " order by rand() LIMIT ".$num;
$list = $GLOBALS['db']->getAll($sql);
}elseif ($type == 1) {
$sql .= "  and v.room_type=3 ";
$sql.= " order by rand() LIMIT ".$num;
$list = $GLOBALS['db']->getAll($sql);
} elseif ($type == 2) {
$user_id = intval($GLOBALS['user_info']['id']);//登录用户id
fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
$userfollw_redis = new UserFollwRedisService($user_id);
$user_list = $userfollw_redis->following();
//私密直播  video_private,私密直播结束后， 本表会清空
fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoPrivateRedisService.php');
$video_private_redis = new VideoPrivateRedisService();
$private_list = $video_private_redis->get_video_list($user_id);
if(sizeof($private_list) || sizeof($user_list)){
$sql.="  and v.room_type in (1,3)   order by rand() ";
$list_all = $GLOBALS['db']->getAll($sql);
$count = 0;
foreach($list_all as $k=>$v){
if($count==$num){
break;
}
if (($v['room_type'] == 1 && in_array($v['id'], $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'], $user_list))) {
$list[] = $v;
$count ++;
}
}
}
}
return $list;
}
 */
//格式化时间单位（多少时间前）
function format_show_date($time)
{
    $t = NOW_TIME - $time;
    $f = array(
        '31536000' => '年',
        '2592000' => '个月',
        '604800' => '星期',
        '86400' => '天',
        '3600' => '小时',
        '60' => '分钟',
        '1' => '秒'
    );
    foreach ($f as $k => $v) {
        if (0 != $c = floor($t / (int) $k)) {
            return $c . $v . '前';
        }
    }
}
//获得回放视频的时长（多少小时/多少分钟）
function get_time_len($begin_time, $end_time)
{
    $time = $end_time - $begin_time;
    $palyback = '';
    if ($time / 3600 > 1) {
        $palyback .= intval($time / 3600) . '小时';
        $time = $time % 3600;
    }
    if ($time / 60 > 1) {
        $palyback .= intval($time / 60) . '分钟';
    }
    return $palyback;
}
//格式化时间长度单位 （小时/分钟）
function get_live_time_len($time)
{
    $total_time_format = '';
    if ($time / 3600 >= 1) {
        $total_time_format .= intval($time / 3600) . '小时';
        $time = $time % 3600;
    }
    if ($time / 60 >= 1) {
        $total_time_format .= intval($time / 60) . '分钟';
    }
    return $total_time_format;
}
//格式化时间长度单位（天/小时/分钟）2017-8-9 10:40:18新增
function get_live_time_len2($time)
{
    $total_time_format = '';
    if ($time / 86400 >= 1) {
        $total_time_format .= intval($time / 86400) . '天';
        $time = $time % 86400;
    }
    if ($time / 3600 >= 1) {
        $total_time_format .= intval($time / 3600) . '小时';
        $time = $time % 3600;
    }
    if ($time / 60 >= 1) {
        $total_time_format .= intval($time / 60) . '分钟';
    }
    return $total_time_format;
}
function crontab_do_check_pc_video()
{
    $ret_array = array();
    $m_config = load_auto_cache("m_config");
    $monitor_overtime = $m_config['monitor_overtime'];
    if ($monitor_overtime <= 0) {
        $monitor_overtime = 40;
    }
    $ret_array = array();
    //监听主播掉线（30秒一次监听，如果超过180秒未收到心跳消息，则说明：主播掉线 了
    $t = to_date(NOW_TIME - $monitor_overtime, 'Y-m-d H:i:s');
    $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,video_type,begin_time,end_time,channelid,video_vid,live_in,cate_id,pai_id  from " . DB_PREFIX . "video where live_in = 2 and create_type = 1 and monitor_time > '" . $t . "'";
    $list = $GLOBALS['db']->getAll($sql, true, true);
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    foreach ($list as $v) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_factory = new VideoFactory();
        if ($v['channelid']) {
            $channel_info = $video_factory->Query($v['channelid']);
            $ret_array[] = $channel_info;
            // 正在直播，继续监听
            if ($channel_info['status'] == 1) {
                $data = array('live_in' => 1, 'begin_time' => NOW_TIME);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'UPDATE', " id=" . $v['id']);
                if ($GLOBALS['db']->affected_rows()) {
                    $room_id = $v['id'];
                    $sql = "select user_id,room_type,title,city,cate_id from " . DB_PREFIX . "video where id = " . $room_id;
                    $video = $GLOBALS['db']->getRow($sql);
                    $video_redis->video_online($room_id, $v['group_id']);
                    //将mysql数据,同步一份到redis中
                    sync_video_to_redis($room_id, '*', false);
                    if ($video['cate_id'] > 0) {
                        $sql = "update " . DB_PREFIX . "video_cate a set a.num = (select count(*) from " . DB_PREFIX . "video b where b.cate_id = a.id and b.live_in in (1,3)) where a.id = " . $video['cate_id'];
                        $GLOBALS['db']->query($sql);
                    }
                    if ($video['room_type'] == 3) {
                        crontab_robot($room_id);
                    }
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_data = $user_redis->getRow_db($v['user_id'], array('id', 'nick_name', 'head_image'));
                    $pushdata = array(
                        'user_id' => $v['user_id'], //'主播ID',
                        'nick_name' => $user_data['nick_name'], //'主播昵称',
                        'create_time' => NOW_TIME, //'创建时间',
                        'cate_title' => $video['title'], // '直播主题',
                        'room_id' => $room_id, // '房间ID',
                        'city' => $video['city'], // '直播城市地址',
                        'head_image' => get_spec_image($user_data['head_image']),
                        'status' => 0 //'推送状态(0:未推送，1：推送中；2：已推送）'
                    );
                    $m_config = load_auto_cache("m_config");
                    if (intval($m_config['service_push'])) {
                        $pushdata['pust_type'] = 1; //'推送状态(0:粉丝推送，1：全服推送）';
                    } else {
                        $pushdata['pust_type'] = 0; //'推送状态(0:粉丝推送，1：全服推送）';
                    }
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "push_anchor", $pushdata, 'INSERT');
                }
            }
        }
    }
    return $ret_array;
}
function crontab_do_check_upload_video($create_type = 1)
{
    $create_type = intval($create_type) ? intval($create_type) : 0;
    $ret_array = array();
    $sql = "select id,video_vid,live_in,group_id from " . DB_PREFIX . "video_history where live_in = 2 and create_type = " . $create_type;
    $list = $GLOBALS['db']->getAll($sql, true, true);
    if ($list) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_redis = new VideoRedisService();
        $video_factory = new VideoFactory();
        foreach ($list as $v) {
            // 用户上传的视频轮询取得播放地址
            $ret = $video_factory->DescribeVodPlayUrls($v['video_vid']);
            if (empty($ret['urls'])) {
                continue;
            }
            $play_url = array_shift($ret['urls']);
            $data = array('live_in' => 0, 'is_del_vod' => 0, 'play_url' => $play_url, 'end_time' => NOW_TIME);
            $GLOBALS['db']->autoExecute(DB_PREFIX . "video_history", $data, 'UPDATE', " id=" . $v['id']);
            $video_redis->update_db($v['id'], $data);
            $video_redis->video_online($v['id'], $v['group_id']);
            $ret_array[] = $data;
        }
    }
    return $ret_array;
}
function crontab_do_end_video()
{
    try {
        $m_config = load_auto_cache("m_config");
        $monitor_overtime = $m_config['monitor_overtime'];
        if ($monitor_overtime <= 0) {
            $monitor_overtime = 40;
        }
        $ret_array = array();
        //监听主播掉线（30秒一次监听，如果超过180秒未收到心跳消息，则说明：主播掉线 了
        $t = to_date(NOW_TIME - $monitor_overtime, 'Y-m-d H:i:s');
        $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,video_type,begin_time,end_time,channelid,video_vid,live_in,cate_id,pai_id from " . DB_PREFIX . "video where (live_in = 1 or live_in = 2) and monitor_time < '" . $t . "'";
        if (OPEN_BM == 1) {
            $t1 = to_date(NOW_TIME - 43200, 'Y-m-d H:i:s');
            $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,video_type,begin_time,end_time,channelid,video_vid,live_in,cate_id,pai_id from " . DB_PREFIX . "video where (live_in = 1 or live_in = 2) and (is_bm=0 and  monitor_time < '" . $t . "') or (is_bm=1 and  monitor_time < '" . $t1 . "') ";
        }
        $list = $GLOBALS['db']->getAll($sql, true, true);
        foreach ($list as $k => &$v) {
            if ($v['video_type'] > 0) {
                //0:腾讯云互动直播;1:腾讯云直播;2:千秀云直播
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
                $video_factory = new VideoFactory();
                $channel_info = $video_factory->Query($v['channelid']);
                // 正在直播，继续监听
                if ($channel_info['status'] == 1) {
                    continue;
                }
            }
            //子房间过滤
            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                $parent_id = $GLOBALS['db']->getOne("SELECT parent_id FROM " . DB_PREFIX . "child_room WHERE child_id = {$v['id']} and is_delete = 0");
                if ($parent_id) {
                    if ($GLOBALS['db']->getOne("select live_in from fanwe_video where id = " . $parent_id)) {
                        continue;
                    }
                }
            }
            //结束直播
            if ($v['live_in'] == 1) {
                $cate_id = $v['cate_id'];
            } else {
                $cate_id = 0;
            }
            $system_user_id = $m_config['tim_identifier']; //系统消息
            $ext = array();
            $ext['type'] = 17;
            $ext['desc'] = '网络不佳，已结束直播';
            $ext['room_id'] = $v['id'];
            #构造高级接口所需参数
            $msg_content = array();
            //创建array 所需元素
            $msg_content_elem = array(
                'MsgType' => 'TIMCustomElem', //自定义类型
                'MsgContent' => array(
                    'Data' => json_encode($ext),
                    'Desc' => ''
                    //  'Ext' => $ext,
                    //  'Sound' => '',
                )
            );
            //将创建的元素$msg_content_elem, 加入array $msg_content
            array_push($msg_content, $msg_content_elem);
            fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
            $api = createTimAPI();
            $group_info = $api->group_get_group_member_info($v['group_id'], 0, 0);
            if ($group_info['MemberList']) {
                $to = array_column($group_info['MemberList'], "Member_Account");
            }
            $exceed = 500; //一次最多发送500条
            if (sizeof($to) > $exceed) {
                $num = ceil(sizeof($to) / $exceed);
                for ($i = 0; $i < $num; $i++) {
                    $to_account = array_slice($to, $i * $exceed, $exceed);
                    $api->openim_batchsendmsg($system_user_id, $msg_content, $to_account);
                }
            } else {
                $api->openim_batchsendmsg($system_user_id, $msg_content, $to);
            }
            $ret = do_end_video($v, $v['video_vid'], 1, $cate_id);
            $ret['func'] = 'do_end_video';
            $ret_array[] = $ret;
        }
        /*
        fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $list = $video_redis->get_off_video_monitor_time($monitor_overtime);
        foreach ( $list as $k => $v )
        {
        $video_data = $video_redis->getRow_db($v);
        $ret_array[]=redis_do_end_video($video_redis,$video_data,$video_data['video_vid'],1,$video_data['cate_id']);
        }
         */
        return $ret_array;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
function crontab_do_end_video_2()
{
    try {
        $ret_array = array();
        // 结束直播5分钟后，
        $sql = "select id,is_del_vod,video_type,channelid,begin_time,create_time,end_time,user_id,vote_number,destroy_group_status,group_id from " . DB_PREFIX . "video where end_time < " . (NOW_TIME - 300) . " and live_in = 0 limit 10";
        $list = $GLOBALS['db']->getAll($sql);
        if ($list) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
            $api = createTimAPI();
            foreach ($list as $k => $v) {
                //1:表示已经清空了,录制视频;0:未做清空操作
                if ($v['is_del_vod'] == 1) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
                    $video_factory = new VideoFactory();
                    /*if($v['video_type'] > 0 && $v['channelid']&& strpos($v['channelid'],'_')){
                    $ret = $video_factory->GetVodRecordFiles($v['channelid'], $v['create_time']);
                    } else {
                    $fileName = $v['id'];
                    if($v['video_type'] == 1){
                    $fileName = 'live'.$v['id'] ;
                    }
                    $ret = $video_factory->DescribeVodPlayInfo($fileName);
                    }*/
                    $ret = get_vodset_by_video_id($v['id']);
                    /*if ($ret['totalCount'] > 0){*/
                    if ($ret['total_count'] > 0) {
                        //视频存在
                        $sql = "update " . DB_PREFIX . "video set is_del_vod = 0 where id = " . $v['id'];
                        $GLOBALS['db']->query($sql);
                        $v['is_del_vod'] = 0;
                        $data = array();
                        $data['is_del_vod'] = 0;
                        $video_redis->update_db($v['id'], $data);
                    }
                }
                //直播结束 后相关数据处理（在后台定时执行）
                //1、解散群组
                if ($v['destroy_group_status'] == 1 && $v['is_del_vod'] == 1) {
                    if ($v['group_id'] != '') {
                        fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                        $api = createTimAPI();
                        $ret = $api->group_destroy_group($v['group_id']);
                        //删除：观众列表
                        $video_redis->del_viewer($v['id']);
                        $sql = "update " . DB_PREFIX . "video set destroy_group_status = '" . $ret['ErrorCode'] . "' where id = " . $v['id'];
                        $GLOBALS['db']->query($sql);
                    } else {
                        $sql = "update " . DB_PREFIX . "video set destroy_group_status = 0 where id = " . $v['id'];
                        $GLOBALS['db']->query($sql);
                    }
                }
                //2、结束 旁边直播
                /*
                 *旁边直播,在客户端做结束操作；如果客户端异常退出而没有结束时，腾讯云在10分钟后也会自动结束并清空
                if ($v['channelid']){
                //停止直播
                fanwe_require(APP_ROOT_PATH.'system/QcloudApi/QcloudApi.php');
                $m_config =  load_auto_cache("m_config");
                $config = array('SecretId'       => $m_config['qcloud_secret_id'],
                'SecretKey'      => $m_config['qcloud_secret_key'],
                'RequestMethod'  => 'GET',
                'DefaultRegion'  => 'gz');
                $service = QcloudApi::load(QcloudApi::MODULE_LIVE, $config);
                $package = array('channelIds.1' => $v['channelid']);
                $a = $service->StopLVBChannel($package);
                $stop_channel = 0;
                if ($a === false) {
                $error = $service->getError();
                $stop_channel = $error->getCode();
                }else{
                $stop_channel = 1;
                }
                $sql = "update ".DB_PREFIX."video set stop_channel = '".$stop_channel."' where id = ".$v['id'];
                $GLOBALS['db']->query($sql);
                }
                 */
                $ret = do_end_video_2($video_redis, $api, $v['id']);
                $ret['func'] = 'do_end_video_2';
                $ret_array[] = $ret;
            }
        }
        return $ret_array;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
/**
 * 添加机器人,每隔几秒随机添加几个人
 */
function crontab_robot($video_id = 0)
{
    if ($video_id == 0) {
        $sql = "SELECT id,group_id,robot_num,max_robot_num FROM " . DB_PREFIX . "video where robot_num < max_robot_num and live_in in (1,3) and room_type = 3 and robot_time < " . (NOW_TIME - rand(20, 30));
    } else {
        $sql = "SELECT id,group_id,robot_num,max_robot_num FROM " . DB_PREFIX . "video where robot_num < max_robot_num and live_in in (1,3) and room_type = 3 and id = " . $video_id;
    }
    $list = $GLOBALS['db']->getAll($sql, true, true);
    if (count($list) > 0) {
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $max_num = $m_config['robot_num'];
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $video_viewer = new VideoViewerRedisService();
    }
    foreach ($list as $k => $v) {
        //添加机器人
        $robot_num = $v['robot_num'];
        $max_robot_num = $v['max_robot_num'] ? $v['max_robot_num'] : $max_num;
        $video_id = $v['id'];
        if ($robot_num < $max_robot_num) {
            $rand_num = rand(6, 10);
            $robot_keys = $video_viewer->redis->srandmember($video_viewer->user_robot_db, $rand_num);
            // $user_array = array();
            // foreach ($robot_keys as $key => $user_id) {
            // $user_array[$user_id]['user_level'] = $video_viewer->redis->hGet($video_viewer->user_db . $user_id, 'user_level');
            // }
            //新入群成员列表
            foreach ($robot_keys as $k => $user_id) {
                if ($video_viewer->redis->hGet($video_viewer->user_db . $user_id, 'is_admin') == 1) {
                    continue;
                }
                $begin_time = get_gmtime();
                if ($video_viewer->redis->zScore($video_viewer->video_viewer_level_db . $video_id, $user_id) === false) {
                    //实际观众数统计：累计观众列表和; [score 为负数是：机器人; 正数是：真实观众] $user_array[$user_id]['user_level']
                    $video_viewer->redis->zAdd($video_viewer->video_viewer_level_db . $video_id, -1, $user_id);
                    $video_viewer->redis->hIncrBy($video_viewer->video_db . $video_id, 'robot_num', 1);
                    $video_viewer->redis->hIncrBy($video_viewer->video_db . $video_id, 'max_watch_number', 1);
                    //记录直播间的机器人头像 去除：直播间机器人独立列表 score 为负数是：机器人
                    //$video_redis->add_robot($video_id, $user_id);
                }
            }
            $video = $video_redis->getRow_db($video_id, array('robot_num'));
            $robot_num = intval($video['robot_num']);
            if ($robot_num > 0) {
                $sql = "update " . DB_PREFIX . "video set robot_num =  " . $robot_num . ",robot_time=" . NOW_TIME . " where id = " . $video_id;
                $GLOBALS['db']->query($sql);
            }
        }
    }
}
//获取一个新的房间号,同时记录分配给那个系统使用;
function get_max_room_id($sysid = 0)
{
    $sql = "insert into " . DB_PREFIX . "room_id (id,sysid) values(0,$sysid)";
    $GLOBALS['db']->query($sql);
    $room_id = $GLOBALS['db']->insert_id();
    return $room_id;
}
/**
 * IP地址查询接口(API)
 */
function get_ip_info()
{
    //http://www.hi-docs.com/Article/detail-Mzk=.html
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    //$url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.CLIENT_IP;
    //$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.CLIENT_IP;
    $url = 'http://ip.ws.126.net/ipquery?ip=' . CLIENT_IP;
    $req = $trans->request($url, array(), 'GET');
    //$req = json_decode($req['body'],true);
    $req['body'] = mb_convert_encoding($req['body'], 'utf-8', 'gbk');
    $b = substr($req['body'], strpos($req['body'], "{"), strpos($req['body'], "}"));
    $req['city'] = substr($b, strpos($b, "{city:") + 7, strpos($b, "\",") - 7);
    $req['city'] = str_replace("市", "", $req['city']);
    $req['province'] = substr($b, strpos($b, "province:") + 10, -4);
    $req['province'] = str_replace("省", "", $req['province']);
    /*
    {
    ret: 1,
    start: -1,
    end: -1,
    country: "美国",
    province: "加利福尼亚",
    city: "Zenia",
    district: "",
    isp: "",
    type: "",
    desc: ""
    }*/
    $info = array();
    $info['ip'] = CLIENT_IP;
    $info['country'] = $req['country'];
    $info['province'] = $req['province'];
    $info['city'] = $req['city'];
    $info['district'] = $req['district'];
    return $info;
}
function get_ip_infos()
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    $url = "https://dm-81.data.aliyun.com/rest/160601/ip/getIpInfo.json?ip=" . CLIENT_IP;
    $date = array();
    $date['AppKey'] = '23489658';
    $date['AppKeyAppSecret'] = '71d175d90329b580f989268f957f152a';
    $req = $trans->request($url, $date, 'GET');
    return $req;
    /*$req = json_decode($req['body'],true);
$info = array();
$info['ip'] = CLIENT_IP;
$info['country'] = $req['country'];
$info['province'] = $req['province'];
$info['city'] = $req['city'];
$info['district'] = $req['district'];
return $info;*/
}
/**
 * 距离计算(API)
 */
function get_distance_order($from, $to)
{
    // http://lbs.qq.com/webservice_v1/guide-distance.html
    $m_config = load_auto_cache("m_config");
    //  test
    if (!isset($m_config["qcloud_app_key"])) {
        $m_config["qcloud_app_key"] = "UNDBZ-KYNLX-LDS4K-ZBARP-VT37E-I2FCL";
    }
    $url = 'http://apis.map.qq.com/ws/distance/v1/?' . http_build_query(array(
        "mode" => "walking",
        "from" => $from,
        "to" => implode(';', $to),
        "output" => "json",
        "key" => $m_config["qcloud_app_key"]
    ));
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    $response = $trans->request($url, array(), 'GET');
    $data = json_decode($response['body'], true);
    if ($data['status'] != 0) {
        return array();
    }
    return $data['result']['elements'];
}
/**
 * 格式化时间
 */
function date_time_format(&$date_time)
{
    $time = strtotime($date_time);
    $ri = date('Y年m月d日 ', $time);
    $zhou = date('N', $time);
    if ($zhou == 1) {
        $zhou = " 周一 ";
    } elseif ($zhou == 2) {
        $zhou = " 周二 ";
    }if ($zhou == 3) {
        $zhou = " 周三 ";
    }if ($zhou == 4) {
        $zhou = " 周四 ";
    }if ($zhou == 5) {
        $zhou = " 周五 ";
    }if ($zhou == 6) {
        $zhou = " 周六 ";
    }if ($zhou == 7) {
        $zhou = " 周日 ";
    }
    $fen = date(' H时i分', $time);
    $date_time = $ri . $zhou . $fen;
}
/*
 * 在线人数进行整理
 */
function deal_online_cate_num()
{
}
/**
 * 通过  房间号 获取视频播放信息列表；注:在手机端开启录制时,要以房间号作为文件名;
 * https://www.qcloud.com/doc/api/257/1373
 * @param unknown_type $video_id
 * @return Ambigous <multitype:number NULL unknown , number, string, unknown>
 */
function get_vodset_by_video_id($video_id)
{
    $root = array();
    $root['status'] = 1;
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    $video = $video_redis->getRow_db($video_id, array('id', 'channelid', 'begin_time', 'video_type'));
    $fileName = $video_id . '_' . to_date($video['begin_time'], 'Y-m-d-H'); //互动直播 例如：2018376_2017-01-12-18-03-19
    if ($video['video_type'] == 1 && $video['channelid']) {
        $fileName = 'live' . $video_id . '_' . to_date($video['begin_time'], 'Y-m-d-H'); //云直播&频道托管    例如：live2018376_2017-01-12-18-03-19
        if (strpos($video['channelid'], '_')) {
            $fileName = $video['channelid']; //云直播&直播码 例如：2811_311359b479_dd55c664b4acf27c5138
        }
    }
    $ret = get_vodset_by_filename($fileName);
    //兼容 云直播&直播码接入&点播前缀有live
    if ($ret['status'] != 1) {
        if ($video['video_type'] == 1 && $video['channelid'] && strpos($video['channelid'], '_')) {
            $fileName = 'live' . $video['channelid']; //例如：live6311_311359b479_dd55c664b4acf27c5138
        }
        $ret = get_vodset_by_filename($fileName);
    }
    $root['total_count'] = 0;
    if ($ret['status'] == 1) {
        $root['vodset'][] = $ret['ret'];
        $root['total_count'] = $ret['ret']['totalCount'];
    } else {
        $root['status'] = 0;
        $root['error'] = $ret['error'];
    }
    return $root;
}
/**
 * 通过  房间号 获取视频播fileIds列表
 * https://www.qcloud.com/doc/api/257/1373
 * @param unknown_type $video_id
 */
function get_vod_fileIds($video_id)
{
    $fileName = $video_id . '_';
    $ret = get_vodset_by_filename($fileName);
    $fileIds = array();
    foreach ($ret['fileSet'] as $k2 => $v2) {
        $fileIds[] = $v2['fileId'];
    }
    return $fileIds;
}
/**
 * 通过  视频名称（前缀匹配） 获取视频播放信息列表
 * https://www.qcloud.com/doc/api/257/1373
 * @param unknown_type $fileName
 * @param unknown_type $page
 * @return multitype:number string unknown
 */
function get_vodset_by_filename($fileName, $page = 1, $page_size = 20)
{
    $root = array();
    $root['status'] = 1;
    fanwe_require(APP_ROOT_PATH . 'system/QcloudApi/QcloudApi.php');
    $m_config = load_auto_cache("m_config");
    $config = array('SecretId' => $m_config['qcloud_secret_id'],
        'SecretKey' => $m_config['qcloud_secret_key'],
        'RequestMethod' => 'GET',
        'DefaultRegion' => 'gz');
    $service = QcloudApi::load(QcloudApi::MODULE_VOD, $config);
    if ($page == 0) {
        $page = 1;
    }
    $package = array('fileName' => $fileName, 'pageNo' => $page, 'pageSize' => $page_size);
    $ret = $service->DescribeVodPlayInfo($package);
    if ($ret === false) {
        $error = $service->getError();
        $root['status'] = 0;
        $root['code'] = $error->getCode();
        $root['error'] = "fileName:" . $fileName . ";code:" . $error->getCode() . ";msg:" . $error->getMessage();
    } else {
        $root['ret'] = $ret;
    }
    $root['package'] = $ret;
    return $root;
}
/**
 * 每隔N秒，将在线直播redis计算的数据同步到mysql中
 */
function crontab_deal_num($s = 5)
{
    $is_ok = $GLOBALS['cache']->set_lock('crontab_deal_num', $s);
    if ($is_ok) {
        $sql = "SELECT id FROM " . DB_PREFIX . "video";
        $list = $GLOBALS['db']->getAll($sql, true, true);
        if (count($list) > 0) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            foreach ($list as $k => $v) {
                $video_id = $v['id'];
                //计算权重
                $video_redis->syn_sort_num($video_id);
                $fields = array('share_count', 'like_count', 'fans_count', 'sort_num', 'vote_number', 'robot_num', 'watch_number', 'virtual_watch_number', 'max_watch_number', 'pk_ticket');
                $video = $video_redis->getRow_db($video_id, $fields);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $video, "UPDATE", "id=" . $video_id);
            }
        }
    }
}
/**
 * 把数据库中的fanwe_video同步到redis上去
 * @param int $video_id
 * @param string $fields 需要同步的字段 * 表示所有
 * @param bool $exinclude_calc；true 不含redis中的计算字段array('share_count','like_count','fans_count', 'sort_num', 'vote_number', 'robot_num','watch_number', 'virtual_watch_number', 'max_watch_number')
 */
function sync_video_to_redis($video_id, $fields = '*', $exinclude_calc = true)
{
    if ($fields == '') {
        $fields = '*';
    }
    $sql = "select " . $fields . " from " . DB_PREFIX . "video where id = " . $video_id;
    $video = $GLOBALS['db']->getRow($sql);
    if ($video == false) {
        $sql = "select " . $fields . " from " . DB_PREFIX . "video_history where id = " . $video_id;
        $video = $GLOBALS['db']->getRow($sql);
    }
    if ($exinclude_calc) {
        $calc_fields = array('share_count', 'like_count', 'fans_count', 'sort_num', 'vote_number', 'robot_num', 'watch_number', 'virtual_watch_number', 'max_watch_number', 'pk_ticket');
        foreach ($video as $k => $v) {
            if (in_array($k, $calc_fields)) {
                unset($video[$k]);
            }
        }
    }
    if ($fields == '*' && isset($video['vote_number'])) {
        unset($video['vote_number']);
    }
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    $video_redis->update_db($video_id, $video);
}
/**
 * 将发送礼物记录移到mysql数据库中
 * $num：一次插入多少条数据; -1取出所有记录;
 *
 * 本函数实际上已经失效;发送礼物记录，发送时直接记录在fanwe_video_prop_ym表中了; 此时用来处理旧的redis中数据,同步到mysql中
 */
function sync_video_prop_to_mysql($num = -1)
{
    $is_ok = $GLOBALS['cache']->set_lock('sync_video_prop_to_mysql', 500);
    if ($is_ok) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoGiftRedisService.php');
        $videoGift_redis = new VideoGiftRedisService();
        //$list = $videoGift_redis->getAll($num);
        if ($num == -1) {
            $num = 10000;
        }
        $list = $videoGift_redis->getAll($num);
        if (count($list) == 0) {
            $list = $videoGift_redis->getAll(-1);
        }
        foreach ($list as $k => $v) {
            $video_prop = json_decode($v, true);
            $video_prop['create_y'] = to_date($video_prop['create_time'], 'Y');
            $video_prop['create_m'] = to_date($video_prop['create_time'], 'm');
            $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
            $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
            $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
            //没做批量插入,主要是考虑支持重复执行
            $GLOBALS['db']->autoExecute(DB_PREFIX . "video_prop", $video_prop, "INSERT", '', 'SILENT');
            if ($GLOBALS['db']->affected_rows()) {
                $videoGift_redis->del_db($k);
            } else {
                $sql = "select id from " . DB_PREFIX . "video_prop where id = " . $k;
                if ($GLOBALS['db']->getOne($sql) > 0) {
                    $videoGift_redis->del_db($k);
                }
            }
        }
        $GLOBALS['cache']->del_lock('sync_video_prop_to_mysql');
    }
}
/**
 * 删除指定文件[前缀搜索]
 * @param unknown_type $fileName
 * @return multitype:unknown
 */
function del_all_vod($fileName)
{
    //$fileName = '_';
    $ret = get_vodset_by_filename($fileName, 1, 80);
    //print_r($ret);
    //$fileIds = array();
    fanwe_require(APP_ROOT_PATH . 'system/QcloudApi/QcloudApi.php');
    $m_config = load_auto_cache("m_config");
    $config = array('SecretId' => $m_config['qcloud_secret_id'],
        'SecretKey' => $m_config['qcloud_secret_key'],
        'RequestMethod' => 'GET',
        'DefaultRegion' => 'gz');
    $service = QcloudApi::load(QcloudApi::MODULE_VOD, $config);
    foreach ($ret['ret']['fileSet'] as $k2 => $v2) {
        $package = array('fileId' => $v2['fileId'], 'priority' => 0);
        $ret2 = $service->DeleteVodFile($package);
        $ret[$v2['fileId']] = $ret2;
    }
    //print_r($fileIds);
    return $ret;
}
/**
 * 历史直播：上架/下架
 * 上架: 将fanwe_video_history 表数据,移到fanwe_video 后，删除fanwe_video_history记录
 * 下架: 将fanwe_video 表数据,移到fanwe_video_history 后，删除fanwe_video记录
 * @param unknown_type $video_id
 * @param unknown_type $status; 0:上架;1:下架;
 */
function video_status($video_id, $status)
{
    $pInTrans = $GLOBALS['db']->StartTrans();
    try
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        if ($status == 0) {
            //历史直播：上架
            $sql = "update " . DB_PREFIX . "video_history set live_in = 3 where live_in = 0 and id = " . $video_id;
            $GLOBALS['db']->query($sql);
            if ($GLOBALS['db']->affected_rows()) {
                $sql = "select * from " . DB_PREFIX . "video_history  where id = " . $video_id;
                $video = $GLOBALS['db']->getRow($sql);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $video, "INSERT");
                //修改话题
                if ($video['cate_id'] > 0) {
                    $sql = "update " . DB_PREFIX . "video_cate a set a.num = (select count(*) from " . DB_PREFIX . "video b where b.cate_id = a.id and b.live_in in (1,3)";
                    $m_config = load_auto_cache("m_config"); //初始化手机端配置
                    if ((defined('OPEN_ROOM_HIDE') && OPEN_ROOM_HIDE == 1) && intval($m_config['open_room_hide']) == 1) {
                        $sql .= " and b.province <> '火星' and b.province <>''";
                    }
                    $sql .= ") where a.id = " . $video['cate_id'];
                    $GLOBALS['db']->query($sql);
                }
                $user_id = intval($video['user_id']);
                $sql = "select sex,ticket,refund_ticket,user_level,fans_count from " . DB_PREFIX . "user where id = " . $user_id;
                $user = $GLOBALS['db']->getRow($sql, true, true);
                $m_config = load_auto_cache("m_config");
                //sort_init(初始排序权重) = (用户可提现秀票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留秀票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
                $sort_init = (intval($user['ticket']) - intval($user['refund_ticket'])) * floatval($m_config['ticke_weight']);
                $sort_init += intval($user['user_level']) * floatval($m_config['level_weight']);
                $sort_init += intval($user['fans_count']) * floatval($m_config['focus_weight']);
                $sql = "update " . DB_PREFIX . "video set sort_init = " . $sort_init . ",watch_number=0,robot_num=0 where id = " . $video_id;
                $GLOBALS['db']->query($sql);
                $sql = "delete from " . DB_PREFIX . "video_history WHERE id=" . $video_id;
                $GLOBALS['db']->query($sql);
                //将mysql数据,同步一份到redis中
                sync_video_to_redis($video_id, '*', false);
                //付费记录从历史表移到原记录表中
                if (intval($video['is_live_pay']) == 1) {
                    syn_history_to_live_pay($video_id);
                }
                $video_redis->video_online($video_id, $video['group_id']);
            }
        } else {
            //回看直播：下架
            $sql = "update " . DB_PREFIX . "video set live_in = 0 where live_in = 3 and id = " . $video_id;
            $GLOBALS['db']->query($sql);
            if ($GLOBALS['db']->affected_rows()) {
                //下架后，将redis数据,同步一份到mysql
                $fields = array('share_count', 'like_count', 'fans_count', 'sort_num', 'vote_number', 'robot_num', 'watch_number', 'virtual_watch_number', 'max_watch_number', 'user_id', 'pk_ticket');
                $video = $video_redis->getRow_db($video_id, $fields);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $video, "UPDATE", "id=" . $video_id);
                //将数据历史表中
                $sql = "select * from " . DB_PREFIX . "video where id = " . $video_id;
                $video = $GLOBALS['db']->getRow($sql);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "video_history", $video, "INSERT");
                //修改话题
                if ($video['cate_id'] > 0) {
                    $sql = "update " . DB_PREFIX . "video_cate a set a.num = (select count(*) from " . DB_PREFIX . "video b where b.cate_id = a.id and b.live_in in (1,3)";
                    $m_config = load_auto_cache("m_config"); //初始化手机端配置
                    if ((defined('OPEN_ROOM_HIDE') && OPEN_ROOM_HIDE == 1) && intval($m_config['open_room_hide']) == 1) {
                        $sql .= " and b.province <> '火星' and b.province <>''";
                    }
                    $sql .= ") where a.id = " . $video['cate_id'];
                    $GLOBALS['db']->query($sql);
                }
                //将mysql数据,同步一份到redis中
                sync_video_to_redis($video_id, '*', false);
                //付费直播记录移到历史表
                if (intval($video['is_live_pay']) == 1) {
                    syn_live_pay_to_history($video_id, $video['user_id']);
                }
                $sql = "delete from " . DB_PREFIX . "video WHERE id=" . $video_id;
                $GLOBALS['db']->query($sql);
            }
        }
        $sql = "select count(*) as num from " . DB_PREFIX . "video_history where is_delete = 0 and is_del_vod = 0 and user_id = '" . $user_id . "'";
        $video_count = $GLOBALS['db']->getOne($sql);
        $sql = "update " . DB_PREFIX . "user set video_count = " . $video_count . " where id = " . $user_id;
        $GLOBALS['db']->query($sql);
        //提交事务,不等 消息推送,防止锁太久
        $GLOBALS['db']->Commit($pInTrans);
        $pInTrans = false; //防止，下面异常时，还调用：Rollback
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_data = array();
        $user_data['video_count'] = $video_count;
        $user_redis->update_db($user_id, $user_data);
        return true;
    } catch (Exception $e) {
        //异常回滚
        $GLOBALS['db']->Rollback($pInTrans);
        return true;
    }
}
/**
 * 查询直播频道详情
 * https://www.qcloud.com/doc/api/258/4717
 * @param unknown_type $channelId
 * @return
 */
function describe_lvb_channel($channelId)
{
    fanwe_require(APP_ROOT_PATH . 'system/QcloudApi/QcloudApi.php');
    $m_config = load_auto_cache("m_config");
    $config = array('SecretId' => $m_config['qcloud_secret_id'],
        'SecretKey' => $m_config['qcloud_secret_key'],
        'RequestMethod' => 'GET',
        'DefaultRegion' => 'gz');
    $service = QcloudApi::load(QcloudApi::MODULE_LIVE, $config);
    $package = array('channelId' => $channelId);
    $ret = $service->DescribeLVBChannel($package);
    return $ret;
}
/**
 * 给用户分配红包
 * @param unknown_type $user_prop_id
 * @param unknown_type $user_id
 * @param unknown_type $money
 */
function allot_red_to_user($user_prop_id, $user_id, $money)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedRedisService.php');
    $videoRed_redis = new VideoRedRedisService();
    $videoRed_redis->add_user_winning($user_prop_id, $user_id, $money);
    //增加：用户秀豆
    $sql = "update " . DB_PREFIX . "user set diamonds = diamonds + " . $money . " where id = " . $user_id;
    $GLOBALS['db']->query($sql);
    user_deal_to_reids(array($user_id));
}
/**
 * 将直播房间中的，红包领取记录,同步一份到mysql中
 * 正常在：直播结束时调用一次，删除直播(视频)时调用一次
 * @param unknown_type $video_id
 */
function syn_red_to_mysql($video_id)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    //直播结束后,将redis中,领取红包记录保存一份到mysql中
    $red_list = $video_redis->get_reds($video_id);
    if (count($red_list) > 0) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedRedisService.php');
        $videoRed_redis = new VideoRedRedisService();
        $pInTrans = $GLOBALS['db']->StartTrans();
        try
        {
            foreach ($red_list as $red_id) {
                $user_list = $videoRed_redis->get_winnings($red_id);
                foreach ($user_list as $k => $v) {
                    $user_id = intval($v['user_id']);
                    $sql = "select id from " . DB_PREFIX . "video_red where video_id =" . $video_id . " and video_prop_id = " . $red_id . " and user_id =" . $user_id;
                    $id = $GLOBALS['db']->getOne($sql);
                    $video_red = array();
                    $video_red['video_id'] = $video_id;
                    $video_red['video_prop_id'] = $red_id;
                    $video_red['user_id'] = $user_id;
                    $video_red['diamonds'] = $v['diamonds'];
                    if ($id > 0) {
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "video_red", $video_red, "UPDATE", "id=" . $id);
                    } else {
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "video_red", $video_red, "INSERT");
                    }
                }
            }
            $GLOBALS['db']->Commit($pInTrans);
        } catch (Exception $e) {
            //异常回滚
            $GLOBALS['db']->Rollback($pInTrans);
        }
    }
}
function filter_false(&$data, $default = array())
{
    foreach ($default as $key => $value) {
        if ($data[$key] == false) {
            $data[$key] = $value;
        }
    }
}
function get_video_url($room_id, $live_in)
{
    if ($live_in == 3 || $live_in == 0) {
        return url('live#show', array('room_id' => $room_id, 'is_vod' => 1));
    } else {
        return url('live#show', array('room_id' => $room_id));
    }
}
function get_live_image($v)
{
    return get_spec_image(empty($v['live_image']) ? $v['head_image'] : $v['live_image'], 285, 160, 1);
}
function getPropTablename($video_id)
{
    $video_id = intval($video_id);
    // $table = DB_PREFIX . 'video';
    // $res = $GLOBALS['db']->getRow("SELECT `prop_table` FROM $table WHERE `id`=$video_id;");
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    $video = $video_redis->getRow_db($video_id, array('prop_table'));
    return $video ? $video['prop_table'] : $video;
}
function createPropTable($time = NOW_TIME)
{
    //获取上个月自增ID
    $prev_m = date("Ym", strtotime("-1 month"));
    $prev_table = DB_PREFIX . 'video_prop_' . $prev_m;
    $result = $GLOBALS['db']->getRow("SHOW TABLES LIKE'$prev_table'");
    if (empty($result)) {
        $prev_table = DB_PREFIX . 'video_prop';
    }
    $increment_id = $GLOBALS['db']->getOne("SELECT MAX(id) from " . $prev_table);
    $increment_id = intval($increment_id) ? intval($increment_id) : 1;
    $table = DB_PREFIX . 'video_prop_' . to_date($time, 'Ym');
    $res = $GLOBALS['db']->getRow("SHOW TABLES LIKE'$table'");
    if (!$res) {
        // 创建新表
        // 表结构
        // -- Table structure for `%DB_PREFIX%video_prop`
        $sql = "CREATE TABLE `$table` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `prop_id` int(10) NOT NULL DEFAULT '0' COMMENT '礼物id',
          `prop_name` varchar(255) NOT NULL COMMENT '道具名',
          `total_score` int(11) NOT NULL COMMENT '积分（from_user_id可获得的积分）合计',
          `total_diamonds` int(11) NOT NULL COMMENT '秀豆（from_user_id减少的秀豆）合计',
          `total_ticket` int(11) NOT NULL DEFAULT '0' COMMENT '秀票(to_user_id增加的秀票）合计;is_red_envelope=1时,为主播获得的：秀豆 数量',
          `from_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '送',
          `to_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '收',
          `create_time` int(10) NOT NULL COMMENT '时间',
          `create_date` date NOT NULL COMMENT '日期字段,按日期归档；要不然数据量太大了；不好维护',
          `create_d` tinyint(2) NOT NULL COMMENT '日',
          `create_w` tinyint(2) NOT NULL COMMENT '周',
          `num` int(10) NOT NULL COMMENT '送的数量',
          `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '直播ID',
          `group_id` varchar(20) NOT NULL COMMENT '群组ID',
          `is_red_envelope` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:红包',
          `msg` varchar(255) NOT NULL COMMENT '弹幕内容',
          `ActionStatus` varchar(10) NOT NULL COMMENT '消息发送，请求处理的结果，OK表示处理成功，FAIL表示失败。',
          `ErrorInfo` varchar(255) NOT NULL COMMENT '消息发送，错误信息',
          `ErrorCode` int(10) NOT NULL COMMENT '错误码',
          `create_ym` varchar(12) NOT NULL COMMENT '年月 如:201610',
          `from_ip` varchar(255) NOT NULL COMMENT '送礼物人IP',
          `is_private` int(4) default 0 COMMENT '判断是否为私信送礼 1表示私信 2表示不是私信',
          `is_award` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为可中奖礼物 1为 是、0为否',
          `is_heat` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热度礼物 1是 0否',
          `is_rocket` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否火箭榜礼物 1是 0否',
          PRIMARY KEY (`id`),
          KEY `idx_ecs_video_prop_cc_1` (`create_ym`,`create_d`,`from_user_id`,`total_diamonds`),
          KEY `from_user_id` (`from_user_id`,`total_diamonds`) USING BTREE,
          KEY `idx_ecs_video_prop_cc_2` (`create_ym`,`from_user_id`,`total_diamonds`),
          KEY `to_user_id` (`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
          KEY `idx_ecs_video_prop_cc_3` (`create_ym`,`create_d`,`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
          KEY `idx_ecs_video_prop_cc_4` (`create_ym`,`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
          KEY `create_ym` (`create_time`,`create_ym`),
          KEY `prop_id` (`prop_id`) USING BTREE,
          KEY `create_ymd` (`create_d`,`create_ym`),
          KEY `create_ymr` (`create_ym`,`is_red_envelope`) USING BTREE,
          KEY `create_wr` (`create_w`,`is_red_envelope`),
          KEY `create_ymrt` (`create_ym`,`is_red_envelope`,`create_time`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT={$increment_id} DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT='送礼物表'";
        $res = $GLOBALS['db']->query($sql);
    }
    /*$res = 1;
    $table = DB_PREFIX . 'video_prop';*/
    return $res ? $table : false;
}
/**
 * 取前50条观众列表，IM推送到客户端
 * @param unknown_type $video_id
 * @param unknown_type $group_id
 * @return mixed
 */
function push_viewer($video_id, $group_id, $page_size = 50)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
    $video_viewer_redis = new VideoViewerRedisService();
    $viewer = $video_viewer_redis->get_viewer_list2($video_id, 1, $page_size);
    for ($i = count($viewer['list']) - 1; $i >= 0; $i--) {
        if ($viewer['list'][$i]['user_id'] == 0) {
            array_splice($viewer['list'], $i, 1);
        }
    }
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/shorturl.php');
    $user_list = array();
    $viewer['list_fields'] = array('user_id', 'user_level', 'head_image', 'v_icon', 'user_ticket', 'is_robot', 'is_authentication');
    $short = shorturl::SHORTURL; // 'http://t.cn/';
    foreach ($viewer['list'] as $k => $v) {
        $head_image = $v['head_image'];
        $v_icon = $v['v_icon'];
        $is_robot = intval($v['is_robot']);
        $is_authentication = intval($v['is_authentication']);
        if (!empty($head_image)) {
            $head_image = shorturl::getShort($head_image);
            if (empty($head_image)) {
                $head_image = $v['head_image'];
            } else {
                $head_image = str_replace($short, '', $head_image);
            }
        }
        if (!empty($v_icon)) {
            $v_icon = shorturl::getShort($v_icon);
            if (empty($v_icon)) {
                $v_icon = $v['v_icon'];
            } else {
                $v_icon = str_replace($short, '', $v_icon);
            }
        }
        $user_ticket = $v['user_ticket'];
        $user2 = array((string) $v['user_id'], (string) $v['user_level'], $head_image, $v_icon, $user_ticket, $is_robot, $is_authentication);
        $user_list[] = $user2;
    }
    unset($viewer['list']);
    $viewer['short_url'] = $short; //如果head_image,v_icon不是http://开头则需要加上short_url
    $viewer['list_data'] = $user_list;
    $viewer['time'] = NOW_TIME;
    $ext = array();
    $ext['type'] = 42; //42 通用数据格式
    $ext['data_type'] = 0; //直播间观众列表
    $ext['data'] = $viewer;
    $msg_content = json_encode($ext);
    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $api = createTimAPI();
    $ret = $api->group_send_group_system_notification($group_id, $msg_content);
    return $ret;
}
/**
 * 回播定时推送观众列表
 * @param unknown_type $video_id
 * @param unknown_type $page_size
 * @param unknown_type $s
 * @return multitype:mixed
 */
function crontab_viewer($video_id, $page_size = 50, $s = 50)
{
    $ret = array();
    $is_ok = $GLOBALS['cache']->set_lock('crontab_viewer_num', $s);
    if ($is_ok) {
        if ($video_id == 0) {
            $sql = "SELECT id,group_id,watch_number FROM " . DB_PREFIX . "video where live_in = 3 and watch_number > 0";
        } else {
            $sql = "SELECT id,group_id,watch_number FROM " . DB_PREFIX . "video where id = " . $video_id;
        }
        $list = $GLOBALS['db']->getAll($sql, true, true);
        if (count($list) > 0) {
            foreach ($list as $k => $v) {
                $ret[] = push_viewer($v['id'], $v['group_id'], $page_size);
            }
        }
        $GLOBALS['cache']->del_lock('crontab_viewer_num');
    }
    return $ret;
}
function filter_all_false($default = array())
{
    if (is_array($default)) {
        foreach ($default as $key => $value) {
            if (is_array($value)) {
                $value = filter_all_false($value);
            } else {
                if ($value === false) {
                    $value = get_type($value);
                }
            }
            $default[$key] = $value;
        }
    } else {
        if ($default === false) {
            $default = get_type($default);
        }
    }
    return $default;
}
/*
判断变量的类型
 */
function get_type($date = '')
{
    switch (gettype($date)) {
        case 'integer':
            return 0;
            break;
            break;
        case 'string':
            return '';
            break;
        case 'array':
            return array();
            break;
        case 'boolean':
            return '0';
            break;
        /* case 'double':
        return 0.00;
        break;
        case 'object':
        return object();
        break;
        case 'resource':
        return 'resource';
        break;
        case 'NULL':
        return 'NULL';
        break;
        case 'object':
        return  'object';
        break;*/
        default:
            return '0';
    }
}
/*
 * 视频合并
 * @param string $channel_id 直播码
 * @param string $new_file_name 新的文件名
 */
function Com_ConcatVideo($channel_id, $new_file_name)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
    $video_factory = new VideoFactory();
    $ret = $video_factory->ConcatVideo($channel_id, $new_file_name);
    return $ret;
}
//使用的sdk版本 0默认腾讯云、1金山
//映射关系类型  腾讯云直播, 金山云，星域，千秀云 ，阿里云
//video_type     1          2        3      4       5
//sdk_type       0          1        1      1       1
function get_sdk_info($video_type)
{
    switch ($video_type) {
        case 1:
            return $sdk_type = 0;
            break;
        default;
            return $sdk_type = 1;
            break;
    }
}
//------付费相关------
/**
 * 按时付费直播结束后转按场付费
 * @param $video
 */
function change_pay_type($video)
{
    if (intval($video['live_pay_type']) != 1) {
        $sql = "update " . DB_PREFIX . "video set live_pay_type = 1 where id = " . $video['id'];
        $GLOBALS['db']->query($sql);
    }
}
/**
 * 是否付费过
 */
function get_pay_video_info($video_info)
{
    $root = array('status' => 1, 'error' => '');
    //初始化参数
    $live_pay_time = strim($video_info['live_pay_time']);
    $live_pay_type = intval($video_info['live_pay_type']);
    $live_fee = intval($video_info['live_fee']);
    $live_is_mention = intval($video_info['live_is_mention']);
    $is_live_pay = intval($video_info['is_live_pay']);
    $user_id = intval($GLOBALS['user_info']['id']); //用户ID
    $video_user_id = intval($video_info['user_id']); //主播ID
    $live_in = intval($video_info['live_in']); //直播间ID
    $room_id = intval($video_info['id']); //直播间ID
    $is_pay_over = 0; //是否付费   1 已付费；0未付费
    $new_room_id = 0;
    if (intval($video_info['pay_room_id']) > 0) {
        $room_id = $video_info['pay_room_id'];
        $new_room_id = $video_info['id'];
    }
    //非付费直播间 或者 直播不正常 或者 直播不存在 跳过
    if ($is_live_pay == 1 && $live_fee > 0 && $live_in != 0 && $room_id > 0) {
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登录.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            //默认提醒
            if ($room_id > 0) {
                //获取付费记录信息
                $sql = "select id,pay_time_next,total_time,total_diamonds,live_pay_type from " . DB_PREFIX . "live_pay_log  where from_user_id = " . $user_id . " and to_user_id = " . $video_user_id . " and video_id=" . $room_id;
                $live_pay_log_info = $GLOBALS['db']->getRow($sql);
                //不可提醒
                $now_time = NOW_TIME;
                $root['status'] = 0;
                if (intval($live_pay_log_info['id']) > 0 && ($live_pay_log_info['live_pay_type'] == 1 || ($live_pay_log_info['pay_time_next'] > $now_time && $live_pay_log_info['live_pay_type'] == 0))) {
                    $is_pay_over = 1;
                    $root['status'] = 1;
                }
            }
        }
    }
    $root['is_live_pay'] = $is_live_pay;
    $root['live_pay_type'] = $live_pay_type;
    $root['live_fee'] = $live_fee;
    $root['is_pay_over'] = $is_pay_over;
    return $root;
}
/**
 * 将付费直播记录移到历史表
 * 正常在：直播结束时调用一次，删除直播(视频)时调用一次
 * @param unknown_type $video_id
 */
function syn_live_pay_to_history($video_id, $user_id)
{
    if ($video_id > 0) {
        $sql = "select MAX(id) from " . DB_PREFIX . "live_pay_log_history";
        $live_pay_log_history_mid = $GLOBALS['db']->getOne($sql);
        $sql = "select MAX(id) from " . DB_PREFIX . "live_pay_log";
        $live_pay_mid = $GLOBALS['db']->getOne($sql);
        if ($live_pay_log_history_mid > $live_pay_mid) {
            $video_max = $live_pay_log_history_mid + 1;
            $sql = "alter table " . DB_PREFIX . "live_pay_log AUTO_INCREMENT=" . $video_max;
            $GLOBALS['db']->query($sql);
        }
        //将本次直播付费记录 移到历史表中
        $fields = 'id,total_time,total_ticket,total_diamonds,from_user_id,to_user_id,create_time,create_date,create_ym,create_d,create_w,live_fee,live_pay_time,live_pay_date,video_id,group_id,pay_time_end,pay_time_next,live_is_mention_time,live_is_mention_pay,live_pay_type,new_room_id,total_score,uesddiamonds_to_score,ticket_to_rate';
        $sql = "insert into " . DB_PREFIX . "live_pay_log_history(" . $fields . ") select " . $fields . " from " . DB_PREFIX . "live_pay_log where video_id='" . $video_id . "' and to_user_id=" . $user_id;
        $GLOBALS['db']->query($sql);
        $sql = "delete from " . DB_PREFIX . "live_pay_log where video_id='" . $video_id . "' and to_user_id=" . $user_id;
        $GLOBALS['db']->query($sql);
    }
}
/**
 * 将付费直播从历史表移到原记录表中
 * 正常在：直播结束时调用一次，删除直播(视频)时调用一次
 * @param unknown_type $video_id
 */
function syn_history_to_live_pay($video_id)
{
    if ($video_id > 0) {
        //将本次直播付费记录 从历史表移到原记录表中
        $fields = 'total_time,total_ticket,total_diamonds,from_user_id,to_user_id,create_time,create_date,create_ym,create_d,create_w,live_fee,live_pay_time,live_pay_date,video_id,group_id,pay_time_end,pay_time_next,live_is_mention_time,live_is_mention_pay,live_pay_type,new_room_id,total_score,uesddiamonds_to_score,ticket_to_rate';
        $sql = "insert into " . DB_PREFIX . "live_pay_log(" . $fields . ") select " . $fields . " from " . DB_PREFIX . "live_pay_log_history where video_id='" . $video_id . "'";
        $GLOBALS['db']->query($sql);
        $sql = "delete from " . DB_PREFIX . "live_pay_log_history where video_id='" . $video_id . "'";
        $GLOBALS['db']->query($sql);
    }
}
//----------------竞拍相关------------------
/**
 * 根据竞拍ID 获得 竞拍参与表
 */
function get_pailog_table($paiid = 0)
{
    $idx = intval($paiid / 10);
    return DB_PREFIX . "pai_log_" . $idx;
}
/**
 * $goods  pai_goods 的信息
 * $type=0 竞拍的人回调， $type=1 主播的回调
 */
function format_pai_goods(&$goods, $type = 0)
{
    if ($goods['imgs'] != '') {
        $goods['imgs'] = json_decode($goods['imgs'], 1);
        //$goods['img']=$goods['imgs']['0'];
        $goods['img'] = get_spec_image($goods['imgs']['0']);
        if ($goods['imgs'] == "") {
            $goods['imgs'] = array();
        } else {
            foreach ($goods['imgs'] as $k => $v) {
                //$goods['imgs'][$k]=get_domain().APP_ROOT.$v;
                $goods['imgs'][$k] = get_spec_image($v);
            }
        }
    } else {
        $goods['imgs'] = array();
    }
    if ($goods['status'] == 0) {
        if (PAI_YANCHI_MODULE == 0) {
            $goods['pai_left_time'] = $goods['pai_time'] * 3600 + $goods['create_time'] + $goods['now_yanshi'] * $goods['pai_yanshi'] * 60 - NOW_TIME;
        } else {
            $goods['pai_left_time'] = $goods['end_time'] - NOW_TIME;
        }
        if ($goods['pai_left_time'] < 0) {
            $goods['pai_left_time'] = 0;
        }
    } else {
        $goods['pai_left_time'] = 0;
    }
    if (isset($goods['user_pai_info']['consignee_district']) && $goods['user_pai_info']['consignee_district'] != "") {
        $goods['user_pai_info']['consignee_district'] = json_decode($goods['user_pai_info']['consignee_district'], 1);
        if ($goods['user_pai_info']['consignee_district'] == "") {
            $goods['user_pai_info']['consignee_district'] = array();
        }
    } else {
        $goods['user_pai_info']['consignee_district'] = array();
    }
    if ($goods['district'] != '') {
        $goods['district'] = json_decode($goods['district'], 1);
        if ($goods['district'] == "") {
            $goods['district'] = array();
        }
    } else {
        $goods['district'] = array();
    }
    if ($goods['last_pai_diamonds'] == 0) {
        $goods['last_pai_diamonds'] = $goods['qp_diamonds'];
    }
    if ($goods['is_true'] == 0) {
        //虚拟
        if ($type == 1) {
            if ($goods['order_status'] == 1) {
                $goods['info_status'] = 1;
                $goods['button_status'] = 0;
                $goods['expire_time'] = strtotime($goods['order_time']) - 8 * 3600 + MAX_PAI_PAY_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 2) {
                $goods['info_status'] = 2;
                $goods['button_status'] = 2;
                $date_time = strtotime($goods['date_time']) - 8 * 3600;
                if ($date_time - NOW_TIME > 0) {
                    //约会倒计时
                    $goods['expire_time'] = $date_time - NOW_TIME;
                    $goods['info_status_type'] = 0;
                } else {
                    //约会确认倒计时
                    $goods['expire_time'] = $date_time + MAX_PODCAST_CONFIRM_TIME - NOW_TIME;
                    $goods['info_status_type'] = 1;
                }
            } elseif ($goods['order_status'] == 3) {
                $goods['info_status'] = 3;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 4) {
                $goods['info_status'] = 4;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 5) {
                $goods['info_status'] = 5;
                $goods['button_status'] = 5;
                $goods['expire_time'] = 0; //strtotime($goods['order_status_time']) + 16*3600 - NOW_TIME;
            } elseif ($goods['order_status'] == 6) {
                $goods['info_status'] = 6;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 7) {
                $goods['info_status'] = 7;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } else {
                $goods['info_status'] = 0;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            }
        } else {
            if ($goods['order_status'] == 1) {
                $goods['info_status'] = 1;
                $goods['button_status'] = 1;
                $goods['expire_time'] = strtotime($goods['order_time']) - 8 * 3600 + MAX_PAI_PAY_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 2) {
                $goods['info_status'] = 2;
                $goods['button_status'] = 0;
                $date_time = strtotime($goods['date_time']) - 8 * 3600;
                if ($date_time > NOW_TIME) {
                    //约会倒计时
                    $goods['expire_time'] = $date_time - NOW_TIME;
                    $goods['info_status_type'] = 0;
                } else {
                    //主播未确认7天，倒计时
                    $goods['expire_time'] = $date_time + MAX_PODCAST_CONFIRM_TIME - NOW_TIME;
                    $goods['info_status_type'] = 1;
                }
            } elseif ($goods['order_status'] == 3) {
                $goods['info_status'] = 3;
                $goods['button_status'] = 3;
                //买家未确认7天，倒计时
                $goods['expire_time'] = intval($goods['order_status_time']) + MAX_USER_CONFIRM_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 4) {
                $goods['info_status'] = 4;
                $goods['button_status'] = 4;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 5) {
                $goods['info_status'] = 5;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 6) {
                $goods['info_status'] = 6;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 7) {
                $goods['info_status'] = 7;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } else {
                $goods['info_status'] = 0;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            }
        }
    } else {
        //实物
        if ($type == 1) {
            if ($goods['order_status'] == 1) {
                $goods['info_status'] = 1;
                $goods['button_status'] = 0;
                $goods['expire_time'] = strtotime($goods['order_time']) - 8 * 3600 + MAX_PAI_PAY_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 2) {
                $goods['info_status'] = 2;
                $goods['button_status'] = 2;
                $goods['expire_time'] = strtotime($goods['pay_time']) - 8 * 3600 + MAX_PODCAST_CONFIRM_VIRTUAL_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 3) {
                $goods['info_status'] = 3;
                $goods['button_status'] = 0;
                $goods['expire_time'] = intval($goods['order_status_time']) + MAX_USER_CONFIRM_VIRTUAL_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 4) {
                $goods['info_status'] = 4;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 5) {
                $goods['info_status'] = 5;
                $goods['button_status'] = 5;
                $goods['expire_time'] = 0; //strtotime($goods['order_status_time']) + 16*3600 - NOW_TIME;
            } elseif ($goods['order_status'] == 6) {
                $goods['info_status'] = 6;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 7) {
                $goods['info_status'] = 7;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } else {
                $goods['info_status'] = 0;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            }
        } else {
            if ($goods['order_status'] == 1) {
                $goods['info_status'] = 1;
                $goods['button_status'] = 1;
                $goods['expire_time'] = strtotime($goods['order_time']) - 8 * 3600 + MAX_PAI_PAY_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 2) {
                $goods['info_status'] = 2;
                $goods['button_status'] = 0;
                $goods['expire_time'] = strtotime($goods['pay_time']) - 8 * 3600 + MAX_PODCAST_CONFIRM_VIRTUAL_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 3) {
                $goods['info_status'] = 3;
                $goods['button_status'] = 3;
                //买家未确认7天，倒计时
                $goods['expire_time'] = intval($goods['order_status_time']) + MAX_USER_CONFIRM_VIRTUAL_TIME - NOW_TIME;
            } elseif ($goods['order_status'] == 4) {
                $goods['info_status'] = 4;
                $goods['button_status'] = 4;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 5) {
                $goods['info_status'] = 5;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 6) {
                $goods['info_status'] = 6;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } elseif ($goods['order_status'] == 7) {
                $goods['info_status'] = 7;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            } else {
                $goods['info_status'] = 0;
                $goods['button_status'] = 0;
                $goods['expire_time'] = 0;
            }
        }
    }
    $goods['expire_date_time'] = strtotime($goods['date_time']) - 8 * 3600 - NOW_TIME;
    if ($goods['expire_date_time'] < 0) {
        $goods['expire_date_time'] = 0;
    }
    if ($goods['date_time'] != '') {
        date_time_format($goods['date_time']);
    }
    if ($goods['order_status_time'] != '') {
        $goods['order_status_time'] = to_date($goods['order_status_time'], 'Y-m-d H:i:s');
    }
    if ($goods['order_status'] == 2) {
        $goods['order_status_time'] = 0;
    }
    if ($goods['pay_time'] == '0000-00-00 00:00:00') {
        $goods['pay_time'] = 0;
    }
    if ($goods['order_status'] == 4) {
        $goods['final_time'] = $goods['order_status_time'];
        $goods['order_status_time'] = 0;
    }
    $goods['pai_logs_url'] = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=pailogs&id=' . $goods['id'];
}
/**
 * 格式化出价记录
 */
function format_pai_logs(&$list, $status)
{
    foreach ($list as $k => $v) {
        $str_len = mb_strlen($list[$k]['user_name'], 'utf-8');
        if ($str_len > 2) {
            $firstStr = mb_substr($list[$k]['user_name'], 0, 1, 'utf-8');
            $lastStr = mb_substr($list[$k]['user_name'], -1, 1, 'utf-8');
            $list[$k]['user_name'] = $firstStr . '**' . $lastStr;
        }
        if ($status == 0 || $status == 1) {
            if ($k == 0) {
                $list[$k]['pai_status'] = "领先";
            } else {
                $list[$k]['pai_status'] = "出局";
            }
        } elseif ($status == 2) {
            $list[$k]['pai_status'] = "流拍";
        } elseif ($status == 3) {
            if ($k == 0) {
                $list[$k]['pai_status'] = "失败";
            } else {
                $list[$k]['pai_status'] = "出局";
            }
        } elseif ($status == 4) {
            if ($k == 0) {
                $list[$k]['pai_status'] = "成功";
            } else {
                $list[$k]['pai_status'] = "出局";
            }
        }
        //出价
        //$list[$k]['pai_diamonds'] = $v['pai_sort'] * $v['jj_diamonds']+$v['qp_diamonds'];
        $list[$k]['pai_date_format'] = to_date($v['pai_time'], "m.d H:i:s");
        if ($v['status'] == 0) {
            $list[$k]['status_format'] = "未支付";
        } elseif ($v['status'] == 1) {
            $list[$k]['status_format'] = "已支付";
        } elseif ($v['status'] == 2) {
            $list[$k]['status_format'] = "已流拍";
        }
    }
}
/*
 * 排查订单支付超时
 * 批量 查询 pai_join ，当 pai_status=1 ， order_time+15*60<now_time,
 * 则关闭 订单 order_status=6，扣去保证金,更新 goods_order;
 * 同时查询下一名支付用户
 * （1）若没有，则修改 pai_goods 的状态 status=2 表示流拍 ，同时推送消息，拍卖失败
 * （2）若有下一名，则新增下单，同时更新 pai_join,pai_goods （pai_status =2 同时金额较高的）
 */
function deal_payment_timeout()
{
    $pai_join_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "pai_join where pai_status=1 and  order_time-1+" . MAX_PAI_PAY_TIME . " <" . NOW_TIME);
    foreach ($pai_join_list as $k => $v) {
        $auth = '';
        if (isset($_REQUEST['auth'])) {
            $auth = $_REQUEST['auth'];
        }
        $info = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "pai_goods WHERE id=" . intval($v['pai_id']));
        $bz_diamonds = intval($info['bz_diamonds']); //保证金
        $podcast_id = intval($info['podcast_id']); //主播ID
        $id = intval($info['id']); //商品ID
        $name = strim($info['name']);
        $time = NOW_TIME;
        if ($v['user_id'] == $info['last_user_id']) {
            //关闭订单 （扣去保证金？）
            $sql = "update " . DB_PREFIX . "goods_order set order_status = 6 ,order_status_time=" . NOW_TIME . " where id=" . intval($v['order_id']);
            $GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "pai_join set order_status = 6 ,pai_status=3 where id=" . intval($v['id']);
            $GLOBALS['db']->query($sql);
            //保证金退还主播
            $sql = "update " . DB_PREFIX . "user set diamonds = diamonds + " . $bz_diamonds . " where id = " . $podcast_id;
            $GLOBALS['db']->query($sql);
            fanwe_require(APP_ROOT_PATH . '/mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            user_deal_to_reids(array($podcast_id));
            $account_diamonds = $user_redis->getOne_db($podcast_id, 'diamonds'); //查询主播秀豆
            //会员账户 秀豆变更日志表
            $diamonds_log_data = array(
                'pai_id' => $id,
                'user_id' => $podcast_id,
                'diamonds' => $bz_diamonds, //变更数额
                'account_diamonds' => $account_diamonds, //账户余额
                'memo' => '竞拍' . $name . ',买家超时付款，主播获得保证金', //备注
                'create_time' => $time,
                'create_date' => to_date($time, 'Y-m-d H:i:s'),
                'create_time_ymd' => to_date($time, 'Y-m-d'),
                'create_time_y' => to_date($time, 'Y'),
                'create_time_m' => to_date($time, 'm'),
                'create_time_d' => to_date($time, 'd'),
                'type' => 1
            );
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_diamonds_log", $diamonds_log_data);
            //写入用户日志
            $data = array();
            $data['diamonds'] = $bz_diamonds;
            $param['type'] = 8; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
            $log_msg = '竞拍' . $info['name'] . ',买家超时付款，主播获得保证金'; //备注
            account_log_com($data, $podcast_id, $log_msg, $param);
            //消息发送
            $user_ids = array();
            $user_ids[] = intval($v['user_id']);
            $content = "您参与的竞拍：‘" . $info['name'] . "’ 付款超时，扣除您缴纳的保证金！";
            FanweServiceCall("message", "send", array("send_type" => 'no_pay', "user_ids" => $user_ids, "content" => $content));
            //主播收到保证金消息发送
            $user_ids = array();
            $user_ids[] = $podcast_id;
            $content = "竞拍用户超时未付款，已把保证金转入你账户。";
            FanweServiceCall("message", "send", array("send_type" => 'no_pay', "user_id" => $user_ids, "content" => $content));
        } else {
            //更改订单状态
            $sql = "update " . DB_PREFIX . "goods_order set order_status = 6 ,order_status_time=" . NOW_TIME . " where id=" . intval($v['order_id']);
            $GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "pai_join set order_status = 6 ,pai_status=3 where id=" . intval($v['id']);
            $GLOBALS['db']->query($sql);
            //消息发送
            $user_ids = array();
            $user_ids[] = intval($v['user_id']);
            $content = "您参与的竞拍：‘" . $info['name'] . "’ 付款超时！";
            FanweServiceCall("message", "send", array("send_type" => 'no_pay', "user_ids" => $user_ids, "content" => $content));
        }
        $next_pai_join = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "pai_join where pai_status=2 and pai_diamonds >0 and pai_id=" . intval($v['pai_id']) . " ORDER BY pai_diamonds DESC ");
        if ($next_pai_join) {
            //中拍创建订单
            $data = array();
            $data['pai_id'] = intval($v['pai_id']);
            $data['user_id'] = intval($next_pai_join['user_id']);
            $data['auth'] = $auth;
            $result = deal_pai_do_order($data);
            if (count($result) > 0) {
                $pai_goods_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "pai_goods where id=" . intval($v['pai_id']) . " ");
                $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . intval($pai_goods_info['podcast_id']) . "  and live_in=1");
                $user_list = $GLOBALS['db']->getAll("select user_id,pai_status,order_id,order_status,pai_diamonds,order_time from " . DB_PREFIX . "pai_join where pai_id=" . intval($v['pai_id']) . " and pai_diamonds >0 ORDER BY pai_diamonds DESC limit 0,3");
                //房间内推送
                $ext = array();
                $ext['type'] = 26;
                $ext['room_id'] = intval($video_info['id']);
                $ext['pai_id'] = $data['pai_id'];
                $ext['post_id'] = intval($pai_goods_info['podcast_id']);
                $ext['desc'] = '';
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
                //-------------------------
                $ext['buyer'] = array();
                foreach ($user_list as $k1 => $v1) {
                    $buyer_data = array();
                    if (intval($v1['user_id']) > 0) {
                        $buyer_data = $user_redis->getRow_db(intval($v1['user_id']), $fields);
                        $buyer_data['user_id'] = intval($v1['user_id']);
                        $buyer_data['type'] = intval($v1['pai_status']);
                        $buyer_data['head_image'] = get_spec_image($buyer_data['head_image']);
                        $buyer_data['nick_name'] = ($buyer_data['nick_name']);
                        if ($buyer_data['type'] == 1) {
                            $buyer_data['left_time'] = $v1['order_time'] + MAX_PAI_PAY_TIME - NOW_TIME;
                            $order_sn = $GLOBALS['db']->getOne("select order_sn from " . DB_PREFIX . "goods_order where id=" . intval($v1['order_id']) . " ");
                            //$buyer_data['pay_url']=SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pai_user&act=order&order_sn='.$order_sn;
                            $buyer_data['goods_name'] = $pai_goods_info['name'];
                            $buyer_data['order_sn'] = $order_sn;
                            if ($pai_goods_info['imgs'] != '') {
                                $pai_goods_info['imgs'] = json_decode($pai_goods_info['imgs']);
                                foreach ($pai_goods_info['imgs'] as $k2 => $v2) {
                                    /*if ($auth!='') {
                                    $buyer_data['goods_icon']=$auth.$v2;
                                    }else{
                                    $buyer_data['goods_icon']=get_domain().APP_ROOT.$v2;
                                    }*/
                                    $buyer_data['goods_icon'] = get_spec_image($v2);
                                    break;
                                }
                            } else {
                                $buyer_data['goods_icon'] = "";
                            }
                            //$ext['desc'] = '恭喜用户'.$buyer_data['nick_name'].'出价'.intval($v1['pai_diamonds']).'成功拍得'.$pai_goods_info['name'];
                            $ext['desc'] = '出价' . intval($v1['pai_diamonds']) . '成功拍得' . $pai_goods_info['name'];
                            $ext['user'] = $buyer_data;
                        } else {
                            $buyer_data['left_time'] = 0;
                        }
                        $buyer_data['pai_diamonds'] = intval($v1['pai_diamonds']);
                    }
                    $ext['buyer'][] = $buyer_data;
                }
                //-------------------------
                #构造高级接口所需参数
                $tim_data = array();
                $tim_data['ext'] = $ext;
                $tim_data['podcast_id'] = strim($pai_goods_info['podcast_id']);
                $tim_data['group_id'] = strim($video_info['group_id']);
                get_tim_api($tim_data);
                /*
            $msg_content = array();
            //创建array 所需元素
            $msg_content_elem = array(
            'MsgType' => 'TIMCustomElem',       //自定义类型
            'MsgContent' => array(
            'Data' => json_encode($ext),
            'Desc' => '',
            )
            );
            //将创建的元素$msg_content_elem, 加入array $msg_content
            array_push($msg_content, $msg_content_elem);
            fanwe_require(APP_ROOT_PATH.'system/tim/TimApi.php');
            $api = createTimAPI();
            $ret = $api->group_send_group_msg2(intval($pai_goods_info['podcast_id']), intval($video_info['group_id']), $msg_content);
             */
            }
        } else {
            //流拍
            $sql = "update " . DB_PREFIX . "pai_goods set status = 2 ,order_status = 6 where id = " . intval($v['pai_id']);
            $GLOBALS['db']->query($sql);
            $pai_goods_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "pai_goods where id=" . intval($v['pai_id']) . " ");
            $sql = "update " . DB_PREFIX . "video set pai_id = 0 where user_id=" . intval($pai_goods_info['podcast_id']) . " ";
            $GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "goods set inventory=inventory+1 where id=" . $info['goods_id'] . "";
            $GLOBALS['db']->query($sql); //商品流拍，减去的库存增加回去
            $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . intval($pai_goods_info['podcast_id']) . "  and live_in=1");
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video_data = array();
            $video_data['pai_id'] = 0;
            $re = $video_redis->update_db(intval($video_info['id']), $video_data);
            //退回保证金
            $pai_id = intval($v['pai_id']);
            $time = NOW_TIME;
            $user_list = $GLOBALS['db']->getAll("SELECT id,user_id,bz_diamonds,status,pai_status FROM " . DB_PREFIX . "pai_join WHERE pai_id=" . $pai_id);
            $user_ids = array();
            foreach ($user_list as $k1 => $v1) {
                $user_ids[] = $v1['user_id'];
                //退还保证金 bz_diamonds  不为超时即退保证金
                if (intval($v1['status']) == 0 && intval($v1['pai_status']) == 3 && $v1['user_id'] == $info['last_user_id']) {
                    $sql = "update " . DB_PREFIX . "pai_join set status = 2 where id=" . intval($v1['id']) . " ";
                    $GLOBALS['db']->query($sql);
                } else {
                    fanwe_require(APP_ROOT_PATH . '/mapi/lib/redis/BaseRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    //$user_redis->lock_diamonds(intval($v1['user_id']),intval($v1['bz_diamonds']));
                    //$account_diamonds = $user_redis->getOne_db(intval($v1['user_id']),'use_diamonds');
                    $sql = "update " . DB_PREFIX . "user set diamonds = diamonds + " . intval($v1['bz_diamonds']) . " where id = " . intval($v1['user_id']);
                    $GLOBALS['db']->query($sql);
                    user_deal_to_reids(array(intval($v1['user_id'])));
                    $account_diamonds = $user_redis->getOne_db(intval($v1['user_id']), 'diamonds');
                    $sql = "update " . DB_PREFIX . "pai_join set status = 1 where id=" . intval($v1['id']) . " ";
                    $GLOBALS['db']->query($sql);
                    //会员账户 秀豆变更日志表
                    $diamonds_log_data = array(
                        'pai_id' => $pai_id,
                        'user_id' => intval($v1['user_id']),
                        'diamonds' => intval($v1['bz_diamonds']), //变更数额
                        'account_diamonds' => $account_diamonds, //账户余额
                        'memo' => $pai_goods_info['name'] . '退还保证金', //备注
                        'create_time' => $time,
                        'create_date' => to_date($time, 'Y-m-d H:i:s'),
                        'create_time_ymd' => to_date($time, 'Y-m-d'),
                        'create_time_y' => to_date($time, 'Y'),
                        'create_time_m' => to_date($time, 'm'),
                        'create_time_d' => to_date($time, 'd'),
                        'type' => 1 //1 提交保证金
                    );
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "user_diamonds_log", $diamonds_log_data);
                    //写入用户日志
                    $data = array();
                    $data['diamonds'] = intval($v1['bz_diamonds']);
                    $param['type'] = 8; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
                    $log_msg = $pai_goods_info['name'] . '退还保证金'; //备注
                    account_log_com($data, intval($v1['user_id']), $log_msg, $param);
                }
            }
            //流拍房间内推送
            $ext = array();
            $ext['type'] = 27;
            $ext['room_id'] = intval($video_info['id']);
            $ext['pai_id'] = intval($v['pai_id']);
            $ext['post_id'] = intval($pai_goods_info['podcast_id']);
            $ext['out_type'] = 1;
            $ext['desc'] = "很遗憾，" . $pai_goods_info['name'] . "竞拍流拍";
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
            $ext['user'] = $user_redis->getRow_db(intval($info['podcast_id']), $fields);
            $ext['user']['user_id'] = intval($info['podcast_id']);
            $ext['user']['head_image'] = get_spec_image($ext['user']['head_image']);
            $ext['user']['nick_name'] = ($ext['user']['nick_name']);
            //-------------------------
            $user_list_out = $GLOBALS['db']->getAll("select user_id,pai_status,order_id,order_status,pai_diamonds,order_time from " . DB_PREFIX . "pai_join where pai_id=" . intval($v['pai_id']) . " and pai_diamonds >0 ORDER BY pai_diamonds DESC limit 0,3");
            $ext['buyer'] = array();
            foreach ($user_list_out as $k1 => $v1) {
                $buyer_data = array();
                if (intval($v1['user_id']) > 0) {
                    $buyer_data = $user_redis->getRow_db(intval($v1['user_id']), $fields);
                    $buyer_data['user_id'] = intval($v1['user_id']);
                    $buyer_data['type'] = intval($v1['pai_status']);
                    $buyer_data['head_image'] = get_spec_image($buyer_data['head_image']);
                    $buyer_data['nick_name'] = ($buyer_data['nick_name']);
                    if ($buyer_data['type'] == 1) {
                        $buyer_data['left_time'] = $v1['order_time'] + MAX_PAI_PAY_TIME - NOW_TIME;
                        //$order_sn=$GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."goods_order where id=".intval($v1['order_id'])." ");
                        //$buyer_data['pay_url']=SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pai_user&act=order&order_sn='.$order_sn;
                    } else {
                        $buyer_data['left_time'] = 0;
                    }
                    $buyer_data['pai_diamonds'] = intval($v1['pai_diamonds']);
                }
                $ext['buyer'][] = $buyer_data;
            }
            //-----------------------
            #构造高级接口所需参数
            $tim_data = array();
            $tim_data['ext'] = $ext;
            $tim_data['podcast_id'] = strim($pai_goods_info['podcast_id']);
            $tim_data['group_id'] = strim($video_info['group_id']);
            get_tim_api($tim_data);
            /*
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
        'MsgType' => 'TIMCustomElem',       //自定义类型
        'MsgContent' => array(
        'Data' => json_encode($ext),
        'Desc' => '',
        )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        array_push($msg_content, $msg_content_elem);
        fanwe_require(APP_ROOT_PATH.'system/tim/TimApi.php');
        $api = createTimAPI();
        $ret = $api->group_send_group_msg2(intval($pai_goods_info['podcast_id']), intval($video_info['group_id']), $msg_content);
         */
        }
    }
}
/*查看拍卖超时，并结束拍卖，进入支付，或者 流拍状态
 * pai_goods 当status=0，create_time+pai_time+now_yanshi*pai_yanshi <now_time
 * (1) last_user_id 为空，则表示无人竞拍，则修改状态 当status=2，同时推送消息 表示流拍
 * (2)last_user_id 不为空 ，第一名 进行下单，更新 pai_join 中 pai_status=1 ，第二名和第三名  pai_status=2，同时推送消息 竞拍成功
 */
function deal_pai_timeout()
{
    if (PAI_YANCHI_MODULE == 0) {
        $pai_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "pai_goods where status=0  and is_delete=0 and  create_time+pai_time*3600+now_yanshi*pai_yanshi*60-1-1 <" . NOW_TIME);
    } else {
        $pai_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "pai_goods where status=0  and is_delete=0 and  end_time-1 <" . NOW_TIME);
    }
    foreach ($pai_list as $k => $v) {
        $auth = '';
        if (isset($_REQUEST['auth'])) {
            $auth = $_REQUEST['auth'];
        }
        //中拍
        $data = array();
        $data['pai_id'] = intval($v['id']);
        $data['user_id'] = intval($v['last_user_id']);
        $data['auth'] = $auth;
        if (intval($v['last_user_id']) > 0) {
            $sql = "update " . DB_PREFIX . "pai_goods set status = 1 where id = " . intval($v['id']) . " and status=0 ";
            $GLOBALS['db']->query($sql);
            $result = deal_pai_do_order($data);
            if (count($result) > 0) {
                //更新排队状态
                $user_list = $GLOBALS['db']->getAll("select user_id from " . DB_PREFIX . "pai_join where pai_id=" . intval($v['id']) . " and pai_diamonds >0  ORDER BY pai_diamonds DESC limit 1,2");
                $user_array = array();
                foreach ($user_list as $k2 => $v2) {
                    $user_array[] = intval($v2['user_id']);
                }
                $sql = "update " . DB_PREFIX . "pai_join set pai_status = 2 where pai_id= " . $data['pai_id'] . " and user_id in (" . implode(",", $user_array) . ")  ";
                $GLOBALS['db']->query($sql);
                //发送排队消息
                if (count($user_array) > 0) {
                    $content = "您参与的竞拍：‘" . $v['name'] . "’ 正在结算排队中！";
                    FanweServiceCall("message", "send", array("send_type" => 'tip_towait', "user_ids" => $user_array, "content" => $content));
                }
                $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . intval($v['podcast_id']) . "  and live_in=1");
                //房间内推送
                $ext = array();
                $ext['type'] = 25;
                $ext['room_id'] = intval($video_info['id']);
                $ext['pai_id'] = $data['pai_id'];
                $ext['post_id'] = intval($v['podcast_id']);
                $ext['desc'] = '';
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
                //-------------------------
                $ext['buyer'] = array();
                $user_list_all = $GLOBALS['db']->getAll("select user_id,pai_status,order_id,order_status,pai_diamonds,order_time from " . DB_PREFIX . "pai_join where pai_id=" . intval($v['id']) . " and pai_diamonds >0 ORDER BY pai_diamonds DESC limit 0,3");
                foreach ($user_list_all as $k1 => $v1) {
                    $buyer_data = array();
                    if (intval($v1['user_id']) > 0) {
                        $buyer_data = $user_redis->getRow_db(intval($v1['user_id']), $fields);
                        $buyer_data['user_id'] = intval($v1['user_id']);
                        $buyer_data['type'] = intval($v1['pai_status']);
                        $buyer_data['head_image'] = get_spec_image($buyer_data['head_image']);
                        $buyer_data['nick_name'] = ($buyer_data['nick_name']);
                        $buyer_data['user_name'] = ($buyer_data['user_name']);
                        if ($buyer_data['type'] == 1) {
                            $buyer_data['left_time'] = $v1['order_time'] + MAX_PAI_PAY_TIME - NOW_TIME;
                            $order_sn = $GLOBALS['db']->getOne("select order_sn from " . DB_PREFIX . "goods_order where id=" . intval($v1['order_id']) . " ");
                            //$buyer_data['pay_url']=SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pai_user&act=order&order_sn='.$order_sn;
                            $buyer_data['goods_name'] = $v['name'];
                            $buyer_data['order_sn'] = $order_sn;
                            if ($v['imgs'] != '') {
                                $v['imgs'] = json_decode($v['imgs']);
                                foreach ($v['imgs'] as $k2 => $v2) {
                                    /*if ($auth!='') {
                                    $buyer_data['goods_icon']=$auth.$v2;
                                    }else{
                                    $buyer_data['goods_icon']=get_domain().APP_ROOT.$v2;
                                    }*/
                                    $buyer_data['goods_icon'] = get_spec_image($v2);
                                    break;
                                }
                            } else {
                                $buyer_data['goods_icon'] = "";
                            }
                            //$ext['desc'] = '恭喜用户'.$buyer_data['nick_name'].'出价'.intval($v1['pai_diamonds']).'成功拍得'.$v['name'];
                            $ext['desc'] = '出价' . intval($v1['pai_diamonds']) . '成功拍得' . $v['name'];
                            $ext['user'] = $buyer_data;
                        } else {
                            $buyer_data['left_time'] = 0;
                        }
                        $buyer_data['pai_diamonds'] = intval($v1['pai_diamonds']);
                    }
                    $ext['buyer'][] = $buyer_data;
                }
                //-------------------------
                #构造高级接口所需参数
                $tim_data = array();
                $tim_data['ext'] = $ext;
                $tim_data['podcast_id'] = strim($v['podcast_id']);
                $tim_data['group_id'] = strim($video_info['group_id']);
                get_tim_api($tim_data);
                /*
            $msg_content = array();
            //创建array 所需元素
            $msg_content_elem = array(
            'MsgType' => 'TIMCustomElem',       //自定义类型
            'MsgContent' => array(
            'Data' => json_encode($ext),
            'Desc' => '',
            )
            );
            //将创建的元素$msg_content_elem, 加入array $msg_content
            array_push($msg_content, $msg_content_elem);
            fanwe_require(APP_ROOT_PATH.'system/tim/TimApi.php');
            $api = createTimAPI();
            $ret = $api->group_send_group_msg2(intval($v['podcast_id']), intval($video_info['group_id']), $msg_content);
             */
            }
        } else {
            //流拍
            $sql = "update " . DB_PREFIX . "pai_goods set status = 2 where id = " . intval($v['id']) . " and status=0 ";
            $GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "video set pai_id = 0 where user_id=" . intval($v['podcast_id']) . " ";
            $GLOBALS['db']->query($sql);
            $pai_goods_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "pai_goods where id=" . intval($v['id']) . " ");
            $sql = "update " . DB_PREFIX . "goods set inventory=inventory+1 where id=" . $pai_goods_info['goods_id'] . "";
            $GLOBALS['db']->query($sql); //流拍，减去的库存增加回去
            //退回保证金
            $pai_id = intval($v['id']);
            $time = NOW_TIME;
            $user_list = $GLOBALS['db']->getAll("SELECT id,user_id,bz_diamonds,status,pai_status FROM " . DB_PREFIX . "pai_join WHERE pai_id=" . $pai_id);
            $user_ids = array();
            foreach ($user_list as $k1 => $v1) {
                $user_ids[] = $v1['user_id'];
                //退还保证金 bz_diamonds  不为超时即退保证金
                if (intval($v1['status']) == 0 && intval($v1['pai_status']) != 3) {
                    fanwe_require(APP_ROOT_PATH . '/mapi/lib/redis/BaseRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $sql = "update " . DB_PREFIX . "user set diamonds = diamonds + " . intval($v1['bz_diamonds']) . " where id = " . intval($v1['user_id']);
                    $GLOBALS['db']->query($sql);
                    user_deal_to_reids(array(intval($v1['user_id'])));
                    $account_diamonds = $user_redis->getOne_db(intval($v1['user_id']), 'diamonds');
                    $sql = "update " . DB_PREFIX . "pai_join set status = 1 where id=" . intval($v1['id']) . " ";
                    $GLOBALS['db']->query($sql);
                    //会员账户 秀豆变更日志表
                    $diamonds_log_data = array(
                        'pai_id' => $pai_id,
                        'user_id' => intval($v1['user_id']),
                        'diamonds' => intval($v1['bz_diamonds']), //变更数额
                        'account_diamonds' => $account_diamonds, //账户余额
                        'memo' => $pai_goods_info['name'] . '退还保证金', //备注
                        'create_time' => $time,
                        'create_date' => to_date($time, 'Y-m-d H:i:s'),
                        'create_time_ymd' => to_date($time, 'Y-m-d'),
                        'create_time_y' => to_date($time, 'Y'),
                        'create_time_m' => to_date($time, 'm'),
                        'create_time_d' => to_date($time, 'd'),
                        'type' => 1 //1 提交保证金
                    );
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "user_diamonds_log", $diamonds_log_data);
                    //写入用户日志
                    $data = array();
                    $data['diamonds'] = intval($v1['bz_diamonds']);
                    $param['type'] = 8; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
                    $log_msg = $pai_goods_info['name'] . '退还保证金'; //备注
                    account_log_com($data, intval($v1['user_id']), $log_msg, $param);
                } else if (intval($v1['status']) == 0 && intval($v1['pai_status']) == 3) {
                    $sql = "update " . DB_PREFIX . "pai_join set status = 2 where id=" . intval($v1['id']) . " ";
                    $GLOBALS['db']->query($sql);
                }
            }
            $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . intval($v['podcast_id']) . "  and live_in=1");
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video_data = array();
            $video_data['pai_id'] = 0;
            $re = $video_redis->update_db(intval($video_info['id']), $video_data);
            //流拍房间内推送
            $ext = array();
            $ext['type'] = 27;
            $ext['room_id'] = intval($video_info['id']);
            $ext['pai_id'] = $data['pai_id'];
            $ext['post_id'] = intval($v['podcast_id']);
            $ext['out_type'] = 0;
            $ext['desc'] = "很遗憾，" . $v['name'] . "竞拍流拍";
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
            $ext['user'] = $user_redis->getRow_db(intval($v['podcast_id']), $fields);
            $ext['user']['user_id'] = intval($v['podcast_id']);
            $ext['user']['head_image'] = get_spec_image($ext['user']['head_image']);
            $ext['user']['nick_name'] = ($ext['user']['nick_name']);
            #构造高级接口所需参数
            $tim_data = array();
            $tim_data['ext'] = $ext;
            $tim_data['podcast_id'] = strim($v['podcast_id']);
            $tim_data['group_id'] = strim($video_info['group_id']);
            get_tim_api($tim_data);
            /*
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
        'MsgType' => 'TIMCustomElem',       //自定义类型
        'MsgContent' => array(
        'Data' => json_encode($ext),
        'Desc' => '',
        )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        array_push($msg_content, $msg_content_elem);
        fanwe_require(APP_ROOT_PATH.'system/tim/TimApi.php');
        $api = createTimAPI();
        $ret = $api->group_send_group_msg2(intval($v['podcast_id']), intval($video_info['group_id']), $msg_content);
         */
        }
    }
}
/*
 * 拍卖订单 下单
 * $data = array("pai_id"=>$pai_id,"user_id"=>$user_id);
 */
function deal_pai_do_order($data)
{
    return FanweServiceCall("pai_podcast", "create_order", $data);
}
/*
 * 拍卖订单 状态修改（暂行）
 * 查询所有status位2.3的订单
 * 2超时，未发货 超过约会时间，进入5，退款流程
 * 3超时，未收货 超过时间7天自动更新确认
 *
 *
 */
function deal_pai_order_status()
{
    $order_list = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "goods_order where  order_source='local' and order_type='pai' and  order_status=3 and refund_platform=0 and order_status_time-1+" . MAX_PODCAST_CONFIRM_TIME . " <" . NOW_TIME);
    if ($order_list) {
        //$sql = "UPDATE ".DB_PREFIX."goods_order  SET  order_status=7 ,order_status_time=".NOW_TIME."  WHERE order_source='local' and order_type='pai' and  order_status=3 and order_status_time+".MAX_USER_CONFIRM_TIME." <".NOW_TIME;
        //$GLOBALS['db']->query($sql);
        foreach ($order_list as $k => $v) {
            $sql = "UPDATE " . DB_PREFIX . "goods_order  SET  order_status=7 ,order_status_time=" . NOW_TIME . "  WHERE id=" . intval($v['id']);
            $GLOBALS['db']->query($sql);
            $to_podcast_id = intval($v['podcast_id']);
            $podcast_ticket = intval($v['podcast_ticket']);
            $pai_id = intval($v['pai_id']);
            $user_id = intval($v['viewer_id']);
            $sql = "update " . DB_PREFIX . "pai_goods set order_status = 7 where id=" . $pai_id . " ";
            $GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "pai_join set order_status = 7 where user_id=" . $user_id . " and pai_id=" . $pai_id . " ";
            $GLOBALS['db']->query($sql);
            $pai_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "pai_goods where id=" . $pai_id);
            //主播获得竞拍收益
            fanwe_require(APP_ROOT_PATH . '/mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $podcast_ticket . " where id = " . $to_podcast_id;
            $GLOBALS['db']->query($sql);
            user_deal_to_reids(array($to_podcast_id));
            //写入用户日志
            $data = array();
            $data['ticket'] = $podcast_ticket;
            $param['type'] = 8; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
            $log_msg = $pai_info['name'] . '竞拍收益'; //备注
            account_log_com($data, intval($to_podcast_id), $log_msg, $param);
            //分销功能 计算抽成
            if (defined('OPEN_DISTRIBUTION') && OPEN_DISTRIBUTION == 1) {
                $total_ticket = $podcast_ticket;
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                $table = DB_PREFIX . 'distribution_log';
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $to_user_id = $user_redis->getOne_db($user_id, 'p_user_id'); //用户总的：秀票数
                $ticket = 0;
                $result = 0;
                if (intval($to_user_id) > 0 && intval($m_config['distribution']) == 1 && $user_id > 0) {
                    $ticket = round($m_config['distribution_rate'] * 0.01 * $total_ticket, 1);
                    $sql = "select id from " . $table . " where to_user_id = " . $to_user_id . " and from_user_id = " . $user_id;
                    $distribution = $GLOBALS['db']->getOne($sql);
                    $distribution_id = $distribution['id'];
                    if (intval($distribution_id) > 0) {
                        $sql = "update " . $table . " set ticket = ticket + " . $ticket . " where id = " . $distribution_id;
                        $GLOBALS['db']->query($sql);
                        if ($GLOBALS['db']->affected_rows()) {
                            $result = 1;
                        }
                    } else {
                        //插入:分销日志
                        $video_prop = array();
                        $video_prop['from_user_id'] = $user_id;
                        $video_prop['to_user_id'] = $to_user_id;
                        $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                        $video_prop['ticket'] = $ticket;
                        $video_prop['create_time'] = NOW_TIME;
                        $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                        $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                        $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
                        //将日志写入mysql表中
                        $field_arr = array('from_user_id', 'to_user_id', 'create_date', 'ticket', 'create_time', 'create_ym', 'create_d', 'create_w');
                        $fields = implode(",", $field_arr);
                        $valus = implode(",", $video_prop);
                        $sql = "insert into " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                        $GLOBALS['db']->query($sql);
                        $result = $GLOBALS['db']->insert_id();
                    }
                    if (intval($result) > 0) {
                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $ticket . " where id = " . $to_user_id;
                        $GLOBALS['db']->query($sql);
                    }
                }
            }
            $user_ids = array();
            $user_ids[] = $to_podcast_id;
            $user_ids[] = intval($v['viewer_id']);
            $info = $GLOBALS['db']->getRow("SELECT pg.* FROM " . DB_PREFIX . "pai_goods as pg   WHERE pg.id=" . $pai_id);
            $content = "竞拍：‘" . $info['name'] . "’ 已自动确认完成！";
            $rs = FanweServiceCall("message", "send", array("send_type" => 'viewer_to_over_tryst', "user_ids" => $user_ids, "content" => $content));
        }
    }
    $order_list_2 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "goods_order where  order_source='local' and order_type='pai' and  order_status=2  and refund_platform=0 and order_status_time-1+" . MAX_USER_CONFIRM_TIME . " <" . NOW_TIME);
    if ($order_list_2) {
        //未确认自动退款
        /*
        foreach($order_list_2 as $k=>$v){
        $sql = "UPDATE ".DB_PREFIX."goods_order  SET  order_status=5 ,order_status_time=".NOW_TIME." ,refund_buyer_status=1   WHERE id=".intval($v['id']);
        $GLOBALS['db']->query($sql);
        $pai_id=intval($v['pai_id']);
        $user_id=intval($v['viewer_id']);
        $sql = "update ".DB_PREFIX."pai_goods set order_status = 5 where id=".$pai_id." ";
        $GLOBALS['db']->query($sql);
        $sql = "update ".DB_PREFIX."pai_join set order_status = 5 where user_id=".$user_id." and pai_id=".$pai_id." ";
        $GLOBALS['db']->query($sql);
        $user_ids=array();
        $user_ids[]=intval($v['podcast_id']);
        $user_ids[]=intval($v['viewer_id']);
        $info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg   WHERE pg.id=".$pai_id);
        $content="竞拍：‘".$info['name']."’ 进入退款流程！";
        $rs=FanweServiceCall("message","send",array("send_type"=>'to_refund',"user_ids"=>$user_ids,"content"=>$content));
        }
         */
        //未确认自动确认
        foreach ($order_list_2 as $k => $v) {
            $sql = "UPDATE " . DB_PREFIX . "goods_order  SET  order_status=3 ,order_status_time=" . NOW_TIME . " ,refund_buyer_status=1   WHERE id=" . intval($v['id']);
            $GLOBALS['db']->query($sql);
            $pai_id = intval($v['pai_id']);
            $user_id = intval($v['viewer_id']);
            $sql = "update " . DB_PREFIX . "pai_goods set order_status = 3 where id=" . $pai_id . " ";
            $GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "pai_join set order_status = 3 where user_id=" . $user_id . " and pai_id=" . $pai_id . " ";
            $GLOBALS['db']->query($sql);
            $user_ids = array();
            $user_ids[] = intval($v['viewer_id']);
            $info = $GLOBALS['db']->getRow("SELECT pg.* FROM " . DB_PREFIX . "pai_goods as pg   WHERE pg.id=" . $pai_id);
            $content = "主播‘" . $info['podcast_name'] . "’已确认完成‘" . $info['name'] . "’";
            $rs = FanweServiceCall("message", "send", array("send_type" => 'podcast_to_over_tryst', "user_ids" => $user_ids, "content" => $content));
        }
    }
    $order_list_3 = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "goods_order where  order_source='local' and order_type='shop' and  order_status=1  and refund_platform=0 and create_time-1+" . MAX_PAI_PAY_TIME . " <" . NOW_TIME);
    if ($order_list_3) {
        foreach ($order_list_3 as $key => $value) {
            if ($value['is_p'] == 0) {
                $sql = "UPDATE " . DB_PREFIX . "goods SET inventory=inventory+" . intval($value['number']) . " WHERE id=" . intval($value['goods_id']);
                $GLOBALS['db']->query($sql); //增加库存
            }
            $sql = "UPDATE " . DB_PREFIX . "goods_order SET order_status=6 ,order_status_time=" . NOW_TIME . " WHERE id=" . intval($value['id']);
            $GLOBALS['db']->query($sql);
            $user_ids = array();
            $user_ids[] = intval($value['viewer_id']);
            $info = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "goods WHERE id=" . intval($value['goods_id']));
            $content = "您购买的商品" . $info['name'] . "超时未付款，已自动取消订单.";
            $rs = FanweServiceCall("message", "send", array("send_type" => 'podcast_to_over_tryst', "user_ids" => $user_ids, "content" => $content));
        }
    }
}
//-------------家族收益处理-------------
/**
 * @ param int $user_id 主播ID
 * @ param int $family_id 家族ID
 * @ param float $family_income 家族收益
 * @ param int $video_id 直播ID
 */
function family_receipts($user_id, $family_id, $family_income, $video_id = 0)
{
    $sql = "select f.id as family_id,fj.id as join_id,f.status as family_status,f.user_id from " . DB_PREFIX . "family_join as fj left join " . DB_PREFIX . "family as f on f.id = fj.family_id where fj.status =1 and fj.user_id = " . $user_id . " and fj.family_id =" . $family_id;
    $family_info = $GLOBALS['db']->getRow($sql);
    if ($family_info['join_id'] > 0 && $family_info['family_status'] == 1 && $family_info['family_id'] > 0) {
        if ($family_income) {
            //增加：家族收益
            $sql = "update " . DB_PREFIX . "family set contribution = contribution + " . $family_income . " where id = " . $family_id;
            $GLOBALS['db']->query($sql);
            //将家族收益汇入家族长
            $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $family_income . " where id = " . $family_info['user_id'];
            $status = $GLOBALS['db']->query($sql);
            user_deal_to_reids(array($family_info['user_id']));
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            if (intval($m_config['family_profit_platform']) == 0) {
                $sql = "update " . DB_PREFIX . "user set refund_ticket = refund_ticket + " . $family_income . " where id = " . $user_id . " and ticket > refund_ticket + " . $family_income;
                $GLOBALS['db']->query($sql);
                user_deal_to_reids(array($user_id));
                if ($status) {
                    //写入用户日志
                    $data = array();
                    $data['ticket'] = intval($family_income);
                    $data['video_id'] = intval($video_id) > 0 ? $video_id : 0;
                    $param['type'] = 4; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4 扣除家族收益
                    $log_msg = '扣除家族收益' . $data['ticket'] . '秀票';
                    account_log_com($data, $user_id, $log_msg, $param);
                }
            }
            if ($status) {
                $log_msg = '收取家族收益' . $data['ticket'] . '秀票';
                account_log_com($data, $family_info['user_id'], $log_msg, $param);
            }
            return true;
        }
        return false;
    } else {
        return false;
    }
}
//-------------商城相关-------------
//第三方商城接口
function third_interface($user_id, $url, $args = array())
{
    fanwe_require(APP_ROOT_PATH . 'system/saas/SAASAPIClient.php');
    $appid = FANWE_APP_ID_YM;
    $appsecret = FANWE_AES_KEY_YM;
    $client = new SAASAPIClient($appid, $appsecret);
    $user_info = $GLOBALS['db']->getRow("SELECT id,mobile,apns_code,device_type FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
    // 设置头部参数
    $head_args = array();
    $head_args['userId'] = $user_info['id'];
    $head_args['mobile'] = $user_info['mobile'];
    /*$client_args = array();
    $client_args['ios_uuid']='';
    $client_args['android_imei']='';
    if ($user_info['device_type']==1) {
    $client_args['android_imei']=$user_info['apns_code'];
    }elseif ($user_info['device_type']==2) {
    $client_args['ios_uuid']=$user_info['apns_code'];
    }
    $client_args['app_terminal']=$user_info['device_type'];
    $client_args['app_channel']='';
    $head_args['client']=$client_args;*/
    // 设置请求参数（根据不同业务需要设置）
    $args = array_merge($head_args, $args);
    $ret = $client->invoke($url, $args);
    return $ret;
}
//o2o商城
function third_o2o_mall($url, $args = array())
{
    fanwe_require(APP_ROOT_PATH . 'system/saas/SAASAPIClient.php');
    $appid = FANWE_APP_ID_YM;
    $appsecret = FANWE_AES_KEY_YM;
    $client = new SAASAPIClient($appid, $appsecret);
    $ret = $client->invoke($url, $args);
    return $ret;
}
//第三方商城接口----竞拍商品数量
function good_number($user_id)
{
    $ret = third_interface($user_id, 'http://gw1.yimile.cc/V1/Commodity.json?action=GetUserDistributionCommodityQuantity');
    $data = array();
    if ($ret['code'] == 0) {
        $data = $ret['data'];
        return $data['commodityQuantity'];
    } else {
        return 0;
    }
}
//h5链接
function go_h5($user_id, $url, $args = array(), $type = 0)
{
    $user_info = $GLOBALS['db']->getRow("SELECT id,mobile,apns_code,device_type FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
    // 设置头部参数
    $head_args = array();
    $head_args['userId'] = $user_info['id'];
    $head_args['mobile'] = $user_info['mobile'];
    // 设置请求参数（根据不同业务需要设置）
    $args = array_merge($head_args, $args);
    fanwe_require(APP_ROOT_PATH . 'system/saas/SAASAPIClient.php');
    $appid = FANWE_APP_ID_YM;
    $appsecret = FANWE_AES_KEY_YM;
    $client = new SAASAPIClient($appid, $appsecret);
    if ($type == 0) {
        $url = $url . '?_saas_params=' . base64_encode(json_encode($client->makeRequestParameters($args)));
    } else {
        $url = $url . '&_saas_params=' . base64_encode(json_encode($client->makeRequestParameters($args)));
    }
    return $url;
}
//第三方商城--竟拍管理订单详情接口
function auction_order_detail($order_sn)
{
    $root = array();
    $user_id = intval($GLOBALS['user_info']['id']);
    if ($user_id == 0) {
        $root['status'] = 10007;
        $root['error'] = "请先登录";
        api_ajax_return($root);
    }
    $head_args['orderNo'] = $order_sn;
    $ret = third_interface($user_id, 'http://gw1.yimile.cc/V1/Order.json?action=AuctionOrderDetail', $head_args);
    if ($ret['code'] == 0) {
        $root['status'] = 1;
        if ($ret['data']['orderInfo']['consignDate'] != '') {
            $root['time'] = $ret['data']['orderInfo']['consignDate'];
        } else {
            $root['time'] = 0;
        }
        $express = array();
        if ($ret['data']['express'] != '') {
            $express['express_no'] = $ret['data']['express']['expressNo'];
            $express['express_detail'] = $ret['data']['express']['expressDetail'];
            $express['express_time'] = $ret['data']['express']['expressTime'];
        }
        $root['express'] = $express;
        $shopinfo = array();
        if ($ret['data']['shopInfo'] != '') {
            $shopinfo['shop_id'] = $ret['data']['shopInfo']['shopId'];
            $shopinfo['shop_name'] = $ret['data']['shopInfo']['shopName'];
            $shopinfo['shop_qq'] = $ret['data']['shopInfo']['shopQQ'];
        }
        $root['shopinfo'] = $shopinfo;
    } else {
        $root['error'] = "获取失败";
    }
    return $root;
}
//第三方商城----竟拍保证金获取用户地址
function get_user_addressdetail($user_id)
{
    // 调用服务
    $ret = third_interface($user_id, 'http://gw1.yimile.cc/V1/User.json?action=GetUserAddressDetail');
    $taxe = array();
    if ($ret['code'] == 0) {
        $taxe['id'] = $ret['data']['userAddressId'];
        $taxe['user_id'] = $user_id;
        $taxe['consignee'] = $ret['data']['consignee'];
        $taxe['consignee_mobile'] = $ret['data']['mobile'];
        if ($ret['data']['regionAddress'] != '') {
            $sheng = explode("省", $ret['data']['regionAddress']);
            if ($sheng[1] == '') {
                $shi = explode("市", $sheng[0]);
                $qu = explode("区", $shi[1]);
            } else {
                $shi = explode("市", $sheng[1]);
                $qu = explode("区", $shi[1]);
            }
            if ($sheng[1] == '') {
                $taxe['consignee_district'] = array('province' => '', 'city' => $shi[0] . '市', 'area' => $qu[0] . '区');
            } else {
                $taxe['consignee_district'] = array('province' => $sheng[0] . '省', 'city' => $shi[0] . '市', 'area' => $qu[0] . '区');
            }
        } else {
            $taxe['consignee_district'] = array('province' => '', 'city' => '', 'area' => '');
        }
        $taxe['consignee_address'] = $ret['data']['address'];
        $taxe['is_default'] = 0;
        $taxe['create_time'] = 0;
    }
    return $taxe;
}
//第三方商城--竟拍订单提交
function create_auction_order($user_id, $goods_id, $nick_name, $zbuser_id, $zbmobile)
{
    $root = array();
    $head_args['commodityId'] = $goods_id;
    $head_args['nickName'] = $nick_name;
    $head_args['zbUserId'] = $zbuser_id;
    $head_args['zbMobile'] = $zbmobile;
    $ret = third_interface($user_id, 'http://gw1.yimile.cc/V1/Order.json?action=CreateAuctionOrder', $head_args);
    if ($ret['code'] == 0) {
        $root['status'] = 1;
        $root['orderNo'] = $ret['data']['orderNo'];
        $root['totalPayPrice'] = $ret['data']['totalPayPrice'];
    } else {
        $root['error'] = "获取失败";
    }
    return $root;
}
function family_receipts1($video_id, $user_id, $video)
{
    $video_id = intval($video_id);
    $user_id = intval($user_id);
    if ($video_id == 0 || $user_id == 0) {
        return;
    }
    $family_id = $GLOBALS['db']->getOne("select family_id from " . DB_PREFIX . "user where id = " . $user_id, true, true);
    if ($family_id > 0) {
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $family_income = 0;
        //判断是否扣除过
        $user_log_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_log where user_id = " . $user_id . " and video_id=" . intval($video_id));
        if ($m_config['profit_ratio'] > 0 && intval($user_log_id) == 0) {
            $family_income = intval(intval($video['vote_number']) * 0.01 * $m_config['profit_ratio']);
            $result = family_receipts($user_id, $family_id, $family_income, $video_id);
            if ($result) {
                $video['vote_number'] = $video['vote_number'] - $family_income;
            }
        }
        $family_score = $family_income;
        //合并贡献收益 判断，用户当前等级积分
        $cte = floatval($m_config['contribution_to_experience']);
        if ($cte > 0) {
            $family_score += $family_income * $cte;
        }
        $video_time = (NOW_TIME - $video["begin_time"]);
        //合并直播时长 判断，用户当前等级积分
        $vte = floatval($m_config['videotime_to_experience']);
        if ($vte > 0) {
            $family_score += $video_time * $vte;
        }
        $sql = "update " . DB_PREFIX . "family set video_time = video_time + " . $video_time . ",score = score + " . $family_score . " where status=1 and id = " . $family_id;
        $GLOBALS['db']->query($sql);
        $family_info = $GLOBALS['db']->getRow("SELECT id as family_id,family_level,video_time,score FROM " . DB_PREFIX . "family WHERE id=" . $family_id . " and status=1", true, true);
        family_level_syn($family_info);
    }
}
//-------------公会无抽成模式---------------
/**
 * @param $video 直播结束相关信息
 * @return bool
 */
function society_receipts($video)
{
    if (intval($video['vote_number']) <= 0) {
        return false;
    }
    //公会的收益
    $society_income = intval($video['vote_number']);
    $user_info = $GLOBALS['db']->getRow("select society_id,society_settlement_type,society_chieftain from " . DB_PREFIX . "user where id=" . $video['user_id']);
    $society_info = $GLOBALS['db']->getRow("select user_id,status from " . DB_PREFIX . "society where id=" . $user_info['society_id']);
    //判断是否扣除过
    $user_log_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_log where type=10 and user_id = " . $video['user_id'] . " and video_id=" . intval($video['id']));
    if ($user_info['society_id'] > 0 && $society_info['status'] == 1 && intval($user_log_id) == 0) {
        //主播的收益加到公会中
        $pInTrans = $GLOBALS['db']->StartTrans();
        try
        {
            //收益添加到公会收益中
            $GLOBALS['db']->query("update " . DB_PREFIX . "society set chairman_earnings=chairman_earnings+" . $society_income . " where id=" . $user_info['society_id']);
            if ($user_info['society_chieftain'] == 0) {
                //排除会长
                //将用户上交的公会的秀票计入已提现
                $sql = "update " . DB_PREFIX . "user set refund_ticket = refund_ticket + " . $society_income . ",society_ticket=society_ticket+" . $society_income . " where id = " . $video['user_id'] . " and ticket >=" . $society_income;
                $status = $GLOBALS['db']->query($sql);
                user_deal_to_reids(array($video['user_id']));
            } else {
                $status = true;
            }
            if ($status) {
                if ($user_info['society_chieftain'] == 0) {
                    //排除会长
                    //将收益汇入公会长
                    $sql_chieftain = "update " . DB_PREFIX . "user set ticket = ticket + " . $society_income . " where id = " . $society_info['user_id'];
                    $GLOBALS['db']->query($sql_chieftain);
                    user_deal_to_reids(array($society_info['user_id']));
                    //写入会长日志
                    earnings_log($society_income, $user_info['society_id'], $video['user_id'], 10, '获取收益：公会成员' . $video['user_id'] . '贡献' . $society_income . '秀票', $society_info['user_id'], $video['id']);
                }
                if ($user_info['society_chieftain']) {
                    $contribution_id = $society_info['user_id'];
                } else {
                    $contribution_id = 0;
                }
                //写入用户日志
                earnings_log($society_income, $user_info['society_id'], $contribution_id, 10, '直播获取收益' . $society_income . '秀票', $video['user_id'], $video['id']);
                //公会等级积分的写入
                society_level_syn($society_income, $video["begin_time"], $user_info['society_id']);
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                //记录到公会主播收益表
                $society_data = array();
                $society_earning_id = $GLOBALS['db']->getOne('select id from ' . DB_PREFIX . "society_earning where video_id=" . $video['id']); //是否已经记录过
                if (!$society_earning_id && $user_info['society_id'] > 0) {
                    //收益还未记录且主播是公会成员
                    $society_data['video_id'] = $video['id'];
                    $society_data['user_id'] = $video['user_id'];
                    $society_data['vote_number'] = $video['vote_number'];
                    $society_data['begin_time'] = $video['begin_time'];
                    $society_data['end_time'] = $video['end_time'] ? $video['end_time'] : NOW_TIME;
                    $society_data['timelen'] = $society_data['end_time'] - $society_data['begin_time'];
                    $society_data['society_id'] = $user_info['society_id'];
                    $society_data['end_date'] = to_date($society_data['end_time'], 'Y-m-d');
                    $society_data['end_Y'] = to_date($society_data['end_time'], 'Y');
                    $society_data['end_m'] = to_date($society_data['end_time'], 'm');
                    $society_data['end_d'] = to_date($society_data['end_time'], 'd');
                    $society_data['end_w'] = to_date($society_data['end_time'], 'W');
                    $society_data['society_settlement_type'] = $m_config['society_pattern'];
                    $society_data['society_number'] = $society_income;
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "society_earning", $society_data);
                }
            }
            //提交事务
            $GLOBALS['db']->Commit($pInTrans);
        } catch (Exception $e) {
            //异常回滚
            $GLOBALS['db']->Rollback($pInTrans);
            //写入用户日志
            earnings_log($society_income, $user_info['society_id'], $contribution_id, 10, '直播获取收益' . $society_income . '秀票，失败', $video['user_id'], $video['id']);
            //写入会长日志
            earnings_log($society_income, $user_info['society_id'], $video['user_id'], 10, '获取收益：公会成员' . $video['user_id'] . '贡献' . $society_income . '秀票，失败', $society_info['user_id'], $video['id']);
        }
        //-----事务结束-----
    }
    return;
}

/**
 * [independent_society_receipts 独立模式的公会]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-09-21T19:46:23+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $video [description]
 * @return    [type] [description]
 */
function independent_society_receipts($video)
{
    if ((intval($video['society_ticket']) <= 0) && (intval($video['vote_number']) <= 0)) {
        return false;
    }
    //公会的收益
    $society_income = intval($video['society_ticket']);
    $vote_number = intval($video['vote_number']);
    $user_info = $GLOBALS['db']->getRow("SELECT society_id,society_settlement_type,society_chieftain from " . DB_PREFIX . "user where id=" . $video['user_id']);
    $society_info = $GLOBALS['db']->getRow("SELECT user_id,status from " . DB_PREFIX . "society where id=" . $user_info['society_id']);
    //判断是否扣除过
    if ($user_info['society_id'] > 0 && $society_info['status'] == 1) {
        //主播的收益加到公会中
        $pInTrans = $GLOBALS['db']->StartTrans();
        try
        {
            //收益添加到公会收益中
            $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "society SET new_chairman_earnings=new_chairman_earnings+" . $society_income . " WHERE id=" . $user_info['society_id']);
            // 主播不用上交
            // if ($user_info['society_chieftain'] == 0) {
            //     //排除会长
            //     //将用户上交的公会的秀票计入已提现
            //     $sql = "update " . DB_PREFIX . "user set refund_ticket = refund_ticket + " . $society_income . ",society_ticket=society_ticket+" . $society_income . " where id = " . $video['user_id'] . " and ticket >=" . $society_income;
            //     $status = $GLOBALS['db']->query($sql);
            //     user_deal_to_reids(array($video['user_id']));
            // } else {
            //     $status = true;
            // }
            // if ($status) {
            if ($user_info['society_chieftain'] == 0) {
                //排除会长
                //将收益汇入公会长
                $sql_chieftain = "update " . DB_PREFIX . "user set ticket = ticket + " . $society_income . " where id = " . $society_info['user_id'];
                $GLOBALS['db']->query($sql_chieftain);
                user_deal_to_reids(array($society_info['user_id']));
                //写入会长日志
                earnings_log($society_income, $user_info['society_id'], $video['user_id'], 10, '获取收益：公会成员' . $video['user_id'] . '直播，平台发放' . $society_income . '秀票', $society_info['user_id'], $video['id']);
            }
            if ($user_info['society_chieftain']) {
                $contribution_id = $society_info['user_id'];
            } else {
                $contribution_id = 0;
            }
            //写入用户日志
            earnings_log($vote_number, $user_info['society_id'], $contribution_id, 8, '直播获取收益' . $vote_number . '秀票', $video['user_id'], $video['id']);
            //公会等级积分的写入
            society_level_syn($society_income, $video["begin_time"], $user_info['society_id']);
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            //记录到公会主播收益表
            $society_data = array();
            $society_earning_id = $GLOBALS['db']->getOne('select id from ' . DB_PREFIX . "society_earning where video_id=" . $video['id']); //是否已经记录过
            if (!$society_earning_id && $user_info['society_id'] > 0) {
                //收益还未记录且主播是公会成员
                $society_data['video_id'] = $video['id'];
                $society_data['user_id'] = $video['user_id'];
                $society_data['vote_number'] = $video['vote_number'];
                $society_data['begin_time'] = $video['begin_time'];
                $society_data['end_time'] = $video['end_time'] ? $video['end_time'] : NOW_TIME;
                $society_data['timelen'] = $society_data['end_time'] - $society_data['begin_time'];
                $society_data['society_id'] = $user_info['society_id'];
                $society_data['end_date'] = to_date($society_data['end_time'], 'Y-m-d');
                $society_data['end_Y'] = to_date($society_data['end_time'], 'Y');
                $society_data['end_m'] = to_date($society_data['end_time'], 'm');
                $society_data['end_d'] = to_date($society_data['end_time'], 'd');
                $society_data['end_w'] = to_date($society_data['end_time'], 'W');
                $society_data['society_settlement_type'] = $m_config['society_pattern'];
                $society_data['society_number'] = $society_income;
                $GLOBALS['db']->autoExecute(DB_PREFIX . "society_earning", $society_data);
            }
            // }
            //提交事务
            $GLOBALS['db']->Commit($pInTrans);
        } catch (Exception $e) {
            //异常回滚
            $GLOBALS['db']->Rollback($pInTrans);
            //写入用户日志
            earnings_log($vote_number, $user_info['society_id'], $contribution_id, 8, '直播获取收益' . $vote_number . '秀票，失败', $video['user_id'], $video['id']);
            //写入会长日志
            earnings_log($society_income, $user_info['society_id'], $video['user_id'], 10, '获取收益：公会成员' . $video['user_id'] . '直播，平台发放' . $society_income . '秀票，失败', $society_info['user_id'], $video['id']);
        }
        //-----事务结束-----
    }
    return;
}
//-------------平台收益,针对无公会人员-------------
/**
 * @ param int $user_id 主播ID
 * @ param float $m_config 配置信息
 * @ param int $video 直播信息
 * @ param int $video_id 直播ID
 */
function platform_receipts($user_id, $m_config, $video, $video_id = 0)
{
    $society_income = 0;
    $user_log_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_log where type<>7 and user_id = " . $user_id . " and video_id=" . intval($video_id));
    if ($m_config['society_platform_earnings'] >= 0 && intval($user_log_id) == 0 && $m_config['society_platform_earnings'] < 100 && $video['vote_number'] > 0) {
        //写入用户日志
        earnings_log($video['vote_number'], 0, 0, 11, '直播获取收益' . intval($video['vote_number']) . '秀票', $user_id, $video_id);
        if ($m_config['society_platform_earnings'] == 0) {
            //平台不抽成
            return true;
        }
        $society_income = intval(intval($video['vote_number']) * 0.01 * $m_config['society_platform_earnings']);
        //增加收益更新到用户上
        //$sql = "update " . DB_PREFIX . "user set ticket = ticket + ".$video['vote_number'].",refund_ticket = refund_ticket + " . $society_income . " where id = " .$user_id. " and ticket > refund_ticket + " . $society_income;
        $sql = "update " . DB_PREFIX . "user set refund_ticket = refund_ticket + " . $society_income . " where id = " . $user_id . " and ticket > refund_ticket + " . $society_income;
        $status = $GLOBALS['db']->query($sql);
        user_deal_to_reids(array($user_id));
        if ($status) {
            //写入用户日志
            earnings_log($society_income, 0, 0, 11, '扣除平台收益' . $society_income . '秀票', $user_id);
            return true;
        } else {
            return false;
        }
    }
}
//-------------公会有抽成模式，会长收益-------------
/**
 * @ param int $user_id 主播ID
 * @ param int $society_id 公会ID
 * @ param float $society_income 直播结束所得收益
 * @ param int $video_id 直播ID
 */
function society_receipts1($user_id, $society_id, $society_income, $vote_number, $video_id = 0)
{
    $sql = "select s.id as society_id,sa.id as apply_id,s.status as society_status,s.user_id from " . DB_PREFIX . "society_apply as sa left join " . DB_PREFIX . "society as s on s.id = sa.society_id where sa.status in(1,3) and sa.user_id = " . $user_id . " and sa.society_id =" . $society_id;
    $society_info = $GLOBALS['db']->getRow($sql);
    $society_chieftain = $GLOBALS['db']->getOne("select society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
    if ($society_info['apply_id'] > 0 && $society_info['society_status'] == 1 && $society_info['society_id'] > 0) {
        if ($society_income) {
            if ($society_chieftain == 0) {
                // 排除会长自己
                //增加：公会收益
                $sql = "update " . DB_PREFIX . "society set contribution = contribution + " . $society_income . " where id = " . $society_id;
                $GLOBALS['db']->query($sql);
                //将公会收益汇给会长
                $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $society_income . " where id = " . $society_info['user_id'];
                $status = $GLOBALS['db']->query($sql);
                user_deal_to_reids(array($society_info['user_id']));
            } else {
                //增加：公会收益
                $sql = "update " . DB_PREFIX . "society set contribution = contribution + " . $vote_number . " where id = " . $society_id;
                $GLOBALS['db']->query($sql);
            }
            if ($society_chieftain) {
                $contribution_id = $user_id;
            } else {
                $contribution_id = 0;
            }
            //收益情况写入用户日志
            earnings_log($vote_number, $society_id, $contribution_id, 8, '直播获取收益' . $vote_number . '秀票', $user_id, $video_id);
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            if (intval($m_config['society_profit_platform']) == 0 && $society_chieftain == 0) {
                //判断会长抽成是否由平台支付，0否1是
                $sql = "update " . DB_PREFIX . "user set refund_ticket = refund_ticket + " . $society_income . " where id = " . $user_id . " and ticket > refund_ticket + " . $society_income;
                $GLOBALS['db']->query($sql);
                user_deal_to_reids(array($user_id));
                //写入用户日志
                earnings_log($society_income, $society_id, 0, 8, '扣除公会长抽成' . $society_income . '秀票', $user_id, $video_id);
            }
            if ($society_chieftain == 0) {
                //写入公会长日志
                earnings_log($society_income, $society_id, $user_id, 8, '获取公会成员' . $user_id . '：' . $society_income . '秀票', $society_info['user_id'], $video_id);
            }
            return true;
        }
        return false;
    } else {
        return false;
    }
}
//-------------公会等级积分-------------
/**
 * @ param int $m_config 配置信息
 * @ param int $user_id 用户ID
 * @ param int $video_id 直播ID
 * @ param array $video 直播信息
 * @ param array $user_info 包含成员的 公会ID和会长标识
 */
function society_lv_jf($m_config, $user_id, $video_id, $video, $user_info)
{
    $society_income = 0;
    //判断是否扣除过
    $user_log_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_log where type=8 and user_id = " . $user_id . " and video_id=" . intval($video_id));
    if ($m_config['society_profit_ratio'] >= 0 && intval($user_log_id) == 0 && $m_config['society_profit_ratio'] < 100) {
        if ($m_config['society_profit_ratio'] == 0) {
            //写入用户日志
            earnings_log($video['vote_number'], $user_info['society_id'], 0, 8, '直播获取收益' . intval($video['vote_number']) . '秀票', $user_id, $video_id);
            return true;
        }
        $society_income = intval(intval($video['vote_number']) * 0.01 * $m_config['society_profit_ratio']);
        $result = society_receipts1($user_id, $user_info['society_id'], $society_income, $video['vote_number'], $video_id);
        if ($result) {
            //公会等级积分的写入
            $society_score = intval($video['vote_number']);
            society_level_syn($video['vote_number'], $video['begin_time'], $user_info['society_id']);
            //记录到公会主播收益表
            $society_data = array();
            $society_earning_id = $GLOBALS['db']->getOne('select id from ' . DB_PREFIX . "society_earning where video_id=" . $video['id']); //是否已经记录过
            if (!$society_earning_id && $user_info['society_id'] > 0) {
//收益还未记录且主播是公会成员
                $society_data['video_id'] = $video['id'];
                $society_data['user_id'] = $video['user_id'];
                $society_data['vote_number'] = $video['vote_number'];
                $society_data['begin_time'] = $video['begin_time'];
                $society_data['end_time'] = $video['end_time'] ? $video['end_time'] : NOW_TIME;
                $society_data['timelen'] = $society_data['end_time'] - $society_data['begin_time'];
                $society_data['society_id'] = $user_info['society_id'];
                $society_data['end_date'] = to_date($society_data['end_time'], 'Y-m-d');
                $society_data['end_Y'] = to_date($society_data['end_time'], 'Y');
                $society_data['end_m'] = to_date($society_data['end_time'], 'm');
                $society_data['end_d'] = to_date($society_data['end_time'], 'd');
                $society_data['end_w'] = to_date($society_data['end_time'], 'W');
                $society_data['society_settlement_type'] = $m_config['society_pattern'];
                $society_chieftain = $GLOBALS['db']->getOne("select society_chieftain from " . DB_PREFIX . "user where id=$user_id");
                if ($society_chieftain) {
                    $society_data['society_number'] = $society_score;
                } else {
                    $society_data['society_number'] = $society_income;
                }
                $GLOBALS['db']->autoExecute(DB_PREFIX . "society_earning", $society_data);
            }
        }
    }
}
//-------------公会等级积分写入-------------
/**
 * @ param int    $society_income 直播结束所得收益
 * @ param number $begin_time 直播时长
 * @ param int    $society_id 公会ID
 */
function society_level_syn($society_income, $begin_time, $society_id)
{
    $m_config = load_auto_cache("m_config"); //初始化手机端配置
    $society_score = 0;
    //合并贡献收益 判断，用户当前等级积分
    $cte = intval($m_config['society_lv_contribution']);
    if ($cte > 0 && $cte < 100) {
        $society_score += $society_income * $cte * 0.01;
    }
    if ($begin_time > 0) {
        //区分私信送礼物
        $video_time = (NOW_TIME - $begin_time);
        //合并直播时长 判断，用户当前等级积分
        $vte = intval($m_config['society_lv_videotime']);
        if ($vte > 0 && $vte < 100) {
            $society_score += $video_time * $vte * 0.01;
        }
    }
    if ($cte > 0 || ($begin_time > 0 && $vte > 0)) {
        //积分写入
        $GLOBALS['db']->query("update " . DB_PREFIX . "society set score=score+" . intval($society_score) . " where id=" . $society_id);
        $society_info = $GLOBALS['db']->getRow("SELECT id as society_id,society_level,score FROM " . DB_PREFIX . "society WHERE id=" . $society_id . " and status=1", true, true);
        //society_level_syn($society_info);
        $society_score = $society_info['score'];
        $society_level_info = load_auto_cache("society_level");
        $society_level = null;
        foreach ($society_level_info as $v) {
            // 两层关系即可
            unset($society_level['next_level']);
            $v['next_level'] = $society_level;
            $society_level = $v;
            if ($v['score'] <= intval($society_score)) {
                break;
            }
        }
        if ($society_level) {
            $GLOBALS['db']->query("update " . DB_PREFIX . "society set society_level = " . $society_level['level'] . " where id = " . $society_info['society_id']);
        }
        return $society_level;
    }
}
//判断用户是否参与众筹
function is_ordered($room_id, $user_id)
{
    $is_order = 0;
    $deal_id = $GLOBALS['db']->getOne("select deal_id from " . DB_PREFIX . "edu_video_info where video_id= " . $room_id);
    if ($deal_id) {
        $deal_order_count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "edu_deal_order where deal_id= " . $deal_id . " and order_status=1 and is_refund=0 and user_id =" . $user_id);
        if ($deal_order_count > 0) {
            $is_order = 1;
        }
    }
    return $is_order;
}
/**
 * 返回用户等级信息,当前经验和下一级所需经验
 * @param $user_id
 * @return mixed
 */
function get_level_up_grade($user_id)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
    $user_redis = new UserRedisService();
    $user_data = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
    user_leverl_syn($user_data);
    $level = $GLOBALS['db']->getRow("select ul.level as user_level, ul.score as l_score from " . DB_PREFIX . "user_level as ul  where ul.level=" . intval($user_data['user_level']));
    $m_config = load_auto_cache("m_config");
    $level['u_score'] = $user_data['score'] + floor($user_data['online_time'] * floatval($m_config['onlinetime_to_experience']));
    $level['up_score'] = $GLOBALS['db']->getOne("select score from " . DB_PREFIX . "user_level  where level=" . intval($user_data['user_level'] + 1));
    if (intval($level['up_score']) <= 0) {
        $l_up_score = $GLOBALS['db']->getOne("select score from " . DB_PREFIX . "user_level  where level>" . intval($user_data['user_level'] + 1));
        if ($l_up_score['score'] > 0) {
            $level['up_score'] = $l_up_score['score'];
        } else {
            $level['up_score'] = '满级';
        }
    }
    $level['u_score'] = intval($level['u_score']);
    $level['up_score'] = intval($level['up_score']);
    return $level;
}
/**
 * //暮橙定制: IM推送用户等级和经验信息
 * @param $user_id
 * @return bool
 */
function push_level_info($user_id)
{
    $m_config = load_auto_cache("m_config");
    $system_user_id = $m_config['tim_identifier']; //系统消息
    $level = get_level_up_grade($user_id);
    $level_info = array();
    $level_info['user_level'] = $level['user_level'];
    if ($level['up_score'] == '满级') {
        $level_info['score_msg'] = "升级还需0" . $m_config['ticket_name'];
    } else {
        $score = $level['up_score'] - $level['u_score'];
        $level_info['score_msg'] = "升级还需" . $score . $m_config['ticket_name'];
    }
    $ext2 = array();
    $ext2['type'] = 61; //上海暮橙定制: 用户送礼给主播,系统返回推送用户等级和升级经验信息
    $ext2['level_info'] = $level_info;
    $msg_content2 = array();
    //创建array 所需元素
    $msg_content_elem2 = array(
        'MsgType' => 'TIMCustomElem', //自定义类型
        'MsgContent' => array(
            'Data' => json_encode($ext2),
            'Desc' => ''
        )
    );
    //将创建的元素$msg_content_elem, 加入array $msg_content
    array_push($msg_content2, $msg_content_elem2);
    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $api = createTimAPI();
    $ret2 = $api->openim_send_msg2($system_user_id, (string) $user_id, $msg_content2);
    if ($ret2['ActionStatus'] != 'OK') {
        //二次处理
        $ret2 = $api->openim_send_msg2($system_user_id, (string) $user_id, $msg_content2);
    }
    if ($ret2['ActionStatus'] == 'OK') {
        return true;
    } else {
        return false;
    }
}
/**
 * //公会收益日志
 * @param int $ticket   秀票数
 * @param int $type     类型
 * @param string $log   日志内容
 * @param int $user_id  用户id
 * @param int $society_id 公会id
 * @param int $contribution_id 会长抽取收益的成员id
 * @param int $video_id 直播间id
 * */
function earnings_log($ticket, $society_id, $contribution_id, $type, $log, $user_id, $video_id = 0)
{
    $data = array();
    $data['ticket'] = $ticket;
    $data['society_id'] = $society_id;
    $data['contribution_id'] = $contribution_id; //会长记录
    $data['video_id'] = $video_id;
    $param['type'] = $type; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票  4 分享获得秀票 5 登录赠送积分 6 观看付费直播 7游戏收益8扣除公会收益9分销收益10公会长收益11平台收益 12公会操作13私信收入
    $log_msg = $log;
    account_log_com($data, $user_id, $log_msg, $param);
}
/**
 * 获取程序执行时间
 * @param int $type 0：初次执行时间 1：最终执行时间
 */
function get_runtime($pram, $type = 0)
{
    //get_runtime(array(__FILE__,__LINE__,__METHOD__),1);
    if ($type == 0) {
        global $get_runtime_t1;
        $get_runtime_t1 = microtime(true);
        log_file('文件名：' . $pram[0] . ',行数：' . $pram[1] . ',方法：' . $pram[2] . '，首次执行时间' . $get_runtime_t1 . '秒', 'run_time');
    } else {
        $get_runtime_t1 = $GLOBALS['get_runtime_t1'];
        $get_runtime_t2 = microtime(true);
        log_file('文件名：' . $pram[0] . ',行数：' . $pram[1] . ',方法：' . $pram[2] . '，最后执行时间' . $get_runtime_t2 . '秒', 'run_time');
        log_file('文件名：' . $pram[0] . ',行数：' . $pram[1] . ',方法：' . $pram[2] . '，耗时' . floatval($get_runtime_t2 - $get_runtime_t1) . '秒', 'run_time');
    }
}
/**
 * [request_mixed 混流]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-07-18T18:10:55+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $data [description]
 * @return    [type] [description]
 */
function request_mixed($data)
{
    $m_config = load_auto_cache('m_config');
    $key = $m_config['qcloud_auth_key']; //$qcloud_security_key
    $t = get_gmtime() + 86400;
    //http://fcgi.video.qcloud.com/common_access?cmd=appid&interface=Mix_StreamV2&t=t&sign=sign

    $url = "http://fcgi.video.qcloud.com/common_access?" . http_build_query(array('appid' => $m_config['vodset_app_id'], 'interface' => 'Mix_StreamV2', 't' => $t, 'sign' => md5($key . $t)));

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');

    $trans = new transport();

    $post_json = json_encode($data);
    //print_r($post_json);

    return $trans->request($url, $post_json, 'POST');
}

/**
 * [up_rocket_all 更新火箭榜单]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-08-21T16:57:07+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $user_id [description]
 * @return    [type] [description]
 */
function up_rocket_all($user_id)
{
    $now_time = time();
    $create_time = $now_time; //时间
    $update_time = $now_time; //时间
    $update_date = date('Y-m-d H:i:s', $now_time); //日期字段，按日期归档；
    $update_ym = date('Ym', $now_time); //年月 如:201610
    $update_d = date('d', $now_time); //日
    $update_w = date('w', $now_time); //周
    //更新火箭榜
    if ($GLOBALS['db']->getOne("SELECT id FROM  " . DB_PREFIX . "rank_rocket_all WHERE user_id=" . $user_id)) {
        //更新
        $sql = "UPDATE " . DB_PREFIX . "rank_rocket_all set update_time = $update_time ,update_date= '" . $update_date . "',update_ym = '" . $update_ym . "',update_d = '" . $update_d . "',update_w = '" . $update_w . "' WHERE user_id = " . $user_id;
        $GLOBALS['db']->query($sql);
    } else {
        $rocket_all = array();
        $rocket_all['user_id'] = $user_id;
        $rocket_all['create_time'] = $create_time;
        $rocket_all['update_time'] = $update_time;
        //插入
        $GLOBALS['db']->autoExecute(DB_PREFIX . "rank_rocket_all", $rocket_all, "INSERT");
    }
}
/**
 * [user_guard_syn 守护数据童虎]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-08-07T11:40:33+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $user_id [description]
 * @param     [type] $anchor_id [description]
 * @param     [type] $data [description]
 * @return    [type] [description]
 */
function user_guard_syn($user_id, $anchor_id, $data)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GuardianRecordRedisService.php');

    $guard = new GuardianRecordRedisService();

    $end_time = $data['day_length'] * 24 * 3600;
    $now_timw = time();
    $guard_row = $GLOBALS['db']->getRow("SELECT guard_id,user_id,anchor_id,level,start_time,end_time FROM " . DB_PREFIX . "guardian_record WHERE `user_id` ={$user_id} AND  `anchor_id` = {$anchor_id}");
    $ds = array();
    if (!empty($guard_row)) {
        // 过期
        if ($guard_row['end_time'] <= time()) {
            $ds = array(
                'guard_id' => $data['guard_id'],
                'user_id' => $user_id,
                'anchor_id' => $anchor_id,
                'level' => 1,
                'start_time' => $now_timw,
                'end_time' => ($now_timw + $end_time)
            );
            $sql = 'UPDATE ' . DB_PREFIX . "guardian_record SET `guard_id`= '{$ds['guard_id']}', `level` = 1, `start_time` = '{$now_timw}', `end_time` = '" . ($now_timw + $end_time) . "' WHERE `user_id` = '{$user_id}' AND `anchor_id` = '{$anchor_id}'";
        } else {
            $ds = array(
                'guard_id' => $data['guard_id'],
                'user_id' => $user_id,
                'anchor_id' => $anchor_id,
                'level' => ((int) ((time() - $guard_row['start_time']) / (3600 * 24 * 30))) ?: 1,
                'start_time' => $guard_row['start_time'],
                'end_time' => ($guard_row['end_time'] + $end_time)
            );
            $sql = 'UPDATE ' . DB_PREFIX . "guardian_record SET `guard_id`= '{$ds['guard_id']}', `level` = {$ds['level']}, `end_time` = end_time+{$end_time} WHERE `user_id` = '{$user_id}' AND `anchor_id` = '{$anchor_id}'";
        }
        $GLOBALS['db']->query($sql);
    } else {
        $ds = array(
            'guard_id' => $data['guard_id'],
            'user_id' => $user_id,
            'anchor_id' => $anchor_id,
            'level' => 1,
            'start_time' => $now_timw,
            'end_time' => ($now_timw + $end_time)
        );
        $sql = 'INSERT INTO ' . DB_PREFIX . "guardian_record (`guard_id`, `user_id`, `anchor_id`, `level`, `start_time`, `end_time`) VALUES ('{$ds['guard_id']}', '{$user_id}', '{$anchor_id}', 1, '{$now_timw}', '" . ($now_timw + $end_time) . "')";
        $GLOBALS['db']->query($sql);
    }
    if ($GLOBALS['db']->affected_rows() && !empty($ds)) {
        $guard->set_db($user_id, $anchor_id, $ds);
    }
}

/**
 * [user_guard_syn_redis 每次进去同步数据到redis]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-08-10T10:08:48+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $user_id [description]
 * @param     [type] $anchor_id [description]
 * @return    [type] [description]
 */
function user_guard_syn_redis($user_id, $anchor_id)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GuardianRecordRedisService.php');
    $guard = new GuardianRecordRedisService();
    if (!($guard_row = $guard->get_guard($user_id, $anchor_id))) {
        if ($guard_row = $GLOBALS['db']->getRow("SELECT guard_id,user_id,anchor_id,level,start_time,end_time FROM " . DB_PREFIX . "guardian_record WHERE `user_id` = {$user_id} AND `anchor_id` = {$anchor_id}")) {
            if ($guard_row['end_time'] <= time()) {
                return null;
            }
            $guard_row['level'] = ((int) ((time() - $guard_row['start_time']) / (3600 * 24 * 30))) ?: 1;
            $GLOBALS['db']->autoExecute(DB_PREFIX . 'guardian_record', $guard_row, 'UPDATE', '', 'SILENT');
            $guard->set_db($user_id, $anchor_id, $guard_row);
        }
    }
    return $guard_row;
}
/**
 * [get_mount 获取进场坐骑]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-08-22T10:20:04+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $user_id [description]
 * @param     [type] $mount_id [description]
 * @return    [type] [description]
 */
function get_mount($user_id, $mount_id)
{
    $user_mount = load_auto_cache('user_mounts', array('user_id' => $user_id, 'mount_id' => $mount_id));
    if (!empty($user_mount) && is_array($user_mount)) {
        if ($user_mount['end_time'] > time()) {
            return load_auto_cache("mount_id", array('id' => $mount_id));
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function user_mount_syn($user_id, $data)
{
    $end_time = $data['day_length'] * 24 * 3600;
    $now_timw = time();
    $mount_row = $GLOBALS['db']->getRow("SELECT mount_id,user_id,create_time,end_time FROM " . DB_PREFIX . "user_mounts WHERE `user_id` ={$user_id} AND `mount_id` = {$data['mount_id']}");
    $ds = array();
    if (!empty($mount_row)) {
        // 过期
        if ($mount_row['end_time'] <= time()) {
            $ds = array(
                'mount_id' => $data['mount_id'],
                'user_id' => $user_id,
                'create_time' => $now_timw,
                'end_time' => ($now_timw + $end_time)
            );
            $sql = 'UPDATE ' . DB_PREFIX . "user_mounts SET `create_time` = '{$now_timw}', `end_time` = '" . ($now_timw + $end_time) . "' WHERE `user_id` = '{$user_id}' AND `mount_id`= '{$ds['mount_id']}'";
        } else {
            $ds = array(
                'mount_id' => $data['mount_id'],
                'user_id' => $user_id,
                'create_time' => $mount_row['create_time'],
                'end_time' => ($mount_row['end_time'] + $end_time)
            );
            $sql = 'UPDATE ' . DB_PREFIX . "user_mounts SET `end_time` = end_time+{$end_time} WHERE `user_id` = '{$user_id}' AND `mount_id`= '{$ds['mount_id']}'";
        }
    } else {
        $ds = array(
            'mount_id' => $data['mount_id'],
            'user_id' => $user_id,
            'create_time' => $now_timw,
            'end_time' => ($now_timw + $end_time)
        );
        $sql = 'INSERT INTO ' . DB_PREFIX . "user_mounts (`mount_id`, `user_id`, `create_time`, `end_time`) VALUES ('{$ds['mount_id']}', '{$user_id}', '{$now_timw}', '" . ($now_timw + $end_time) . "')";
    }
    return $GLOBALS['db']->query($sql);
}

/**
 * [SendVideoMsg 发送直播消息]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-09-11T13:51:43+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $room_id [description]
 * @param     [type] $msg [description]
 * @param     array $sender [description]
 */
function SendVideoMsg($room_id, $msg, $sender = array())
{
    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $api = createTimAPI();
    //群播一个：直播消息
    $ext = array();
    $ext['type'] = 9; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
    $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
    $ext['fonts_color'] = ''; //字体颜色
    $ext['desc'] = $msg; //禁言通知消息;
    $ext['desc2'] = $msg; //禁言通知消息;

    if (empty($sender)) {
        //消息发送者
        $sender = array();
        $sender['user_id'] = $GLOBALS['user_info']['id']; //发送人昵称
        $sender['nick_name'] = $GLOBALS['user_info']['nick_name']; //发送人昵称
        $sender['head_image'] = get_spec_image($GLOBALS['user_info']['head_image']); //发送人头像
        $sender['user_level'] = $GLOBALS['user_info']['user_level']; //用户等级
    }

    $ext['sender'] = $sender;

    #构造高级接口所需参数
    $msg_content = array();
    //创建array 所需元素
    $msg_content_elem = array(
        'MsgType' => 'TIMCustomElem', //自定义类型
        'MsgContent' => array(
            'Data' => json_encode($ext),
            'Desc' => ''
            //    'Ext' => $ext,
            //    'Sound' => '',
        )
    );
    //将创建的元素$msg_content_elem, 加入array $msg_content
    array_push($msg_content, $msg_content_elem);
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();
    $video = $video_redis->getRow_db($room_id, array('group_id'));

    $ret = $api->group_send_group_msg2($GLOBALS['user_info']['id'], $video['group_id'], $msg_content);
    if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
        //10002 系统错误，请再次尝试或联系技术客服。
        $ret = $api->group_send_group_msg2($GLOBALS['user_info']['id'], $video['group_id'], $msg_content);
    }
    return $ret;
}
/**
 * [luhn 银行卡校验]
 * @Author    ZiShang520@gmail.com
 * @DateTime  2018-09-25T15:46:46+0800
 * @copyright (c) ZiShang520 All Rights Reserved
 * @param     [type] $value [description]
 * @return    [type] [description]
 */
function luhn($value)
{
    $luhn = str_split(strrev((string) $value));
    foreach ($luhn as $key => &$v) {
        if ($key % 2 === 1) {
            $v <<= 1;
            $v = $v > 9 ? $v - 9 : $v;
        }
    }
    $num = array_sum($luhn);
    return ($num > 0) && (($num % 10) === 0);
}
