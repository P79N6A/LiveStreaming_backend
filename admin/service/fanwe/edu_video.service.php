<?php

class  edu_videoService
{
    const AUTH_TYPE_TEACHER = '教师';//教师认证类型
    const AUTH_TYPE_ORG = '机构';//机构认证类型

    public function insert_edu_video_info($param)
    {
        if ($param['user_authentication_type'] == self::AUTH_TYPE_TEACHER) {
            $tags = $GLOBALS['db']->getOne("select tags from " . DB_PREFIX . "edu_teacher where user_id=" . intval($param['user_id']) . "");
        } elseif ($param['user_authentication_type'] == self::AUTH_TYPE_ORG) {
            $tags = $GLOBALS['db']->getOne("select tags from " . DB_PREFIX . "edu_org where user_id=" . intval($param['user_id']) . "");
        } else {
            $tags = '';
        }

        //edu_video插入教育直播数据
        $edu_video_data['video_id'] = intval($param['video_id']);
        $edu_video_data['deal_id'] = intval($param['deal_id']);
        $edu_video_data['edu_cate_id'] = intval($param['edu_cate_id']);
        $edu_video_data['tags'] = $tags;
        $edu_video_data['video_code'] = strim($param['video_code']);
        $edu_video_data['is_verify'] = intval($param['is_verify']);
        $edu_video_data['booking_class_id'] = intval($param['booking_class_id']);
        $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_video_info", $edu_video_data, 'INSERT');
    }

    /*
    //直播众筹开始直播，推送通知支持者
    /*$deal_info[id,name]
     * */
    public function deal_private_push_user($param)
    {
        $deal_info = $param['deal_info'];
        $video_id = intval($param['video_id']);
        $user_id = intval($GLOBALS['user_info']['id']);

        $deal_id = intval($deal_info['id']);
        $order_list = $GLOBALS['db']->getAll("select o.user_id,o.id as order_id from " . DB_PREFIX . "edu_deal_order as o where o.deal_id=" . intval($deal_id) . " and o.order_status=1");
        $order_users = array_map('array_shift', $order_list);
        $content = "你的课程{$deal_info['name']}老师已经进入直播间。赶快去上课吧。";
        $status = $this->private_push_user(array(
            'video_id' => $video_id,
            'user_id' => $user_id,
            'users' => implode(',', $order_users),
            'content' => $content,
        ));
        return $status;
    }

    public function deal_modify_push_user($deal_info)
    {
        $order_list = $GLOBALS['db']->getAll("select o.user_id,o.id as order_id from " . DB_PREFIX . "edu_deal_order as o where o.deal_id= {$deal_info['id']} and o.order_status=1");
        $order_users = array_column($order_list, 'user_id');
        $content = "你的预约{$deal_info['name']}主播修改了直播时间。";

        $this->push_user($order_users, array(
            'content' => $content,
            'type' => 10,
            'room_id' => 0,
        ));
    }

    /*
     * $param['video_id']
     * $param['user_id']
     * $param['users'] 会员id,多个用逗号隔开
     * $param['content']
     * */
    public function private_push_user($param)
    {
        $user_id = intval($param['user_id']);
        $video_id = intval($param['video_id']);

        $sql = "select id,city,user_id from " . DB_PREFIX . "video where room_type = 1 and live_in = 2 and id =" . $video_id . " and user_id=" . $user_id . "";
        $video = $GLOBALS['db']->getRow($sql);

        if ($video) {
            $user_list = explode(',', $param['users']);//将选中的：私聊 数据添加到数据库中

            if (!$user_list) {
                return 0;
            }

            $user_list = array_unique($user_list);
            $user_list_array_chunk = array_chunk($user_list, 500);//一次推送500

            //私密直播redis增加类
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
            $video_private_redis = new VideoPrivateRedisService();

            //推送类
            fanwe_require(APP_ROOT_PATH . 'system/schedule/android_list_schedule.php');
            fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_list_schedule.php');

            foreach ($user_list_array_chunk as $kk => $user_item) {
                //开始
                foreach ($user_item as $k => $v) {
                    $video_private_redis->push_user($video_id, $v);
                }

                //推送通知：
                //推送消息文本
                $content = $param['content'];
                $room_id = $video_id;
                $user_ids = implode(',', $user_item);

                $code_sql = "select u.apns_code,u.device_type  from " . DB_PREFIX . "user u where u.device_type in (1,2) and u.id in (" . $user_ids . ")";
                $code_list = $GLOBALS['db']->getAll($code_sql);
                //得到机器码列表
                $apns_app_code_list = array();
                $apns_ios_code_list = array();
                $j = $i = 0;
                foreach ($code_list as $kk => $vv) {
                    //获取android机器码
                    if ($vv['device_type'] == 1) {
                        $apns_app_code_list[$i] = $vv['apns_code'];
                        $i++;
                    }

                    //获取IOS机器码
                    if ($vv['device_type'] == 2) {
                        $apns_ios_code_list[$j] = $vv['apns_code'];
                        $j++;
                    }
                }

                //安卓推送信息
                if (count($apns_app_code_list) > 0) {
                    $AndroidList = new android_list_schedule();
                    $data = array(
                        'dest' => implode(",", $apns_app_code_list),
                        'content' => $content,
                        'room_id' => $room_id,
                        'type' => 0,
                    );

                    $ret_android = $AndroidList->exec($data);
                }

                //ios 推送信息
                if (count($apns_ios_code_list) > 0) {
                    $IosList = new ios_list_schedule();
                    $ios_data = array(
                        'dest' => implode(",", $apns_ios_code_list),
                        'content' => $content,
                        'room_id' => $room_id,
                        'type' => 0,
                    );
                    $ret_ios = $IosList->exec($ios_data);
                }
                //end
            }

            $status = 1;
        } else {
            $status = 0;
        }

        return $status;
    }

    public function push_app($param)
    {
        $this->push_user($param['user_id'], array(
            'content' => $param['content'],
            'type' => 10,
            'room_id' => 0,
        ));
    }

    public function push_teacher($param)
    {
        $this->push_user($param['user_id'], array(
            'content' => $param['content'],
            'type' => 11,
            'room_id' => 0,
        ));
    }

    public function push_org($param)
    {
        $this->push_user($param['user_id'], array(
            'content' => $param['content'],
            'type' => 12,
            'room_id' => 0,
        ));
    }

    public function push_deal($param)
    {
        $this->push_user($param['user_id'], array(
            'content' => $param['content'],
            'type' => 13,
            'room_id' => 0,
        ));
    }

    private function push_user($user_ids, $data)
    {
        if (!is_array($user_ids)) {
            $user_ids = array($user_ids);
        }
        $user_ids = array_unique($user_ids);
        if (empty($user_ids)) {
            return;
        }

        $code_sql = "select u.apns_code,u.device_type  from " . DB_PREFIX . "user u where u.device_type in (1,2) and u.id in (" . implode(',',
                $user_ids) . ")";
        $code_list = $GLOBALS['db']->getAll($code_sql);
        //得到机器码列表
        $apns_app_code_list = array();
        $apns_ios_code_list = array();
        $j = $i = 0;
        //推送类
        fanwe_require(APP_ROOT_PATH . 'system/schedule/android_list_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_list_schedule.php');
        foreach ($code_list as $kk => $vv) {
            //获取android机器码
            if ($vv['device_type'] == 1) {
                $apns_app_code_list[$i] = $vv['apns_code'];
                $i++;
            }

            //获取IOS机器码
            if ($vv['device_type'] == 2) {
                $apns_ios_code_list[$j] = $vv['apns_code'];
                $j++;
            }
        }

        //安卓推送信息
        if (count($apns_app_code_list) > 0) {
            $data['dest'] = implode(",", $apns_app_code_list);
            $AndroidList = new android_list_schedule();
            $AndroidList->exec($data);
        }

        //ios 推送信息
        if (count($apns_ios_code_list) > 0) {
            $data['dest'] = implode(",", $apns_ios_code_list);
            $IosList = new ios_list_schedule();
            $IosList->exec($data);
        }
    }

}
