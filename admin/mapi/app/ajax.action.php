<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class ajaxCModule extends BaseModule
{
    public function upload_img()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $width = intval($_REQUEST['w']) ? intval($_REQUEST['w']) : 100;
            $height = intval($_REQUEST['h']) ? intval($_REQUEST['h']) : 100;
            $dst = $_REQUEST['dst'];
            $has_callback = $_REQUEST['has_callback'];
            $upload_url = get_manage_url_name() . '?m=PublicFile&a=do_upload&upload_type=1&dir=image&w=' . $width . '&h=' . $height . "&dst=" . $dst;
            $scale_w = floatval($width / $height);
            $scale_w = number_format($scale_w, 1);
            $scale = $scale_w . '/1';

            $root['h'] = $height;
            $root['scale'] = $scale;
            $root['upload_url'] = $upload_url;
            $root['has_callback'] = $has_callback;
        }

        api_ajax_return($root);
    }

}
