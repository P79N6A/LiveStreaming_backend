<?php
/**
 *
 */
class weixin_distribution_logModel extends NewModel
{
    public function addLog($user_id, $money, $des, $type = 1, $game_log_id = 0, $is_ticket = false)
    {
        /**
         * 一级分销人id
         */
        $first_distreibution_id = 0;
        /**
         * 一级分销金额
         */
        $first_distreibution_money = 0;
        /**
         * 二级分销人id
         */
        $second_distreibution_id = 0;
        /**
         * 二级分销金额
         */
        $second_distreibution_money = 0;

        $m_config = load_auto_cache("m_config");

        $model = self::build('weixin_distribution');
        $user  = $model->field('pid')->selectOne(['user_id' => $user_id]);
        /**
         * 计算分销金额
         */
        if ($user['pid']) {
            $first_distreibution_id    = intval($user['pid']);
            $first_distreibution       = $model->field('pid,first_rate')->selectOne(['user_id' => $first_distreibution_id]);
            $first_distreibution_money = intval($money / 100 * ($first_distreibution['first_rate'] ? $first_distreibution['first_rate'] : $m_config['weixin_first_rate']));
            if ($first_distreibution['pid']) {
                $second_distreibution_id    = intval($first_distreibution['pid']);
                $second_distreibution       = $model->field('pid,second_rate')->selectOne(['user_id' => $second_distreibution_id]);
                $second_distreibution_money = intval($money / 100 * ($second_distreibution['second_rate'] ? $second_distreibution['second_rate'] : $m_config['weixin_second_rate']));
            }
        }
        $distreibution_money = $money - $first_distreibution_money - $second_distreibution_money;

        $create_time = NOW_TIME;
        $is_ticket   = intval(defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1 || $is_ticket);
        $res         = $this->insert(compact('money', 'user_id', 'distreibution_money', 'first_distreibution_id', 'first_distreibution_money', 'second_distreibution_id', 'second_distreibution_money', 'des', 'type', 'game_log_id', 'create_time', 'is_ticket'));
        if (!$res) {
            return false;
        }
        /**
         * 分销金额分发，日志添加
         */
        if ($first_distreibution_money + $second_distreibution_money) {
            $coin       = -$first_distreibution_money - $second_distreibution_money;
            $user_model = self::build('user');
            if ($is_ticket) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $log_model  = self::build('user_log');
                $res        = $user_model->update(['ticket' => ['ticket + ' . $coin]], ['id' => $user_id]);
                $log_data   = [
                    'log_time'     => NOW_TIME,
                    'log_admin_id' => 0,
                    'money'        => 0,
                    'type'         => 7,
                    'prop_id'      => 0,
                    'score'        => 0,
                    'point'        => 0,
                    'podcast_id'   => 0,
                    'diamonds'     => 0,
                    'video_id'     => 0,
                ];
                if ($res) {
                    $user_redis->inc_field($user_id, 'ticket', $coin);
                    $log_data['log_info'] = $des . '抽成(上交)';
                    $log_data['user_id']  = $user_id;
                    $log_data['ticket']   = $coin;
                    $log_model->insert($log_data);
                }
                if ($first_distreibution_money) {
                    $res = $user_model->update(['ticket' => ['ticket + ' . $first_distreibution_money]], ['id' => $first_distreibution_id]);
                    if ($res) {
                        $user_redis->inc_field($first_distreibution_id, 'ticket', $first_distreibution_money);
                        $log_data['log_info'] = $des . '一级抽成(抽取)';
                        $log_data['user_id']  = $first_distreibution_id;
                        $log_data['ticket']   = $first_distreibution_money;
                        $log_model->insert($log_data);
                    }
                }
                if ($second_distreibution_money) {
                    $res = $user_model->update(['ticket' => ['ticket + ' . $second_distreibution_money]], ['id' => $second_distreibution_id]);
                    if ($res) {
                        $user_redis->inc_field($second_distreibution_id, 'ticket', $second_distreibution_money);
                        $log_data['log_info'] = $des . '二级抽成(抽取)';
                        $log_data['user_id']  = $second_distreibution_id;
                        $log_data['ticket']   = $second_distreibution_money;
                        $log_model->insert($log_data);
                    }
                }
            } else {
                $coin_log_model   = self::build('coin_log');
                $res              = $user_model->coin($user_id, $coin);
                $account_diamonds = $user_model->coin($user_id);
                if ($res) {
                    $coin_log_model->addLog($user_id, -1, $coin, $account_diamonds, $des . '抽成(上交)');
                }
                if ($first_distreibution_money) {
                    $res              = $user_model->coin($first_distreibution_id, $first_distreibution_money);
                    $account_diamonds = $user_model->coin($first_distreibution_id);
                    if ($res) {
                        $coin_log_model->addLog($first_distreibution_id, -1, $first_distreibution_money, $account_diamonds, $des . '一级抽成(抽取)');
                    }
                }
                if ($second_distreibution_money) {
                    $res              = $user_model->coin($second_distreibution_id, $second_distreibution_money);
                    $account_diamonds = $user_model->coin($second_distreibution_id);
                    if ($res) {
                        $coin_log_model->addLog($second_distreibution_id, -1, $second_distreibution_money, $account_diamonds, $des . '二级抽成(抽取)');
                    }
                }
            }
        }
        return $distreibution_money;
    }
    public function muitAddLog($game_log_id, $rate, $des)
    {
        if ($rate) {
            $model = self::build('user_game_log');
            $list  = $model->field('id,user_id,money')->select(['type' => 2, 'game_log_id' => $game_log_id]);
            foreach ($list as $value) {
                $gain = $value['money'] / $rate;
                if ($gain) {
                    $distreibution_money = $this->addLog($value['user_id'], $gain, $des, 2, $game_log_id);
                    if ($distreibution_money === false) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/PushLog.class.php');
                        PushLog::log($this->getLastSql());
                        continue;
                    }
                    $distreibution_money = $gain - $distreibution_money;
                    if ($distreibution_money) {
                        $model->update(['money' => ['money - ' . $distreibution_money]], ['id' => $value['id']]);
                    }
                }
            }
        }
    }
    /**
     * 下级礼物明细及汇总
     * @param  [type]  $user_id [description]
     * @param  string  $where   [description]
     * @param  int     $date    [description]
     * @return array
     * [
     *     'sum_first'         => 一级分销人数,
     *     'sum_second'        => 二级分销人数,
     *     'sum_child'         => 三级以上分销人数,
     *     'total_diamonds'    => 总礼物金额,
     *     'list'              => [
     *         'user_id'       => 用户id,
     *         'nick_name'     => 昵称,
     *         'game_log_id'   => 游戏id,
     *         'total_diamonds'=> 分销金额,
     *         'create_time'   => 创建时间,
     *     ],
     * ]
     */
    public function childProp($user_id, $where = '', $date = false, $is_group = true)
    {
        if ($date === false) {
            $date = NOW_TIME;
        }
        $table = 'video_prop_' . to_date($date, 'Ym') . ' l';

        $user_id    = intval($user_id);
        $model      = self::build('weixin_distribution');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('user_id', 'topid')->selectOne(compact('user_id'));

        $where['d.topid']        = $user_d['topid'];
        $where['l.from_user_id'] = ['d.user_id'];
        if ($user_d['user_id'] == $user_d['topid']) {
            $sum_child = $model->count(['topid' => $user_d['topid']]);
        } else {
            $sum_child = $sum_first + $sum_second;
            if (!isset($where['d.user_id'])) {
                $ids = array_merge($first, $second);
                if ($ids) {
                    $where['d.user_id'] = ['in', $ids];
                } else {
                    return [
                        'sum_first'  => 0,
                        'sum_second' => 0,
                        'sum_child'  => 0,
                        'sum_bet'    => 0,
                        'sum_gain'   => 0,
                        'list'       => [
                        ],
                    ];
                }
            }
        }
        $first  = $first ? $first : [0];
        $second = $second ? $second : [0];
        $sum    = $this->table('weixin_distribution d', $table)->field([
            ['SUM(total_diamonds*(d.user_id in (' . implode($first, ',') . '))) first_distribution'],
            ['SUM(total_diamonds*(d.user_id in (' . implode($second, ',') . '))) second_distreibution'],
            ['SUM(total_diamonds) total_diamonds'],
        ])->selectOne($where);

        $first_distribution   = intval($sum['first_distribution']);
        $second_distreibution = intval($sum['second_distreibution']);
        $total_diamonds       = intval($sum['total_diamonds']);
        if ($is_group) {
            $field = [
                'l.from_user_id user_id',
                'l.to_user_id',
                'u.nick_name',
                ['SUM(l.total_diamonds) total_diamonds'],
                'l.create_time',
            ];
            $this->group('l.from_user_id');
        } else {
            $field = [
                'l.from_user_id user_id',
                'l.to_user_id',
                'u.nick_name',
                'l.total_diamonds',
                'l.create_time',
            ];
        }

        $where['u.id'] = ['d.user_id'];

        $list = $this->table('weixin_distribution d', $table, 'user u')->field($field)->select($where);
        return compact('sum_first', 'sum_second', 'sum_child', 'total_diamonds', 'list', 'first_distribution', 'second_distreibution');
    }
    /**
     * 下级游戏明细及汇总
     * @param  [type] $user_id [description]
     * @return array
     * [
     *     'sum_first'       => 一级分销人数,
     *     'sum_second'      => 二级分销人数,
     *     'sum_child'       => 三级以上分销人数,
     *     'sum_bet'         => 总下注金额,
     *     'sum_gain'        => 总收益金额,
     *     'list'            => [
     *         'user_id'     => 用户id,
     *         'nick_name'   => 昵称,
     *         'game_log_id' => 游戏id,
     *         'bet'         => 下注金额,
     *         'gain'        => 收益,
     *         'create_time' => 创建时间,
     *     ],
     * ]
     */
    public function childGame($user_id, $where = '', $is_group = true)
    {
        $user_id    = intval($user_id);
        $model      = self::build('weixin_distribution');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('user_id', 'topid')->selectOne(compact('user_id'));

        $where['d.topid']   = $user_d['topid'];
        $where['l.user_id'] = ['d.user_id'];
        if ($user_d['user_id'] == $user_d['topid']) {
            $sum_child = $model->count(['topid' => $user_d['topid']]);
        } else {
            $sum_child = $sum_first + $sum_second;
            if (!isset($where['d.user_id'])) {
                $ids = array_merge($first, $second);
                if ($ids) {
                    $where['d.user_id'] = ['in', $ids];
                } else {
                    return [
                        'sum_first'  => 0,
                        'sum_second' => 0,
                        'sum_child'  => 0,
                        'sum_bet'    => 0,
                        'sum_gain'   => 0,
                        'list'       => [
                        ],
                    ];
                }
            }
        }
        $sum = $this->table('weixin_distribution d', 'user_game_log_history l')->field(['SUM(l.money *(type = 1)) sum_bet'], ['SUM(l.money *(type = 2)) sum_gain'])->selectOne($where);

        $sum_bet  = intval($sum['sum_bet']);
        $sum_gain = intval($sum['sum_gain']);

        $field = [
            'l.user_id',
            'u.nick_name',
            'l.game_log_id',
            ['SUM(l.money *(type = 1)) bet'],
            ['SUM(l.money *(type = 2)) gain'],
            'l.create_time',
        ];

        $where['u.id'] = ['d.user_id'];
        if ($is_group) {
            $this->group('l.user_id');
        } else {
            $this->group('l.game_log_id');
        }

        $list = $this->table('weixin_distribution d', 'user_game_log_history l', 'user u')->field($field)->select($where);
        return compact('sum_first', 'sum_second', 'sum_child', 'sum_bet', 'sum_gain', 'list');
    }
    public function paymentNotice($user_id, $where = [])
    {
        $user_id    = intval($user_id);
        $model      = self::build('weixin_distribution');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('user_id', 'topid')->selectOne(compact('user_id'));

        $where['d.topid']   = $user_d['topid'];
        $where['l.user_id'] = ['d.user_id'];
        $where['l.is_paid'] = 1;

        $field = [
            'l.user_id',
            'u.nick_name',
            'l.money',
            'l.create_time',
        ];
        $where['l.user_id'] = ['u.id'];
        $where['u.id']      = ['d.user_id'];
        if ($user_d['user_id'] != $user_d['topid']) {
            $ids = array_merge($first, $second);
            $ids = empty($ids) ? [0] : $ids;
            $where['d.user_id'] = ['in', $ids];
        }
        $list = $this->table('weixin_distribution d', 'payment_notice l', 'user u')->field($field)->select($where);
        return compact('list');
    }
    /**
     * 下级分销明细及汇总
     * @param  [type] $user_id [description]
     * @return array
     * [
     *     'sum_first'                      => 一级分销人数,
     *     'sum_second'                     => 二级分销人数,
     *     'sum_child'                      => 三级以上分销人数,
     *     'sum_money'                      => 总金额,
     *     'sum_distribution'               => 总分销金额,
     *     'list'                           => [
     *         'user_id'                    => 用户id,
     *         'nick_name'                  => 昵称,
     *         'game_log_id'                => 游戏id,
     *         'money'                      => 总金额,
     *         'distreibution_money'        => 分销金额,
     *         'first_distreibution_money'  => 一级分销金额,
     *         'second_distreibution_money' => 二级分销金额,
     *         'create_time'                => 创建时间,
     *     ],
     * ]
     */
    public function childDistribution($user_id, $where = [], $is_group = true)
    {
        $user_id    = intval($user_id);
        $model      = self::build('weixin_distribution');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('user_id', 'topid')->selectOne(compact('user_id'));

        $where['d.topid']   = $user_d['topid'];
        $where['l.user_id'] = ['d.user_id'];
        if ($user_d['user_id'] == $user_d['topid']) {
            $sum_child    = $model->count(['topid' => $user_d['topid']]);
            $first_field  = 'l.first_distreibution_money';
            $second_field = 'l.second_distreibution_money';
        } else {
            $where[] = [
                'l.first_distreibution_id'  => $user_id,
                'l.second_distreibution_id' => ['=', $user_id, 'or'],
            ];
            $first_field  = "l.first_distreibution_money*(l.first_distreibution_id={$user_id})";
            $second_field = "l.second_distreibution_money*(l.second_distreibution_id={$user_id})";
        }

        $first  = $first ? $first : [0];
        $second = $second ? $second : [0];

        $sum = $this->table('weixin_distribution d', 'weixin_distribution_log l')->field(
            ['sum(`money`) sum_money'],
            ['SUM(money*(d.user_id in (' . implode($first, ',') . '))) first_distribution'],
            ['SUM(money*(d.user_id in (' . implode($second, ',') . '))) second_distreibution'],
            ["sum({$first_field}+{$second_field}) sum_distribution"]
        )->selectOne($where);

        $sum_money            = intval($sum['sum_money']);
        $first_distribution   = intval($sum['first_distribution']);
        $second_distreibution = intval($sum['second_distreibution']);
        $sum_distribution     = intval($sum['sum_distribution']);

        $where['u.id'] = ['d.user_id'];
        if ($is_group) {
            $field = [
                'l.user_id',
                'u.nick_name',
                ['0 as game_log_id'],
                ['sum(l.money) as money'],
                ['sum(l.distreibution_money) as distreibution_money'],
                ["sum({$first_field}) as first_distreibution_money"],
                ["sum({$second_field}) as second_distreibution_money"],
                [strtotime(date('Y-m-01')) . ' as create_time'],
            ];
            $this->group('l.user_id');
        } else {
            $field = [
                'l.user_id',
                'u.nick_name',
                'l.game_log_id',
                'l.money',
                'l.distreibution_money',
                'l.first_distreibution_money',
                'l.second_distreibution_money',
                'l.create_time',
            ];
        }
        $list = $this->table('weixin_distribution d', 'weixin_distribution_log l', 'user u')->field($field)->select($where);
        return compact('sum_first', 'sum_second', 'sum_child', 'sum_money', 'sum_distribution', 'list', 'first_distribution', 'second_distreibution');
    }
    /**
     * [childPropDistribution description]
     * @param  [type] $user_id [description]
     * @param  string $where   [description]
     * @return [type]          [description]
     */
    public function childPropDistribution($user_id, $where = '', $is_group = true)
    {

        $where['l.type'] = 1;
        return $this->childDistribution($user_id, $where, $is_group);
    }
    /**
     * [childGameDistribution description]
     * @param  [type] $user_id [description]
     * @param  string $where   [description]
     * @return [type]          [description]
     */
    public function childGameDistribution($user_id, $where = '', $is_group = true)
    {
        $where['l.type'] = 2;
        return $this->childDistribution($user_id, $where, $is_group);
    }
}
