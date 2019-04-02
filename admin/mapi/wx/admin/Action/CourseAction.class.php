<?php

/**
 *
 */
class CourseAction extends CommonAction
{
    public function index()
    {
        $_REQUEST['type'] = intval($_REQUEST['type']);
        $map              = array('type' => $_REQUEST['type'], 'is_delete' => 0);
        $id               = intval($_REQUEST['id']);
        $title            = trim($_REQUEST['title']);
        if ($id) {
            $map['id'] = $id;
        }
        if ($title) {
            $map['title'] = array('like', '%' . trim($title) . '%');
        }
        $model = D(MODULE_NAME);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    public function set_effect()
    {
        $id          = intval($_REQUEST['id']);
        $ajax        = intval($_REQUEST['ajax']);
        $info        = M(MODULE_NAME)->where("id=" . $id)->getField("title");
        $c_is_effect = M(MODULE_NAME)->where("id=" . $id)->getField("is_effect"); //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        M(MODULE_NAME)->where("id=" . $id)->setField("is_effect", $n_is_effect);
        save_log($info . l("SET_EFFECT_" . $n_is_effect), 1);
        clear_auto_cache("get_help_cache");
        clear_auto_cache("article_notice");
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }
    public function set_recommend()
    {
        $id             = intval($_REQUEST['id']);
        $ajax           = intval($_REQUEST['ajax']);
        $info           = M(MODULE_NAME)->where("id=" . $id)->getField("title");
        $c_is_recommend = M(MODULE_NAME)->where("id=" . $id)->getField("is_recommend"); //当前状态
        $n_is_recommend = $c_is_recommend == 0 ? 1 : 0; //需设置的状态
        $res = M(MODULE_NAME)->where("id=" . $id)->setField("is_recommend", $n_is_recommend);
        if ($res) {
            M('course_season')->where("pid=" . $id)->setField("is_recommend", $n_is_recommend);
        }
        save_log($info . l("SET_EFFECT_" . $n_is_recommend), 1);
        clear_auto_cache("get_help_cache");
        clear_auto_cache("article_notice");
        $this->ajaxReturn($n_is_recommend, l("SET_EFFECT_" . $n_is_recommend), 1);
    }
    public function edit()
    {
        $_REQUEST['type'] = intval($_REQUEST['type']);

        $id = intval($_REQUEST['id']);
        $vo = M(MODULE_NAME)->find($id);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function update()
    {
        $data        = M(MODULE_NAME)->create();
        $data['img'] = $_REQUEST['image'];
        //clear_auto_cache("prop_list");
        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("title");
        //开始验证有效性
        if (!check_empty($data['title'])) {
            ajax_return(array('status' => 0, 'error' => '请输入分集名称'));
        }
        if (!check_empty($data['img'])) {
            ajax_return(array('status' => 0, 'error' => '请上传封面'));
        }
        if (!check_empty($data['content'])) {
            ajax_return(array('status' => 0, 'error' => '请输入内容'));
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
            ajax_return(array('status' => 1, 'error' => '更新成功'));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            ajax_return(array('status' => 0, 'error' => '更新错误'));
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
    public function pageviewsStatistics()
    {
        $model     = M('CourseSeasonLog');
        $c_s_model = M('CourseSeason');
        $table     = array('fanwe_course_season' => 's', 'fanwe_course_season_log' => 'sl');
        // 今日最佳
        $date  = to_date(NOW_TIME, 'Y-m-d');
        $where = "s.id = course_season_id AND sl.create_date = '$date'";
        // 播放时长
        $res = $model->table($table)->field('s.*,sl.course_season_id,SUM(sl.view_time) AS sum')->where($where)->group('course_season_id')->order(array('sum desc'))->limit(10)->select();
        foreach ($res as $key => $value) {
            $res[$key]['long_time'] = round($season['long_time'] * $value['view_times'] / 60, 2);
        }
        $this->assign('today_sum', $res);
        // 播放人次
        $res = $model->table($table)->field('s.*,sl.course_season_id,COUNT(1) AS count')->where($where)->group('course_season_id')->order(array('count desc'))->limit(10)->select();
        $this->assign('today_count', $res);
        // 近7日数据
        $date = date("Y-m-d", strtotime("-7 day"));
        $where = "s.id = course_season_id AND sl.create_date > '$date'";
        // 播放时长
        $res = $model->table($table)->field('s.*,sl.course_season_id,SUM(sl.view_time) AS sum')->where($where)->group('course_season_id')->order(array('sum desc'))->limit(10)->select();
        foreach ($res as $key => $value) {
            $res[$key]['long_time'] = round($season['long_time'] * $value['view_times'] / 60, 2);
        }
        $this->assign('seven_day_sum', $res);
        // 播放人次
        $res = $model->table($table)->field('s.*,sl.course_season_id,COUNT(1) AS count')->where($where)->group('course_season_id')->order(array('count desc'))->limit(10)->select();
        $this->assign('seven_day_count', $res);
        $this->display();
    }
}
