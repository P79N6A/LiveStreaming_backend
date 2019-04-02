<?php

class treeModule extends baseModule
{
    public function index()
    {
        $uid = $GLOBALS['user_info']['id'];
        $user_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $uid);
        es_session::set("user_info", $user_data);
        es_cookie::set("client_ip", CLIENT_IP, 3600 * 24 * 30);
        es_cookie::set("nick_name", $user_data['nick_name'], 3600 * 24 * 30);
        es_cookie::set("user_id", $user_data['id'], 3600 * 24 * 30);
        es_cookie::set("user_pwd", md5($user_data['user_pwd'] . "_EASE_COOKIE"), 3600 * 24 * 30);
        es_cookie::set("is_agree", $user_data['is_agree'], 3600 * 24 * 30);
        es_cookie::set("PHPSESSID2", es_session::id(), 3600 * 24 * 30);

        $url_tree_index = SITE_DOMAIN . APP_ROOT . '/frontEnd/xzqk/index.html?t=' . NOW_TIME;
        app_redirect($url_tree_index);
    }

    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            ajax_return(array(
                'status' => 0,
                'error' => '未登录'
            ));
        }
        return $user_id;
    }

    /*
     * 树苗展示列表
     */
    public function tree_list()
    {
        $user_id = self::getUserId(); //判断是否的登陆

        $title = strim($_REQUEST['title']); //树苗名称
        $page['page'] = intval($_REQUEST['page']); //页码
        $page_size = intval($_REQUEST['page_size']); //每页数量
        $page['page'] = $page['page'] ? $page['page'] : 1;
        $page_size = $page_size ? $page_size : 20;
        $m_config = load_auto_cache('m_config');

        $where = " is_effect =1 ";

        //根据树苗名称对订单检索
        if ($title) {
            $where .= " and title like '%" . $title . "%'";
        }

        $field = " id,title,image,description,diamonds,create_at ";
        $table = DB_PREFIX . "qk_tree";
        $order = " order by sort desc ";
        $limit = (($page['page'] - 1) * $page_size) . "," . $page_size;
        $sql = "SELECT $field FROM $table WHERE $where $order limit " . $limit;
        $count_sql = "SELECT count(id) FROM $table WHERE $where";

        $rs_count = $GLOBALS['db']->getOne($count_sql, true, true);
        $list = $GLOBALS['db']->getAll($sql, true, true);

        foreach ($list as $k => $v) {
            $list[$k]['image'] = get_spec_image($v['image']);
            $list[$k]['title'] = htmlspecialchars_decode($v['title']);
        }

        $diamonds_name = $m_config['diamonds_name'];
        $page['has_next'] = ($rs_count > $page['page'] * $page_size) ? 1 : 0;
        $error = '';
        $status = 1;

        api_ajax_return(compact('error', 'status', 'rs_count', 'list', 'page', 'diamonds_name'));
    }

    /*
     * 树苗购买
     */
    public function order_tree()
    {
        $user_id = self::getUserId(); //判断是否的登陆

        $tree_id = intval($_REQUEST['tree_id']); //树苗ID

        if (!$tree_id) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '请选择树苗!'
            ));
        }

        $table = DB_PREFIX . "qk_tree";
        $where = " is_effect =1 ";
        $tree_info = $GLOBALS['db']->getRow("SELECT diamonds,title FROM $table WHERE $where and id =" . $tree_id, true, true);

        //判断树苗是否存在
        if (empty($tree_info)) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '树苗不存在!'
            ));
        }

        //判断用户余额
        $user_info = $GLOBALS['db']->getRow("SELECT diamonds FROM " . DB_PREFIX . "user where id =" . $user_id);

        if ($user_info['diamonds'] < $tree_info['diamonds']) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '用户余额不足!'
            ));
        }

        $pInTrans = $GLOBALS['db']->StartTrans();
        try {
            //减少用户秀豆
            $sql = "update " . DB_PREFIX . "user set diamonds = diamonds - " . $tree_info['diamonds'] . ", use_diamonds = use_diamonds + " . $tree_info['diamonds'] . " where id = '" . $user_id . "' and diamonds >= " . $tree_info['diamonds'];
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                $data['create_time'] = NOW_TIME;
                $data['user_id'] = $user_id;
                $data['pay'] = $tree_info['diamonds'];
                $data['tree_id'] = $tree_id;
                $now = DateTime::createFromFormat('U.u', microtime(true));
                $data['order_no'] = $now->format('ymdHisu') . mt_rand(1000, 9999);

                $fields = " diamonds,use_diamonds ";
                $sql = "select " . $fields . " from " . DB_PREFIX . "user where id = " . $user_id;
                $user_data = $GLOBALS['db']->getRow($sql);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "qk_tree_order", $data, "INSERT");
                $order_id = $GLOBALS['db']->insert_id();

                //提交事务
                $GLOBALS['db']->Commit($pInTrans);
                $pInTrans = false; //防止，下面异常时，还调用：Rollback

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($user_id, $user_data);

                //写入用户日志
                $data = array();
                $data['diamonds'] = $tree_info['diamonds'];
                $param['type'] = 21; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 20教育 21青稞树苗
                $log_msg = "购买'" . $tree_info['title'] . "'树苗，购买支付，订单号:" . $order_id; //备注
                account_log_com($data, $user_id, $log_msg, $param);

                $root['error'] = '购买成功';
                $root['status'] = 1;
            } else {
                $GLOBALS['db']->Rollback($pInTrans);
                $root['error'] = '购买失败';
                $root['status'] = 1;
            }
        } catch (Exception $e) {
            //异常回滚
            $root['error'] = $e->getMessage();
            $root['status'] = 0;
            $GLOBALS['db']->Rollback($pInTrans);
        }

        api_ajax_return($root);
    }

    /*
     *树苗详情
     */
    public function tree_info()
    {
        $user_id = self::getUserId(); //判断是否的登陆

        $root = array();
        $tree_id = intval($_REQUEST['tree_id']);
        $m_config = load_auto_cache('m_config');

        //判断参数是否有误
        if (!$tree_id) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '请选择树苗!'
            ));
        }

        $table = DB_PREFIX . "qk_tree";
        $where = " is_effect =1 ";
        $tree_info = $GLOBALS['db']->getAll("SELECT id,title,image,description,diamonds,create_at FROM $table WHERE $where and id =" . $tree_id,
            true, true);

        //判断树苗是否存在
        if (empty($tree_info)) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '树苗不存在!'
            ));
        }

        $user_diamonds = $GLOBALS['db']->getOne("SELECT diamonds FROM " . DB_PREFIX . "user where id =" . $user_id,
            true, true);

        foreach ($tree_info as $k => $v) {
            $tree_info[$k]['image'] = get_spec_image($v['image']);
            $tree_info[$k]['title'] = htmlspecialchars_decode($v['title']);
            $tree_info[$k]['planting_site'] = $m_config['planting_site']; //读取后台配置的种植地点
            //判断用户余额
            if ($user_diamonds < $v['diamonds']) {
                $tree_info[$k]['notice'] = '您的' . $m_config['diamonds_name'] . '不足，请充值';
            } else {
                $tree_info[$k]['notice'] = '';
            }
            $tree_info[$k]['ticket_name'] = $m_config['diamonds_name']; //秀票名称
            $tree_info[$k]['app_name'] = $m_config['app_name']; //app名称
            $tree_info[$k]['app_logo'] = get_spec_image($m_config['app_logo']); //app的logo图片
        }

        $root['data'] = $tree_info;
        $root['status'] = 1;
        $root['error'] = '';

        api_ajax_return($root);
    }

    /*
     * 购买记录
     * */
    public function order_list()
    {
        $user_id = self::getUserId(); //判断是否的登陆

        $page['page'] = intval($_REQUEST['page']); //页码
        $page_size = intval($_REQUEST['page_size']); //每页数量
        $page['page'] = $page['page'] ? $page['page'] : 1;
        $page_size = $page_size ? $page_size : 20;
        $order = " order by o.create_time desc ";
        $limit = (($page['page'] - 1) * $page_size) . "," . $page_size; //分页起始

        //根据树苗名称对订单检索
        if (strim($_REQUEST['title'])) {
            $where = " and t.title like '%" . strim($_REQUEST['title']) . "%' ";
        }

        //返回参数初始化
        $error = '';
        $status = 1;

        $table = DB_PREFIX . "qk_tree_order o," . DB_PREFIX . "qk_tree t ";
        $sql = "SELECT o.* ,t.title,t.image FROM $table WHERE  o.tree_id = t.id and o.user_id =" . $user_id . " $where $order limit $limit";
        $list = $GLOBALS['db']->getAll($sql, true, true);

        $count_sql = "SELECT COUNT(*) FROM $table WHERE o.tree_id = t.id and o.user_id =" . $user_id . " $where";
        $rs_count = $GLOBALS['db']->getOne($count_sql, true, true);

        if (empty($list)) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '暂无购买记录!'
            ));
        }

        $m_config = load_auto_cache('m_config');
        foreach ($list as $k => $v) {
            $list[$k]['image'] = get_spec_image($v['image']);
            $list[$k]['create_day'] = to_date($v['create_time'], 'Y-m-d');
            $list[$k]['create_time'] = to_date($v['create_time'], 'H:i');
            $tree_info_list = $GLOBALS['db']->getAll("SELECT image,create_at,shoot_time FROM " . DB_PREFIX . "qk_tree_info WHERE order_id =" . $v['id'],
                true, true);
            foreach ($tree_info_list as $kk => $vv) {
                $tree_info_list[$kk]['image'] = get_spec_image($vv['image']);
                $image_info = getimagesize($vv['image']);
                $tree_info_list[$kk]['width'] = $image_info[0];
                $tree_info_list[$kk]['height'] = $image_info[1];
                $tree_info_list[$kk]['create_at'] = to_date($vv['shoot_time'], 'Y-m-d H:i');
                $tree_info_list[$kk]['shoot_time'] = to_date($vv['shoot_time'], 'Y-m-d H:i');
            }
            $list[$k]['image_list'] = $tree_info_list;
            $list[$k]['title'] = htmlspecialchars_decode($v['title']);
            $list[$k]['planting_site'] = $m_config['planting_site'];
        }

        $page['has_next'] = ($rs_count > $page['page'] * $page_size) ? 1 : 0;

        api_ajax_return(compact('error', 'status', 'rs_count', 'list', 'page'));
    }

    /* 用户树苗首页
     *
     */
    public function tree_index()
    {
        $user_id = self::getUserId(); //判断是否的登陆

        $root = array();
        $root['status'] = 1;
        $root['error'] = '';
        $m_config = load_auto_cache('m_config');

        //用户头像

        $user = $GLOBALS['db']->getRow("SELECT nick_name,head_image,thumb_head_image FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
        $head_image = $user['head_image'] ? $user['head_image'] : $user['thumb_head_image']; //用户头像
        if (empty($head_image)) {
            $head_image = $m_config['app_logo'];
        }

        $root['user_id'] = $user_id;
        $root['head_image'] = get_spec_image($head_image);
        $root['planting_site'] = $m_config['planting_site']; //读取后台配置的种植地点
        $root['coordinate'] = $m_config['coordinate']; //读取后台配置的种植地点坐标
        $root['app_name'] = $m_config['app_name'];
        $root['tree_desc'] = $m_config['tree_desc'];

        $table = DB_PREFIX . "qk_tree_order";
        $sql = "SELECT COUNT(id) FROM $table WHERE user_id =" . $user_id;
        $root['tree_count'] = $GLOBALS['db']->getOne($sql, true, true);
        $root['share_url'] = get_domain() . "/frontEnd/xzqk/index.html#/tree_share?user_id=" . $user_id;

        api_ajax_return($root);
    }

    //分享
    public function share_tree()
    {
        $user_id = intval($_REQUEST['user_id']);
        $m_config = load_auto_cache('m_config');
        $root = array();
        $root['status'] = 1;
        $root['error'] = '';

        $app_logo = get_spec_image($m_config['app_logo']); //app的logo图片
        //获取用户头像
        $user = $GLOBALS['db']->getRow("SELECT nick_name,head_image,thumb_head_image FROM " . DB_PREFIX . "user WHERE id =" . $user_id,
            true, true);
        if (empty($user)) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '分享用户不存在!'
            ));
        }

        $tree_count = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "qk_tree_order WHERE user_id =" . $user_id);
        $head_image = $user['head_image'] ? $user['head_image'] : $user['thumb_head_image'];
        $head_image = $head_image ? $head_image : $app_logo;

        $root['nick_name'] = $user['nick_name'];
        $root['head_image'] = get_spec_image($head_image);
        $root['app_name'] = $m_config['app_name']; //app名称
        $root['download_url'] = get_domain() . "/mapi/index.php?ctl=app_download&act=index";
        $root['tree_count'] = $tree_count;

        api_ajax_return($root);
    }

}
