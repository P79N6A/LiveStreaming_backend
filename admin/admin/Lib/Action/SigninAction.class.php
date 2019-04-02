<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class SigninAction extends CommonAction
{
    public function index()
    {
        parent::index();
    }

    public function add()
    {
        $this->assign("new_day", M(MODULE_NAME)->max("day") + 1);
        $prop_group_list = load_auto_cache('prop_group_list');
        $this->assign('prop_group_list', $prop_group_list);
        $this->display();
    }
    public function edit()
    {
        $id = intval($_REQUEST['id']);

        $condition['id'] = $id;

        $vo = M(MODULE_NAME)->where($condition)->find();
        $prop_group_list = load_auto_cache('prop_group_list');
        $this->assign('prop_group_list', $prop_group_list);
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
            $types = [];
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
                if (!in_array($data['type'], $types)) {
                    $types[] = $data['type'];
                }
            }
            if ($info) {
                $info = implode(",", $info);
            }

            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
                foreach ($types as $type) {
                    self::orderSort($type);
                }
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("signin_list");
                clear_auto_cache("signin_day");
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
        if (!check_empty($data['name'])) {
            $this->error("请输入名称");
        }
        if (!check_empty($data['prop_id'])) {
            $this->error("请选择礼物");
        }
        // 更新数据
        $log_info = $data['name'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("signin_list");
            clear_auto_cache("signin_day");
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

        $data['is_order'] = 0;

        $type = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("type");
        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("name");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['name'])) {
            $this->error("请输入名称");
        }
        if (!check_empty($data['prop_id'])) {
            $this->error("请选择礼物");
        }
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("signin_list");
            clear_auto_cache("signin_day");
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);

        $info = M(MODULE_NAME)->where("id=" . $id)->getField("adm_name");
        $c_is_effect = M(MODULE_NAME)->where("id=" . $id)->getField("is_effect"); //当前状态
        $type = M(MODULE_NAME)->where("id=" . $id)->getField("type"); //当前状态
        if (conf("DEFAULT_ADMIN") == $info) {
            $this->ajaxReturn($c_is_effect, l("DEFAULT_ADMIN_CANNOT_EFFECT"), 1);
        }
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        M(MODULE_NAME)->save(["is_effect" => $n_is_effect, 'id' => $id]);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }
}
