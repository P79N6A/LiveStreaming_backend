<?php

class edu_coursesService
{
    public function get_courses_info($data)
    {
        $id = intval($data['id']);
        $sql = "select cou.id,cou.title,cou.image,cou.price,cou.courses_count,cou.sale_count,cou.play_count,cou.user_id,cou.description,cou.tags" .
            ",u.nick_name as teacher,u.head_image,u.authentication_type,u.is_authentication" .
            " from " . DB_PREFIX . "edu_courses as cou " .
            " left join " . DB_PREFIX . "user as u on u.id=cou.user_id where cou.is_effect=1 and cou.id=" . $id . " ";
        $courses = $GLOBALS['db']->getRow($sql);
        if (empty($courses)) {
            return $courses;
        }

        $courses['image'] = get_spec_image($courses['image']);
        $courses['head_image'] = get_spec_image($courses['head_image']);
        $courses['is_authentication'] = $courses['is_authentication'] == 2 ? true : false;
        $courses['musicofasong_url'] = replace_public($courses['musicofasong_url']);
        if ($courses['tags'] != '') {
            $courses['tags'] = explode(',', $courses['tags']);
        } else {
            $courses['tags'] = array();
        }

        return $courses;
    }

    public function get_teacher_info($data)
    {
        $user_id = intval($data['user_id']);
        $sql = "select * from " . DB_PREFIX . "edu_teacher where user_id=" . $user_id . "";

        $teacher = $GLOBALS['db']->getRow($sql);
        return $teacher;
    }

    public function get_org_info($data)
    {

        $user_id = intval($data['user_id']);
        $sql = "select * from " . DB_PREFIX . "edu_org where user_id=" . $user_id . "";

        $org_info = $GLOBALS['db']->getRow($sql);

        return $org_info;
    }

    public function get_courses_class($data)
    {
        $course_id = intval($data['course']['id']);
        $course_user_id = $data['course']['user_id'];
        $login_user_id = intval($data['login_user_id']);

        //获取分组数据
        $sql = "select id,course_id,title,play_url,play_hls,musicofasong as musicofasong_url,price from " . DB_PREFIX . "edu_class_group where course_id=" . $course_id . " and is_delete = 0 order by sort desc,id";
        $class_group_list = $GLOBALS['db']->getAll($sql);

        //获取课时数据
        $sql_2 = "select id,title,price,course_id,group_id,type,play_url,play_hls,musicofasong as musicofasong_url from " . DB_PREFIX . "edu_class where course_id=" . $course_id . " and is_delete = 0 order by sort desc,id";
        $class_list = $GLOBALS['db']->getAll($sql_2);

        //会员登录 获取会员支付订单
        $user_pay_course_count = 0;
        $user_pay_class = array();
        $user_pay_group = array();
        if ($login_user_id > 0) {
            $user_order = $GLOBALS['db']->getAll("select id,course_id,group_id,class_id,buy_type from " . DB_PREFIX . "edu_class_order where user_id=" . $login_user_id . " and course_id=" . $course_id . " ");
            foreach ($user_order as $k => $v) {
                if ($v['buy_type'] == 1) {
                    //购买全部课程
                    $user_pay_course_count = 1;
                    break;
                } elseif ($v['buy_type'] == 2) {
                    //购买视频
                    $user_pay_group[] = $v['group_id'];
                } elseif ($v['buy_type'] == 3) {
                    //购买课时
                    $user_pay_class[] = $v['class_id'];
                }
            }

        }

        //分组数据处理
        $class_group = array();
        foreach ($class_group_list as $k => $v) {
            $class_group_list[$k]['is_has_video'] = empty($class_group_list[$k]['play_url']) ? 0 : 1;

            $class_group_list[$k]['is_pay'] = $login_user_id == $course_user_id || (($login_user_id > 0) && (!empty($user_pay_course_count) || in_array($v['id'],
                        $user_pay_group))) ? 1 : 0;
            if ($class_group_list[$k]['is_pay'] == 0 && ($data['course']['price'] > 0 || $v['price'] > 0)) {
                $class_group_list[$k]['play_url'] = '';
            } else {
                $class_group_list[$k]['play_url'] = replace_public($v['play_url']);
            }

            if ($class_group_list[$k]['is_pay'] == 0 && $data['course']['price'] > 0) {
                $class_group_list[$k]['price'] = $data['course']['price'];
            }

            $class_group[$v['id']] = $class_group_list[$k];
            $class_group[$v['id']]['audio_list'] = array();
            $class_group[$v['id']]['video_list'] = array();
        }

        //课时数据处理并放到分组数据里
        foreach ($class_list as $k => $v) {
            $class_list[$k]['is_pay'] = $login_user_id == $course_user_id || (($login_user_id > 0) && (!empty($user_pay_course_count) || in_array($v['id'],
                        $user_pay_class))) ? 1 : 0;
            if ($class_list[$k]['is_pay'] == 0 && $data['course']['price'] > 0) {
                $class_list[$k]['price'] = $data['course']['price'];
            }

            if ($class_list[$k]['is_pay'] == 0 && ($data['course']['price'] > 0 || $v['price'] > 0)) {
                $class_list[$k]['play_url'] = '';
                $class_list[$k]['musicofasong_url'] = '';
            }

            if (!$v['play_url']) {
                $class_list[$k]['play_url'] = replace_public($v['play_url']);
            }

            if (!$v['musicofasong_url']) {
                $class_list[$k]['musicofasong_url'] = replace_public($v['musicofasong_url']);
            }

            if ($v['type'] == 1) {
                $class_group[$v['group_id']]['audio_list'][] = $class_list[$k];
            } else {
                $class_group[$v['group_id']]['video_list'][] = $class_list[$k];
            }

        }

        return array_values($class_group);
    }

}