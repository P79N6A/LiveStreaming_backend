<?php

class select_heat_video_auto_cache extends auto_cache
{
    private $key = "select_heat_video:video:";
    public function load($param)
    {
        $sex = intval($param['sex_type']);
        $city = strim($param['area_type']);
        $cate_id = intval($param['cate_id']);

        $has_private = intval($param['has_private']); //1：包括私密直播
        $is_classify = intval($param['is_classify']); //分类
        $order = strim($param['order']); //排序字段
        $sort = strim($param['sort']); //排序方式

        if ($city == '热门' || $city == 'null') {
            $city = '';
        }
        $this->key .= $sex . '_' . $city . '_' . $cate_id . '_' . $has_private . '_' . $is_classify;
        if ($order) {
            $this->key .= '_' . $order;
        }
        if ($sort) {
            $this->key .= '_' . $sort;
        }
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                $has_is_authentication = intval($m_config['has_is_authentication']) ? 1 : 0;
                //Url分类跳转ljz
                if (defined('OPEN_CLASSIFY_URL') && OPEN_CLASSIFY_URL) {
                    $is_gather = ',v.is_gather';
                } else {
                    $is_gather = '';
                }

                if ($has_is_authentication && $has_private == 0) {
                    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.in_livepk, v.video_type, v.create_type, v.room_type,
						(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, u.head_image,u.thumb_head_image,v.live_image, v.xpoint,v.ypoint,
						u.v_type, u.v_icon, u.nick_name,u.user_level,v.is_live_pay" . $is_gather . ",v.live_pay_type,v.live_fee,u.create_time as user_create_time, u.luck_num FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id  where v.live_in in (1,3) and u.is_authentication = 2 and u.is_hot_on =0 and u.mobile != '13888888888' and u.mobile != '13999999999'  ";
                } else {
                    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.in_livepk, v.video_type, v.create_type, v.room_type,
						(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, u.head_image,u.thumb_head_image,v.live_image, v.xpoint,v.ypoint,
						u.v_type, u.v_icon, u.nick_name,u.user_level,v.is_live_pay" . $is_gather . ",v.live_pay_type,v.live_fee,u.create_time as user_create_time, u.luck_num FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id  where v.live_in in (1,3) and u.is_hot_on =0 and  u.mobile != '13888888888' and u.mobile != '13999999999'  ";
                }

                if ($is_classify) {
                    $sql .= " and v.classified_id = " . $is_classify; //分类
                }

                if ($has_private == 1) {
                    $sql .= ' and v.room_type in (1,3)'; //1:私密直播;3:直播
                } else {
                    $sql .= ' and v.room_type = 3'; //1:私密直播;3:直播
                }

                if ($sex == 1 || $sex == 2) {
                    $sql .= ' and v.sex = ' . $sex;
                }

                if ($city != '') {
                    $sql .= " and v.province = '" . $city . "'";
                }

                if ($cate_id > 0) {
                    $sql .= " and v.cate_id = '" . $cate_id . "'";
                }

                if ($has_private != 1 && (defined('OPEN_ROOM_HIDE') && OPEN_ROOM_HIDE == 1) && intval($m_config['open_room_hide']) == 1) {
                    $sql .= " and v.province <> '火星' and v.province <>''";
                }
                if ($order) {
                    $sql .= " order by v." . $order;
                    if ($sort) {
                        $sql .= " " . $sort;
                    }
                } else {
                    $sql .= "  order by v.sort_num desc,v.sort desc";
                }

                $list = $GLOBALS['db']->getAll($sql, true, true);
                fanwe_require(APP_ROOT_PATH . 'mapi/car/core/common_car.php');
                $rank = get_heat_rank_cache();
                $rank_arr = array();
                if (!empty($rank['list'])) {
                    foreach ($rank['list'] as $kk => $vv) {
                        $rank_arr[$vv['video_id']] = $kk + 1;
                    }
                }
                $live_in_1 = array();
                $live_in_0 = array();
                foreach ($list as $k => &$v) {
                    //判断用户是否为今日创建的新用户，是：1，否：0
                    if (date('Y-m-d') == date('Y-m-d', $list[$k]['user_create_time'] + 3600 * 8)) {
                        $v['today_create'] = 1;
                    } else {
                        $v['today_create'] = 0;
                    }
                    if ($v['live_image'] == '') {
                        if (strpos($v['head_image'], 'wx.qlogo.cn')) {
                            $v['live_image'] = strtr($v['headimgurl'], array('/96' => '/0'));
                        } elseif (strpos($v['head_image'], 'http://q.qlogo.cn/')) {
                            $v['live_image'] = strtr($v['headimgurl'], array('/40' => '/100'));
                        } else {
                            $v['live_image'] = get_spec_image($v['head_image']);
                        }
                        $v['head_image'] = get_spec_image($v['head_image']);
                    } else {
                        $v['live_image'] = get_spec_image($v['live_image']);
                        $v['head_image'] = get_spec_image($v['head_image'], 150, 150);
                    }

                    if (strpos($v['head_image'], 'wx.qlogo.cn')) {
                        $v['thumb_head_image'] = strtr($v['head_image'], array('/96' => '/46'));
                    } else {
                        if ($v['thumb_head_image'] == '') {
                            $v['thumb_head_image'] = get_spec_image($v['head_image'], 150, 150);
                        } else {
                            //$v['thumb_head_image'] = get_abs_img_root($v['thumb_head_image']);
                            $v['thumb_head_image'] = get_spec_image($v['thumb_head_image'], 150, 150);
                        }
                    }
                    $v['heat_rank'] = intval($rank_arr[$v['room_id']]);

                    $v['medals'] = array_map(function ($_v) {
                        return $_v['icon'];
                    }, load_auto_cache('user_medals', array('user_id' => $v['user_id'], 'no_expired' => true)));

                    $v['live_state'] = '';

                    if ($v['luck_num'] != 0) {
                        $v['user_id'] = $v['luck_num'];
                    }
                    if ($v['live_in'] == 1) {
                        if ($v['is_live_pay'] == 0) {
                            $v['live_state'] = '直播';
                        } else if ($v['is_live_pay'] == 1) {
                            $v['live_state'] = '付费直播';
                        }
                        $live_in_1[$k] = $v;
                    } else {
                        if ($v['is_live_pay'] == 1) {
                            $v['live_state'] = '付费回播';
                        } else if ($v['is_live_pay'] == 0 && intval($v['is_gather']) != 1) {
                            $v['live_state'] = '回播';
                        } else if (intval($v['is_gather']) == 1) {
                            $v['live_state'] = '直播';
                        }
                        $live_in_0[$k] = $v;
                    }
                    unset($list[$k]);
                }
                array_multisort(array_column($live_in_1, 'heat_rank'), SORT_ASC, $live_in_1);
                array_multisort(array_column($live_in_0, 'heat_rank'), SORT_ASC, $live_in_0);

                $list = array_merge($live_in_1, $live_in_0);

                $GLOBALS['cache']->set($this->key, $list, 10, true);

                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                //echo $this->key;
            }
        }

        if ($list == false) {
            $list = array();
        }

        return $list;
    }

    public function rm()
    {

        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {

        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
