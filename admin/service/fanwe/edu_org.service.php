<?php

class  edu_orgService
{
    public function get_org_info($id)
    {
        $id = intval($id);
        $sql = "select org.id,org.user_id,org.title,org.logo,org.address,org.view_num,org.is_recommend,org.desc_video,org.desc_video_image,org.store_url,org.images,org.members,org.description" .
            " from " . DB_PREFIX . "edu_org as org where org.id= " . $id;
        $org_info = $GLOBALS['db']->getRow($sql);

        $org_info['logo'] = get_spec_image($org_info['logo']);
        $org_info['desc_video_image'] = get_spec_image($org_info['desc_video_image'], 470, 300);
        $org_info['desc_video'] = add_domain_url($org_info['desc_video']);
        $images = json_decode($org_info['images'], true);
        $members = json_decode($org_info['members'], true);

        foreach ($images as &$image) {
            $image = get_spec_image($image, 750, 350);
        }
        unset($image);
        $org_info['images'] = empty($images) ? array() : $images;

        foreach ($members as &$member) {
            $member['avatar'] = get_spec_image($member['avatar'], 200, 200);
        }
        unset($member);

        $org_info['members'] = empty($members) ? array() : $members;

        return $org_info;
    }

    public function get_org_list($param)
    {
        $page = intval($param['page']);
        $page_size = intval($param['page_size']);

        if ($page > 0) {

            $limit = " limit " . ($page - 1) * $page_size . "," . $page_size . "";
        } elseif ($param['limit'] > 0) {
            $limit = " limit " . intval($param['limit']) . "";
        } else {
            $limit = '';
        }

        $sql = "select org.id,org.user_id,org.title,org.address,org.view_num,org.is_recommend,org.images,org.description" .
            " from " . DB_PREFIX . "edu_org as org where org.is_recommend=1 and org.is_effect=1 order by org.id desc" . $limit . "";
        $org_list = $GLOBALS['db']->getAll($sql);
        foreach ($org_list as $k => $v) {
            $images = json_decode($v['images'], true);
            $org_list[$k]['image'] = empty($images) ? 'default' : $images[0];
            unset($org_list[$k]['images']);
        }


        return $org_list;
    }

    public function get_class_offline($param)
    {
        $org_id = intval($param['org_id']);
        $user_id = intval($param['user_id']);
        $page = $param['page'] > 0 ? $param['page'] : 1;
        $page_size = empty($param['page_size']) ? 20 : $param['page_size'];

        $offset = ($page - 1) * $page_size;
        $sql = "select id,user_id,title,description,image,price,price as class_fee,class_num from " . DB_PREFIX . "edu_class_offline where user_id=" . $org_id . " and is_delete = 0 order by id desc limit {$offset},{$page_size}";
        $list = $GLOBALS['db']->getAll($sql);

        $classes = array();
        foreach ($list as $k => $v) {
            $v['image'] = get_spec_image($v['image']);
            $v['is_pay'] = $user_id == $org_id ? 1 : 0;
            $classes[$v['id']] = $v;
        }

        if ($user_id > 0 && !empty($classes)) {
            $orders = $GLOBALS['db']->getAll("select id,class_id from " . DB_PREFIX . "edu_offline_order where user_id = {$user_id} and class_id in (" . implode(',',
                    array_keys($classes)) . ")");
            foreach ($orders as $order) {
                $classes[$order['class_id']]['is_pay'] = 1;
            }
        }

        return array_values($classes);
    }
}
