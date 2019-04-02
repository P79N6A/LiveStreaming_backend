<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class PkLiveHistoryAction extends CommonAction
{
    public function index()
    {

        $where = '';
        $parameter = '';
        //列表过滤器，生成时间搜索查询Map对象
        $map = $this->com_search();
        //查看是否有进行时间搜索
        if (!empty($map['start_time']) && !empty($map['end_time'])) {
            $parameter .= " & start_time BETWEEN '" . $map['start_time'] . "' AND '" . $map['end_time'] . "'";
            $where .= " AND start_time BETWEEN '" . $map['start_time'] . "' AND '" . $map['end_time'] . "'";
        }
        //查看是否有进行使用者或接收者ID搜索
        if (!empty($_REQUEST['from_user_id'])) {
            $parameter .= " & user_id LIKE '%" . strim($_REQUEST['from_user_id']) . "%'";
            $where .= " AND user_id LIKE '%" . strim($_REQUEST['from_user_id']) . "%'";
        }
        if (!empty($_REQUEST['to_user_id'])) {
            $parameter .= " & to_user_id LIKE '%" . strim($_REQUEST['to_user_id']) . "%' ";
            $where .= " AND to_user_id LIKE '%" . strim($_REQUEST['to_user_id']) . "%' ";
        }
        $model = D();
        $sql_str = "SELECT * FROM  `" . DB_PREFIX . "video_livepk_history` WHERE 1 ";
        $sql_str .= $where . " GROUP BY pk_time, pk_theme, stop_time";
        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'id', false);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        foreach ($voList as &$value) {
            $user_info = $user_redis->getRow_db($value['user_id'], array('nick_name', 'user_level'));
            $to_user_info = $user_redis->getRow_db($value['to_user_id'], array('nick_name', 'user_level'));
            $value['user_nick_name'] = $user_info['nick_name'];
            $value['to_user_nick_name'] = $to_user_info['nick_name'];
        }
        $this->assign('list', $voList);
        $this->display();
    }

    public function delete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M('VideoLivepkHistory')->where($condition)->findAll();
            $list = M('VideoLivepkHistory')->where($condition)->delete();
            foreach ($rel_data as $data) {
                $info[] = "[PK记录:" . $data['id'] . "]";
            }
            if ($info) {
                $info = implode(",", $info);
            }
            if ($list !== false) {
                save_log($info . "成功删除", 1);
                $this->success("成功删除", $ajax);
            } else {
                save_log($info . "删除出错", 0);
                $this->error("删除出错", $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
}
