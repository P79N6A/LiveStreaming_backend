<?php

class SocietyAction extends CommonAction
{
    protected static function str_trim($str)
    {
        $str = strim($str);
        $str = preg_replace("@<script(.*?)</script>@is", "", $str);
        $str = preg_replace("@<iframe(.*?)</iframe>@is", "", $str);
        $str = preg_replace("@<style(.*?)</style>@is", "", $str);
        return preg_replace("@<(.*?)>@is", "", $str);
    }

    /**
     * 公会列表
     */
    public function index()
    {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        //追加默认参数
        if ($this->get("default_map")) {
            $map = array_merge($map, $this->get("default_map"));
        }

        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        if ($name == 'WeiboUserLevel') {
            $name = 'UserLevel';
        }
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $list = $this->get("list");

        if ($name == 'IndexImage') {
            foreach ($list as $k => $v) {
                $list[$k]['image'] = get_spec_image($v['image']);
            }
        }

        $where = 's.user_id = u.id';
        if (isset($_REQUEST['name'])) {
            $where .= ' and s.name like \'%' . addslashes($_REQUEST['name']) . '%\'';
        }
        if (isset($_REQUEST['nick_name'])) {
            $where .= ' and u.nick_name like \'%' . addslashes($_REQUEST['nick_name']) . '%\'';
        }
        if ($_REQUEST['begin_time']) {
            $where .= ' and s.create_time>=' . strtotime($_REQUEST['begin_time']);
        }
        if ($_REQUEST['end_time']) {
            $where .= ' and s.create_time<=' . strtotime($_REQUEST['end_time']);
        }
        if (!isset($_REQUEST['status'])) {
            $_REQUEST['status'] = -1;
        }
        if ($_REQUEST['status'] != -1) {
            $where .= ' and s.status=' . intval($_REQUEST['status']);
        } else {
            $where .= ' and s.status!=4 ';
        }

        $user_id = intval($_REQUEST['user_id']);
        if ($user_id > 0) {
            $where .= ' and s.user_id=' . $user_id;
        }

        //排序字段
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
            if ($order == 'id') {
                $order = 's.id';
            } elseif ($order == 'create_time') {
                $order = 's.create_time';
            }
        }
        if (isset($_REQUEST['_sort'])) {
            $_REQUEST['_sort'] ? $sort = 'asc' : $sort = 'desc';
            $where .= "order by ";
            $where .= $order;
            $where .= " ";
            $where .= $sort;
        } else {
            $where .= " order by s.id desc ";
        }

        $model = M('society');
        $table = DB_PREFIX . 'society s,' . DB_PREFIX . 'user u';
        $count = $model->table($table)->where($where)->count();
        $p = new Page($count, $listRows = 10);
        if ($count) {
            $field = 's.*,u.nick_name';
            $list = $model->table($table)->where($where)->field($field)->limit($p->firstRow . ',' . $p->listRows)->select();
            /*admin_ajax_return($model ->getLastSql());*/
            //var_dump($list);
            foreach ($list as $key => $value) {
                $list[$key]['create_time'] = to_date($value['create_time']);
                $list[$key]['name'] = ($value['name']);
                $list[$key]['nick_name'] = ($value['nick_name']);
                $list[$key]['manifesto'] = ($value['manifesto']);
                if ($list[$key]['logo'] != '') {
                    $list[$key]['logo'] = get_spec_image($value['logo'], 35, 35);
                }
            }
        }
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $this->assign("society_pattern", $m_config['society_pattern']);

        $this->assign("page", $p->show());
        $this->assign("list", $list);
        $this->display();

    }

    /**
     * 公会详情
     */
    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $model = M('society');
        $table = DB_PREFIX . 'society s,' . DB_PREFIX . 'user u';
        $field = 's.*,u.nick_name';
        $where = 's.user_id = u.id and s.id=' . $id;
        $data = $model->table($table)->where($where)->field($field)->find();
        if ($data) {
            $data['create_time'] = to_date($data['create_time']);
            $data['logo'] = get_spec_image($data['logo']);
            $data['name'] = ($data['name']);
            $data['manifesto'] = ($data['manifesto']);
        }
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $this->assign("society_pattern", $m_config['society_pattern']);
        $this->assign('open_society_code', $m_config['open_society_code']);

        $this->assign('vo', $data);
        $this->display();
    }

    /**
     * 更新公会信息
     */
    public function update()
    {

        $id = intval($_REQUEST['id']);
        $status = intval($_REQUEST['status']);
        $logo = $_REQUEST['logo'];
        $memo = self::str_trim($_REQUEST['memo']) ? self::str_trim($_REQUEST['memo']) : 0;
        $manifesto = self::str_trim($_REQUEST['manifesto']);
        $name = self::str_trim($_REQUEST['name']);
        $is_withdraw = self::str_trim($_REQUEST['is_withdraw']);

        $m_config = load_auto_cache('m_config');
        $refund_rate = $_REQUEST['refund_rate'];
        if ($refund_rate <= 0 || floatval($refund_rate) >= 1) {
            $this->error("比例范围为0~1之间，不包括0和1");
        } elseif (floatval($refund_rate) == 0) {
            $refund_rate = floatval($m_config['society_public_rate']);
        }
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $id)));
        if (!$id) {
            $this->error("参数错误");
        }

        //ljz 获取排序
        $rank = intval($_REQUEST['society_rank']);
        if ($rank < 0) {
            $this->error("参数必须大于0");
        }
        $modal = M('society');
        $society = $modal->field('name,user_id,status,society_rank,is_withdraw')->where("id=" . $id)->find();

        if (($m_config['society_pattern'] == 3)) {
            if ($society['is_withdraw'] == 0) {
                if (empty($is_withdraw)) {
                    $this->error("请选择是否可以提现");
                }
            } else {
                $is_withdraw = $society['is_withdraw'];
            }
        }

        $log_info = $society['name'];
        $user_id = $society['user_id'];
        $status = $society['status'] == '1' ? 1 : $status;
        $society_rank = $society['society_rank'];
        //$res      = $modal->save(array('memo' => $memo,'name'=>$name,'manifesto'=>$manifesto,'status' => $status,'society_rank' => $rank,'refund_rate' => $refund_rate,'id' => $id));
        $mysql = "update " . DB_PREFIX . "society set logo='$logo',memo='$memo',name='$name',manifesto='$manifesto',status=$status,society_rank=$rank,refund_rate=$refund_rate,is_withdraw=$is_withdraw where id=" . $id;
        $res = $GLOBALS['db']->query($mysql);
        M('user')->save(array('society_id' => $id, 'society_chieftain' => 1, 'id' => $user_id));

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_redis->update_db($user_id, array('society_id' => $id, 'society_chieftain' => 1));

        if (false === $res) {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        } else {
            //成功提示
            clear_auto_cache("banner_list");
            load_auto_cache("banner_list");
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        }
    }

    /**
     * 公会成员
     */
    public function view()
    {
        $id = intval($_REQUEST['id']);
        $model = M('society');
        $field = 'name';
        $where = 'id=' . $id;
        $society = $model->where($where)->field($field)->find();
        $society['name'] = ($society['name']);
        $table = DB_PREFIX . 'society s,' . DB_PREFIX . 'user u,' . DB_PREFIX . 'society_apply sa';

        $where = 'u.id =sa.user_id  and s.id =' . $id . ' and  sa.society_id=' . $id;

        if (!isset($_REQUEST['status'])) {
            $_REQUEST['status'] = -1;
        }
        if ($_REQUEST['status'] != -1) {
            $where .= ' and sa.status=' . intval($_REQUEST['status']);
        } else {
            $where .= ' and sa.status<4';
        }
        $user_id = intval($_REQUEST['user_id']); //成员id
        if ($user_id > 0) {
            $where .= ' and sa.user_id=' . intval($_REQUEST['user_id']);
        }

        $count = $model->table($table)->where($where)->count();
        $p = new Page($count, $listRows = 20);
        if ($count) {
            $field = 'u.is_authentication,u.society_chieftain,u.nick_name,u.id,u.head_image,sa.society_id,sa.`status`,sa.create_time,sa.apply_type,sa.deal_time';

            $list = $model->table($table)->where($where)->field($field)->order('s.id')->limit($p->firstRow . ',' . $p->listRows)->select();
            log_result($list);
            foreach ($list as $key => $value) {

                if ($value['apply_type']) {
                    $list[$key]['create_time'] = to_date($value['deal_time'] - 28800);
                } else {
                    $list[$key]['create_time'] = to_date($value['create_time'] - 28800);
                }
                if ($value['is_authentication'] == 2) {
                    $list[$key]['is_authentication'] = '已认证';
                } else {
                    $list[$key]['is_authentication'] = '未认证';
                }
                $list[$key]['head_image'] = get_spec_image($value['head_image']);
                $list[$key]['nick_name'] = ($value['nick_name']);
                if ($value['society_chieftain'] == 1) {
                    $list[$key]['status'] = 5;
                }
            }
        }

        $this->assign('id', $id);
        $this->assign('society', $society);
        $this->assign('list', $list);
        $this->assign("page", $p->show());

        $this->display();
    }

    /**
     * 解散公会
     */
    public function dissolve()
    {
        $id = intval($_REQUEST['id']);
        if (!$id) {
            $this->error("参数错误");
        }
        $model = M('society');
        $society_member = $GLOBALS['db']->getOne("SELECT COUNT(id) as zh from " . DB_PREFIX . "user WHERE society_id=" . $id . ' AND society_chieftain = 0');
        if (intval($society_member) > 1) {
            $this->error("公会还有其他未退出的成员");
        } else {
            $data = array();
            $data['society_id'] = 0;
            $data['society_chieftain'] = 0;
            $user_id = $model->where('id=' . $id)->getField('user_id');
            M('user')->where('id=' . $user_id)->save($data);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_redis->update_db($user_id, array('society_id' => 0, 'society_chieftain' => 0));

            //将请求加入公会的申请设为拒绝
            M('society_apply')->where('society_id=' . $id)->setField("status", 2);
            //status=4 已解散工会
            //$res = $model->where("id=".$id)->setField("status",4);
            $res = $model->where("id=" . $id)->delete();
            $log_info = $id;
            if ($res === false) {
                //错误提示
                save_log($log_info . "公会解散失败", 0);
                $this->error(L("公会解散失败"), 0, $log_info . L("公会解散失败"));
            } else {
                //成功提示
                save_log($log_info . L("公会解散成功"), 1);
                $this->success(L("公会解散成功"));
            }
        }
    }

    /**
     * 对申请入会的人员进行操作
     */
    public function join_operate()
    {
        $user_id = intval($_REQUEST['id'] ? $_REQUEST['id'] : 0);
        $apply_status = intval($_REQUEST['status'] ? $_REQUEST['status'] : 0);
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0);
        //查询是否已有公会
        $res1 = $GLOBALS['db']->getOne("select society_id from " . DB_PREFIX . "user where id=$user_id");
        if ($user_id && $society_id && $res1 == 0) {
            $newtime = time();
            if ($apply_status == 1) {
//同意加入
                //查询是否已经被拒绝，防止卡顿现象导致操作异常
                $res2 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=$user_id and society_id=$society_id");
                if (!empty($res2)) {
                    //删除在其他公会的申请
                    $res = $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where society_id!=" . $society_id . " and user_id=" . $user_id);
                    if ($res) {
                        $GLOBALS['db']->query("update " . DB_PREFIX . "society_apply set status=1,deal_time=" . $newtime . " where user_id=" . $user_id . " and society_id=" . $society_id . " and apply_type=0");
                        $GLOBALS['db']->query("update " . DB_PREFIX . "society set user_count=user_count+1 where id=" . $society_id);
                        $GLOBALS['db']->query("update " . DB_PREFIX . "user set society_id=" . $society_id . " where id=" . $user_id);
                        //写入用户日志
                        $data = array();
                        $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                        $param['type'] = 12; //类型12表示公会相关操作
                        $log_msg = '成功加入【' . $society_name . '】公会';
                        account_log_com($data, $user_id, $log_msg, $param);
                        $this->success();
                    }
                } else {
                    $this->error(L("操作失败，该成员已被拒绝加入，请刷新页面"), 0);
                }

            } elseif ($apply_status == 2) {
//拒绝加入
                //查询是否已同意入会，防止卡顿现象导致操作异常
                $res2 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=$user_id and society_id=$society_id and status=1");
                if (empty($res2)) {
                    $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=" . $user_id . " and society_id=" . $society_id . " and apply_type=0");
                    //写入用户日志
                    $data = array();
                    $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                    $param['type'] = 12; //类型12表示公会相关操作
                    $log_msg = '加入【' . $society_name . '】公会被拒绝';
                    account_log_com($data, $user_id, $log_msg, $param);
                    $this->success();
                } else {
                    $this->error(L("操作失败，该成员已被拒绝加入，请刷新页面"), 0);
                }

            }
        } else {
            $this->error(L("操作失败，该成员已有公会，请刷新页面"), 0);
        }
    }

    /**
     * 对申请退会的人员进行操作
     */
    public function out_operate()
    {
        $user_id = intval($_REQUEST['id'] ? $_REQUEST['id'] : 0);
        $apply_status = intval($_REQUEST['status'] ? $_REQUEST['status'] : 0);
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0);
        if ($user_id && $society_id) {
            $newtime = time();
            if ($apply_status == 1) {
//同意退出
                //删除他公会的申请
                $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where society_id=" . $society_id . " and user_id=" . $user_id);
                $GLOBALS['db']->query("update " . DB_PREFIX . "society set user_count=user_count-1 where id=" . $society_id);
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set society_id=0 where id=" . $user_id);

                //写入用户日志
                $data = array();
                $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                $param['type'] = 12; //类型12表示公会相关操作
                $log_msg = '成功退出【' . $society_name . '】公会';
                account_log_com($data, $user_id, $log_msg, $param);
                $this->success();
            } elseif ($apply_status == 2) {
//拒绝退出
                $GLOBALS['db']->query("update " . DB_PREFIX . "society_apply set apply_type=0,status=1,deal_time=" . $newtime . " where user_id=" . $user_id . " and society_id=" . $society_id);

                //写入用户日志
                $data = array();
                $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                $param['type'] = 12; //类型12表示公会相关操作
                $log_msg = '退出【' . $society_name . '】公会被拒绝';
                account_log_com($data, $user_id, $log_msg, $param);
                $this->success();
            }
        }
    }

    /**
     * 踢出成员
     */
    public function getout_operate()
    {
        $user_id = intval($_REQUEST['id'] ? $_REQUEST['id'] : 0);
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0);
        if ($user_id && $society_id) {
            $newtime = time();
            //删除他公会的申请
            $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where society_id=" . $society_id . " and user_id=" . $user_id);
            $GLOBALS['db']->query("update " . DB_PREFIX . "society set user_count=user_count-1 where id=" . $society_id);
            $GLOBALS['db']->query("update " . DB_PREFIX . "user set society_id=0 where id=" . $user_id);

            //写入用户日志
            $data = array();
            $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
            $param['type'] = 12; //类型12表示公会相关操作
            $log_msg = '被【' . $society_name . '】公会踢出';
            account_log_com($data, $user_id, $log_msg, $param);
            $this->success();
        }
    }

    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M("society")->where("id=" . $id)->getField("id");
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("society")->where("id=" . $id)->setField("society_rank", $sort);
        //redis同步
        require_once APP_ROOT_PATH . "/admin/Lib/Action/RedisCommon.class.php";
        $data = M(MODULE_NAME)->where("id=" . $id)->find();
        $redisCommon = new Ridescommon();
        $redisCommon->video_cate_list($data['id'], $data, 'update');
        save_log($log_info . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }

    /**
     * 公会长收益统计
     */
    public function statistics()
    {
        $map = $this->com_search(); //获取时间搜索状态

        $id = strim($_REQUEST['id']); //公会ID
        $sql = "SELECT user_id from " . DB_PREFIX . "society where id = " . $id;
        $chief_id = $GLOBALS['db']->getOne($sql); //获取公会长ID
        $user_name = ($GLOBALS['db']->getOne("select nick_name from " . DB_PREFIX . "user where id = " . $chief_id)); //会长昵称
        $this->assign("chiefid", $user_name);
        /*admin_ajax_return($chief_id);*/

        $parameter = '';
        $sql_w = '';
        //查看是否有进行时间搜索
        if ($map['start_time'] != '' && $map['end_time'] != '') {
            $parameter .= " log_time between '" . $map['start_time'] . "' and '" . $map['end_time'] . "'&";
            $sql_w .= " log_time between '" . $map['start_time'] . "' and '" . $map['end_time'] . "' and ";
        }
        //查看是否有进行贡献会员的ID或昵称搜索
        if (strim($_REQUEST['mid']) != '') {
            $sql_w .= " u2.id like '%" . strim($_REQUEST['mid']) . "%' and ";
        }
        if (strim($_REQUEST['nick_name']) != '') {
            $sql_w .= " nick_name like '%" . strim($_REQUEST['nick_name']) . "%' and ";
        }

        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        if ($m_config['society_pattern'] == 1) {
            $utype = '8,13';
        } elseif ($m_config['society_pattern'] == 2) {
            $utype = '10,13';
        }

        $model = D();
        $sql_str = "SELECT u2.id as mid,nick_name,u1.ticket,u1.log_time as time " . "FROM " . DB_PREFIX . "user_log u1," . DB_PREFIX . "user u2 where u1.society_id=$id and u1.contribution_id = u2.id and u1.type in($utype) and u1.user_id = " . $chief_id;

        $sql_str .= " and " . $sql_w . " 1=1 ";

        /*if(strim($_REQUEST['name'])!=''){
        admin_ajax_return($sql_str);
        } */
        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'u1.log_time');

        for ($i = 0; $i < count($voList); $i++) {
            $voList[$i]['time'] = to_date($voList[$i]['time']);
            $voList[$i]['nick_name'] = ($voList[$i]['nick_name']);
        }

        $this->assign('list', $voList);

        //获取总量与总金额
        $sql_count = "SELECT sum(u1.ticket) as count " . "FROM " . DB_PREFIX . "user_log u1," . DB_PREFIX . "user u2 where u1.society_id=$id and u1.contribution_id = u2.id and u1.type in($utype) and u1.user_id = " . $chief_id;
        $sql_count .= " and " . $sql_w . " 1=1 ";
        /*admin_ajax_return($sql_count);*/

        $count = $GLOBALS['db']->getOne($sql_count);

        $this->assign("count", $count);
        $this->display();
        return;
    }

    /**
     * 批量确认审核界面
     */
    public function batch_examine()
    {
        $id = $_REQUEST['id'];
        $implode_id = implode(',', (explode(',', $id)));
        $refund_data = $GLOBALS['db']->getAll("select s.id,s.name,s.status,u.nick_name from " . DB_PREFIX . "society s inner join " . DB_PREFIX . "user u on s.user_id=u.id where s.id in($implode_id)");
        $status = intval($_REQUEST['status']);
        $info = array();
        foreach ($refund_data as $k => $v) {
            $refund_data[$k]['nick_name'] = ($v['nick_name']);
        }
        if ($status) {
            $info['do'] = '允许';
        } else {
            $info['do'] = '不允许';
        }
        $this->assign("info", $info);
        $this->assign("refund_data", $refund_data);
        $this->assign("status", $status);
        $this->assign("implode_id", $implode_id);
        $this->display();
    }
    /**
     * 批量审核操作
     */
    public function batch_examine_operate()
    {
        $uid = $_REQUEST['id'];
        $implode_id = implode(',', (explode(',', $uid)));
        $status = intval($_REQUEST['status']);

        $refund_data = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "society where id in($implode_id)");
        foreach ($refund_data as $key => $val) {
            if ($val['status'] == 1) {
                $this->error("您勾选的公会中包含已审核通过的公会，请重新勾选");
                exit;
            }
        }
        $res = $GLOBALS['db']->query("update " . DB_PREFIX . "society set status=" . $status . " where id in($implode_id)");
        if ($res) {
            $this->success("操作成功");
        }
    }
}
