<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class gamesModule extends baseModule
{
    /**
     * redis对象实例
     * @var [type]
     */
    protected static $redis, $video_redis;
    /**
     * 构造函数，实例化redis对象，导入模型库
     */
    public function __construct()
    {
        parent::__construct();
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GamesRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        self::$redis = new GamesRedisService();
        self::$video_redis = new VideoRedisService();
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/Model.class.php');
        Model::$lib = dirname(__FILE__);
    }

    public function __destruct()
    {
        parent::__destruct();
    }
    /**
     * api返回信息
     * @param  string  $error  错误信息
     * @param  integer $status 错误状态
     * @param  array   $data   返回数据
     * @return void
     */
    protected static function returnError($error = '出错了！', $status = 0, $data = [])
    {
        $data['status'] = $status;
        $data['error'] = $error;
        if ($error == '参数错误') {
            $data['data'] = $_REQUEST;
        }
        api_ajax_return($data);
    }
    /**
     * 日志写入
     * @param  object $data 日志数据
     * @return void
     */
    protected static function pushLog($data)
    {
        if (IS_DEBUG) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/PushLog.class.php');
            PushLog::log($data);
        }
    }
    /**
     * 获取用户id
     * @return integer 登录用户id
     */
    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($_REQUEST['test'] == 'test' && IS_DEBUG) {
            return 687;
        }
        if (!$user_id) {
            self::returnError('未登录');
        }
        return $user_id;
    }
    /**
     * 获取游戏配置
     * @param  integer $id    游戏（种类）id
     * @param  string  $field 数据字段
     * @return array          游戏（种类）数据
     */
    protected static function getGameById($id, $field = '')
    {
        $game = Model::build('games')->field($field)->selectOne(['id' => $id]);
        if (!$game) {
            self::returnError('游戏参数错误');
        }
        return $game;
    }
    /**
     * 根据主播id获取直播间id以及group_id
     * @param  integer $user_id 主播用户id
     * @return array            [$video_id,$group_id]
     */
    protected static function getLiveVideoByUserId($user_id)
    {
        $video = Model::build('video')->getLiveVideoByUserId($user_id, 'id,group_id');

        $video_id = intval($video['id']);
        if (!($video['id'] && $video['group_id'])) {
            self::returnError('不在直播状态');
        }
        return [$video_id, $video['group_id']];
    }
    /**
     * 获取游戏状态信息
     * @param  integer $video_id 直播间id
     * @return array             [$last_game, $last_log_id, $banker_status, $banker_id]
     */
    protected static function getLastGameByVideoId($video_id)
    {
        $model = Model::build('games');
        return $model::getLastGameByVideoId($video_id);
    }
    /**
     * 获取上庄信息
     * @param  integer $video_id 直播间id
     * @param  array   $option   下注选项倍数数据
     * @return array             [$banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet]
     */
    protected static function getBankerStatus($video_id, $option)
    {
        $model = Model::build('games');
        return $model::getBankerStatus($video_id, $option);
    }
    /**
     * 游戏参数转换为字符串（参数类型转换）
     * @param  array $option 下注选项倍数数据
     * @return array         下注选项倍数数据
     */
    protected static function parseOption($option)
    {
        $model = Model::build('games');
        return $model::parseOption($option);
    }
    /**
     * 下庄状态变化，金额返还
     * @param  integer $video_id 直播间id
     * @param  string  $group_id IM群组id
     * @return [type]            [description]
     */
    protected static function stopRedisBanker($video_id, $group_id)
    {
        $model = Model::build('games');
        self::pushLog($model::stopRedisBanker($video_id, $group_id));
    }

    /**
     * 发牌
     * @return [type] [description]
     */
    public function start()
    {
        $user_id = self::getUserId();
        $game_id = intval($_REQUEST['id']);
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);

        $game_log_id = Model::build('games')->startGame($game_id, $video_id, $group_id, $user_id);
        if (is_string($game_log_id)) {
            self::returnError($game_log_id);
        }
        self::returnError('', 1, compact('game_log_id'));
    }

    /**
     * 下注
     * @return [type] [description]
     */
    public function bet()
    {
        $user_id = self::getUserId();
        $id = intval($_REQUEST['id']);
        $bet = intval($_REQUEST['bet']);
        $money = intval($_REQUEST['money']);
        $key = [1 => 'option1', 2 => 'option2', 3 => 'option3'];
        if (!isset($key[$bet])) {
            self::returnError('参数错误');
        }
        $field = 'status,group_id,podcast_id,create_time,long_time,game_id,video_id,option,rate';
        $game = self::$redis->get($id, $field);
        $option = json_decode($game['option'], 1);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $account_diamonds = $coin = $user_redis->getOne_db(intval($user_id), 'coin');
        if (defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) {
            $account_diamonds = $user_redis->getOne_db(intval($user_id), 'diamonds');
        }
        $return = [
            'account_diamonds' => intval($account_diamonds),
            'coin' => intval($coin)
        ];
        if (!$game['status']) {
            self::returnError('游戏不存在', 0, $return);
        }
        if (intval($game['create_time'] + $game['long_time'] - NOW_TIME) <= 0) {
            self::returnError('超出投注时间', 0, $return);
        }
        $video = self::$video_redis->getRow_db($game['video_id'], ['banker_id', 'banker_status', 'coin']);
        if ($video['banker_status'] == 2) {
            if ($video['banker_id'] == $user_id) {
                self::returnError('庄家不能投注', 0, $return);
            }
            $op_bet = self::$redis->getBet($id, [$bet]);
            if ($video['coin'] / ($option[$bet] - 1) < $money + $op_bet[0]) {
                self::returnError('超出庄家底金最大投注金额', 0, $return);
            }
        }
        $user_model = Model::build('user');

        $res = $user_model->coin($user_id, -$money);
        if (!$res) {
            self::returnError('余额不足', 0, $return);
        }
        $account_diamonds = $user_model->coin($user_id);
        Model::build('coin_log')->addLog($user_id, $id, -$money, $account_diamonds, '游戏投注');
        Model::build('user_game_log')->addLog($id, $game['podcast_id'], $money, $user_id, $bet, 1);
        self::$redis->bet($id, $bet, $money, $user_id);
        if ($game['rate'] >= rand(1, 50)) {
            $bet_option = $option;
            unset($bet_option[$bet]);
            $bet_option = array_keys($bet_option);
            self::$redis->inc($id, 'option' . $bet_option[array_rand($bet_option, 1)], intval($money * rand(100, 300) / 1000) * 10);
        }
        list($bet, $user_bet) = self::$redis->getBet($id, [1, 2, 3], $user_id);

        $time = intval($game['create_time'] + $game['long_time'] - NOW_TIME);
        $time = $time > 0 ? $time : 0;
        // 新推送
        $ext = [
            'type' => 39,
            'room_id' => intval($game['video_id']),
            'desc' => '',
            'time' => $time,
            'game_id' => intval($game['game_id']),
            'game_log_id' => $id,
            'game_status' => intval($game['status']),
            'game_action' => 2,
            'game_data' => ['bet' => $bet]
        ];
        $res = timSystemNotify($game['group_id'], $ext);
        self::pushLog(compact('res', 'ext', 'data'));
        if (defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) {
            $account_diamonds = $user_redis->getOne_db(intval($user_id), 'diamonds');
        }
        $coin = $user_redis->getOne_db(intval($user_id), 'coin');
        self::returnError('', 1, ['data' => [
            'game_status' => intval($game['status']),
            'time' => $time,
            'account_diamonds' => $account_diamonds,
            'coin' => $coin,
            'game_data' => compact('bet', 'user_bet')
        ]]);
    }

    /**
     * 进入直播间获取游戏信息
     * @return [type] [description]
     */
    public function get_video()
    {
        $id = intval($_REQUEST['id']);
        $video_id = intval($_REQUEST['video_id']);
        $user_id = self::getUserId();
        if (!$id && !$video_id) {
            self::returnError('参数错误');
        }
        if (!$id && $video_id) {
            $id = intval(self::$video_redis->getOne_db($video_id, 'game_log_id'));
        }
        if (!$id) {
            self::returnError('');
        }
        $field = ['video_id', 'group_id', 'status', 'game_id', 'create_time', 'long_time', 'option', 'bet_option', 'dices', 'win'];
        for ($i = 1; $i <= 3; $i++) {
            $field[] = 'option' . $i;
            $field[] = 'option' . $i . ':' . $user_id;
            $field[] = 'option_win' . $i;
            $field[] = 'option_cards' . $i;
            $field[] = 'option_type' . $i;
        }
        $data = self::$redis->get($id, $field);
        $video_id = intval($data['video_id']);
        $option = self::parseOption(json_decode($data['option'], 1));
        if ($data['game_id'] === false) {
            self::returnError('游戏不存在');
        }

        list($banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet) = self::getBankerStatus($video_id, $option);

        $video = self::$video_redis->getRow_db($video_id, ['principal', 'auto_start']);

        $principal = intval($video['principal']);
        $auto_start = intval($video['auto_start']);

        $public_cards = json_decode($data['public_cards'], 1);
        $public_cards = $public_cards ? $public_cards : [];
        $dices = json_decode($data['dices'], 1);
        $dices = $dices ? $dices : [];
        $win = intval($data['win']);
        $bet = [];
        $user_bet = [];
        $cards_data = [];
        for ($i = 1; $i <= 3; $i++) {
            $bet[] = intval($data['option' . $i]);
            $user_bet[] = intval($data['option' . $i . ':' . $user_id]);
            $cards = json_decode($data['option_cards' . $i], 1);
            $type = intval($data['option_type' . $i]);
            $cards_data[] = [
                'cards' => $cards ? $cards : [],
                'type' => $type ? $type : 0
            ];
        }
        $time = intval($data['create_time'] + $data['long_time'] - NOW_TIME);
        $ext = [
            'type' => 39,
            'user_id' => $user_id,
            'desc' => '',
            'room_id' => $data['video_id'],
            'time' => $time > 0 ? $time : 0,
            'game_id' => $data['game_id'],
            'game_log_id' => $id,
            'game_status' => $data['status'],
            'game_action' => 6,
            'auto_start' => $auto_start,
            'option' => array_values($option),
            'bet_option' => json_decode($data['bet_option'], 1),
            'game_data' => compact('public_cards', 'win', 'bet', 'user_bet', 'cards_data', 'dices'),
            'banker_status' => $banker_status,
            'banker' => compact('banker_status', 'banker_id', 'banker_log_id', 'banker_name', 'banker_img', 'coin', 'max_bet'),
            'principal' => $principal
        ];
        $res = timSystemNotify($data['group_id'], $ext, [$user_id]);
        self::pushLog(compact('res', 'ext'));
        self::returnError('', 1, ['data' => $ext, 'game_id' => $data['game_id']]);
    }

    /**
     * 获取游戏记录
     * @return [type] [description]
     */
    public function log()
    {
        $podcast_id = intval($_REQUEST['podcast_id']);
        $game_id = intval($_REQUEST['game_id']);
        if (!$podcast_id || !$game_id) {
            self::returnError('参数错误');
        }
        $number = intval($_REQUEST['number']);
        $where = [
            'podcast_id' => $podcast_id,
            'game_id' => $game_id,
            'status' => 2,
            'result' => ['>', 0]
        ];
        $data = Model::build('game_log')->getList('result', $where, 'id desc', $number ? $number : 20);
        $res = [];
        foreach ($data as $value) {
            if ($value['result']) {
                $res[] = intval($value['result']);
            }
        }
        self::returnError('', 1, ['data' => $res]);

    }

    /**
     * 停止游戏
     * @return [type] [description]
     */
    public function stop()
    {
        $user_id = self::getUserId();
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);
        list(
            $last_game,
            $last_log_id,
            $banker_status,
            $banker_id
        ) = self::getLastGameByVideoId($video_id);
        if ($banker_status) {
            self::stopRedisBanker($video_id, $group_id);
        }
        if (!$last_log_id) {
            self::returnError('不在游戏状态');
        }
        if (self::$redis->isVideoLock($video_id)) {
            self::returnError('操作频率太高了，请等下再点！');
        }
        self::$redis->lockVideo($video_id);
        if ($last_game) {
            $res = Model::build('game_log')->stop($last_log_id);
            if (!$res) {
                self::$redis->unLockVideo($video_id);
                self::returnError('服务器繁忙');
            }
        }
        self::$redis->set($last_log_id, array('long_time' => 0));
        self::$video_redis->update_db($video_id, array('game_log_id' => 0, 'auto_start' => 0));
        if (!$last_game) {
            $ext = array(
                'type' => 34,
                'desc' => ''
            );
            $res = timSystemNotify($group_id, $ext);
        }
        self::$redis->unLockVideo($video_id);
        self::returnError('正在关闭游戏', 1);
    }

    /**
     * 更新用户金币并返回赚取金额
     * @return [type] [description]
     */
    public function userDiamonds()
    {
        $game_log_id = intval($_REQUEST['id']);
        $user_id = self::getUserId();
        $gain = 0;
        if ($game_log_id) {
            $where = [
                'game_log_id' => $game_log_id,
                'user_id' => $user_id,
                'bet' => 0,
                'type' => 2
            ];
            $alert_key = 'game_gain_for_alert:' . md5("$user_id:$game_log_id");
            $game = self::$redis->get($game_log_id, ['video_id', 'group_id', 'podcast_id', $alert_key]);
            if ($game['podcast_id'] == $user_id && OPEN_DIAMOND_GAME_MODULE) {
                $gain = 0;
            } else {
                $log = Model::build('user_game_log')->field('money')->selectOne($where);

                $where = [
                    'game_log_id' => $game_log_id,
                    'user_id' => $user_id,
                    'type' => 1
                ];
                $gain = $log ? intval($log['money']) : 0;
                if (defined('GAME_WINNER') && GAME_WINNER) {
                    $gain -= Model::build('user_game_log')->sum('money', $where);
                }
            }
            $where = [
                'l.game_log_id' => $game_log_id,
                'l.user_id' => ['u.id'],
                'l.podcast_id' => ['<>', 'l.user_id', 'and', 1],
                'l.bet' => 0,
                'l.type' => 2
            ];
            if (defined('GAME_WINNER') && GAME_WINNER) {
                $winner = Model::build('user_game_log')->table('user_game_log l,user u')->field('u.nick_name,l.money')->order('l.money desc')->selectOne($where);
                if (!$winner) {
                    unset($winner);
                }
            }

            if ($gain) {
                $m_config = load_auto_cache("m_config");
                $game_gain_for_alert = intval($m_config['game_gain_for_alert']);
                $gain_gift = intval(defined('GAME_REWARD') && GAME_REWARD == 1);
                if ($game_gain_for_alert || $gain_gift) {
                    if ($game['podcast_id'] != $user_id) {
                        if ($game_gain_for_alert && $gain >= $game_gain_for_alert && !$game[$alert_key]) {
                            self::$redis->set($game_log_id, [$alert_key => 1]);

                            $video_id = intval($game['video_id']);
                            $group_id = $game['group_id'];
                            $podcast_id = intval($game['podcast_id']);
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                            $user_redis = new UserRedisService();
                            $user_info = $user_redis->getRow_db($user_id, array('nick_name'));
                            $nick_name = ($user_info['nick_name']);
                            self::popMessage($video_id, $group_id, $podcast_id, $podcast_id, "恭喜{$nick_name}赢得{$gain}");
                        }
                        if ($gain_gift) {
                            $where = ['diamonds' => ['<=', $gain]];
                            $gift_list = Model::build('prop')->getList($where);
                            if (empty($gift_list)) {
                                unset($gift_list);
                            }
                        }
                    }
                }
            }
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_diamonds = $coin = intval($user_redis->getOne_db($user_id, 'coin'));
        if ((defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) || (defined('OPEN_SEND_DIAMONDS_MODULE') && OPEN_SEND_DIAMONDS_MODULE == 1)) {
            $user_diamonds = intval($user_redis->getOne_db($user_id, 'diamonds'));
        }
        self::returnError('', 1, compact('user_diamonds', 'coin', 'gain', 'gift_list', 'winner'));
    }

    /**
     * 获取兑换比例
     * @return [type] [description]
     */
    public function exchangeRate()
    {
        $m_config = load_auto_cache("m_config");
        $rate = $m_config['coin_exchange_rate'] ? floatval($m_config['coin_exchange_rate']) : 1;
        self::returnError('', 1, ['exchange_rate' => $rate]);
    }

    /**
     * 秀豆兑换游戏币
     * @return [type] [description]
     */
    public function exchangeCoin()
    {
        $user_id = self::getUserId();
        $diamonds = intval($_REQUEST['diamonds']);
        if ($diamonds < 1) {
            self::returnError('请输入兑换秀豆');
        }
        Connect::beginTransaction();
        //减少用户秀豆
        $sql = "update " . DB_PREFIX . "user set diamonds = diamonds - " . $diamonds . "  where id = '" . $user_id . "' and diamonds >= " . $diamonds;
        $GLOBALS['db']->query($sql);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        if ($GLOBALS['db']->affected_rows()) {
            $user_redis->inc_field($user_id, 'diamonds', -$diamonds);
            $account_diamonds = $user_redis->getOne_db(intval($user_id), 'diamonds');
            //会员账户 秀豆变更日志表
            $log_model = Model::build('user_log');
            $log_data = [
                'log_time' => NOW_TIME,
                'log_admin_id' => 0,
                'money' => 0,
                'type' => 7,
                'prop_id' => 0,
                'score' => 0,
                'point' => 0,
                'podcast_id' => 0,
                'diamonds' => -$diamonds,
                'video_id' => 0,
                'log_info' => '兑换游戏币',
                'user_id' => $user_id,
                'ticket' => 0
            ];
            $log_model->insert($log_data);
            $user_model = Model::build('user');
            $m_config = load_auto_cache("m_config");
            $rate = $m_config['coin_exchange_rate'] ? floatval($m_config['coin_exchange_rate']) : 1;
            $res = $user_model->coin($user_id, $diamonds * $rate);
            if (!$res) {
                Connect::rollback();
                self::returnError('兑换失败');
            }
            $coin = $user_model->coin($user_id);
            Model::build('coin_log')->addLog($user_id, -1, $diamonds * $rate, $coin, '秀豆兑换游戏币');
            Connect::commit();
            self::returnError('兑换成功', 1, compact('account_diamonds', 'coin'));
        }
        Connect::rollback();
        self::returnError('余额不足');
    }

    /**
     * 赠送游戏币
     * @return [type] [description]
     */
    public function sendCoin()
    {
        $user_id = self::getUserId();

        $is_nospeaking = Model::build('user')->field('is_nospeaking')->selectOne(['id' => $user_id]);
        $is_nospeaking = intval($is_nospeaking['is_nospeaking']);

        if ($is_nospeaking) {
            self::returnError('被im全局禁言，不能赠送游戏币');
        }
        $coin = intval($_REQUEST['coin']);
        $to_user_id = intval($_REQUEST['to_user_id']);
        if (!($user_id && $to_user_id && $coin > 0)) {
            self::returnError('请输入游戏币');
        }
        $user_model = Model::build('user');
        Connect::beginTransaction();
        $res = $user_model->coin($user_id, -$coin, 'coin');
        if (!$res) {
            Connect::rollback();
            self::returnError('余额不足');
        }
        $res = $user_model->coin($to_user_id, $coin, 'coin');
        if (!$res) {
            Connect::rollback();
            self::returnError('赠送游戏币失败');
        }
        $coin_log_model = Model::build('coin_log');

        $coin1 = $user_model->coin($user_id, false, 'coin');
        $nick_name = $user_model->getOneById($to_user_id, 'nick_name');
        $nick_name = ($nick_name['nick_name']);
        $coin_log_model->addLog($user_id, -1, -$coin, $coin1, "赠送($nick_name)游戏币");
        $coin2 = $user_model->coin($to_user_id, false, 'coin');
        $nick_name = $user_model->getOneById($user_id, 'nick_name');
        $nick_name = ($nick_name['nick_name']);
        $coin_log_model->addLog($to_user_id, -1, $coin, $coin2, "收到($nick_name)游戏币");
        Connect::commit();
        api_ajax_return(
            [
                'status' => 1,
                'error' => '赠送成功',
                'from_msg' => "送给你{$coin}游戏币",
                'to_msg' => "收到{$coin}游戏币",
                'from_score' => "",
                'to_ticket' => 0,
                'to_diamonds' => 0,
                'to_user_id' => $to_user_id,
                'prop_icon' => get_domain() . '/public/gift/jinbi.png',
                'prop_id' => 0,
                'total_ticket' => 0
            ]
        );
    }
    /**
     * 赠送秀豆
     * @return [type] [description]
     */
    public function sendDiamonds()
    {
        $user_id = self::getUserId();

        $is_nospeaking = Model::build('user')->field('is_nospeaking')->selectOne(['id' => $user_id]);
        $is_nospeaking = intval($is_nospeaking['is_nospeaking']);

        if ($is_nospeaking) {
            self::returnError('被im全局禁言，不能赠送秀豆');
        }
        $diamonds = intval($_REQUEST['diamonds']);
        $to_user_id = intval($_REQUEST['to_user_id']);
        if (!($user_id && $to_user_id && $diamonds > 0)) {
            self::returnError('请输入秀豆');
        }
        $user_model = Model::build('user');
        Connect::beginTransaction();
        $res = $user_model->coin($user_id, -$diamonds, 'diamonds');
        if (!$res) {
            Connect::rollback();
            self::returnError('余额不足');
        }
        $res = $user_model->coin($to_user_id, $diamonds, 'diamonds');
        if (!$res) {
            Connect::rollback();
            self::returnError('赠送秀豆失败');
        }
        $coin_log_model = Model::build('coin_log');

        $diamonds1 = $user_model->coin($user_id, false, 'diamonds');
        $nick_name = $user_model->getOneById($to_user_id, 'nick_name');
        $nick_name = ($nick_name['nick_name']);
        $coin_log_model->addLog($user_id, -1, -$diamonds, $diamonds1, "赠送($nick_name)秀豆");
        $diamonds2 = $user_model->coin($to_user_id, false, 'diamonds');
        $nick_name = $user_model->getOneById($user_id, 'nick_name');
        $nick_name = ($nick_name['nick_name']);
        $coin_log_model->addLog($to_user_id, -1, $diamonds, $diamonds2, "收到($nick_name)秀豆");
        Connect::commit();
        api_ajax_return(
            [
                'status' => 1,
                'error' => '赠送成功',
                'from_msg' => "送给你{$diamonds}秀豆",
                'to_msg' => "收到{$diamonds}秀豆",
                'from_score' => "",
                'to_ticket' => 0,
                'to_diamonds' => $diamonds,
                'to_user_id' => $to_user_id,
                'prop_icon' => get_domain() . '/public/images/y3.png',
                'prop_id' => 0,
                'total_ticket' => 0
            ]
        );
    }

    /**
     * iOS日志记录
     * @return [type] [description]
     */
    public function pushTest()
    {
        $user_id = self::getUserId();
        $test_system_im = json_decode($_REQUEST['test_system_im']);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/PushLog.class.php');
        PushLog::log(compact('user_id', 'test_system_im'));
        self::returnError('', 1);
    }
    /**
     * 开启上庄
     * @return [type] [description]
     */
    public function openBanker()
    {
        /**
         * 判断游戏状态
         */
        $user_id = self::getUserId();
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);
        list(
            $last_game,
            $last_log_id,
            $banker_status,
            $banker_id
        ) = self::getLastGameByVideoId($video_id);

        if ($last_game) {
            self::returnError('上局游戏未结束');
        } else {
            $game['status'] = self::$redis->get('status');
            if ($game['status'] == 1) {
                self::returnError('上局游戏未结束');
            }
        }
        if (self::$redis->isVideoLock($video_id)) {
            self::returnError('操作频率太高了，请等下再点！');
        }
        self::$redis->lockVideo($video_id);
        /**
         * 转换游戏状态
         */
        $game = self::$redis->get($last_log_id, 'game_id');
        $game_id = $game['game_id'];
        $game = self::getGameById($game_id, 'principal');
        if (!$banker_status) {
            self::$video_redis->update_db($video_id, array('banker_status' => 1, 'principal' => $game['principal']));
        }
        /**
         * 群发开启上庄消息
         */
        $ext = [
            'type' => 43,
            'desc' => '',
            'room_id' => $video_id,
            'action' => 1,
            'banker_status' => 1,
            'data' => [
                'principal' => intval($game['principal'])
            ]
        ];
        $res = timSystemNotify($group_id, $ext);
        self::pushLog(compact('res', 'ext'));
        self::$redis->unLockVideo($video_id);
        self::returnError('', 1, $ext);
    }
    /**
     * 主播下庄
     * @return [type] [description]
     */
    public function stopBanker()
    {
        /**
         * 判断游戏状态
         */
        $user_id = self::getUserId();
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);
        list(
            $last_game,
            $last_log_id,
            $banker_status,
            $banker_id
        ) = self::getLastGameByVideoId($video_id);
        if ($last_game) {
            self::returnError('上局游戏未结束');
        }
        if (self::$redis->isVideoLock($video_id)) {
            self::returnError('操作频率太高了，请等下再点！');
        }
        self::$redis->lockVideo($video_id);
        /**
         * 返还正在上庄的玩家底金
         */
        Model::build('banker_log')->returnCoin(['video_id' => $video_id, 'status' => ['in', [1, 3]]], '主播下庄');
        /**
         * 改变上庄状态
         */
        $field = ['game_id', 'option'];
        $data = self::$redis->get($last_log_id, $field);
        $option = json_decode($data['option'], 1);

        list($banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet) = self::getBankerStatus($video_id, $option);

        $data = [
            'banker_id' => 0,
            'banker_status' => 0,
            "banker_log_id" => 0,
            "banker_name" => '',
            "banker_img" => '',
            'coin' => 0
        ];
        self::$video_redis->update_db($video_id, $data);
        /**
         * 群发下庄消息
         */
        $ext = [
            'type' => 43,
            'desc' => '',
            'room_id' => $video_id,
            'action' => 4,
            'banker_status' => 0,
            'data' => [
                'banker' => compact(
                    "banker_id",
                    "banker_log_id",
                    "banker_name",
                    "banker_img",
                    'coin'
                )
            ]
        ];
        $res = timSystemNotify($group_id, $ext);
        self::pushLog(compact('res', 'ext'));
        self::$redis->unLockVideo($video_id);
        self::returnError('', 1, $ext);
    }
    /**
     * 申请上庄
     * @return [type] [description]
     */
    public function applyBanker()
    {
        $coin = intval($_REQUEST['coin']);
        $video_id = intval($_REQUEST['video_id']);
        /**
         * 判断游戏状态
         */
        $user_id = self::getUserId();
        list(
            $last_game,
            $last_log_id,
            $banker_status,
            $banker_id
        ) = self::getLastGameByVideoId($video_id);
        if ($last_game) {
            self::returnError('上局游戏未结束');
        }
        if (!$banker_status) {
            self::returnError('已关闭上庄');
        }
        if ($banker_id) {
            self::returnError('已上庄');
        }
        $banker_log_model = Model::build('banker_log');

        $log = $banker_log_model->field('id')->selectOne(
            [
                'user_id' => $user_id,
                'video_id' => $video_id,
                'status' => 1
            ]
        );
        if ($log) {
            self::returnError('已经申请上庄啦！');
        }
        $game = self::$redis->get($last_log_id, 'game_id,group_id,podcast_id');
        $group_id = $game['group_id'];
        $game_id = $game['game_id'];
        $podcast_id = $game['podcast_id'];
        $game = self::getGameById($game_id, 'principal');
        if ($coin < $game['principal']) {
            self::returnError('上庄金额不能低于底金');
        }
        if (self::$redis->isVideoLock($video_id)) {
            self::returnError('服务器繁忙，请等下再点！');
        }
        /**
         * 添加上庄申请记录
         */
        $model = Model::build('user');
        // $res   = $model->coin($user_id, -$coin);
        // if (!$res) {
        //     self::returnError('余额不足');
        // }
        $account_diamonds = $model->coin($user_id);
        if ($account_diamonds < $coin) {
            self::returnError('余额不足');
        }
        // Model::build('coin_log')->addLog($user_id, -1, -$coin, $account_diamonds, '上庄底金');
        $banker_log_model->addLog($video_id, $user_id, $coin);
        $list = $banker_log_model->getBankerList($video_id, 20);
        foreach ($list as $value) {
            if ($value['coin'] < $coin || $value['banker_id'] == $user_id) {
                /**
                 * 群发申请上庄消息
                 */
                $ext = [
                    'type' => 43,
                    'desc' => '',
                    'room_id' => $video_id,
                    'action' => 2,
                    'banker_status' => 1,
                    'data' => [
                        'banker_list' => $list
                    ]
                ];
                $res = timSystemNotify($group_id, $ext, [$podcast_id]);
                self::pushLog(compact('res', 'ext'));
                break;
            }
        }
        self::returnError('上庄成功', 1, ['coin' => $account_diamonds]);
    }
    /**
     * 选庄
     * @return [type] [description]
     */
    public function chooseBanker()
    {
        $banker_log_id = intval($_REQUEST['banker_log_id']);
        /**
         * 判断游戏状态
         */
        $user_id = self::getUserId();
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);
        list(
            $last_game,
            $last_log_id,
            $banker_status,
            $banker_id
        ) = self::getLastGameByVideoId($video_id);
        if ($last_game) {
            self::returnError('上局游戏未结束');
        }
        if (!$banker_status) {
            self::returnError('已关闭上庄');
        }
        if ($banker_id) {
            self::returnError('已上庄');
        }
        $model = Model::build('banker_log');
        $banker = $model->field('user_id,coin')->selectOne(
            [
                'id' => $banker_log_id,
                'video_id' => $video_id,
                'status' => 1
            ]
        );
        if (!$banker) {
            self::returnError('数据错误');
        }
        if (self::$redis->isVideoLock($video_id)) {
            self::returnError('操作频率太高了，请等下再点！');
        }
        self::$redis->lockVideo($video_id);
        $user_model = Model::build('user');

        $res = $user_model->coin($banker['user_id'], -$banker['coin']);
        if (!$res) {
            // $model->update(['status' => 2], ['id' => $banker_log_id]);
            self::$redis->unLockVideo($video_id);
            self::returnError('该玩家余额不足，请重新刷新列表');
        }
        $account_diamonds = $user_model->coin($banker['user_id']);
        Model::build('coin_log')->addLog($banker['user_id'], -1, -$banker['coin'], $account_diamonds, '上庄底金');
        /**
         * 改变上庄状态,退还未选中上庄玩家底金
         */
        $model->chooseBanker($banker_log_id, $video_id);
        $user = Model::build('user')->field('nick_name,head_image')->selectOne(['id' => $banker['user_id']]);
        $data = [
            'banker_status' => 2,
            "banker_id" => intval($banker['user_id']),
            "banker_log_id" => $banker_log_id,
            "banker_name" => ($user['nick_name']),
            "banker_img" => get_spec_image($user['head_image']),
            'coin' => intval($banker['coin'])
        ];
        self::$video_redis->update_db($video_id, $data);
        unset($data['banker_status']);
        /**
         * 群发选庄消息
         */
        $ext = [
            'type' => 43,
            'desc' => '',
            'room_id' => $video_id,
            'action' => 3,
            'banker_status' => 2,
            'data' => [
                'banker' => $data
            ]
        ];
        $res = timSystemNotify($group_id, $ext);
        self::pushLog(compact('res', 'ext'));
        self::$redis->unLockVideo($video_id);
        self::returnError('', 1, $ext);
    }
    /**
     * 申请上庄玩家列表
     * @return [type] [description]
     */
    public function getBankerList()
    {
        $user_id = self::getUserId();
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);

        $model = Model::build('banker_log');
        $banker_list = $model->getBankerList($video_id, 20);
        foreach ($banker_list as $key => $value) {
            $banker_list[$key]['banker_name'] = ($value['banker_name']);
        }
        self::returnError('', 1, compact('banker_list'));
    }
    /**
     * 获取庄家状态
     * @return [type] [description]
     */
    public function getBankerCoin()
    {
        $id = intval($_REQUEST['id']);
        $video_id = intval($_REQUEST['video_id']);
        if (!$id && !$video_id) {
            self::returnError('参数错误');
        }
        if (!$id && $video_id) {
            $id = intval(self::$video_redis->getOne_db($video_id, 'game_log_id'));
        }
        $field = ['game_id', 'option', 'video_id'];
        $data = self::$redis->get($id, $field);
        $option = json_decode($data['option'], 1);
        $video_id = intval($data['video_id']);
        if ($data['game_id'] === false) {
            self::returnError('游戏不存在');
        }
        list($banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet) = self::getBankerStatus($video_id, $option);
        self::returnError('', 1, ['data' => compact('banker_status', 'banker_id', 'banker_log_id', 'banker_name', 'banker_img', 'coin', 'max_bet')]);
    }
    /**
     * 弹幕消息推送
     * @param  [type] $room_id    [description]
     * @param  [type] $group_id   [description]
     * @param  [type] $podcast_id [description]
     * @param  [type] $user_id    [description]
     * @param  [type] $msg        [description]
     * @return [type]             [description]
     */
    protected static function popMessage($room_id, $group_id, $podcast_id, $user_id, $msg)
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_info = $user_redis->getRow_db($user_id, array('nick_name', 'head_image', 'user_level', 'v_icon'));

        $user_info['user_id'] = $user_id;
        $user_info['head_image'] = get_spec_image($user_info['head_image']);

        $ext = [
            'type' => 2, //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
            'room_id' => $room_id, //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
            'num' => 1,
            'prop_id' => 0, //礼物id
            'icon' => '', //图片，是否要: 大中小格式？
            'user_prop_id' => 0, //红包时用到，抢红包的id
            'total_ticket' => intval($user_redis->getOne_db($podcast_id, 'ticket')), //用户总的：秀票数
            'to_user_id' => 0, //礼物接收人（主播）
            'fonts_color' => '', //字体颜色
            'desc' => $msg, //弹幕消息
            'desc2' => $msg, //弹幕消息
            'sender' => $user_info
        ];
        $msg_content = array(
            'MsgType' => 'TIMCustomElem',
            'MsgContent' => array(
                'Data' => json_encode($ext),
                'Desc' => ''
            )
        );
        // PK
        fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
        $api = createTimAPI();
        $ret = $api->group_send_group_msg2($user_id, $group_id, [$msg_content]);
    }
    /**
     * 分销收益列表
     * @return [type] [description]
     */
    public function getDistributionList()
    {
        $user_id = self::getUserId();
        $data = Model::build('game_distribution')->getDistributionList($user_id);
        self::returnError('', 1, compact('data'));
    }
    /**
     * 输入邀请码(定制)
     * @return [type] [description]
     */
    public function invitationCode()
    {
        $user_id = self::getUserId();
        $code = trim($_REQUEST['code']);
        $model = Model::build('user');
        $invitation_id = $model->getInvitationBycode($code);
        if ($invitation_id == $user_id || !$invitation_id) {
            self::returnError('邀请码错误！');
        }
        if ($model->update(compact('invitation_id'), ['id' => $user_id]) === false) {
            self::returnError('失败');
        } else {
            self::returnError('成功', 1);
        }
    }
    /**
     * 验证邀请码(定制)
     * @return [type] [description]
     */
    public function checkCode()
    {
        $user_id = self::getUserId();
        $m_config = load_auto_cache("m_config");
        if ($m_config['enter_invitation_code']) {
            $has_invitation_code = intval(Model::build('user')->getInvitationId($user_id) > 0);
        } else {
            $has_invitation_code = 1;
        }
        $invitation_tip = $m_config['enter_invitation_code_tip'];
        self::returnError('', 1, compact('has_invitation_code', 'invitation_tip'));
    }
    public function autoStart()
    {
        $user_id = self::getUserId();
        $auto_start = intval($_REQUEST['auto_start']);
        list(
            $video_id,
            $group_id
        ) = self::getLiveVideoByUserId($user_id);
        $data = compact('auto_start');
        self::$video_redis->update_db($video_id, $data);
        self::returnError('', 1, $data);
    }
}
