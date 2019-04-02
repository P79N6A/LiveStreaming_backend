<?php

/**
 * 中间计次记录
 */
class AwardRedisService extends BaseRedisService
{
    /**
     * @var string 游戏数据前缀
     */
    public $video_award_db;
    public $diamonds_db;

    /**
     * AwardRedisService constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->video_award_db = $this->prefix . 'award:' . date('Ymd:');
        $this->diamonds_db = $this->prefix . 'all_diamonds:';
        $this->multiple_quotient = $this->prefix . 'multiple_quotient:';
    }

    public function inc($user_id)
    {
        $user_id = intval($user_id);
        if (!$user_id) {
            return 0;
        }
        $num = $this->redis->Incr($this->video_award_db . $user_id);
        $this->redis->Expireat($this->video_award_db . $user_id, strtotime('23:59:59'));
        return $num;
    }

    public function del($user_id)
    {
        $user_id = intval($user_id);
        if (!$user_id) {
            return false;
        }
        return $this->redis->Del($this->video_award_db . $user_id);
    }

    public function diamonds($diamonds = 0)
    {
        $num = $this->redis->Incrby($this->diamonds_db, (int) $diamonds);
        $this->redis->Expireat($this->diamonds_db, strtotime('23:59:59'));
        return $num;
    }

    public function set_num($multiple, $quotient, $num)
    {
        $this->redis->Hset($this->multiple_quotient . $multiple . $quotient, $num, true);
        $this->redis->Expireat($this->multiple_quotient . $multiple . $quotient, strtotime('23:59:59'));
    }

    public function has_num($multiple, $quotient, $num)
    {
        return $this->redis->Hexists($this->multiple_quotient . $multiple . $quotient, $num);
    }
}
