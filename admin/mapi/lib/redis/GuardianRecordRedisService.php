<?php

/**
 * 守护
 */
class GuardianRecordRedisService extends BaseRedisService
{
    public $user_follow_db; //set有序数据=user_id  用户关注的会员ID
    public $user_followed_by_db; // set有序数据=user_id  关注用户的会员ID
    //    var $user_hash_db; //所有会员数据 user_id hash数据 存储在线数据
    //var $user_db; //:user_id  hash数据，会员数据

    /**
     * [__construct 初始化]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T10:52:16+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     */
    public function __construct()
    {

        parent::__construct();
        // 用户守护的主播
        $this->user_guard_db = $this->prefix . 'new_user_guarding:';
        // 主播拥有的守护
        $this->user_guarded_by_db = $this->prefix . 'new_user_guarded_by:';
    }

    /**
     * [set_db 守护数据]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T11:11:09+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     [type] $anchor_id [description]
     * @param     [type] $data [description]
     * @return    [type] [description]
     */
    public function set_db($user_id, $anchor_id, $data)
    {
        $pipe = $this->redis->multi();
        filter_null($data);
        $this->redis->hMSet($this->user_guard_db . $user_id, array($anchor_id => json_encode($data)));
        $this->redis->hMSet($this->user_guarded_by_db . $anchor_id, array($user_id => json_encode($data)));
        $replies = $pipe->exec();
        if ($replies[0]) {
            return $anchor_id;
        } else {
            return 0;
        }
    }

    /**
     * [get_guard 获取用户守护的主播]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T11:47:41+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     integer $anchor_id [description]
     * @return    [type] [description]
     */
    public function get_guard($user_id, $anchor_id = 0)
    {
        try {
            if (empty($anchor_id)) {
                return array_filter(array_map(function ($value) {
                    $data = json_decode($value, true);
                    if (empty($data)) {
                        return null;
                    }
                    if ($data['end_time'] <= time()) {
                        return null;
                    }
                }, $this->redis->hGetAll($this->user_guard_db . $user_id)));
            } else {
                $data = json_decode($this->redis->hGet($this->user_guard_db . $user_id, $anchor_id), true);
                if (empty($data)) {
                    return null;
                }
                if ($data['end_time'] <= time()) {
                    return null;
                }
                return $data;
            }
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * [get_guard_len 获取守护的数量]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T11:50:58+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function get_guard_len($user_id)
    {
        return $this->redis->hLen($this->user_guard_db . $user_id);
    }

    /**
     * [del_guard 删除关系]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T11:55:09+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     [type] $anchor_id [description]
     * @return    [type] [description]
     */
    public function del_guard($user_id, $anchor_id)
    {
        return $this->redis->hDel($this->user_guard_db . $user_id) && $this->redis->hDel($this->user_guarded_by_db . $anchor_id);
    }

    /**
     * [get_guarded 获取主播的守护]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T13:54:21+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     [type] $anchor_id [description]
     * @param     integer $user_id [description]
     * @return    [type] [description]
     */
    public function get_guarded($anchor_id, $user_id = 0)
    {
        try {
            if (empty($user_id)) {
                return array_filter(array_map(function ($value) {
                    $data = json_decode($value, true);
                    if (empty($data)) {
                        return null;
                    }
                    if ($data['end_time'] <= time()) {
                        return null;
                    }
                }, $this->redis->hGetAll($this->user_guarded_by_db . $anchor_id)));
            } else {
                $data = json_decode($this->redis->hGet($this->user_guarded_by_db . $anchor_id, $user_id), true);
                if (empty($data)) {
                    return null;
                }
                if ($data['end_time'] <= time()) {
                    return null;
                }
                return $data;
            }
        } catch (\Exception $e) {
            return;
        }
    }
    /**
     * [get_guarded_len 获取该主播的守护人数]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-06T13:56:17+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     [type] $anchor_id [description]
     * @return    [type] [description]
     */
    public function get_guarded_len($anchor_id)
    {
        return $this->redis->hLen($this->user_guarded_by_db . $anchor_id);
    }
}
