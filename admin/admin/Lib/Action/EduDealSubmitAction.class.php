<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class EduDealSubmitAction extends CommonAction
{

    const AUTH_TYPE_TEACHER = '教师';//教师认证类型
    const AUTH_TYPE_ORG = '机构';//机构认证类型

    public function index()
    {
        if (trim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . trim($_REQUEST['name']) . '%');
        }

        $user_id = intval($_REQUEST['user_id']);
        if ($user_id) {
            $map['user_id'] = array('eq', $user_id);
        }

        $create_time_2 = empty($_REQUEST['create_time_2']) ? to_date(get_gmtime(),
            'Y-m-d') : strim($_REQUEST['create_time_2']);
        $create_time_2 = to_timespan($create_time_2) + 24 * 3600;
        if (trim($_REQUEST['create_time_1']) != '') {
            $map["create_time"] = array('between', array(to_timespan($_REQUEST['create_time_1']), $create_time_2));
        }

        $map['deal_status'] = array('in', '2,3');
        $map['is_effect'] = 0;
        $map['is_delete'] = 0;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = 'EduDeal';
        $model = M($name);
        if (!empty ($model)) {
            $this->_list($model, $map);
        }

        $this->display();
    }


    public function edit()
    {
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M("EduDeal")->where($condition)->find();

        //状态
        if ($vo['deal_status'] == 1) {
            $vo['deal_status_name'] = '通过';
        } elseif ($vo['deal_status'] == 2) {
            $vo['deal_status_name'] = '未审核';
        } elseif ($vo['deal_status'] == 3) {
            $vo['deal_status_name'] = '未通过';
        }

        $vo['begin_time'] = to_date(to_timespan($vo['begin_time']), 'Y-m-d H:i:s');
        $vo['end_time'] = to_date(to_timespan($vo['end_time']), 'Y-m-d H:i:s');
        if (defined('ORDER_ZC') && ORDER_ZC == 1) {
            $this->assign('order_zc', 1);
            $image = json_decode($vo['image'], true);
            if (is_array($image) && count($image) > 0) {
                foreach ($image as &$value) {
                    $value = get_spec_image($value, 710, 300);
                }
            } else {
                if (!is_json($vo['image']) && !empty($vo['image'])) {
                    $image = array(get_spec_image($vo['image']));
                } else {
                    $image = null;
                }
            }
            $vo['image'] = $image;
            $this->assign('image_num', count($image));

            if(empty($vo['introduce_video'])){
                $user_introduce_video = M('User')->field('introduce_video')->where('id = '.$vo['user_id'])->find();
                $vo['introduce_video'] = get_spec_image($user_introduce_video['introduce_video']);
            }else{
                $vo['introduce_video'] = get_spec_image($vo['introduce_video']);
            }
            $this->assign('introduce_video', $vo['introduce_video']);

            if(empty( $vo['desc_image'])){
                $user_introduce_video = M('User')->field('desc_image')->where('id = '.$vo['user_id'])->find();
                $vo['desc_image'] = get_spec_image($user_introduce_video['desc_image']);
            }else{
                $vo['desc_image'] = get_spec_image($vo['desc_image']);
            }
        } else {
            if (is_json($vo['image'])) {
                $vo['image'] = json_decode($vo['image'], true);
                if (is_array($vo['image']) && count($vo['image']) > 0) {
                    foreach ($vo['image'] as &$value) {
                        $value = get_spec_image($value, 710, 300);
                    }
                } else {
                    if (!empty($vo['image'])) {
                        $vo['image'] = array(get_spec_image($vo['image']));
                    } else {
                        $vo['image'] = null;
                    }
                }
                $this->assign('image_num', count($vo['image']));
            } else {
                $vo['image'] = get_spec_image($vo['image'], 710, 300);
            }
        }

        //排序
        $max_sort = M("EduDeal")->max("sort");
        $this->assign('new_sort', $max_sort + 1);

        $this->assign('vo', $vo);

        //分类
        $cate_list = M("EduCourseCategory")->where("is_effect=1")->findAll();
        $this->assign('cate_list', $cate_list);

        //拥金
        $this->assign('default_pay_radio', app_conf("PAY_RADIO"));


        $this->display();
    }

    public function update()
    {
        B('FilterString');
        $data = M("EduDeal")->create();

        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));

        if ($data['deal_status'] != 1 && $data['deal_status'] != 3) {
            $this->error("请选择审核的操作");
        }

        if (empty($data['begin_time'])) {
            $this->error("请输入项目开始时间");
        }

        if (empty($data['end_time'])) {
            $this->error("请输入项目结束时间");
        }

        if (empty($data['video_begin_time'])) {
            $this->error("请输入直播开始时间");
        }

        $deal_info = M("EduDeal")->where("id=" . intval($data['id']))->find();

        $deal_user = M("User")->where("id=" . intval($deal_info['user_id']) . " and is_effect=1")->find();
        if (!$deal_user) {

            $this->error("发起人不存在");
        }

        if (!$deal_info) {
            $this->error("请选择审核的项目");
        }

        $begin_time_num = to_timespan(to_date(to_timespan($data['begin_time']), 'Y-m-d H:i'));
        $end_time_num = to_timespan(to_date(to_timespan($data['end_time']), 'Y-m-d H:i'));
        $video_begin_time_num = to_timespan($data['video_begin_time']);

        if ($data['deal_status'] == 1) {

            if ($data['name'] == '') {
                $this->error("请输入项目名称");
            }

            if (msubstr($data['name'], 0, 30, "utf-8", false) != $data['name']) {
                $this->error("项目名称不能超过30个字");
            }

            if (empty($data['image'])) {
                $this->error("项目名称不能超过30个字");
                $root['error'] = '请上传图片';
                return api_ajax_return($root);
            }


            if (!$begin_time_num) {
                $this->error("请输入项目开始时间");
            }
            if (!$end_time_num) {
                $this->error("请输入项目结束时间");
            }
            if (!$video_begin_time_num) {
                $this->error("请输入直播开始时间");
            }

            if ($begin_time_num < to_timespan(to_date(NOW_TIME, 'Y-m-d'))) {
                $this->error("开始时间不能小于当前时间");
            }
            if ($end_time_num < NOW_TIME) {
                $this->error("结束时间不能小于当前时间");
            }

            if ($end_time_num < $begin_time_num) {
                $this->error("结束时间不能小于开始时间");
            }

            if ($video_begin_time_num < $end_time_num) {
                $this->error("直播开始时间要大于项目结束时间");
            }

            if ($data['limit_num'] <= 0) {
                $this->error("请输入目标数量");
            }

            $max_limit = 10000;
            if ($data['limit_num'] > $max_limit) {
                $this->error("目标数量不能大于" . $max_limit);
            }

            if ($data['price'] <= 0) {
                $this->error("请输入支持价格");
            }

            $max_price = 10000;
            if ($data['price'] > $max_price) {
                $this->error("支持价格不能大于" . $max_price);
            }

            if (empty($data['description'])) {
                $this->error("请输入详情描述");
            }

            //获取会员的tags写入项目中
            if ($deal_user['authentication_type'] == self::AUTH_TYPE_TEACHER) {
                $data['tags'] = M("EduTeacher")->where("user_id = " . $deal_user['id'] . "")->getField("tags");
            } elseif ($deal_user['authentication_type'] == self::AUTH_TYPE_ORG) {
                $data['tags'] = M("EduOrg")->where("user_id=" . $deal_user['id'] . "")->getField("tags");
            }

            if (defined('ORDER_ZC') && ORDER_ZC == 1) {
                $user_introduce_video = M('User')->field('introduce_video,desc_image')->where('id = '.$deal_info['user_id'])->find();
                $video['introduce_video'] = $user_introduce_video['introduce_video'];
                $video['desc_image'] = $user_introduce_video['desc_image'];
                $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_deal", $video, $mode = 'UPDATE', "id=" .  $data['id']);
            }

        } elseif ($data['deal_status'] == 3) {
            if (empty($data['no_pass_memo'])) {
                $this->error("请输入未通过理由");
            }

            $data['is_effect'] = 0;
            $data['sort'] = 0;
        }

        if (defined('ORDER_ZC') && ORDER_ZC == 1) {
            $data['image'] = json_encode(array_values(array_filter($data['image'])));
        }
        // 更新数据
        $data['begin_time'] = to_date($begin_time_num);
        $data['end_time'] = to_date($end_time_num);
        $data['video_begin_time'] = to_date($video_begin_time_num - $video_begin_time_num % 60);
        $list = M("EduDeal")->save($data);
        $log_info = $deal_info['title'] . "(ID：" . $deal_info['id'] . ")";
        if (false !== $list) {
            //通知发起人项目状态
            if ($data['deal_status'] == 1 && $data['is_effect'] == 1) {
                $time = to_date(NOW_TIME, 'm-d H:i:s');
                FanweServiceCall('edu_video', 'push_deal', array(
                    'user_id' => $deal_info['user_id'],
                    'content' => "您发起的{$deal_info['name']}，{$time}审核上架",
                ));
            }

            //成功提示
            $this->assign("jumpUrl", u(MODULE_NAME . "/index", array("id" => $data['id'])));
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function foreverdelete()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M("EduDeal")->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['title'];
            }
            if ($info) {
                $info = implode(",", $info);
            }
            $list = M("EduDeal")->where($condition)->delete();

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
    //播放视频
    public function play(){
        $m_config =  load_auto_cache("m_config");//初始化手机端配置
        $app_id = $m_config['vodset_app_id'];
        $this->assign('app_id',$app_id);
        $video['live_in'] = 0;
        $video['mp4_url'] = get_spec_image(strim($_REQUEST['introduce_video']));
        $this->assign("video",$video);
        $this->display();
    }

}

?>