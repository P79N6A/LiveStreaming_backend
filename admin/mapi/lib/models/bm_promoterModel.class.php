<?php
/**
 *
 */
class bm_promoterModel extends NewModel
{
    static $user_redis;
    /**
     * [addGameLog description]
     * @param [type] $game_log_id    游戏日志id
     * @param [type] $result         获胜方下注项
     * @param [type] $win_times      赢钱比例
     * @param [type] $promoter_times 推广抽成比例
     * @param [type] $platform_times 平台抽成比例
     */
    public function addGameLog($game_log_id, $result, $win_times, $promoter_times, $platform_times)
    {
        $pre         = DB_PREFIX;
        $create_time = NOW_TIME;
        self::$sql   = "INSERT INTO {$pre}bm_promoter_game_log (
            user_id,
            game_id,
            game_log_id,
            bm_pid,
            sum_bet,
            sum_gain,
            sum_win,
            promoter_gain,
            platform_gain,
            user_gain,
            gain,
            create_time
        ) SELECT
            user_id,
            game_id,
            {$game_log_id} AS game_log_id,
            bm_pid,
            sum_bet,
            sum_gain,
            sum_gain - sum_bet AS sum_win,
            FLOOR({$promoter_times} * ABS(sum_gain - sum_bet)) AS promoter_gain,
            FLOOR({$platform_times} * ABS(sum_gain - sum_bet)) AS platform_gain,
            FLOOR((sum_gain - sum_bet) - (
                (FLOOR({$promoter_times} * ABS(sum_gain - sum_bet))+FLOOR({$platform_times} * ABS(sum_gain - sum_bet))) * (sum_gain > sum_bet)
            )) AS user_gain,
            FLOOR((sum_bet - sum_gain) - (
                (FLOOR({$promoter_times} * ABS(sum_gain - sum_bet))+FLOOR({$platform_times} * ABS(sum_gain - sum_bet))) * (sum_bet > sum_gain)
            )) AS gain,
            {$create_time} AS create_time
        FROM
            (
                SELECT
                    l.user_id,
                    g.game_id,
                    u.bm_pid,
                    SUM(l.money*(l.type = 1)) AS sum_bet,
                    SUM(l.money *(l.bet = {$result})) * {$win_times} AS sum_gain
                FROM
                    {$pre}game_log AS g,
                    {$pre}user_game_log AS l,
                    {$pre}bm_promoter AS p,
                    {$pre}user AS u
                WHERE
                    u.id = l.user_id
                AND u.bm_pid = p.user_id
                AND l.game_log_id = g.id
                AND l.game_log_id = {$game_log_id}
                GROUP BY
                    l.user_id
            ) AS a";
        return Connect::exec(self::$sql);
    }
    public static function checkFile()
    {
        if (!self::$user_redis) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            self::$user_redis = new UserRedisService();
        }
    }
    public function payPromoter()
    {
        self::checkFile();
        $model     = self::build('bm_promoter_game_log');
        $bmp_model = self::build('bm_promoter');
        $list      = $model->select(['is_count' => 0]);
        foreach ($list as $value) {
            $game_log_id = $value['game_log_id'];
            $user_id = $value['gain'] > 0 ? $value['bm_pid'] : $value['user_id'];
            //lym 修正 当 推广商赢时候，重复扣流水当问题
            if($user_id==$value['bm_pid']){
                $value['gain'] = $value['gain'] + $value['promoter_gain'] + $value['platform_gain'];
            }

            self::payCoin($value['bm_pid'], $game_log_id, $value['gain'], '推广商收支');

            self::payCoin($user_id, $game_log_id, -$value['promoter_gain'], '推广中心抽成');
            self::payCoin($user_id, $game_log_id, -$value['platform_gain'], '平台抽成');
            $bmp = $bmp_model->field('pid')->selectOne(['user_id' => $value['bm_pid']]);
            if ($bmp) {
                self::payCoin($bmp['pid'], $game_log_id, $value['promoter_gain'], '推广中心抽成收入');
            }
            $model->update(['is_count' => 1], ['id' => $value['id']]);
        }
    }
    public static function payCoin($user_id, $game_log_id, $coin, $des)
    {
        self::checkFile();
        $user_model = self::build('user');
        $res        = $user_model->update(array('coin' => array('coin + ' . $coin)), ['id' => $user_id]);
        if ($res) {
            self::$user_redis->inc_field($user_id, 'coin', $coin);
            $account_diamonds = $user_model->coin($user_id);
            $res              = self::build('coin_log')->addLog($user_id, $game_log_id, $coin, $account_diamonds, $des);
        }
    }
    public function changePid($pid, $to_pid)
    {
        return self::build('user')->update(['bm_pid' => $to_pid], ['bm_pid' => $pid, 'is_robot' => 0]);
    }
    public function update_promoter_child($user_id, $is_pommoter = 0)
    {
        if ($is_pommoter == 1) {
            $GLOBALS['db']->query("update " . DB_PREFIX . "bm_promoter set child_count=(SELECT temp.id from (SELECT COUNT(id) as id from " . DB_PREFIX . "bm_promoter where pid=" . intval($user_id) . " and is_effect=1 and status=1)temp ) where user_id= " . intval($user_id) . "");
        } else {
            $GLOBALS['db']->query("update " . DB_PREFIX . "bm_promoter set child_count=(SELECT COUNT(id) as id from " . DB_PREFIX . "user where bm_pid=" . intval($user_id) . " and is_effect=1 and is_robot=0) where user_id= " . intval($user_id) . "");
        }
    }

    //更新推广商子集个数
    /*更新推广商子集个数
     * $user_id 推广中心绑定的会员id
     * $takt_time 秒数
     * $name_suffix cookie后缀名
     * */
    public function update_promoter_two_child($user_id,$takt_time,$name_suffix)
    {
        $takt_time=$takt_time?$takt_time:600;//更新间隔秒数

        $client_ip = get_client_ip();
        $promoter_child_update_cookie_name = md5($client_ip . "_promoter_child_update_" . $name_suffix);
        $promoter_child_update_time = es_cookie::get($promoter_child_update_cookie_name);
        if (NOW_TIME - $promoter_child_update_time > $takt_time) {
            $user_id=intval($user_id);
            if($user_id >0)
            {
                $where=" and p.pid=".$user_id."";
            }else{
                $where=" and p.pid > 0";
            }
            $GLOBALS['db']->query("update " . DB_PREFIX . "bm_promoter as p set p.child_count=(SELECT COUNT(1)  from " . DB_PREFIX . "user as u where u.bm_pid=p.user_id and u.is_effect=1 and u.is_robot=0) where p.is_effect=1 and p.status=1 ".$where." ");

            es_cookie::set($promoter_child_update_cookie_name, NOW_TIME);
        }
    }

    public function isPromoter($user_id)
    {
        return !!self::build('bm_promoter')->selectOne(['user_id'=>intval($user_id),'status'=>1]);
    }
}
