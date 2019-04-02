<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

class AwardMultipleAction extends CommonAction
{
    //列表
    public function index()
    {

        if (strim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . strim($_REQUEST['name']) . '%');
        }
        $model = D(MODULE_NAME);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $list = $this->get("list");
        $this->assign('list', $list);
        $this->display();
    }

    //插入
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
        if (!check_empty($data['multiple'])) {
            $this->error("中奖的倍数");
        }
        // if (!check_empty($data['probability'])) {
        //     $this->error("中奖概率");
        // }
        if (intval($data['multiple']) < 0) {
            $this->error("中奖的倍数不能小于0");
        }
        if (intval($data['denominator']) == 0) {
            $this->error("除数不能为0");
        }
        // if (intval($data['is_effect']) > 0) {
        //     $sql = "SELECT sum(probability) as probability from fanwe_award_multiple where is_effect = 1";
        //     $old_pro = $GLOBALS['db']->getOne($sql);
        //     if (intval($old_pro) + intval($data['probability']) > 100) {
        //         $limit = 100 - intval($old_pro);
        //         $this->error("设置错误，当前设置的中奖概率不能大于" . $limit);
        //     }
        // }
        // 更新数据
        $log_info = $data['name'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("award_list");
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }

    //编辑
    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $this->assign('vo', $vo);
        $this->display();
    }
    //更新
    public function update()
    {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("name");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['name'])) {
            $this->error("请输入名称");
        }
        if (!check_empty($data['multiple'])) {
            $this->error("中奖的倍数");
        }
        // if (!check_empty($data['probability'])) {
        //     $this->error("中奖概率");
        // }
        if (intval($data['multiple']) < 0) {
            $this->error("中奖的倍数不能小于0");
        }
        if (intval($data['denominator']) == 0) {
            $this->error("除数不能为0");
        }
        // if (intval($data['is_effect']) > 0) {
        //     $d_id = $data['id'];
        //     $sql = "SELECT sum(probability) as probability from fanwe_award_multiple where is_effect = 1 and id not in (" . $d_id . ")";
        //     $old_pro = $GLOBALS['db']->getOne($sql);
        //     if (intval($old_pro) + intval($data['probability']) > 100) {
        //         $limit = 100 - intval($old_pro);
        //         $this->error("设置错误，当前设置的中奖概率不能大于" . $limit);
        //     }
        // }
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("award_list", array('id' => $data['id']));
            clear_auto_cache("award_multiple", array('id' => $data['id']));
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
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
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("award_list");
                clear_auto_cache("award_multiple", array('id' => $data['id']));
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
 * 子动画
 */
    public function award_item()
    {
        $award_id = intval($_REQUEST['id']);
        $award_info = M("AwardMultiple")->getById($award_id);

        $this->assign("award_info", $award_info);
        if ($award_info) {
            $map['award_id'] = $award_info['id'];
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $model = D("AwardAnimated");
            if (!empty($model)) {
                $this->_list($model, $map);
            }
        }
        $this->display();
    }
    /**
     * 添加子动画
     */
    public function add_award_item()
    {
        $award_id = intval($_REQUEST['id']);
        $award_info = M("AwardMultiple")->getById($award_id);
        $this->assign("award_info", $award_info);
        $this->display();
    }
/**
 * 写入子动画
 */
    public function insert_award_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("AwardAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        $Count = M('AwardAnimated')->where('award_id = ' . $data['award_id'])->count();
        if ($Count >= 5) {
            $this->error('已经添加五个动画！不能再添加');
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add_award_item", array("id" => $data['award_id'])));

        $award_name = M('AwardMultiple')->where('award_id = ' . $data['award_id'])->getField("name");
        // 更新数据
        $list = M("AwardAnimated")->add($data);
        $log_info = $award_name . "中奖动画: " . $data['award_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            clear_auto_cache("award_multiple", array('id' => $data['award_id']));
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
    public function edit_award_item()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M("AwardAnimated")->where($condition)->find();
        $this->assign('vo', $vo);
        $award_info = M("AwardMultiple")->where("id=" . intval($vo['award_id']) . "")->find();
        $this->assign('award_info', $award_info);
        $this->display();
    }
/**
 * 更新子动画
 */
    public function update_award_item()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("AwardAnimated")->create();
        if (!check_empty($data['url'])) {
            $this->error("请上传图标");
        }
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit_award_item", array("id" => $data['id'])));

        $award_item = M("AwardAnimated")->getById(intval($data['id']));

        if (!$award_item) {
            $this->error("更新失败");
        }

        $award_name = M('AwardMultiple')->where('award_id = ' . $data['award_id'])->getField("name");
        // 更新数据
        $list = M("AwardAnimated")->save($data);

        $log_info = $award_name . "中奖动画: " . $data['award_id'];
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            clear_auto_cache("award_multiple", array('id' => $data['award_id']));
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
    public function del_award_item()
    {
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("AwardAnimated")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $award_id = $data['award_id'];
            }

            $award_name = M('AwardMultiple')->where('award_id = ' . $award_id)->getField("name");
            $info = $award_name . "中奖动画: " . $data['award_id'];
            $list = M("AwardAnimated")->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                clear_auto_cache("award_multiple", array('id' => $data['award_id']));
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
