<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class courseCModule extends baseCModule
{
    protected static function getUserId($is_return = true)
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id && $is_return) {
            api_ajax_return(array(
                'status' => 0,
                'error'  => '未登录',
            ));
        }
        return $user_id;
    }
    protected static function returnCourseList($where, $list_type)
    {
        $page      = intval($_REQUEST['page']);
        $page_size = intval($_REQUEST['page_size']);
        $page      = $page ? $page : 1;
        $page_size = $page_size ? $page_size : 20;

        $model = Model::build('course');
        $list  = $model->getList($where, 'id,title,content,img,is_hot,is_recommend,type', array(($page - 1) * $page_size, $page_size));
        foreach ($list as $key => $value) {
            $list[$key]['views']  = $model->sumViews($value['id']);
            $list[$key]['newest'] = to_date($model->newest($value['id']), 'y-m-d');
        }

        $count      = $model->count($where);
        $total_page = ceil($count / $page_size);

        $status     = 1;
        $error      = '';
        $page_title = $where['type'] ? '家长必修' : '企业经营';
        api_ajax_return(compact('status', 'error', 'total_page', 'list', 'list_type', 'page_title'));
    }
    /**
     * 首页
     * @return [type] [description]
     */
    public function index()
    {
        $model       = Model::build('course_season');
        $index_model = Model::build('index_image');

        $where          = array('is_recommend' => 1);
        $recommend_list = $model->getRecommendList('cs.id,cs.title name,cs.img,cs.long_time');
        $banner         = $index_model->getList(array('show_position' => 10));
        $left_list      = $index_model->getList(array('show_position' => 11), 'title name,image img,url', 1);
        $right_list     = $index_model->getList(array('show_position' => 12), 'title name,image img,url', 2);
        $status         = 1;
        $error          = '';
        $page_title     = '首页';
        api_ajax_return(compact('status', 'error', 'banner', 'left_list', 'right_list', 'recommend_list', 'page_title'));
    }
    /**
     * 老余说最新列表
     * @return [type] [description]
     */
    public function yu_list()
    {
        $where = array(
            'type' => 0,
        );
        $list_type = trim($_REQUEST['list_type']);
        switch ($list_type) {
            case 'hot':
                $where['is_hot'] = 1;
                break;
            case 'recommend':
                $where['is_recommend'] = 1;
                break;
            default:
                break;
        }
        self::returnCourseList($where, $list_type);
    }
    /**
     * 齐家学堂列表
     * @return [type] [description]
     */
    public function qi_list()
    {
        $where = array(
            'type' => 1,
        );
        $list_type = trim($_REQUEST['list_type']);
        switch ($list_type) {
            case 'hot':
                $where['is_hot'] = 1;
                break;
            case 'recommend':
                $where['is_recommend'] = 1;
                break;
            default:
                break;
        }
        self::returnCourseList($where, $list_type);
    }
    /**
     * 搜索
     * @return [type] [description]
     */
    public function search()
    {
        $search      = trim($_REQUEST['search']);
        $search_type = intval($_REQUEST['search_type']);
        $where       = array();
        if (isset($_REQUEST['type'])) {
            $type = intval($_REQUEST['type']);

            $where['type'] = $type;
        } else {
            $type = '';
        }
        if ($search_type) {
            $model = Model::build('course_season');
            $field = 'id,title,img,is_vip,long_time,view_times,create_time';
        } else {
            $model = Model::build('course');
            $field = 'id,title,img,is_hot,is_recommend,type,create_time';
        }
        if ($search) {
            $where = array(
                'title' => array('like', '%' . $search . '%'),
            );
        }
        $list       = $model->getList($where, $field);
        $total_page = 1;
        if (!$search_type) {
            $s_model = Model::build('course_season');
            $where   = array('is_delete' => 0, 'is_effect' => 1);
            foreach ($list as $key => $value) {
                $where['pid']             = $value['id'];
                $list[$key]['count']      = $s_model->count($where);
                $list[$key]['view_times'] = $s_model->sum('view_times', $where);
            }
        }
        $status     = 1;
        $error      = '';
        $page_title = '搜索';
        api_ajax_return(compact('status', 'error', 'list', 'search', 'search_type', 'type', 'page_title', 'total_page'));
    }
    /**
     *
     * 详情页
     * @return [type] [description]
     */
    public function detail()
    {
        $is_json = $_REQUEST['post_type'] == 'json';
        $user_id = self::getUserId(0);
        $id      = intval($_REQUEST['id']);
        $pid     = intval($_REQUEST['pid']);
        if ($is_json && $id) {
            $data = Model::build('course_season')->getOneById($id);
        } else if ($id) {
            $c_s_m  = Model::build('course_season');
            $data   = $c_s_m->getOneById($id);
            $where  = array('pid' => $data['pid']);
            $list   = $c_s_m->getList($where);
            $course = Model::build('course')->getOneById($data['pid']);
        } else if ($pid) {
            $c_s_m  = Model::build('course_season');
            $course = Model::build('course')->getOneById($pid);
            $where  = array('pid' => $pid);
            $list   = $c_s_m->getList($where);
            $data   = array();
            $id     = intval($list[0]['id']);
            if ($list) {
                $data = $c_s_m->getOneById($id);
            }
        } else {
            api_ajax_return(array(
                'status' => 0,
                'error'  => '参数错误',
            ));
        }
        $course['count'] = sizeof($list);
        $data['vip']     = Model::build('vip')->isVip($user_id);
        if ($data['is_vip'] && !$data['vip']) {
            $data['video_url'] = false;
            $data['sound_url'] = false;
        } else {
            $data['current_time'] = Model::build('course_season_log')->getCurrentTime($user_id, $id);
        }
        $status     = 1;
        $error      = '';
        $page_title = $course['title'] ? $course['title'] : $data['title'];
        api_ajax_return(compact('status', 'error', 'data', 'list', 'course', 'page_title'));
    }
    public function check()
    {
        $user_id      = self::getUserId(0);
        $id           = intval($_REQUEST['id']);
        $current_time = intval($_REQUEST['current_time']);
        Model::build('course_season_log')->check($id, $user_id, $current_time);
        api_ajax_return(array(
            'status' => 1,
            'error'  => '',
        ));
    }
    public function vip()
    {
        $pid = intval($_REQUEST['pid']);

        $user_id = self::getUserId();

        $user_model    = Model::build('user');
        $vip_model     = Model::build('vip');
        $article_model = Model::build('article');

        $user      = $user_model->getInfo($user_id);
        $vip_list  = $vip_model->getList(array('vip_lv' => array('>=', $vip_model->getVip($user_id))));
        $show1     = $article_model->selectOne(array('id' => 28));
        $show2     = $article_model->selectOne(array('id' => 29));
        $show3     = $article_model->selectOne(array('id' => 30));
        $vip_level = array(
            1 => $show1 ? $show1['content'] : '',
            2 => $show2 ? $show2['content'] : '',
            3 => $show3 ? $show3['content'] : '',
        );

        $status     = 1;
        $error      = '';
        $page_title = '会员';
        api_ajax_return(compact('status', 'error', 'user', 'vip_list', 'vip_level', 'pid', 'page_title'));
    }
    public function vip_exchange()
    {
        $user_id = self::getUserId();
        if ($_POST) {
            $res = Model::build('vip_exchange')->exchange(trim($_POST['code']), $user_id);
            if ($res) {
                api_ajax_return(array('status' => 1, 'error' => '兑换成功'));
            } else {
                api_ajax_return(array('status' => 0, 'error' => '兑换失败'));
            }
        } else {
            $user_model = Model::build('user');

            $user = $user_model->getInfo($user_id);

            $status = 1;
            $error  = '';
            api_ajax_return(compact('status', 'error', 'user'));
        }
    }
    // 高端会员介绍
    public function article_high_member()
    {
        $page_title = '世维学社，余老师的经典知识学习平台';
        api_ajax_return(compact('page_title'));
    }
}
