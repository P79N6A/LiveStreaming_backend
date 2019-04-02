<?php

/**
 * shouhu守护管理
 */
class GuardAction extends CommonAction
{
    public function index()
    {
        if (strim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . strim($_REQUEST['name']) . '%');
        }

        if ($_REQUEST['is_animated'] != '' && intval($_REQUEST['is_animated']) != -1) {
            $map['is_animated'] = intval($_REQUEST['is_animated']);
        }

        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $list = $this->get("list");
        // $cate_list = M("VideoCate")->findAll();
        // $this->assign("cate_list", $cate_list);
        $this->display();
    }

    public function add()
    {
        $this->assign("new_sort", M("Guard")->max("sort") + 1);
        $this->display();
    }
    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $this->assign('vo', $vo);
        $this->display();
    }

    //彻底删除指定记录
    public function foreverdelete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info) {
                $info = implode(",", $info);
            }

            $list = M(MODULE_NAME)->where($condition)->delete();
            //删除相关预览图
            //                foreach($rel_data as $data)
            //                {
            //                    @unlink(get_real_path().$data['preview']);
            //                }
            if ($list !== false) {
                //删除子动画
                $animate_condition = array('guard_id' => array('in', explode(',', $id)));
                $list = M("GuardAnimated")->where($animate_condition)->delete();
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("guard_rule_list");
                clear_auto_cache("guard_id", array('id' => $id));
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(MODULE_NAME)->create();
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));
        $m_config = load_auto_cache('m_config');
        $ticket_name = $m_config['ticket_name'] != '' ? $m_config['ticket_name'] : '秀票';
        if (!check_empty($data['name'])) {
            $this->error("请输入名称");
        }
        if (!check_empty($data['icon'])) {
            $this->error("请输入图标");
        }
        if ((intval($data['score']) == 0)) {
            $data['score'] = 0;
        }
        if (intval($data['ticket']) == 0) {
            $data['ticket'] = 0;
        }
        if (intval($data['is_animated']) == 2) {
            if (!check_empty($data['anim_type'])) {
                $this->error("请输入大型动画守护类型");
            }
        }
        if ((intval($data['diamonds']) == 0) && (intval($data['score']) == 0)) {
            if (intval($data['ticket']) != 0) {
                $this->error('免费守护的' . $ticket_name . '数量必须为0');
            }
        }
        if (!check_empty($data['content'])) {
            $this->error("请输入进场内容");
        }
        // 更新数据
        $log_info = $data['name'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("guard_rule_list");
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }

    public function update()
    {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        clear_auto_cache("guard_rule_list");
//        if($_FILES['preview']['name']!='')
        //        {
        //            $result = $this->uploadImage();
        //            if($result['status']==0)
        //            {
        //                $this->error($result['info'],$ajax);
        //            }
        //            //删除图片
        //            @unlink(get_real_path().M("Article")->where("id=".$data['id'])->getField("preview"));
        //            $data['preview'] = $result['data'][0]['bigrecpath'].$result['data'][0]['savename'];
        //        }
        $m_config = load_auto_cache('m_config');
        $ticket_name = $m_config['ticket_name'] != '' ? $m_config['ticket_name'] : '秀票';
        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("name");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['name'])) {
            $this->error("请输入名称");
        }
        if (!check_empty($data['icon'])) {
            $this->error("请输入图标");
        }
        if ((intval($data['score']) == 0)) {
            $data['score'] = 0;
        }
        if (intval($data['ticket']) == 0) {
            $data['ticket'] = 0;
        }
        if (intval($data['is_animated']) == 2) {
            if (!check_empty($data['anim_type'])) {
                $this->error("请输入大型动画守护类型");
            }
        }
        if ((intval($data['diamonds']) == 0) && (intval($data['score']) == 0)) {
            if (intval($data['ticket']) != 0) {
                $this->error('免费守护的' . $ticket_name . '数量必须为0');
            }
        }
        if (!check_empty($data['content'])) {
            $this->error("请输入进场内容");
        }
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("guard_rule_list");
            clear_auto_cache("guard_id", array('id' => $data['id']));
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        clear_auto_cache("guard_rule_list");
        $id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $info = M(MODULE_NAME)->where("id=" . $id)->getField("name");
        $c_is_effect = M(MODULE_NAME)->where("id=" . $id)->getField("is_effect"); //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        M(MODULE_NAME)->where("id=" . $id)->setField("is_effect", $n_is_effect);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    public function set_sort()
    {
        clear_auto_cache("guard_rule_list");
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M(MODULE_NAME)->where("id=" . $id)->getField("name");
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("Guard")->where("id=" . $id)->setField("sort", $sort);
        save_log($log_info . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }

    /**
     * [guard_item 守护动画列表]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-03T18:10:37+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function guard_item()
    {
        $guard_id = intval($_REQUEST['id']);
        $guard_info = M("Guard")->getById($guard_id);

        $this->assign("guard_info", $guard_info);
        if ($guard_info) {
            $map['guard_id'] = $guard_info['id'];
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $model = D("GuardAnimated");
            if (!empty($model)) {
                $this->_list($model, $map);
            }
        }
        $this->display();
    }
    /**
     * 添加子动画
     */
    public function add_guard_item()
    {
        $guard_id = intval($_REQUEST['id']);
        $guard_info = M("Guard")->getById($guard_id);
        $this->assign("guard_info", $guard_info);
        $this->display();
    }
/**
 * 写入子动画
 */
    public function insert_guard_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("GuardAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        $Count = M('GuardAnimated')->where('guard_id = ' . $data['guard_id'])->count();
        if ($Count >= 5) {
            $this->error('已经添加五个动画！不能再添加');
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add_guard_item", array("id" => $data['guard_id'])));

        $guard_name = M('Guard')->where('guard_id = ' . $data['guard_id'])->getField("name");
        // 更新数据
        $list = M("GuardAnimated")->add($data);
        $log_info = $guard_name . "守护动画: " . $data['guard_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("guard_id", array('id' => $data['guard_id']));
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }
/**
 * 编辑子动画
 */
    public function edit_guard_item()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M("GuardAnimated")->where($condition)->find();
        $this->assign('vo', $vo);
        $guard_info = M("Guard")->where("id=" . intval($vo['guard_id']) . "")->find();
        $this->assign('guard_info', $guard_info);
        $this->display();
    }
    /**
     * 更新子动画
     */
    public function update_guard_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("GuardAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit_guard_item", array("id" => $data['id'])));

        $guard_item = M("GuardAnimated")->getById(intval($data['id']));

        if (!$guard_item) {
            $this->error("更新失败");
        }

        $guard_name = M('Guard')->where('guard_id = ' . $data['guard_id'])->getField("name");
        // 更新数据
        $list = M("GuardAnimated")->save($data);

        $log_info = $guard_name . "守护动画: " . $data['guard_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("guard_id", array('id' => $data['guard_id']));
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"));
        }
    }
    /**
     * 删除子动画
     */
    public function del_guard_item()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("GuardAnimated")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $guard_id = $data['guard_id'];
            }

            $guard_name = M('Guard')->where('guard_id = ' . $guard_id)->getField("name");
            $info = $guard_name . "守护动画: " . $data['guard_id'];
            $list = M("GuardAnimated")->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("guard_id", array('id' => $guard_id));
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    /**
     * [guard_rules 交易规则]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-04T11:05:33+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function guard_rules()
    {
        $guard_id = intval($_REQUEST['id']);
        $guard_info = M("Guard")->getById($guard_id);

        $this->assign("guard_info", $guard_info);
        if ($guard_info) {
            $map['guard_id'] = $guard_info['id'];
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $model = D("GuardRules");
            if (!empty($model)) {
                $this->_list($model, $map);
            }
        }
        $this->display();
    }
    /**
     * 添加子动画
     */
    public function add_guard_rules()
    {
        $guard_id = intval($_REQUEST['id']);
        $guard_info = M("Guard")->getById($guard_id);
        $this->assign("guard_info", $guard_info);
        $this->display();
    }
/**
 * 写入子动画
 */
    public function insert_guard_rules()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("GuardRules")->create();
        $Count = M('GuardRules')->where('guard_id = ' . $data['guard_id'])->count();
        if ($Count >= 5) {
            $this->error('已经添加五个规则！不能再添加');
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add_guard_rules", array("id" => $data['guard_id'])));

        $guard_name = M('Guard')->where('guard_id = ' . $data['guard_id'])->getField("name");
        // 更新数据
        $list = M("GuardRules")->add($data);
        $log_info = $guard_name . "守护交易规则: " . $data['guard_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("guard_rule_list");
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }
/**
 * 编辑子动画
 */
    public function edit_guard_rules()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M("GuardRules")->where($condition)->find();
        $this->assign('vo', $vo);
        $guard_info = M("Guard")->where("id=" . intval($vo['guard_id']) . "")->find();
        $this->assign('guard_info', $guard_info);
        $this->display();
    }
    /**
     * 更新子动画
     */
    public function update_guard_rules()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("GuardRules")->create();
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit_guard_rules", array("id" => $data['id'])));

        $guard_rules = M("GuardRules")->getById(intval($data['id']));

        if (!$guard_rules) {
            $this->error("更新失败");
        }

        $guard_name = M('Guard')->where('guard_id = ' . $data['guard_id'])->getField("name");
        // 更新数据
        $list = M("GuardRules")->save($data);

        $log_info = $guard_name . "守护交易规则: " . $data['guard_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("guard_rule_list");
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"));
        }
    }
    /**
     * 删除子动画
     */
    public function del_guard_rules()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("GuardRules")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $guard_id = $data['guard_id'];
            }

            $guard_name = M('Guard')->where('guard_id = ' . $guard_id)->getField("name");
            $info = $guard_name . "守护动画: " . $data['guard_id'];
            $list = M("GuardRules")->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("guard_rule_list");
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
}
