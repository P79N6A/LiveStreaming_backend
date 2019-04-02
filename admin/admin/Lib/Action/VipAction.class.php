<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class VipAction extends CommonAction
{
    public function index()
    {
        $map   = array();
        $model = D(MODULE_NAME);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }
    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $vo = M(MODULE_NAME)->find($id);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function update()
    {
        $data = M(MODULE_NAME)->create();
        //clear_auto_cache("prop_list");
        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("name");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        if (!check_empty($data['name'])) {
            $this->error("请输入VIP配置名称");
        }
        if (!check_empty($data['cost'])) {
            $this->error("请输入价格（元）");
        }
        if (!check_empty($data['month'])) {
            $this->error("请输入时长（月）");
        }
        // 更新数据
        if ($data['id']) {
            $list = M(MODULE_NAME)->save($data);
        } else {
            $data['create_time'] = NOW_TIME;
            $list                = M(MODULE_NAME)->add($data);
        }
        if (false !== $list) {
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
    public function delete()
    {
        //删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id   = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data  = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['title'];
            }
            if ($info) {
                $info = implode(",", $info);
            }

            $list = M(MODULE_NAME)->where($condition)->setField('is_delete', 1);
            if ($list !== false) {
                save_log($info . l("DELETE_SUCCESS"), 1);
                clear_auto_cache("get_help_cache");
                clear_auto_cache("article_notice");
                $this->success(l("DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("DELETE_FAILED"), 0);
                $this->error(l("DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
    public function exchange()
    {
        $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order = 'id desc';
        }
        $model = M('VipExchange');
        $map   = array();
        if (isset($_REQUEST['is_effect'])) {
            $map['is_effect'] = intval($_REQUEST['is_effect']);
        }
        if (isset($_REQUEST['vip_id'])) {
            $map['vip_id'] = intval($_REQUEST['vip_id']);
        }
        $count = $model->where($map)->count('id');

        if ($count > 0) {
            //创建分页对象
            if (!empty($_REQUEST['listRows'])) {
                $listRows = $_REQUEST['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            //分页查询数据
            $voList = $model->where($map)->order($order)->limit($p->firstRow . ',' . $p->listRows)->findAll();
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示

            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
            $sort    = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            foreach ($voList as $k => $v) {
                $voList[$k]['head_image'] = get_spec_image($v['head_image']);
            }
            $this->assign('id', $id);
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
            $this->assign("nowPage", $p->nowPage);
        }

        $vip = M('Vip')->findAll();
        $this->assign('vip', $vip);

        $this->display();
    }
    public function addExchange()
    {
        $adm_session = es_session::get(md5(conf("AUTH_KEY")));
        $adm_id      = intval($adm_session['adm_id']);

        $vip_id = intval($_REQUEST['vip_id']);
        $num    = intval($_REQUEST['num']);

        $model = M('VipExchange');
        $data  = array(
            'vip_id'    => $vip_id,
            'admin_id'  => $adm_id,
            'is_effect' => 1,
        );
        for ($i = 0; $i < $num; $i++) {
            $data['code'] = substr(md5(uniqid('', 1)), 0, 16);
            $model->add($data);
        }
        ajax_return(array(
            'status' => 1,
        ));
    }
}
