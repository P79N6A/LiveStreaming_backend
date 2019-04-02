<?php
/**
 *
 */
class gamesModel extends NewModel
{
    /**
     * 花色字典
     * @var array
     */
    public static $colors = [
        'spade' => 0,
        'heart' => 1,
        'club' => 2,
        'diamond' => 3
    ];
    /**
     * 牌点字典
     * @var array
     */
    public static $figures = [
        'A' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13
    ];
    /**
     * redis对象实例
     * @var [type]
     */
    protected static $redis, $video_redis;
    /**
     * 根据投注总数排序发牌结果
     * @param  array $sum 投注总数
     * @param  array $res 发牌结果
     * @return array      发牌结果
     */
    protected static function sortGame($sum, $res)
    {
        $min = min($sum);
        asort($sum);
        $data = array();
        foreach ($sum as $k => $v) {
            if ($v == $min) {
                $data[] = array_shift($res);
                shuffle($data);
                shuffle($res);
            } else {
                $data[] = array_pop($res);
            }
        }
        $result = array();
        $i = 0;
        foreach ($sum as $k => $v) {
            $result[$k] = $data[$i];
            $i++;
        }
        ksort($result);
        return $result;
    }
    /**
     * 根据字典转换卡牌数据
     * @param  [type] $cards [description]
     * @return [type]        [description]
     */
    public static function parseCards($cards)
    {
        $new_cards = array();
        foreach ($cards as $v) {
            $new_cards[] = array(self::$colors[$v[0]], self::$figures[$v[1]]);
        }
        return $new_cards;
    }
    /**
     * 格式化卡片卡牌数据
     * @param  [type] $cards_data [description]
     * @return [type]             [description]
     */
    protected static function formatCardsData($cards_data)
    {
        $data = [];
        foreach ($cards_data as $key => $value) {
            $cards = self::parseCards($value['cards']);
            $data[] = array(
                'win' => !$key,
                'cards' => $cards,
                'type' => $value['check']['type']
            );
        }
        return $data;
    }
    /**
     * 游戏定时器
     * @return [type] [description]
     */
    public function crontab()
    {
        /**
         * 封装各方法
         * @return [type] [description]
         */
        $microtime = microtime(1);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GamesRedisService.php');
        $redis = new GamesRedisService();
        if ($redis->isLock()) {
            return array('is_lock');
        }
        $redis->lock();
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $game_log_model = self::build('game_log');

        $time = NOW_TIME;
        $games = $game_log_model->field('id,game_id,create_time,long_time')->select(['status' => 1]);
        $game_types = array();
        $return_data = array('time' => 0);
        /**
         * 游戏处理
         */
        if ($games) {
            $m_config = load_auto_cache("m_config");
            $game_commission = +$m_config['game_commission'];
            $podcast_commission = intval(defined('PODCAST_COMMISSION') && PODCAST_COMMISSION);

            $bm_promoter = intval(defined('OPEN_BM') && OPEN_BM);
            if ($bm_promoter) {
                $bm_config = load_auto_cache("bm_config");
                $promoter_times = floatval($bm_config['promoter_center_game_stream_revenue']) / 100;
                $platform_times = floatval($bm_config['sites_game_stream_revenue']) / 100;
            }

            $user_model = self::build('user');
            $games_model = self::build('games');
            $coin_log_model = self::build('coin_log');
            $banker_log_model = self::build('banker_log');
            $user_game_log_model = self::build('user_game_log');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/Poker.class.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/NiuNiu.class.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/DeZhou.class.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/Dice.class.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            foreach ($games as $game) {
                $game_log_id = $game['id'];
                $game_id = $game['game_id'];
                $game_log = $redis->get($game_log_id, 'video_id,podcast_id,group_id,public_cards,banker_id');
                $video_id = $game_log['video_id'];
                $podcast_id = $game_log['podcast_id'];
                $banker_id = $game_log['banker_id'];
                if (!isset($game_types[$game_id])) {
                    $game_types[$game_id] = $games_model->field('commission_rate,rate,option,class,principal,ticket_rate')->selectOne(['id' => $game_id]);
                }
                $game_type = $game_types[$game_id];
                $option = json_decode($game_type['option'], 1);
                if (!in_array($game_type['class'], ['Poker', 'NiuNiu', 'DeZhou', 'Dice'])) {
                    break;
                }
                $game_object = new $game_type['class']();
                if ($game['create_time'] + $game['long_time'] <= $time) {
                    $live_in = $video_redis->getOne_db($video_id, 'live_in');
                    if ($redis->isVideoLock($video_id)) {
                        break;
                    }
                    $redis->lockVideo($video_id);
                    if ($live_in && $game['long_time']) {
                        $sql_time = microtime(1);
                        // 计算投注结果
                        $sum = [];
                        $sum_v = [];
                        $table = DB_PREFIX . 'user_game_log';
                        foreach ($option as $key => $value) {
                            $res = intval($user_game_log_model->sum('money', ['game_log_id' => $game_log_id, 'bet' => $key]));
                            $sum[] = $res;
                            $sum_v[] = $res * $value;
                        }
                        $rate = $user_redis->getOne_db($podcast_id, 'rate');
                        $rate = $rate ? $rate : (+$game_type['rate']);
                        $cheat = rand(1, 100) < $rate && !$banker_id;

                        $dices = [];
                        $cards_data = [];
                        switch ($game_id) {
                            case 1:
                            case 2:
                                $cards_data = self::formatCardsData($game_object->play());
                                if ($cheat) {
                                    $cards_data = self::sortGame($sum_v, $cards_data);
                                } else {
                                    shuffle($cards_data);
                                }
                                break;
                            case 3:
                                $game_object->flop(json_decode($game_log['public_cards'], 1));
                                $data = self::formatCardsData($game_object->play());
                                $cards = self::parseCards($game_object->gp);
                                $gp = array(
                                    'win' => false,
                                    'cards' => $cards,
                                    'type' => 0
                                );
                                if ($game_object->compare($res[0], $res[1]) == $game_object->compare($res[1], $res[0])) {
                                    $data[0]['win'] = false;
                                    $gp['win'] = true;
                                    shuffle($data);
                                } else {
                                    if ($cheat && $sum_v[2] > $sum_v[0]) {
                                        $value = $data[0];
                                        $data[0] = $data[1];
                                        $data[1] = $value;
                                    } else {
                                        shuffle($data);
                                    }
                                }
                                $cards_data = array(
                                    $data[0],
                                    $gp,
                                    $data[1]
                                );
                                break;
                            case 4:
                                if ($cheat) {
                                    $key = [];
                                    $min = min($sum_v);
                                    foreach ($sum_v as $k => $v) {
                                        if ($v == $min) {
                                            $key[] = $k;
                                        }
                                    }
                                    $key = $key[array_rand($key)];
                                    $total = $key == 1 ? 7 : ($key ? rand(2, 6) : rand(8, 12));
                                } else {
                                    $total = false;
                                }
                                $dices = $game_object->play(2, $total);
                                break;
                            default:
                                break;
                        }
                        // 计算得胜结果
                        $result = 0;
                        $data = array('status' => 2);
                        if ($cards_data) {
                            foreach ($cards_data as $key => $value) {
                                if ($value['win']) {
                                    $result = $key + 1;
                                }
                                unset($cards_data[$key]['win']);
                                $data['option_win' . ($key + 1)] = intval($value['win']);
                                $data['option_cards' . ($key + 1)] = json_encode($value['cards']);
                                $data['option_type' . ($key + 1)] = $value['type'];
                            }
                        } else if ($dices) {
                            $res = array_sum($dices);
                            switch ($res) {
                                case 7:
                                    $result = 2;
                                    break;
                                default:
                                    $result = $res > 7 ? 1 : 3;
                                    break;
                            }
                            $data['dices'] = json_encode($dices);
                        }
                        $data['win'] = $result;
                        $times = $option[$result];
                        $redis->set($game_log_id, $data);
                        // MySQL结束游戏
                        // 计算主播收入
                        $commission_rate = $game_type['commission_rate'];
                        $income = array_sum($sum) - $times * $sum[$result - 1];
                        $suit_patterns = json_encode($cards_data ? $cards_data : $dices);
                        $bet = json_encode($sum);
                        $podcast_income = $income > 0 ? intval($income * $commission_rate / 100) : 0;
                        $final_income = $banker_id ? ($income > 0 ? intval($income * $game_commission / 100) : 0) : $income - $podcast_income;
                        $commission = 0;
                        Connect::beginTransaction();
                        // 游戏记录结算
                        if ($game_log_model->resultLog($game_log_id, $result, $bet, $suit_patterns, $podcast_income, $banker_id ? 0 : $final_income) === false) {
                            Connect::rollback();
                            $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $game_log_model->getLastSql();
                            break;
                        }
                        // 玩家收入
                        if ($sum[$result - 1]) {
                            // 获得下注总金额三倍返还
                            if ($user_model->multiAddCoin($game_log_id, $result, $times) === false) {
                                Connect::rollback();
                                $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_model->getLastSql();
                                break;
                            }
                            // 批量插入金币记录
                            if ($coin_log_model->multiAddLog($game_log_id, $result, $times) === false) {
                                Connect::rollback();
                                $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $coin_log_model->getLastSql();
                                break;
                            }
                            // 平台抽成
                            if ($game_commission) {
                                if ($user_model->multiAddCoin($game_log_id, $result, -$game_commission / 100 * $times) === false) {
                                    Connect::rollback();
                                    $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_model->getLastSql();
                                    break;
                                }
                                // 批量插入金币记录
                                if ($coin_log_model->multiAddLog($game_log_id, $result, -$game_commission / 100 * $times, '玩家收入平台抽成') === false) {
                                    Connect::rollback();
                                    $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $coin_log_model->getLastSql();
                                    break;
                                }
                            }
                            // 主播抽成
                            if ($podcast_commission) {
                                if ($user_model->multiAddCoin($game_log_id, $result, -$commission_rate / 100 * $times) === false) {
                                    Connect::rollback();
                                    $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_model->getLastSql();
                                    break;
                                }
                                // 批量插入金币记录
                                if ($coin_log_model->multiAddLog($game_log_id, $result, -$commission_rate / 100 * $times, '玩家收入主播抽成') === false) {
                                    Connect::rollback();
                                    $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $coin_log_model->getLastSql();
                                    break;
                                }
                                $commission = $commission_rate / 100 * $times * $sum[$result - 1];
                                if (defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) {
                                    $ticket = intval($commission * $game_type['ticket_rate'] / 100);
                                    $res = $user_model->update(['ticket' => ['ticket + ' . $ticket]], ['id' => $podcast_id]);
                                    $video_redis->inc_field($video_id, 'vote_number', $ticket);
                                    $in_livepk = $video_redis->getOne_db($video_id, 'in_livepk');
                                    if ($in_livepk) {
                                        $video_redis->inc_field($video_id, 'pk_ticket', $ticket); // PK中
                                    }
                                    if ($res) {
                                        $log_data = [
                                            'log_info' => '主播游戏赢家抽成',
                                            'log_time' => NOW_TIME,
                                            'log_admin_id' => 0,
                                            'money' => 0,
                                            'user_id' => 1,
                                            'type' => 7,
                                            'prop_id' => 0,
                                            'score' => 0,
                                            'point' => 0,
                                            'podcast_id' => $podcast_id,
                                            'diamonds' => 0,
                                            'ticket' => $ticket,
                                            'video_id' => $video_id
                                        ];
                                        self::build('user_log')->insert($log_data);
                                    }
                                    if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                                        if (self::build('game_distribution')->addLog($podcast_id, $video_id, $game_log_id, $ticket, '游戏直播分销') === false) {
                                            Connect::rollback();
                                            $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_game_log_model->getLastSql();
                                            break;
                                        }
                                    }
                                } else {
                                    $res = $user_model->coin($podcast_id, $commission);
                                    $account_diamonds = $user_model->coin($podcast_id);
                                    if ($res) {
                                        //会员账户 金币变更日志表
                                        if ($coin_log_model->addLog($podcast_id, $game_log_id, $commission, $account_diamonds, '主播游戏赢家抽成') === false) {
                                            Connect::rollback();
                                            $return_data[$game_log_id] = 'error:' . __LINE__;
                                            break;
                                        }
                                    }
                                    if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                                        if (self::build('game_distribution')->addLog($podcast_id, $video_id, $game_log_id, $commission, '游戏直播分销') === false) {
                                            Connect::rollback();
                                            $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_game_log_model->getLastSql();
                                            break;
                                        }
                                    }
                                }
                            }
                            $win_rate = (1 - ($game_commission + $commission_rate * $podcast_commission) / 100);
                            $win_times = $times * $win_rate;
                            // 批量插入获胜记录
                            if ($user_game_log_model->multiAddLog($game_log_id, $result, $win_times, $podcast_id) === false) {
                                Connect::rollback();
                                $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_game_log_model->getLastSql();
                                break;
                            }
                            if (defined('WEIXIN_DISTRIBUTION') && WEIXIN_DISTRIBUTION) {
                                self::build('weixin_distribution_log')->muitAddLog($game_log_id, $win_rate, '直播游戏分销');
                            }
                        }
                        // 主播收入增加
                        if ($podcast_income) {
                            if (defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) {
                                $ticket = intval($podcast_income * $game_type['ticket_rate'] / 100);
                                $res = $user_model->update(['ticket' => ['ticket + ' . $ticket]], ['id' => $podcast_id]);
                                $video_redis->inc_field($video_id, 'vote_number', $ticket);
                                $in_livepk = $video_redis->getOne_db($video_id, 'in_livepk');
                                if ($in_livepk) {
                                    $video_redis->inc_field($video_id, 'pk_ticket', $ticket); // PK中
                                }
                                if ($res) {
                                    $log_data = [
                                        'log_info' => '游戏直播收入',
                                        'log_time' => NOW_TIME,
                                        'log_admin_id' => 0,
                                        'money' => 0,
                                        'user_id' => $podcast_id,
                                        'type' => 7,
                                        'prop_id' => 0,
                                        'score' => 0,
                                        'point' => 0,
                                        'podcast_id' => $podcast_id,
                                        'diamonds' => 0,
                                        'ticket' => $ticket,
                                        'video_id' => $video_id
                                    ];
                                    self::build('user_log')->insert($log_data);
                                }

                                //判断是否有公会ljz
                                $user_society = $user_model->field('society_id')->where(array('id' => $podcast_id))->selectOne();
                                if ($user_society['society_id'] != 0) {
                                    //会长id
                                    $president_id = self::build('society')->field('user_id')->where(array('id' => $user_society['society_id']))->selectOne();
                                    //判断公会模式
                                    $m_config = load_auto_cache('m_config');
                                    if ($m_config['society_pattern'] == 2) {
                                        $this->society_pattern_profit(10, '公会收益', $m_config, $user_model, $ticket, $podcast_id, $video_id, $president_id);
                                    } elseif ($m_config['society_pattern'] == 1 && $m_config['society_profit_platform'] != 1) {
                                        $this->society_pattern_profit(8, '会长抽成', $m_config, $user_model, $ticket, $podcast_id, $video_id, $president_id);
                                    }
                                }

                                if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                                    if (self::build('game_distribution')->addLog($podcast_id, $video_id, $game_log_id, $ticket, '游戏直播分销') === false) {
                                        Connect::rollback();
                                        $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_game_log_model->getLastSql();
                                        break;
                                    }
                                }

                            } else {
                                $res = $user_model->coin($podcast_id, $podcast_income);
                                $account_diamonds = $user_model->coin($podcast_id);
                                if ($res) {
                                    //会员账户 金币变更日志表
                                    if ($coin_log_model->addLog($podcast_id, $game_log_id, $podcast_income, $account_diamonds, '游戏直播收入') === false) {
                                        Connect::rollback();
                                        $return_data[$game_log_id] = 'error:' . __LINE__;
                                        break;
                                    }
                                }
                                if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                                    if (self::build('game_distribution')->addLog($podcast_id, $video_id, $game_log_id, $podcast_income, '游戏直播分销') === false) {
                                        Connect::rollback();
                                        $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $user_game_log_model->getLastSql();
                                        break;
                                    }
                                }
                            }
                        }
                        // 主播收入增加
                        if ($podcast_income + $commission) {
                            if ($user_game_log_model->addLog($game_log_id, $podcast_id, $podcast_income + $commission) === false) {
                                Connect::rollback();
                                $return_data[$game_log_id] = 'error:' . __LINE__;
                                break;
                            }
                        }
                        if ($bm_promoter) {
                            $win_times = $win_times ? $win_times : 0;
                            if (self::build('bm_promoter')->addGameLog($game_log_id, $result, $win_times, $promoter_times, $platform_times) === false) {
                                Connect::rollback();
                                $return_data[$game_log_id] = 'error:' . __LINE__ . ' sql:' . $game_log_model->getLastSql();
                                break;
                            }
                        }

                        $stop_banker = false;
                        // 庄家收入
                        if ($banker_id) {
                            $banker_income = $income;
                            if ($income > 0) {
                                $banker_income = intval((100 - $game_commission - $commission_rate) / 100 * $income);
                            }
                            $res = $banker_log_model->update(['coin' => ["`coin`+$banker_income"]], ['user_id' => $banker_id, 'video_id' => $video_id, 'status' => 3]);
                            $video_redis->inc_field($video_id, 'coin', $banker_income);
                            $coin = $video_redis->getOne_db($video_id, 'coin');
                            if ($res) {
                                if ($user_game_log_model->addLog($game_log_id, $banker_id, $banker_income) === false) {
                                    Connect::rollback();
                                    $return_data[$game_log_id] = 'error:' . __LINE__;
                                    break;
                                }
                            }
                            // 强制下庄
                            if ($coin < $game_type['principal']) {
                                $stop_banker = true;
                            }
                        }
                        Connect::commit();
                        $sql_time = microtime(1) - $sql_time;
                        $ids = array();
                        if ($sum[$result - 1]) {
                            $res = $user_game_log_model->field('user_id')->group('user_id')->select(array('game_log_id' => $game_log_id));
                            foreach ($res as $value) {
                                $ids[] = $value['user_id'];
                            }
                        }
                        if ($podcast_income) {
                            $ids[] = $podcast_id;
                        }
                        if (!empty($ids)) {
                            user_deal_to_reids($ids);
                        }
                        $banker = $video_redis->getRow_db($video_id, [
                            'banker_status',
                            'banker_id',
                            'banker_log_id',
                            'banker_name',
                            'banker_img',
                            'coin'
                        ]);
                        $tim_time = microtime(1);
                        // 新推送
                        $ext = array(
                            'type' => 39,
                            'desc' => '',
                            'room_id' => $video_id,
                            'time' => 0,
                            'game_id' => $game_id,
                            'game_log_id' => $game_log_id,
                            'game_status' => 2,
                            'game_action' => 4,
                            'podcast_income' => $podcast_income,
                            'game_data' => array(
                                'win' => $result,
                                'bet' => $sum,
                                'dices' => $dices,
                                'cards_data' => $cards_data
                            ),

                            'banker_status' => intval($banker['banker_status']),
                            'banker' => [
                                'banker_id' => intval($banker['banker_id']),
                                'banker_log_id' => intval($banker['banker_log_id']),
                                'banker_name' => $banker['banker_name'] ? $banker['banker_name'] : '',
                                'banker_img' => $banker['banker_img'] ? $banker['banker_img'] : '',
                                'coin' => intval($banker['coin']),
                                'max_bet' => $banker['coin'] / (max($option) - 1)
                            ]
                        );
                        $res = timSystemNotify($game_log['group_id'], $ext);
                        if ($stop_banker) {
                            if ($banker_log_model->returnCoin(['video_id' => $video_id, 'status' => 3], '底金不足，玩家下庄') == false) {
                                $return_data[$game_log_id] = 'error:' . __LINE__ . $banker_log_model->getLastSql();
                                break;
                            }
                            $banker_ext = [
                                'type' => 43,
                                'desc' => '',
                                'room_id' => $video_id,
                                'action' => 4,
                                'banker_status' => 0,
                                'data' => [
                                    'banker' => [
                                        'banker_id' => intval($banker['banker_id']),
                                        'banker_log_id' => intval($banker['banker_log_id']),
                                        'banker_name' => $banker['banker_name'] ? $banker['banker_name'] : '',
                                        'banker_img' => $banker['banker_img'] ? $banker['banker_img'] : '',
                                        'coin' => intval($banker['coin'])
                                    ]
                                ]
                            ];
                            $data = [
                                'banker_id' => 0,
                                'banker_status' => 0,
                                "banker_log_id" => 0,
                                "banker_name" => '',
                                "banker_img" => '',
                                'coin' => 0
                            ];
                            $video_redis->update_db($video_id, $data);
                            $banker_res = timSystemNotify($game_log['group_id'], $banker_ext);
                        }
                        $tim_time = microtime(1) - $tim_time;
                        $return_data[$game_log_id] = array(
                            'type' => 'result',
                            'id' => $game_log_id,
                            'data' => $ext,
                            'res' => $res,
                            'sql_time' => $sql_time,
                            'tim_time' => $tim_time,
                            'banker_ext' => $banker_ext,
                            'banker_res' => $banker_res
                        );
                        if (defined('OPEN_MISSION') && OPEN_MISSION) {
                            $gamers = $user_game_log_model->field('user_id')->select(['game_log_id' => $game_log_id, 'type' => 1]);
                            if ($gamers) {
                                $mission_model = self::build('mission');
                                foreach ($gamers as $gamer) {
                                    $mission_model->incProgress($gamer['user_id'], 1);
                                }
                            }
                        }
                    } else {
                        // 返还投注
                        Connect::beginTransaction();
                        if ($game_log_model->update(array('status' => 2), array('id' => $game_log_id)) === false) {
                            Connect::rollback();
                            $return_data[$game_log_id] = 'error:' . __LINE__;
                            break;
                        }
                        $redis->set($game_log_id, array('status' => 2));
                        if ($user_model->returnCoin($game_log_id) === false) {
                            Connect::rollback();
                            $return_data[$game_log_id] = 'error:' . __LINE__;
                            break;
                        }
                        if ($coin_log_model->returnCoin($game_log_id) === false) {
                            Connect::rollback();
                            $return_data[$game_log_id] = 'error:' . __LINE__;
                            break;
                        }
                        if ($GLOBALS['db']->affected_rows()) {
                            $res = $user_game_log_model->field('user_id')->group('user_id')->select(array('game_log_id' => $game_log_id));
                            $ids = array();
                            foreach ($res as $value) {
                                $ids[] = $value['user_id'];
                            }
                            user_deal_to_reids($ids);
                        }
                        Connect::commit();
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
                        $ext = array(
                            'type' => 34,
                            'desc' => ''
                        );
                        $res = timSystemNotify($game_log['group_id'], $ext);
                        $return_data[$game_log_id] = array(
                            'type' => 'end_return',
                            'id' => $game_log_id,
                            'res' => $res
                        );
                    }
                    $redis->unLockVideo($video_id);
                } else if ($game['create_time'] + $game['long_time'] > $time && !$banker_id) {
                    // 机器人下注
                    $robot_num = $video_redis->getOne_db($video_id, 'robot_num');
                    if ($robot_num) {
                        $rest = $game['create_time'] + $game['long_time'] - $time;
                        if (rand(1, 300) < $game_type['rate'] && ($time - $game['create_time'] > 5)) {
                            $option = json_decode($game_type['option'], 1);
                            $data = array();
                            $op = array_rand($option);
                            for ($i = 0; $i < $robot_num; $i++) {
                                if (isset($data[$op])) {
                                    $data[$op] += rand(0, 10) * 10;
                                } else {
                                    $data[$op] = rand(0, 10) * 10;
                                }
                            }
                            foreach ($data as $key => $value) {
                                $redis->inc($game_log_id, 'option' . $key, $value);
                            }

                            $data = $redis->get($game_log_id, array('option1', 'option2', 'option3'));
                            $bet = array();
                            for ($i = 1; $i <= 3; $i++) {
                                $bet[] = intval($data['option' . $i]);
                            }
                            $ext = array(
                                'type' => 39,
                                'room_id' => $video_id,
                                'desc' => '',
                                'time' => $rest,
                                'game_id' => $game_id,
                                'game_log_id' => $game_log_id,
                                'game_status' => 1,
                                'game_action' => 2,
                                'game_data' => array(
                                    'bet' => $bet
                                )
                            );
                            $res = timSystemNotify($game_log['group_id'], $ext);
                            $return_data[$game_log_id] = array(
                                'type' => 'robot',
                                'id' => $game_log_id,
                                'ext' => $ext,
                                'res' => $res
                            );
                        }
                    }
                }
            }
        }
        $return_data['clearGameLog'] = $this->clearGameLog();
        if (defined('OPEN_BANKER_MODULE') && OPEN_BANKER_MODULE == 1) {
            $return_data['clearBankerLog'] = $this->clearBankerLog($video_redis);
        }
        if ($bm_promoter) {
            self::build('bm_promoter')->payPromoter();
        }
        $return_data['time'] = microtime(1) - $microtime;
        $redis->unLock();
        return $return_data;
    }
    /**
     * 将关闭的直播游戏上庄记录移入历史记录
     * @return void
     */
    public function clearBankerLog($video_redis)
    {
        $return = [];
        $video_table = DB_PREFIX . 'video';
        $table = DB_PREFIX . 'banker_log';
        $history = DB_PREFIX . 'banker_log_history';
        $res = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM `$table` WHERE `video_id` NOT IN(SELECT id FROM `$video_table` WHERE live_in = 1)");
        $return[__LINE__] = $res;
        if ($res) {
            $res = $GLOBALS['db']->getAll("SELECT `video_id` FROM `$table` WHERE `video_id` NOT IN(SELECT id FROM `$video_table` WHERE live_in = 1) AND `status` IN (1,3) GROUP BY `video_id`");
            $model = self::build('banker_log');
            foreach ($res as $key => $value) {
                $data = [
                    'banker_id' => 0,
                    'banker_status' => 0,
                    "banker_log_id" => 0,
                    "banker_name" => '',
                    "banker_img" => '',
                    'coin' => 0
                ];
                $video_redis->update_db($value['video_id'], $data);
                $res = $model->returnCoin(['video_id' => $value['video_id'], 'status' => ['in', [1, 3]]], '主播退出,退还上庄金额');
                if ($res === false) {
                    Connect::rollback();
                    $return[__LINE__] = $model->getLastSql();
                    return $return;
                }
            }

            Connect::beginTransaction();
            $res = Connect::exec("INSERT INTO `$history`(SELECT * FROM `$table` WHERE `video_id` NOT IN(SELECT id FROM `$video_table` WHERE live_in = 1) AND `status` IN (2,4))");
            if ($res === false) {
                $return[__LINE__] = $res;
                return $return;
            }
            $res = Connect::exec("DELETE FROM `$table` WHERE `video_id` NOT IN(SELECT id FROM `$video_table` WHERE live_in = 1) AND `status` IN (2,4)");
            if ($res === false) {
                Connect::rollback();
                $return[__LINE__] = $res;
                return $return;
            }
            Connect::commit();
        }
        return $return;
    }
    /**
     * 将关闭的直播游戏记录移入历史记录
     * @return void
     */
    public function clearGameLog()
    {
        $return = [];
        $table = DB_PREFIX . 'game_log';
        $video_table = DB_PREFIX . 'video';

        $res = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM `$table` WHERE podcast_id NOT IN(SELECT user_id FROM `$video_table` WHERE live_in = 1) AND `status`=2");
        $return[__LINE__] = $res;
        if ($res) {
            $history = DB_PREFIX . 'game_log_history';
            $log_table = DB_PREFIX . 'user_game_log';
            $log_history = DB_PREFIX . 'user_game_log_history';
            Connect::beginTransaction();
            /**
             * 游戏记录迁移
             * @var [type]
             */
            $res = Connect::exec("INSERT INTO `$history`(SELECT * FROM `$table` WHERE podcast_id NOT IN(SELECT user_id FROM `$video_table` WHERE live_in = 1) AND `status`=2)");
            if ($res === false) {
                Connect::rollback();
                $return[__LINE__] = $res;
                return $return;
            }
            $res = Connect::exec("DELETE FROM `$table` WHERE podcast_id NOT IN (SELECT user_id FROM `$video_table` WHERE live_in = 1) AND `status`=2");
            if ($res === false) {
                Connect::rollback();
                $return[__LINE__] = $res;
                return $return;
            }
            /**
             * 下注记录迁移
             * @var [type]
             */
            $res = Connect::exec("INSERT INTO `$log_history`(SELECT * FROM `$log_table` WHERE `game_log_id` NOT IN(SELECT `id` FROM `$table`))");
            if ($res === false) {
                Connect::rollback();
                $return[__LINE__] = $res;
                return $return;
            }
            $res = Connect::exec("DELETE FROM `$log_table` WHERE `game_log_id` NOT IN (SELECT `id` FROM `$table`)");
            if ($res === false) {
                Connect::rollback();
                $return[__LINE__] = $res;
                return $return;
            }
            Connect::commit();
        }
        return $return;
    }
    public function autoCrontab()
    {
        $define = defined('GAME_AUTO_START') && GAME_AUTO_START;
        if (!$define) {
            return 'no_define';
        }
        $video_model = self::build('video');
        $list = $video_model->field('id,group_id,user_id')->select(['live_in' => 1]);
        if (!$list) {
            return 'no_list';
        }
        $res = [];
        $m_config = load_auto_cache("m_config");

        $auto_time = $m_config['auto_time'];
        $auto_time = $auto_time > 1 ? $auto_time : 5;
        self::checkFile();
        foreach ($list as $video) {
            $video_id = $video['id'];
            if (!self::$video_redis->getOne_db($video_id, 'auto_start')) {
                continue;
            }
            $game_log_id = self::$video_redis->getOne_db($video_id, 'game_log_id');

            $game = self::$redis->get($game_log_id, 'game_id,long_time,create_time,video_id,group_id');
            if ($game['create_time'] + $game['long_time'] + $auto_time > NOW_TIME) {
                continue;
            }
            if (self::$redis->isVideoLock($video_id)) {
                continue;
            }
            $res[] = self::startGame($game['game_id'], $video_id, $video['group_id'], $video['user_id']);
        }
        return $res;
    }
    protected static function checkFile()
    {
        if (!self::$redis) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/GamesRedisService.php');
            self::$redis = new GamesRedisService();
        }
        if (!self::$video_redis) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            self::$video_redis = new VideoRedisService();
        }
    }
    public static function startGame($id, $video_id, $group_id, $user_id)
    {
        self::checkFile();
        $game = self::build('games')->selectOne(['id' => $id]);
        if (!$game) {
            return '游戏参数错误';
        }
        list(
            $last_game,
            $last_log_id,
            $banker_status,
            $banker_id
        ) = self::getLastGameByVideoId($video_id);
        if ($last_game) {
            return '上局游戏未结束';
        }
        if (self::$redis->isVideoLock($video_id)) {
            return '操作频率太高了，请等下再点！';
        }
        self::$redis->lockVideo($video_id);
        if ($banker_status == 1) {
            self::stopRedisBanker($video_id, $group_id);
        }
        $game_log_id = self::build('game_log')->addLog($user_id, $game['long_time'], $id, $banker_id);
        if (!$game_log_id) {
            self::$redis->unLockVideo($video_id);
            return '服务器繁忙';
        }
        self::$video_redis->update_db($video_id, ['game_log_id' => $game_log_id]);
        $data = [
            'podcast_id' => $user_id,
            'long_time' => $game['long_time'],
            'game_id' => $id,
            'create_time' => NOW_TIME,
            'video_id' => $video_id,
            'group_id' => $group_id,
            'option' => $game['option'],
            'bet_option' => $game['bet_option'],
            'status' => 1,
            'banker_id' => $banker_id,
            'rate' => $game['rate']
        ];
        $public_cards = [];
        if (in_array($id, [3])) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/Poker.class.php');
            $poker = new Poker();

            $public_cards = $poker->pickCards(1);
            $data['gp'] = json_encode($public_cards);
            foreach ($public_cards as $key => $value) {
                $public_cards[$key] = [self::$colors[$value[0]], self::$figures[$value[1]]];
            }
        }
        self::$redis->set($game_log_id, $data); // 插入redis
        $option = self::parseOption(json_decode($game['option'], 1));

        list($banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet) = self::getBankerStatus($video_id, $option);
        // 新推送
        $ext = [
            'type' => 39,
            'desc' => '',
            'room_id' => $video_id,
            'time' => $game['long_time'],
            'game_id' => $id,
            'game_log_id' => $game_log_id,
            'game_status' => 1,
            'game_action' => 1,
            'option' => array_values($option),
            'bet_option' => json_decode($game['bet_option'], 1),
            'game_data' => compact('public_cards'),
            'banker_status' => $banker_status,
            'banker' => compact('banker_status', 'banker_id', 'banker_log_id', 'banker_name', 'banker_img', 'coin', 'max_bet')
        ];
        $res = timSystemNotify($group_id, $ext);
        self::$redis->unLockVideo($video_id);
        return $game_log_id;
    }

    /**
     * 获取游戏状态信息
     * @param  integer $video_id 直播间id
     * @return array             [$last_game, $last_log_id, $banker_status, $banker_id]
     */
    public static function getLastGameByVideoId($video_id)
    {
        self::checkFile();
        $last_game = false;
        $video = self::$video_redis->getRow_db($video_id, ['game_log_id', 'banker_status', 'banker_id']);
        $last_log_id = intval($video['game_log_id']);
        $banker_status = intval($video['banker_status']);
        $banker_id = intval($video['banker_id']);
        if ($last_log_id) {
            $last_game = self::$redis->get($last_log_id, 'create_time,long_time');
            $last_game = NOW_TIME < $last_game['create_time'] + $last_game['long_time'] + 1;
        }
        return [$last_game, $last_log_id, $banker_status, $banker_id];
    }
    /**
     * 获取上庄信息
     * @param  integer $video_id 直播间id
     * @param  array   $option   下注选项倍数数据
     * @return array             [$banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet]
     */
    public static function getBankerStatus($video_id, $option)
    {
        self::checkFile();
        $video = self::$video_redis->getRow_db(
            $video_id, [
                'banker_status',
                'banker_id',
                'banker_log_id',
                'banker_name',
                'banker_img',
                'coin'
            ]
        );
        $banker_status = intval($video['banker_status']);
        $banker_id = intval($video['banker_id']);
        $banker_log_id = intval($video['banker_log_id']);
        $banker_name = $video['banker_name'] ? $video['banker_name'] : '';
        $banker_img = $video['banker_img'] ? $video['banker_img'] : '';
        $coin = intval($video['coin']);
        $max_bet = intval($coin / (max($option) - 1));
        return [$banker_status, $banker_id, $banker_log_id, $banker_name, $banker_img, $coin, $max_bet];
    }
    /**
     * 游戏参数转换为字符串（参数类型转换）
     * @param  array $option 下注选项倍数数据
     * @return array         下注选项倍数数据
     */
    public static function parseOption($option)
    {
        foreach ($option as $key => $value) {
            $option[$key] = $value . '';
        }
        return $option;
    }
    /**
     * 下庄状态变化，金额返还
     * @param  integer $video_id 直播间id
     * @param  string  $group_id IM群组id
     * @return [type]            [description]
     */
    public static function stopRedisBanker($video_id, $group_id)
    {
        self::build('banker_log')->returnCoin(['video_id' => $video_id, 'status' => ['in', [1, 3]]], '主播下庄');
        $data = [
            'banker_id' => 0,
            'banker_status' => 0,
            'banker_log_id' => 0,
            'banker_name' => '',
            'banker_img' => '',
            'coin' => 0
        ];
        self::$video_redis->update_db($video_id, $data);
        unset($data['banker_status']);
        $ext = [
            'type' => 43,
            'desc' => '',
            'room_id' => $video_id,
            'action' => 4,
            'banker_status' => 0,
            'data' => [
                'banker' => $data
            ]
        ];
        $res = timSystemNotify($group_id, $ext);
        return compact('res', 'ext', 'video_id', 'group_id');
    }
    protected static function pushLog($data)
    {
        if (IS_DEBUG) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/PushLog.class.php');
            PushLog::log($data);
        }
    }
    //公会不同模式下的游戏抽成
    public function society_pattern_profit($type, $reminder, $m_config, $user_model, $ticket, $podcast_id, $video_id, $president_id)
    {
        if ($type == 8) {
//计算抽成
            $ticket = $ticket * $m_config['society_profit_ratio'];
        }
        //扣除主播收益
        $res1 = $user_model->update(['refund_ticket' => ['refund_ticket + ' . $ticket]], ['id' => $podcast_id]);
        if ($res1) {
            $log_data = [
                'log_info' => '游戏直播收入扣除' . $reminder,
                'log_time' => NOW_TIME,
                'log_admin_id' => 0,
                'money' => 0,
                'user_id' => $podcast_id,
                'type' => $type,
                'prop_id' => 0,
                'score' => 0,
                'point' => 0,
                'podcast_id' => $podcast_id,
                'diamonds' => 0,
                'ticket' => $ticket,
                'video_id' => $video_id
            ];
            self::build('user_log')->insert($log_data);
        }
        //收益转给会长
        $res2 = $user_model->update(['ticket' => ['ticket + ' . $ticket]], ['id' => $president_id]);
        if ($res2) {
            $log_data = [
                'log_info' => '主播' . $podcast_id . '游戏直播收入贡献',
                'log_time' => NOW_TIME,
                'log_admin_id' => 0,
                'money' => 0,
                'user_id' => $president_id,
                'type' => $type,
                'prop_id' => 0,
                'score' => 0,
                'point' => 0,
                'podcast_id' => $podcast_id,
                'diamonds' => 0,
                'ticket' => $ticket,
                'video_id' => $video_id
            ];
            self::build('user_log')->insert($log_data);
        }
    }

}
