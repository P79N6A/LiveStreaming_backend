<?php
class SpeakLevelAction extends CommonAction
{
    public function index()
    {
        $data = $_REQUEST;
        $parameter = $sql_w = '';
        if (intval($data['mount_id'] > 0)) {
            $parameter .= "mount_id=" . intval($data['mount_id']) . "&";
            $sql_w .= "sl.mount_id=" . intval($data['mount_id']) . " and ";
        }

        $model = D();
        $sql_str = "SELECT sl.*,m.`name` FROM `" . DB_PREFIX . "speak_level` AS sl LEFT JOIN " . DB_PREFIX . "mounts AS m ON m.id = sl.mount_id WHERE " . $sql_w . " 1=1"; //取出靓号表中的数据，到user表中取靓号昵称
        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 'sl.begin_level', 1);
        foreach ($volist as &$val) {
            $val['level_scope'] = $val['begin_level'] . "~" . $val['end_level'];
            if ($val['speak_num'] == 0) {
                $val['speak_num'] = '不限制';
            }
            $val['mount_day'] = ($val['mount_day']) ?: '';
        }
        $this->assign("list", $volist);
        $mounts = $GLOBALS['db']->getAll("SELECT id,name FROM " . DB_PREFIX . "mounts WHERE is_effect = 1");
        $this->assign("mounts", $mounts);
        $this->display();
    }

    public function add()
    {
        $id = $_REQUEST['id'];
        $vo = M(MODULE_NAME)->where(array('id' => $ud))->select();
        $this->assign("vo", $vo);
        $mounts = $GLOBALS['db']->getAll("SELECT id,name FROM " . DB_PREFIX . "mounts WHERE is_effect = 1");
        $this->assign("mounts", $mounts);
        $this->display();
    }

    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        if ($vo['speak_num'] == 0) {
            $vo['speak_num'] = '';
        }
        $mounts = $GLOBALS['db']->getAll("SELECT id,name FROM " . DB_PREFIX . "mounts WHERE is_effect = 1");
        $this->assign("mounts", $mounts);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function update()
    {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        //var_dump($data);
        if ($data['begin_level'] < 0) {
            $this->error("开始等级必须大于0");
        }

        if ($data['end_level'] < 0) {
            $this->error("结束等级必须大于0");
        }

        if ($data['end_level'] <= $data['begin_level']) {
            $this->error("结束等级必须大于开始等级");
        }

        if (!check_empty($data['begin_level'])) {
            $this->error("必须填写开始等级");
        }

        if (!check_empty($data['end_level'])) {
            $this->error("必须填写结束等级");
        }

        if (!empty($data['mount_id']) && (empty($data['mount_day']) || ($data['mount_day'] < 1))) {
            $this->error("赠送坐骑的时长至少为1天");
        }

        $list = M(MODULE_NAME)->save($data);

        if (false !== $list) {
            load_auto_cache('speak_level');
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

        $data = M(MODULE_NAME)->create();
        $list = M(MODULE_NAME)->select();
        foreach ($list as $key => $val) {
            if (($data['begin_level'] >= $val['begin_level']) && ($data['end_level'] <= $val['end_level'])) {
                $this->error("您输入的等级已被占用");
            }
            if (($data['begin_level'] >= $val['begin_level']) && ($data['begin_level'] < $val['end_level'])) {
                $this->error("您输入的开始等级已被占用");
            }
            if (($data['end_level'] >= $val['begin_level']) && ($data['end_level'] < $val['end_level'])) {
                $this->error("您输入的结束等级已被占用");
            }
        }

        if ($data['begin_level'] < 0) {
            $this->error("开始等级必须大于0");
        }

        if ($data['end_level'] < 0) {
            $this->error("结束等级必须大于0");
        }

        if ($data['end_level'] <= $data['begin_level']) {
            $this->error("结束等级必须大于开始等级");
        }

        if (!check_empty($data['begin_level'])) {
            $this->error("必须填写开始等级");
        }

        if (!check_empty($data['end_level'])) {
            $this->error("必须填写结束等级");
        }

        if (!empty($data['mount_id']) && (empty($data['mount_day']) || ($data['mount_day'] < 1))) {
            $this->error("赠送坐骑的时长至少为1天");
        }

        $list = M(MODULE_NAME)->add($data);

        if (false !== $list) {
            load_auto_cache('speak_level');
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function delete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        //rm_auto_cache("user_level");
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            //$rel_data = M(MODULE_NAME)->where($condition)->findAll();

            //if($info) $info = implode(",",$info);
            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
                load_auto_cache('speak_level');
                //save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
                $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
            } else {
                //save_log($info.l("FOREVER_DELETE_FAILED"),0);
                $this->error(l("FOREVER_DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }

    }
}
