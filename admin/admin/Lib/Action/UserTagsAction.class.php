<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class UserTagsAction extends CommonAction
{
    public function index()
    {
        if (strim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . strim($_REQUEST['name']) . '%');
        }
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();

        $model = D($name);

        if (!empty($model)) {
            $car = $this->_list($model, $map);
        }
        $this->display();
    }
    public function add()
    {
        $this->assign("new_sort", M("UserTags")->max("sort") + 1);
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

    public function foreverdelete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $id = explode(',', $id);
            //标签下有直播时不删除
            $condition = array('id' => array('in', $id));
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['name'];
            }
            if ($info) {
                $info = implode(",", $info);
            }
            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
                clear_auto_cache("user_tags");
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                if ($unset_num == '') {
                    $this->success(l("FOREVER_DELETE_SUCCESS"));
                } else {
                    $this->success($unset_num);
                }
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                if ($unset_num == '') {
                    $this->error(l("FOREVER_DELETE_FAILED"));
                } else {
                    $this->error($unset_num);
                }
            }
        } else {
            $this->error(l("INVALID_OPERATION"));
        }
    }

    public function insert()
    {
        B('FilterString');
        if (MODULE_NAME == 'UserTagsUrl') {
            //兼容
            $name = 'UserTags';
        } else {
            $name = MODULE_NAME;
        }
        $data = M($name)->create();
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));
        $data['name'] = strim($data['name']);

        if (!check_empty($data['name'])) {
            $this->error("请输入标签名称");
        }
        if (mb_strlen($data['name'], 'utf-8') > 10) {
            $this->error("标签名称不能大于10个字符");
        }
        $cate_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_tags where name = '" . $data['name'] . "'");
        if ($cate_id) {
            $this->error("标签名称已存在");
        }
        // 更新数据
        $log_info = $data['name'];
        $list = M($name)->add($data);

        if (false !== $list) {
            //成功提示
            clear_auto_cache("user_tags");
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
        B('FilterString');
        if (MODULE_NAME == 'UserTagsUrl') {
//兼容
            $name = 'UserTags';
        } else {
            $name = MODULE_NAME;
        }
        $data = M($name)->create();

        $log_info = M($name)->where("id=" . intval($data['id']))->getField("name");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['name'])) {
            $this->error("请输入标签名称");
        }
        if (mb_strlen($data['name'], 'utf-8') > 10) {
            $this->error("标签名称不能大于10个字符");
        }
        $cate_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_tags where name = '" . $data['name'] . "'");
        if ($cate_id && $cate_id != $data['id']) {
            $this->error("标签名称已存在，请重新填写！");
        }
        $list = M($name)->save($data);
        if (false !== $list) {
            clear_auto_cache("user_tags");
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
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
        $c_is_effect = M(MODULE_NAME)->where("id=" . $id)->getField("is_effect"); //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        M(MODULE_NAME)->where("id=" . $id)->setField("is_effect", $n_is_effect);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        clear_auto_cache("user_tags");
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M(MODULE_NAME)->where("id=" . $id)->getField("name");
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M(MODULE_NAME)->where("id=" . $id)->setField("sort", $sort);
        save_log($log_info . l("SORT_SUCCESS"), 1);
        clear_auto_cache("user_tags");
        $this->success(l("SORT_SUCCESS"), 1);
    }

}
