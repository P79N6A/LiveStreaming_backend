<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class RefundRoleAction extends CommonAction
{
    public function index()
    {
        $name = $this->getActionName();
        $map = array();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $list = $this->get("list");
        $this->display();
    }
    public function add()
    {
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
    public function insert()
    {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));

        if (!check_empty($data['ticket_catty_ratio'])) {
            $this->error('提现比例不能为空');
        }
        if (!check_empty($data['level'])) {
            $this->error('用户等级不能为空');
        }
        // 更新数据
        $log_info = '等级提现规则' . $data['level'] . '=>' . $data['ticket_catty_ratio'];
        $id = M(MODULE_NAME)->add($data);
        if (false !== $id) {
            //开始关联节点
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }
    public function update()
    {
        //print_r(MODULE_NAME);exit;
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        $level = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("level");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['ticket_catty_ratio'])) {
            $this->error('提现比例不能为空');
        }
        $log_info = '等级提现规则' . $level . '=>' . $data['ticket_catty_ratio'];
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            clear_auto_cache("refund_role_level", array('level' => $log_info));
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"));
        }
    }
    public function foreverdelete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            if ($info) {
                $info = implode(",", $info);
            }
            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
                clear_auto_cache("refund_role_level");
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
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
