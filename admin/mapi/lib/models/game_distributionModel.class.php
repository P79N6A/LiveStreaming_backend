<?php
/**
 *
 */
class game_distributionModel extends NewModel
{
    /**
     * 获取上级用户信息
     * @param  integer $user_id 用户id
     * @return array            ['id', 'nick_name', 'head_image']
     */
    public function getParent($user_id)
    {
        $parent = null;
        $model  = self::build('user');
        $user   = $model->field('game_distribution_id')->selectOne(['id' => $user_id]);
        if ($user['game_distribution_id']) {
            $parent = $model->field('id', 'nick_name', 'head_image')->selectOne(['id' => $user['game_distribution_id']]);
            if ($parent) {
                $parent['head_image'] = get_spec_image($parent['head_image']);
            }
        }
        return $parent;
    }
    /**
     * 获取分销列表
     * @param  integer $user_id   用户id
     * @param  integer $page      分页页数
     * @param  integer $page_size 分页大小
     * @return array              分销列表
     */
    public function getDistributionList($user_id, $page, $page_size = 20)
    {
        $page    = $page > 0 ? $page : 1;
        $user_id = intval($user_id);
        $field   = [
            'u.id',
            'u.nick_name',
            'u.head_image',
            'gd.is_ticket',
            ["sum(gd.first_distreibution_money * (gd.first_distreibution_id = {$user_id}) + gd.second_distreibution_money * (gd.second_distreibution_id = {$user_id})) as `sum`"],
        ];
        $table = ['user u', 'game_distribution gd'];
        $where = [
            [
                'gd.first_distreibution_id'  => ['=', $user_id],
                'gd.second_distreibution_id' => ['=', $user_id, 'or'],
            ],
            'u.id' => ['gd.user_id'],
        ];
        $list  = $this->table($table)->field($field)->group('u.id,gd.is_ticket')->limit(($page - 1) * $page_size, $page_size)->select($where);
        $count = $this->table($table)->group('u.id')->count($where);
        foreach ($list as $key => $value) {
            $list[$key]['head_image'] = get_spec_image($value['head_image']);
            unset($list[$key]['is_ticket']);
            $list[$key]['sum'] = $value['sum'] . ($value['is_ticket'] ? '秀票' : '游戏币');
        }

        $data = [
            'parent' => $this->getParent($user_id),
            'list'   => $list,
            'page'   => [
                'page'     => $page,
                'has_next' => intval($count > ($page * $page_size)),
            ],
        ];
        if (!$data['parent']) {
            unset($data['parent']);
        }
        return $data;
    }
    public function addLog($user_id, $room_id, $game_log_id, $money, $dec, $is_ticket = false)
    {

        $first_distreibution_id     = 0;
        $first_distreibution_money  = 0;
        $second_distreibution_id    = 0;
        $second_distreibution_money = 0;

        $m_config = load_auto_cache("m_config");

        $model = self::build('user');
        $user  = $model->field('game_distribution_id')->selectOne(['id' => $user_id]);
        if ($user['game_distribution_id']) {
            $first_distreibution_id    = intval($user['game_distribution_id']);
            $first_distreibution       = $model->field('game_distribution_id,game_distribution1')->selectOne(['id' => $first_distreibution_id]);
            $first_distreibution_money = intval($money / 100 * ($first_distreibution['game_distribution1'] ? $first_distreibution['game_distribution1'] : $m_config['game_distribution1']));
            if ($first_distreibution['game_distribution_id']) {
                $second_distreibution_id    = intval($first_distreibution['game_distribution_id']);
                $second_distreibution       = $model->field('game_distribution_id,game_distribution2')->selectOne(['id' => $second_distreibution_id]);
                $second_distreibution_money = intval($money / 100 * ($second_distreibution['game_distribution2'] ? $second_distreibution['game_distribution2'] : $m_config['game_distribution2']));
            }
        }
        $distreibution_money = $money - $first_distreibution_money - $second_distreibution_money;

        $create_time = NOW_TIME;
        $is_ticket   = intval(defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1 || $is_ticket);
        $res         = $this->insert(compact('room_id', 'game_log_id', 'money', 'user_id', 'distreibution_money', 'first_distreibution_id', 'first_distreibution_money', 'second_distreibution_id', 'second_distreibution_money', 'dec', 'create_time', 'is_ticket'));
        if (!$res) {
            return false;
        }
        if ($first_distreibution_money + $second_distreibution_money) {
            $coin = -$first_distreibution_money - $second_distreibution_money;
            if ($is_ticket) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $log_model  = self::build('user_log');
                $res        = $model->update(['ticket' => ['ticket + ' . $coin]], ['id' => $user_id]);
                if ($res) {
                    $user_redis->inc_field($user_id, 'ticket', $coin);
                    $log_data = [
                        'log_info'     => $dec . '抽成(上交)',
                        'log_time'     => NOW_TIME,
                        'log_admin_id' => 0,
                        'money'        => 0,
                        'user_id'      => $user_id,
                        'type'         => 7,
                        'prop_id'      => 0,
                        'score'        => 0,
                        'point'        => 0,
                        'podcast_id'   => $user_id,
                        'diamonds'     => 0,
                        'ticket'       => $coin,
                        'video_id'     => $room_id,
                    ];
                    $log_model->insert($log_data);
                }
                if ($first_distreibution_money) {
                    $res = $model->update(['ticket' => ['ticket + ' . $first_distreibution_money]], ['id' => $first_distreibution_id]);
                    if ($res) {
                        $user_redis->inc_field($first_distreibution_id, 'ticket', $first_distreibution_money);
                        $log_data = [
                            'log_info'     => $dec . '一级抽成(抽取)',
                            'log_time'     => NOW_TIME,
                            'log_admin_id' => 0,
                            'money'        => 0,
                            'user_id'      => $first_distreibution_id,
                            'type'         => 7,
                            'prop_id'      => 0,
                            'score'        => 0,
                            'point'        => 0,
                            'podcast_id'   => $user_id,
                            'diamonds'     => 0,
                            'ticket'       => $first_distreibution_money,
                            'video_id'     => $room_id,
                        ];
                        $log_model->insert($log_data);
                    }
                }
                if ($second_distreibution_money) {
                    $res = $model->update(['ticket' => ['ticket + ' . $second_distreibution_money]], ['id' => $second_distreibution_id]);
                    if ($res) {
                        $user_redis->inc_field($second_distreibution_id, 'ticket', $second_distreibution_money);
                        $log_data = [
                            'log_info'     => $dec . '二级抽成(抽取)',
                            'log_time'     => NOW_TIME,
                            'log_admin_id' => 0,
                            'money'        => 0,
                            'user_id'      => $second_distreibution_id,
                            'type'         => 7,
                            'prop_id'      => 0,
                            'score'        => 0,
                            'point'        => 0,
                            'podcast_id'   => $user_id,
                            'diamonds'     => 0,
                            'ticket'       => $second_distreibution_money,
                            'video_id'     => $room_id,
                        ];
                        $log_model->insert($log_data);
                    }
                }
            } else {
                $coin_log_model   = self::build('coin_log');
                $user_model       = self::build('user');
                $res              = $user_model->coin($user_id, $coin);
                $account_diamonds = $user_model->coin($user_id);
                if ($res) {
                    $coin_log_model->addLog($user_id, $game_log_id, $coin, $account_diamonds, $dec . '抽成(上交)');
                }
                if ($first_distreibution_money) {
                    $res              = $user_model->coin($first_distreibution_id, $first_distreibution_money);
                    $account_diamonds = $user_model->coin($first_distreibution_id);
                    if ($res) {
                        $coin_log_model->addLog($first_distreibution_id, $game_log_id, $first_distreibution_money, $account_diamonds, $dec . '一级抽成(抽取)');
                    }
                }
                if ($second_distreibution_money) {
                    $res              = $user_model->coin($second_distreibution_id, $second_distreibution_money);
                    $account_diamonds = $user_model->coin($second_distreibution_id);
                    if ($res) {
                        $coin_log_model->addLog($second_distreibution_id, $game_log_id, $second_distreibution_money, $account_diamonds, $dec . '二级抽成(抽取)');
                    }
                }
            }
        }
        return $distreibution_money;
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
        $model      = self::build('user');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('id', 'game_distribution_top_id')->selectOne(['id' => $user_id]);

        $where['d.game_distribution_top_id'] = $user_d['game_distribution_top_id'];
        $where['l.from_user_id']             = ['d.id'];
        if ($user_d['id'] == $user_d['game_distribution_top_id']) {
            $sum_child = $model->count(['game_distribution_top_id' => $user_d['game_distribution_top_id']]);
        } else {
            if (!isset($where['d.id'])) {
                $ids = array_merge($first, $second);
                if ($ids) {
                    $where['d.id'] = ['in', $ids];
                } else {
                    return [
                        'sum_first'      => 0,
                        'sum_second'     => 0,
                        'sum_child'      => 0,
                        'total_diamonds' => 0,
                        'list'           => [
                        ],
                    ];
                }
            }
        }
        $sum = $this->table('user d', $table)->field([['SUM(total_diamonds) total_diamonds']])->selectOne($where);

        $total_diamonds = intval($sum['total_diamonds']);
        if ($is_group) {
            $field = [
                'l.from_user_id user_id',
                'l.to_user_id',
                'd.nick_name',
                ['SUM(l.total_diamonds) total_diamonds'],
                'l.create_time',
            ];
            $this->group('l.from_user_id');
        } else {
            $field = [
                'l.from_user_id user_id',
                'l.to_user_id',
                'd.nick_name',
                'l.total_diamonds',
                'l.create_time',
            ];
        }

        $list = $this->table('user d', $table)->field($field)->select($where);
        return compact('sum_first', 'sum_second', 'sum_child', 'total_diamonds', 'list');
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
        $model      = self::build('user');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('id', 'game_distribution_top_id')->selectOne(['id' => $user_id]);

        $where['d.game_distribution_top_id'] = $user_d['game_distribution_top_id'];
        $where['l.user_id']                  = ['d.id'];
        if ($user_d['id'] == $user_d['game_distribution_top_id']) {
            $sum_child = $model->count(['game_distribution_top_id' => $user_d['game_distribution_top_id']]);
        } else {
            if (!isset($where['d.id'])) {
                $ids = array_merge($first, $second);
                if ($ids) {
                    $where['d.id'] = ['in', $ids];
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
        $sum = $this->table('user d', 'user_game_log_history l')->field(['SUM(l.money *(l.game_log_id>0)) sum_bet'], ['SUM(l.money *(l.game_log_id>0)) sum_gain'])->selectOne($where);

        $sum_bet  = intval($sum['sum_bet']);
        $sum_gain = intval($sum['sum_gain']);

        $field = [
            'l.user_id',
            'd.nick_name',
            'l.game_log_id',
            ['SUM(l.money *(l.game_log_id=0)) bet'],
            ['SUM(l.money *(l.game_log_id>0)) gain'],
            'l.create_time',
        ];

        if ($is_group) {
            $this->group('l.user_id');
        } else {
            $this->group('l.game_log_id');
        }
        $list = $this->table('user d', 'user_game_log_history l')->field($field)->select($where);
        return compact('sum_first', 'sum_second', 'sum_child', 'sum_bet', 'sum_gain', 'list');
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
    public function childDistribution($user_id, $where = '', $is_group = true)
    {
        $user_id    = intval($user_id);
        $model      = self::build('user');
        $first      = $model->getChild($user_id);
        $second     = $model->getChild($first);
        $sum_first  = sizeof($first);
        $sum_second = sizeof($second);

        $user_d = $model->field('id', 'game_distribution_top_id')->selectOne(['id' => $user_id]);

        $where['d.game_distribution_top_id'] = $user_d['game_distribution_top_id'];
        $where['l.user_id']                  = ['d.id'];
        if ($user_d['id'] == $user_d['game_distribution_top_id']) {
            $sum_child    = $model->count(['game_distribution_top_id' => $user_d['game_distribution_top_id']]);
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
        $sum = $this->table('user d', 'game_distribution l')->field(
            ['sum(l.money) sum_money'],
            ["sum({$first_field}+{$second_field}) sum_distribution"]
        )->selectOne($where);

        $sum_money        = intval($sum['sum_money']);
        $sum_distribution = intval($sum['sum_distribution']);

        if ($is_group) {
            $field = [
                'l.user_id',
                'd.nick_name',
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
                'd.nick_name',
                'l.game_log_id',
                'l.money',
                'l.distreibution_money',
                'l.first_distreibution_money',
                'l.second_distreibution_money',
                'l.create_time',
            ];
        }

        $list = $this->table('user d', 'game_distribution l')->field($field)->select($where);

        return compact('sum_first', 'sum_second', 'sum_child', 'sum_money', 'sum_distribution', 'list');
    }
    /**
     * [childPropDistribution description]
     * @param  [type] $user_id [description]
     * @param  string $where   [description]
     * @return [type]          [description]
     */
    public function childPropDistribution($user_id, $where = '', $is_group = true)
    {

        $where['l.game_log_id'] = 0;
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
        $where['l.game_log_id'] = ['<>', 0];
        return $this->childDistribution($user_id, $where, $is_group);
    }
}
