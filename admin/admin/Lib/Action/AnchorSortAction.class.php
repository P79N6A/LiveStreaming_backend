<?php
class AnchorSortAction extends CommonAction
{
    public function index()
    {
        parent::index();
    }

    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M('anchor_sort')->where("id=$id")->find();
        $this->assign('vo', $vo);
        $this->display();
    }

    public function update()
    {
        B('FilterString');
        $data = M('anchor_sort')->create();
        if (!check_empty($data['name'])) {
            $this->error("请输入名称");
        }
        $res = M('anchor_sort')->where("id !=" . $data['id'])->select();
        foreach ($res as $key => $val) {
            if (in_array($data['name'], $val)) {
                $this->error("名称已存在");
            }
        }

        $list = M('anchor_sort')->save($data);
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

    public function insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = array();
        $data['name'] = $_REQUEST['name'] ? $_REQUEST['name'] : 0;
        $data['is_effect'] = intval($_REQUEST['is_effect']);
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));
        if (!($data['name'])) {
            $this->error("请输入名称");
        }
        $res = M('anchor_sort')->select();
        foreach ($res as $key => $val) {
            if (in_array($data['name'], $val)) {
                $this->error("名称已存在");
            }
        }

        $list = M('anchor_sort')->add($data);
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

    public function delete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $list = M('anchor_sort')->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("FOREVER_DELETE_FAILED"), 0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        }
    }

    //是否有效
    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $info = M('anchor_sort')->where("id=" . $id)->getField("name");

        $c_is_effect = M('anchor_sort')->where("id=" . $id)->getField("is_effect"); //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态

        M('anchor_sort')->where("id=" . $id)->setField("is_effect", $n_is_effect);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        $this->update_class();
        //clear_auto_cache("anchor_sort");
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    //更新手机版本号
    public function update_class()
    {
        clear_auto_cache("m_config");
        $sql = $GLOBALS['db']->query("update " . DB_PREFIX . "m_config set val=val+1 where code ='init_version'");
        return $sql;
    }
}
