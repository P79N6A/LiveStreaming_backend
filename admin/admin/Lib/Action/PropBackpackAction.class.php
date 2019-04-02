<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class PropBackpackAction extends CommonAction
{
    public function index()
    {
        $data = $_REQUEST;
        $parameter = $sql_w = '';
        //查询昵称
        if (trim($data['user_id'] != '')) {
            $parameter = "user_id like " . urlencode('%' . trim($data['user_id']) . '%') . "&";
            $sql_w .= "pb.user_id like '%" . trim($data['user_id']) . "%' and ";
        }
        if (trim($data['name'] != '')) {
            $parameter = "name like " . urlencode('%' . trim($data['name']) . '%') . "&";
            $sql_w .= "p.name like '%" . trim($data['name']) . "%' and ";
        }

        $model = D();
        $sql_str = "SELECT pb.*,p.`name` FROM `" . DB_PREFIX . "prop_backpack` AS pb LEFT JOIN " . DB_PREFIX . "prop AS p ON p.id = pb.prop_id WHERE " . $sql_w . " 1=1"; //取出靓号表中的数据，到user表中取靓号昵称
        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 'pb.id', 0);
        $this->assign("list", $volist);
        $this->display();
    }

    public function add()
    {
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
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = '背包礼物-' . $data['prop_id'] . '，用户：' . $data['user_id'];
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
        if (empty($data['prop_id'])) {
            $this->error("请选择背包礼物");
        }
        if (empty($data['user_id'])) {
            $this->error("请输入用户id");
        }
        if (empty($data['num'])) {
            $this->error("请输入数量");
        }
        // 更新数据
        $log_info = '添加背包礼物-' . $data['prop_id'] . ' x ' . $data['num'] . ' ，用户：' . $data['user_id'];
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
        $log_info = '更新背包礼物-' . $data['prop_id'] . ' x ' . $data['num'] . '，用户：' . $data['user_id'];
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (empty($data['prop_id'])) {
            $this->error("请选择背包礼物");
        }
        if (empty($data['user_id'])) {
            $this->error("请输入用户id");
        }
        if (empty($data['num'])) {
            $this->error("请输入数量");
        }
        // 更新数据
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
}
