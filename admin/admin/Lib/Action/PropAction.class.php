<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class PropAction extends CommonAction
{
    public function index()
    {

        if (strim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . strim($_REQUEST['name']) . '%');
        }

        if ($_REQUEST['is_animated'] != '' && intval($_REQUEST['is_animated']) != -1) {
            $map['is_animated'] = intval($_REQUEST['is_animated']);
        }

        if ($_REQUEST['g_id'] != '' && intval($_REQUEST['g_id']) != -1) {
            $map['g_id'] = intval($_REQUEST['g_id']);
        }

        if ($_REQUEST['is_special'] != '' && intval($_REQUEST['is_special']) != -1) {
            $map['is_special'] = intval($_REQUEST['is_special']);
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
        $group = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "prop_group");
        $this->assign("group", $group);
        // $m_config = load_auto_cache('m_config');
        // $cate_list = M("VideoCate")->findAll();
        // $this->assign("cate_list", $cate_list);
        // $this->assign("activity_prop_id", $m_config['activity_prop_id']);
        $this->display();
    }

    public function add()
    {
        $this->assign("new_sort", M("Prop")->max("sort") + 1);
        $group = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "prop_group");
        $this->assign("group", $group);
        $this->display();
    }
    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $this->assign('vo', $vo);
        $group = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "prop_group");
        $this->assign("group", $group);
        $this->display();
    }

    //彻底删除指定记录
    public function foreverdelete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)), 'is_special' => 0);
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
                $animate_condition = array('prop_id' => array('in', explode(',', $id)));
                $list = M("PropAnimated")->where($animate_condition)->delete();
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("prop_list");
                clear_auto_cache("prop_group_list");
                clear_auto_cache("signin_list");
                clear_auto_cache("signin_day");
                clear_auto_cache("prop_id", array('id' => $data['id']));
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
        if (!check_empty($data['g_id'])) {
            $this->error("请选择分类");
        }
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
        if ($data['g_id'] == 2) {
            $data['is_award'] = 0;
            $data['is_animated'] = 4;
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
        if (intval($data['is_animated']) != 0) {
            $data['is_award'] = 0;
        }
        if (intval($data['is_animated']) == 2) {
            if (!check_empty($data['anim_type'])) {
                $this->error("请输入大型动画礼物类型");
            }
        }
        if ((intval($data['diamonds']) == 0) && (intval($data['score']) == 0)) {
            if (intval($data['ticket']) != 0) {
                $this->error('免费礼物的' . $ticket_name . '数量必须为0');
            }
        }
        // 更新数据
        $log_info = $data['name'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("prop_list");
            clear_auto_cache("prop_group_list");
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
        clear_auto_cache("prop_list");
        clear_auto_cache("prop_group_list");
        clear_auto_cache("signin_list");
        clear_auto_cache("signin_day");
        // if($_FILES['preview']['name']!='')
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
        $info = M(MODULE_NAME)->where(array('id' => intval($data['id'])))->find();
        $log_info = $info["name"];
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if ($info['is_special'] != 1) {
            if (!check_empty($data['g_id'])) {
                $this->error("请选择分类");
            }
            if (!check_empty($data['name'])) {
                $this->error("请输入名称");
            }
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
        if ($info['is_special'] == 1) {
            $data['is_animated'] = 0;
        }
        if ($data['g_id'] == 2) {
            $data['is_award'] = 0;
            $data['is_animated'] = 4;
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
                $this->error("请输入大型动画礼物类型");
            }
        }
        if ((intval($data['diamonds']) == 0) && (intval($data['score']) == 0)) {
            if (intval($data['ticket']) != 0) {
                $this->error('免费礼物的' . $ticket_name . '数量必须为0');
            }
        }
        if (intval($data['is_animated']) != 0) {
            $data['is_award'] = 0;
        }
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            if ($info['id'] == 1) {
                M("MConfig")->where("code='full_msg_money'")->setField("val", trim($data['diamonds']));
                clear_auto_cache("m_config");
            } else if ($info['id'] == 2) {
                M("MConfig")->where("code='pop_msg_money'")->setField("val", trim($data['diamonds']));
                clear_auto_cache("m_config");
            }
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("prop_id", array('id' => $data['id']));
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        clear_auto_cache("prop_list");
        clear_auto_cache("prop_group_list");
        clear_auto_cache("signin_list");
        clear_auto_cache("signin_day");
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
        clear_auto_cache("prop_list");
        clear_auto_cache("prop_group_list");
        clear_auto_cache("signin_list");
        clear_auto_cache("signin_day");
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M(MODULE_NAME)->where("id=" . $id)->getField("name");
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("Prop")->where("id=" . $id)->setField("sort", $sort);
        save_log($log_info . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }
/**
 * 子动画
 */
    public function prop_item()
    {
        $prop_id = intval($_REQUEST['id']);
        $prop_info = M("Prop")->getById($prop_id);

        $this->assign("prop_info", $prop_info);
        if ($prop_info) {
            $map['prop_id'] = $prop_info['id'];
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $model = D("PropAnimated");
            if (!empty($model)) {
                $this->_list($model, $map);
            }
        }
        $this->display();
    }
    /**
     * 添加子动画
     */
    public function add_prop_item()
    {
        $prop_id = intval($_REQUEST['id']);
        $prop_info = M("Prop")->getById($prop_id);
        $this->assign("prop_info", $prop_info);
        $this->display();
    }
/**
 * 写入子动画
 */
    public function insert_prop_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("PropAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        $Count = M('PropAnimated')->where('prop_id = ' . $data['prop_id'])->count();
        if ($Count >= 5) {
            $this->error('已经添加五个动画！不能再添加');
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add_prop_item", array("id" => $data['prop_id'])));

        $prop_name = M('Prop')->where('prop_id = ' . $data['prop_id'])->getField("name");
        // 更新数据
        $list = M("PropAnimated")->add($data);
        $log_info = $prop_name . "礼物动画: " . $data['prop_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("prop_id", array('id' => $data['prop_id']));
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
    public function edit_prop_item()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M("PropAnimated")->where($condition)->find();
        $this->assign('vo', $vo);
        $prop_info = M("Prop")->where("id=" . intval($vo['prop_id']) . "")->find();
        $this->assign('prop_info', $prop_info);
        $this->display();
    }
/**
 * 更新子动画
 */
    public function update_prop_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("PropAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit_prop_item", array("id" => $data['id'])));

        $prop_item = M("PropAnimated")->getById(intval($data['id']));

        if (!$prop_item) {
            $this->error("更新失败");
        }

        $prop_name = M('Prop')->where('prop_id = ' . $data['prop_id'])->getField("name");
        // 更新数据
        $list = M("PropAnimated")->save($data);

        $log_info = $prop_name . "礼物动画: " . $data['prop_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("prop_id", array('id' => $data['prop_id']));
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
    public function del_prop_item()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("PropAnimated")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $prop_id = $data['prop_id'];
            }

            $prop_name = M('Prop')->where('prop_id = ' . $prop_id)->getField("name");
            $info = $prop_name . "礼物动画: " . $data['prop_id'];
            $list = M("PropAnimated")->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("prop_id", array('id' => $data['prop_id']));
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
     * [set_activity_prop 设置活动礼物]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-06T18:36:10+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     */
    public function set_activity_prop()
    {
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $prop_name = M('Prop')->where('prop_id = ' . $id)->getField("name");
            $info = $prop_name . "活动礼物动画: " . $id;
            $res = M("MConfig")->where("code='activity_prop_id'")->setField("val", trim($id));
            if ($res) {
                save_log($info . l("UPDATE_SUCCESS"), 1);
                $this->success(l("UPDATE_SUCCESS"));
            } else {
                save_log($info . l("UPDATE_FAILED"), 0);
                $this->error(l("UPDATE_FAILED"));
            }
            clear_auto_cache("m_config");
        }
    }
}
