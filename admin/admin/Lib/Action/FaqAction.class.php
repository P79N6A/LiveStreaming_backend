<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class FaqAction extends CommonAction
{
    public function index()
    {
        if (strim($_REQUEST['question']) != '') {//name
            $map['question'] = array('like', '%' . strim($_REQUEST['question']) . '%');

        }
        if (strim($_REQUEST['group']) != '') {
            $map['group'] = strim($_REQUEST['group']);
        }


        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (!empty ($model)) {
            $this->_list($model, $map);
        }

        $list = $this->get("list");
        $group = $GLOBALS['db']->getAll("select distinct(`group`) from " . DB_PREFIX . "faq");
        $this->assign("group", $group);
        $this->display();
    }

    public function add()
    {
        $group = $GLOBALS['db']->getAll("select distinct(`group`) from " . DB_PREFIX . "faq");
        $this->assign("group", $group);
        $this->assign("new_sort", M("Faq")->max("sort") + 1);
        $this->display();
    }

    public function edit()
    {
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $this->assign('vo', $vo);
        $group = $GLOBALS['db']->getAll("select distinct(`group`) from " . DB_PREFIX . "faq");
        $this->assign("group", $group);
        $this->display();
    }


    public function foreverdelete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['question'];
            }
            if ($info) $info = implode(",", $info);
            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
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

    public function insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M(MODULE_NAME)->create();

        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));
        if (!check_empty($data['group'])) {
            if (!check_empty($_REQUEST['define_group'])) {
                $this->error("请输入分组");
            } else {
                $data['group'] = strim($_REQUEST['define_group']);
            }
        }
        if (!check_empty($data['question'])) {
            $this->error("请输入问题");
        }
        if (!check_empty($data['answer'])) {
            $this->error("请输入答案");
        }

        // 更新数据
        $log_info = $data['question'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
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
        $data = M(MODULE_NAME)->create();

        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("question");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['group'])) {
            if (!check_empty($_REQUEST['define_group'])) {
                $this->error("请输入分组");
            } else {
                $data['group'] = strim($_REQUEST['define_group']);
            }
        }
        if (!check_empty($data['question'])) {
            $this->error("请输入问题");
        }
        if (!check_empty($data['answer'])) {
            $this->error("请输入答案");
        }

        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $log_info = M("Faq")->where("id=" . $id)->getField("question");
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("Faq")->where("id=" . $id)->setField("sort", $sort);
        save_log($log_info . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }

    public function set_effect()
    {
        $faq_id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $info = M(MODULE_NAME)->where("id=" . $faq_id)->getField("question");
        $c_is_effect = M("Faq")->where("id=" . $faq_id)->getField("is_effect");//当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        M("Faq")->where("id=" . $faq_id)->setField("is_effect", $n_is_effect);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        clear_auto_cache("get_help_cache");
        clear_auto_cache("article_notice");
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    public function set_show()
    {
        $faq_id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $info = M(MODULE_NAME)->where("id=" . $faq_id)->getField("question");
        $o_is_show = M("Faq")->where("id=" . $faq_id)->getField("is_show");//当前状态
        $n_is_show = $o_is_show == 0 ? 1 : 0; //需设置的状态
        M("Faq")->where("id=" . $faq_id)->setField("is_show", $n_is_show);
        save_log($info . l("SET_SHOW_" . $n_is_show), 1);
        clear_auto_cache("get_help_cache");
        clear_auto_cache("article_notice");
        $this->ajaxReturn($n_is_show, l("SET_SHOW_" . $n_is_show), 1);
    }

}

?>