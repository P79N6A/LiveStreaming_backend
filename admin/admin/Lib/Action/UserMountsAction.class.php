<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class UserMountsAction extends CommonAction
{
    public function index()
    {
        $data = $_REQUEST;
        $parameter = $sql_w = '';
        //查询昵称
        if (trim($data['user_id'] != '')) {
            $parameter = "user_id like " . urlencode('%' . trim($data['user_id']) . '%') . "&";
            $sql_w .= "um.user_id like '%" . trim($data['user_id']) . "%' and ";
        }
        if (intval($data['mount_id'] > 0)) {
            $parameter .= "mount_id=" . intval($data['mount_id']) . "&";
            $sql_w .= "um.mount_id=" . intval($data['mount_id']) . " and ";
        }

        $model = D();
        $sql_str = "SELECT um.*,m.`name` FROM `" . DB_PREFIX . "user_mounts` AS um LEFT JOIN " . DB_PREFIX . "mounts AS m ON m.id = um.mount_id WHERE " . $sql_w . " 1=1"; //取出靓号表中的数据，到user表中取靓号昵称
        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 'um.id', 0);
        $this->assign("list", $volist);
        $mounts = $GLOBALS['db']->getAll("SELECT id,name FROM " . DB_PREFIX . "mounts WHERE is_effect = 1");
        $this->assign("mounts", $mounts);
        $this->display();
    }

    public function add()
    {
        $mounts = $GLOBALS['db']->getAll("SELECT id,name FROM " . DB_PREFIX . "mounts WHERE is_effect = 1");
        $this->assign("mounts", $mounts);
        $this->display();
    }
    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $mounts = $GLOBALS['db']->getAll("SELECT id,name FROM " . DB_PREFIX . "mounts WHERE is_effect = 1");
        $this->assign("mounts", $mounts);
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
                $info[] = '坐骑-' . $data['mount_id'] . '，用户：' . $data['user_id'];
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
                clear_auto_cache('user_mounts');
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
        if (empty($data['mount_id'])) {
            $this->error("请选择坐骑");
        }
        if (empty($data['user_id'])) {
            $this->error("请输入用户id");
        }
        if (empty($data['end_time'])) {
            $this->error("请输入到期时间");
        }
        $data['create_time'] = time();
        $data['end_time'] = strtotime($data['end_time']);
        // 更新数据
        $log_info = '添加坐骑-' . $data['mount_id'] . '，用户：' . $data['user_id'] . ',到期时间' . $data['end_time'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            clear_auto_cache('user_mounts');
            account_log_com(array('diamonds' => 0, 'score' => 0, 'video_id' => 0), $data['user_id'], '管理员赠送坐骑[' . $data['mount_id'] . ']x' . ((new DateTime(date('Y-m-d', $data['create_time'])))->diff(new DateTime(date('Y-m-d', $data['end_time'])))->format('%R%a')) . '天', array('type' => 31));
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
        $log_info = '更新坐骑-' . $data['mount_id'] . '，用户：' . $data['user_id'] . ',到期时间' . $data['end_time'];
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (empty($data['mount_id'])) {
            $this->error("请选择坐骑");
        }
        if (empty($data['user_id'])) {
            $this->error("请输入用户id");
        }
        if (empty($data['end_time'])) {
            $this->error("请输入到期时间");
        }
        $data['end_time'] = strtotime($data['end_time']);
        // 更新数据
        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            clear_auto_cache('user_mounts');
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }
}