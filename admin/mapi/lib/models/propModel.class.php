<?php
/**
 *
 */
class propModel extends NewModel
{
    protected static $api, $user_redis, $video_redis;
    public function getList($where, $field = 'id,name,score,diamonds,icon,pc_icon,pc_gif,ticket,is_much,sort,is_red_envelope,is_animated,anim_type', $order = 'diamonds desc', $limit = 3)
    {
        $where['is_effect'] = 1;
        return self::parseValue($this->field($field)->order($order)->limit($limit)->select($where));
    }
    protected static function parseValue($data)
    {
        if (isset($data[0])) {
            foreach ($data as $key => $value) {
                $data[$key] = self::parseValue($value);
            }
        } else {
            foreach ([
                'icon',
                'pc_icon',
                'pc_gif'
            ] as $value) {
                if (isset($data[$value])) {
                    $data[$value] = get_spec_image($data[$value]);
                }
            }
            foreach ([
                'id',
                'score',
                'diamonds',
                'ticket',
                'is_much',
                'sort',
                'is_red_envelope',
                'is_animated'
            ] as $value) {
                if (isset($data[$value])) {
                    $data[$value] = intval($data[$value]);
                }
            }
        }
        return $data;
    }
    /**
     * 机器人定时器
     * @return [type] [description]
     */
    public function crontabRobot()
    {
        $m_config = load_auto_cache("m_config");
        // $m_config['robot_prop_num']            = 10; //送礼个数
        // $m_config['robot_prop_diamonds']       = 10000; //每个礼物的价值
        // $m_config['robot_prop_total_diamonds'] = 10000; //所有机器人礼物价值
        // $m_config['robot_prop_interval']       = 0; //送礼间隔
        // $m_config['robot_prop_real_interval']  = 0; //真人送礼间隔
        self::checkFile();
        $video_model = self::build('video');
        $live_videos = $video_model->field('id')->select(['live_in' => 1]);
        $root = [];
        foreach ($live_videos as $video) {
            $room_id = $video['id'];
            // 检查机器人送礼金额
            $left = $m_config['robot_prop_total_diamonds'] - self::$video_redis->getOne_db($room_id, 'robot_prop_diamonds');
            if ($left <= 0) {
                $root[$room_id] = 'no_left';
                continue;
            }
            // 检查机器人送礼时间
            $lock_time = self::$video_redis->getOne_db($room_id, 'robot_prop_interval');
            if (!$lock_time) {
                $lock_time = NOW_TIME + $m_config['robot_prop_interval'];
                self::$video_redis->update_db($room_id, ['robot_prop_interval' => $lock_time]);
            }
            if (NOW_TIME < $lock_time) {
                $root[$room_id] = 'lock:' . ($lock_time - NOW_TIME);
                continue;
            }
            $interval = $m_config['robot_prop_interval'] + rand(-30, 30);
            $interval = $interval > 15 ? $interval : 15;
            self::$video_redis->update_db($room_id, ['robot_prop_interval' => NOW_TIME + $interval]);
            // 检查真人送礼时间
            $last_prop = self::build('video_prop')->getNewestOne('create_time', ['video_id' => $room_id]);
            if (NOW_TIME < $last_prop['create_time'] + $m_config['robot_prop_real_interval']) {
                self::$video_redis->update_db($room_id, ['robot_prop_interval' => $last_prop['create_time'] + $m_config['robot_prop_real_interval']]);
                $root[$room_id] = 'real_wait';
                continue;
            }
            // 选取机器人
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
            $video_viewer_redis = new VideoViewerRedisService();

            $viewer = $video_viewer_redis->get_viewer_list2($room_id, 1, 0);
            foreach ($viewer as $key => $value) {
                if (!$value['is_robot'] || !$value['user_id']) {
                    unset($viewer[$key]);
                }
            }
            if (!$viewer) {
                // todo:模拟真实环境redis后修改
                // 选取数据库中的用户
                $viewer = self::build('user')->field('id user_id')->select(['is_robot' => 1]);
            }
            $user_id = intval($viewer[array_rand($viewer, 1)]['user_id']);
            // 选取礼物
            $num = rand(1, intval($m_config['robot_prop_num']));
            $max = intval($left / $num);
            $max = $m_config['robot_prop_diamonds'] > $max ? $max : intval($m_config['robot_prop_diamonds']);

            $prop = $this->field('id,ticket')->select(['diamonds' => ['between', [1, $max]], 'is_red_envelope' => 0, 'is_effect' => 1]);
            $prop_key = array_rand($prop, 1);
            $prop_id = intval($prop[$prop_key]['id']);
            if (!$prop_id) {
                $root[$room_id] = 'no_prop:' . $max;
                continue;
            }
            // 伪造礼物秀票
            self::$video_redis->inc_field($room_id, 'robot_prop_diamonds', $prop[$prop_key]['ticket'] * $num);
            self::$user_redis->inc_field($podcast_id, 'ticket', $prop[$prop_key]['ticket'] * $num);
            // 伪造礼物推送
            $ret = self::sendPropMsg($user_id, $prop_id, $room_id, $num);
            if (IS_DEBUG) {
                $root[$room_id] = $ret;
            } else {
                $root[$room_id] = $ret['ActionStatus'];
            }
        }
        return $root;
    }
    /**
     * 检测方法
     * @return [type] [description]
     */
    protected static function checkFile()
    {
        if (!self::$api) {
            fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
            self::$api = createTimAPI();
        }
        if (!self::$user_redis) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            self::$user_redis = new UserRedisService();
        }
        if (!self::$video_redis) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            self::$video_redis = new VideoRedisService();
        }
    }
    /**
     * 赠送礼物推送消息
     * @param  [type]  $user_id  赠送人id
     * @param  [type]  $prop_id  [description]
     * @param  [type]  $room_id  [description]
     * @param  integer $num      [description]
     * @param  boolean $plus_num [description]
     * @return [type]            [description]
     */
    protected static function sendPropMsg($user_id, $prop_id, $room_id, $num = 1, $plus_num = false, $to_user_id = false)
    {
        self::checkFile();
        $prop = load_auto_cache("prop_id", ['id' => $prop_id]);
        $user = self::$user_redis->getRow_db($user_id, array('nick_name', 'head_image', 'user_level', 'v_icon'));
        $video = self::$video_redis->getRow_db($room_id, ['id', 'podcast_id', 'group_id', 'prop_table', 'room_type']);
        $to_user_id = $to_user_id ? $to_user_id : $video['podcast_id'];
        $res = [];
        for ($i = 0; $i < $num; $i++) {
            $plus_num = ($plus_num && $num == 1) ? $plus_num : ($prop['is_much'] ? ($i + 1) : 1);
            $ext = [
                'type' => 1, //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束; 8:红包
                'num' => $num, //数量
                'is_plus' => $prop['is_much'], //1：数量连续叠加显示;0:不叠加;这个值是从客户端上传过来的
                'is_much' => $prop['is_much'], //1:可以连续发送多个;用于小金额礼物
                'room_id' => $room_id,
                'plus_num' => $plus_num,
                'app_plus_num' => $plus_num,
                'is_animated' => $prop['is_animated'],
                'sender' => [
                    'user_id' => $user_id,
                    'nick_name' => $user['nick_name'],
                    'head_image' => get_spec_image($user['head_image']),
                    'user_level' => $user['user_level'],
                    'v_icon' => $user['v_icon']
                ],
                'prop_id' => $prop_id,
                'icon' => $prop['icon'],
                'user_prop_id' => 0, //插入video_prop的礼物id
                'total_ticket' => intval(self::$user_redis->getOne_db($podcast_id, 'ticket')), //伪造总秀票数
                'to_user_id' => $video['podcast_id'],
                'fonts_color' => '',
                'desc' => "我送了1个" . $prop['name'], //谁送了谁多少个什么
                'desc2' => "我送了1个" . $prop['name'], //我送了谁多少个什么
                'anim_type' => $prop['anim_type'],
                'top_title' => $user['nick_name'] . "送了," . $prop['name'],
                'anim_cfg' => $prop['anim_cfg']
            ];
            $msg_content = [
                'MsgType' => 'TIMCustomElem', //自定义类型
                'MsgContent' => [
                    'Data' => json_encode($ext),
                    'Desc' => ''
                ]
            ];
            $res[] = [
                'ret' => self::$api->group_send_group_msg2($user_id, $video['group_id'], [$msg_content]),
                'ext' => $ext,
                'user_id' => $user_id,
                'prop_id' => $prop_id,
                'group_id' => $video['group_id']
            ];
        }
        return $res;
    }
}
