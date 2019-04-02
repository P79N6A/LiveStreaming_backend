<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class MountsAction extends CommonAction
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
        $this->display();
    }

    public function add()
    {
        $this->assign("new_sort", M("Mounts")->max("sort") + 1);
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
                $animate_condition = array('mount_id' => array('in', explode(',', $id)));
                $list = M("MountsAnimated")->where($animate_condition)->delete();
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache('mount_rule_list');
                clear_auto_cache("mount_id", array('id' => $data['id']));
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
        if (!check_empty($data['diamonds'])) {
            if ($data['is_red_envelope'] == 1) {
                $this->error("请输入消费秀豆");
            }
        }
        if ((intval($data['score']) == 0)) {
            $data['score'] = 0;
        }
        if (intval($data['robot_diamonds']) == 0) {
            $data['robot_diamonds'] = 0;
        }
        if ($data['is_red_envelope'] == 1) {
            if (!(intval($data['diamonds']) > 0)) {
                $this->error("消费秀豆必须大于0");
            }
            if ((intval($data['diamonds'])) <= (intval($data['ticket']))) {
                $this->error("消费秀豆必须大于主播获得的秀豆数量");
            }
            if ((intval($data['diamonds'])) <= (intval($data['ticket'])) + (intval($data['robot_diamonds']))) {
                $this->error("消费秀豆必须大于（主播获得的秀豆数量+机器人获得的秀豆数量)");
            }
        }
        if (intval($data['ticket']) == 0) {
            $data['ticket'] = 0;
        }
        if (intval($data['is_heat']) == 0) {
            $data['is_heat'] = 0;
        }
        if (intval($data['is_animated']) == 2) {
            if (!check_empty($data['anim_type'])) {
                $this->error("请输入大型动画坐骑类型");
            }
        }
        if ((intval($data['diamonds']) == 0) && (intval($data['score']) == 0)) {
            if (intval($data['ticket']) != 0) {
                $this->error('免费坐骑的' . $ticket_name . '数量必须为0');
            }
        }
        // 更新数据
        $log_info = $data['name'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("mount_rule_list");
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
        clear_auto_cache("mount_rule_list");
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
        if (!check_empty($data['diamonds'])) {
            if ($data['is_red_envelope'] == 1) {
                $this->error("请输入消费秀豆");
            }
        }
        if ((intval($data['score']) == 0)) {
            $data['score'] = 0;
        }
        if ($data['is_red_envelope'] == 1) {
            if (!(intval($data['diamonds']) > 0)) {
                $this->error("消费秀豆必须大于0");
            }
            if ((intval($data['diamonds'])) <= (intval($data['ticket']))) {
                $this->error("消费秀豆必须大于主播获得的秀豆数量");
            }
            if ((intval($data['diamonds'])) <= (intval($data['ticket'])) + (intval($data['robot_diamonds']))) {
                $this->error("消费秀豆必须大于（主播获得的秀豆数量+机器人获得的秀豆数量)");
            }
        }
        if (intval($data['ticket']) == 0) {
            $data['ticket'] = 0;
        }
        if (intval($data['is_heat']) == 0) {
            $data['is_heat'] = 0;
        }
        if (intval($data['robot_diamonds']) == 0) {
            $data['robot_diamonds'] = 0;
        }
        if (intval($data['is_animated']) == 2) {
            if (!check_empty($data['anim_type'])) {
                $this->error("请输入大型动画坐骑类型");
            }
        }
        if ((intval($data['diamonds']) == 0) && (intval($data['score']) == 0)) {
            if (intval($data['ticket']) != 0) {
                $this->error('免费坐骑的' . $ticket_name . '数量必须为0');
            }
        }
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("mount_id", array('id' => $data['id']));
            clear_auto_cache('mount_rule_list');
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        clear_auto_cache("mount_rule_list");
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
        clear_auto_cache("mount_rule_list");
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M(MODULE_NAME)->where("id=" . $id)->getField("name");
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("Mounts")->where("id=" . $id)->setField("sort", $sort);
        save_log($log_info . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }
/**
 * 子动画
 */
    public function mount_item()
    {
        $mount_id = intval($_REQUEST['id']);
        $mount_info = M("Mounts")->getById($mount_id);

        $this->assign("mount_info", $mount_info);
        if ($mount_info) {
            $map['mount_id'] = $mount_info['id'];
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $model = D("MountsAnimated");
            if (!empty($model)) {
                $this->_list($model, $map);
            }
        }
        $this->display();
    }
    /**
     * 添加子动画
     */
    public function add_mount_item()
    {
        $mount_id = intval($_REQUEST['id']);
        $mount_info = M("Mounts")->getById($mount_id);
        $this->assign("mount_info", $mount_info);
        $this->display();
    }
/**
 * 写入子动画
 */
    public function insert_mount_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("MountsAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        $Count = M('MountsAnimated')->where('mount_id = ' . $data['mount_id'])->count();
        if ($Count >= 5) {
            $this->error('已经添加五个动画！不能再添加');
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add_mount_item", array("id" => $data['mount_id'])));

        $mount_name = M('Mounts')->where('mount_id = ' . $data['mount_id'])->getField("name");
        // 更新数据
        $list = M("MountsAnimated")->add($data);
        $log_info = $mount_name . "坐骑动画: " . $data['mount_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("mount_id", array('id' => $data['mount_id']));
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
    public function edit_mount_item()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M("MountsAnimated")->where($condition)->find();
        $this->assign('vo', $vo);
        $mount_info = M("Mounts")->where("id=" . intval($vo['mount_id']) . "")->find();
        $this->assign('mount_info', $mount_info);
        $this->display();
    }
/**
 * 更新子动画
 */
    public function update_mount_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("MountsAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit_mount_item", array("id" => $data['id'])));

        $mount_item = M("MountsAnimated")->getById(intval($data['id']));

        if (!$mount_item) {
            $this->error("更新失败");
        }

        $mount_name = M('Mounts')->where('mount_id = ' . $data['mount_id'])->getField("name");
        // 更新数据
        $list = M("MountsAnimated")->save($data);

        $log_info = $mount_name . "坐骑动画: " . $data['mount_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("mount_id", array('id' => $data['mount_id']));
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
    public function del_mount_item()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("MountsAnimated")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $mount_id = $data['mount_id'];
            }

            $mount_name = M('Mounts')->where('mount_id = ' . $mount_id)->getField("name");
            $info = $mount_name . "坐骑动画: " . $data['mount_id'];
            $list = M("MountsAnimated")->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("mount_id", array('id' => $data['mount_id']));
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
     * [mount_rules 交易规则]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-04T11:05:33+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function mount_rules()
    {
        $mount_id = intval($_REQUEST['id']);
        $mount_info = M("Mounts")->getById($mount_id);

        $this->assign("mount_info", $mount_info);
        if ($mount_info) {
            $map['mount_id'] = $mount_info['id'];
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $model = D("MountsRules");
            if (!empty($model)) {
                $this->_list($model, $map);
            }
        }
        $this->display();
    }
    /**
     * 添加子动画
     */
    public function add_mount_rules()
    {
        $mount_id = intval($_REQUEST['id']);
        $mount_info = M("Mounts")->getById($mount_id);
        $this->assign("mount_info", $mount_info);
        $this->display();
    }
/**
 * 写入子动画
 */
    public function insert_mount_rules()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("MountsRules")->create();
        $Count = M('MountsRules')->where('mount_id = ' . $data['mount_id'])->count();
        if ($Count >= 5) {
            $this->error('已经添加五个规则！不能再添加');
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add_mount_rules", array("id" => $data['mount_id'])));

        $mount_name = M('Mounts')->where('mount_id = ' . $data['mount_id'])->getField("name");
        // 更新数据
        $list = M("MountsRules")->add($data);
        $log_info = $mount_name . "坐骑交易规则: " . $data['mount_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("mount_rule_list");
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
    public function edit_mount_rules()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M("MountsRules")->where($condition)->find();
        $this->assign('vo', $vo);
        $mount_info = M("Mounts")->where("id=" . intval($vo['mount_id']) . "")->find();
        $this->assign('mount_info', $mount_info);
        $this->display();
    }
    /**
     * 更新子动画
     */
    public function update_mount_rules()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("MountsRules")->create();
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit_mount_rules", array("id" => $data['id'])));

        $mount_rules = M("MountsRules")->getById(intval($data['id']));

        if (!$mount_rules) {
            $this->error("更新失败");
        }

        $mount_name = M('Mounts')->where('mount_id = ' . $data['mount_id'])->getField("name");
        // 更新数据
        $list = M("MountsRules")->save($data);

        $log_info = $mount_name . "坐骑交易规则: " . $data['mount_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("mount_rule_list");
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
    public function del_mount_rules()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("MountsRules")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $mount_id = $data['mount_id'];
            }

            $mount_name = M('Mounts')->where('mount_id = ' . $mount_id)->getField("name");
            $info = $mount_name . "坐骑动画: " . $data['mount_id'];
            $list = M("MountsRules")->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("mount_rule_list");
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
