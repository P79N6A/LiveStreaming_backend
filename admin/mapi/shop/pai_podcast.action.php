<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH.'mapi/lib/pai_podcast.action.php');
class pai_podcastCModule extends pai_podcastModule
{
    public function upload()
    {
        if ($GLOBALS['user_info']['id'] == 0) {
            ajax_return(array('status' => 0, 'error' => '请先登录'));
        }
        // 开始上传
        // 创建avatar临时目录
        $temp = APP_ROOT_PATH . "public/paiimgs/temp/";
        self::mkdirm($temp);

        $img_result = save_image_upload($_FILES, "file", "attachment/temp", array('origin' => array(600, 600, 0, 0)));
        // 开始移动图片到相应位置
        $id = $GLOBALS['user_info']['id'];

        $dir_name = to_date(get_gmtime(), "Ym/d/H");

        $save_rec_Path = "/public/paiimgs/" . $dir_name . "/origin/"; //上传时先存放原图
        $savePath      = APP_ROOT_PATH . "public/paiimgs/" . $dir_name . "/origin/"; //绝对路径
        self::mkdirm(APP_ROOT_PATH . "public/paiimgs/" . $dir_name . "/origin/");
        //文件名
        $save_name = md5(time() . rand(100, 999)) . $id . ".jpg";
        //相对路径
        $image_file_domain = ".".$save_rec_Path.$save_name;
        //服务器路径
        $image_big_file = $savePath . $save_name;

        //保存文件
        @file_put_contents ( $image_big_file, file_get_contents ( $img_result ['file'] ['thumb'] ['origin'] ['path'] ) );

        if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
        {
        	syn_to_remote_image_server($image_file_domain);
        }

        @unlink ( $img_result ['file'] ['thumb'] ['origin'] ['path'] );
        @unlink ( $img_result ['file'] ['path'] );

        if(file_exists($image_big_file)){
        	$root['status'] = 1;
        	$root['error'] = '上传成功';
        	$root['server_path']      = $image_file_domain;
        	$root['server_full_path'] =get_spec_image($image_file_domain);
        }else{
        	$root['status'] = 0;
        	$root['error'] = '上传失败';
        	$root['path'] ='';
        }
        ajax_return($root);
    }
    /**
     * 虚幻创建文件夹
     * @param  [type]  $path [description]
     * @param  integer $mod  [description]
     * @return [type]        [description]
     */
    protected static function mkdirm($path, $mod = 0777)
    {
        if (!file_exists($path)) {
            self::mkdirm(dirname($path));
            mkdir($path, $mod);
        }
    }

    public function test()
    {

        //推送

        $user_id = intval($_REQUEST['id']);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');

        fanwe_require(APP_ROOT_PATH . 'system/schedule/android_list_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_list_schedule.php');

        fanwe_require(APP_ROOT_PATH . 'system/schedule/android_file_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_file_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');

        $user_type = $GLOBALS['db']->getRow("SELECT apns_code,device_type FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
        if (intval($user_type['device_type']) == 1) {
            print_r('安卓推送');
            //安卓推送信息
            $apns_app_code_list   = array();
            $apns_app_code_list[] = $user_type['apns_code'];

            $AndroidList = new android_list_schedule();
            $data        = array(
                'dest'    => implode(",", $apns_app_code_list),
                'content' => 'ceshi',
                'user_id' => $user_id,
                'room_id' => 0,
                'url'     => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=goods',
                'type'    => 5,
            );
            print_r($data);
            //print_r($AndroidList);
            $ret_android = $AndroidList->exec($data);
            print_r($ret_android);
            print_r('安卓推送结束');
        } elseif (intval($user_type['device_type']) == 2) {
            print_r('ios 推送');
            //ios 推送信息
            $apns_ios_code_list   = array();
            $apns_ios_code_list[] = $user_type['apns_code'];

            $IosList  = new ios_list_schedule();
            $ios_data = array(
                'dest'    => implode(",", $apns_ios_code_list),
                'content' => 'ceshi',
                'user_id' => $user_id,
                'room_id' => 0,
                'url'     => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=goods',
                'type'    => 5,
            );
            print_r($ios_data);
            //print_r($IosList);
            $ret_ios = $IosList->exec($ios_data);
            print_r($ret_ios);
            print_r('ios 推送结束');
        }
    }

    //主播-预载新建实物竞拍
    public function addpaidetail(){
        $root=array();
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods_id = intval($_REQUEST['goods_id']);

        $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.imgs_details,gs.pai_diamonds,gs.description,gs.kd_cost,gs.score,gs.inventory,gs.bz_diamonds,gs.jj_diamonds,gs.pai_time';
        $user_goods_info = $GLOBALS['db']->getRow("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and gs.inventory>0 and gs.is_effect=1 and gs.id=".$goods_id);
        if(!$user_goods_info){
            $root['status']=0;
            $root['error']="主播暂无商品";
            api_ajax_return($root);
        }

        if($user_goods_info['inventory'] != 0){
            $root['status'] = 1;
            $root['error'] = '';
            $data = array();

            $data['goods_id'] = $user_goods_info['goods_id'];
            $goods_name = mb_substr($user_goods_info['name'],0,25);
            $data['name'] = mb_strlen($goods_name) < 25?$goods_name:$goods_name."......";
            $data['qp_diamonds'] = $user_goods_info['pai_diamonds'];
            $data['imgs'] = json_decode($user_goods_info['imgs']);
            $data['bz_diamonds'] = $user_goods_info['bz_diamonds'];
            $data['jj_diamonds'] = $user_goods_info['jj_diamonds'];
            if($data['jj_diamonds'] == 0){
                $data['jj_diamonds']=1;
            }
            $data['pai_time'] = $user_goods_info['pai_time'];
            $data['pai_yanshi'] = 1;
            $data['max_yanshi'] = 1;

            if(OPEN_GOODS == 1){
                $data['shop_id'] = 10086;
                $data['shop_name'] = '刘德华店铺';
            }

            $root['data'] = $data;
        }else{
            $root['status']=0;
            $root['error']="商品库存不足";
        }

        api_ajax_return($root);
    }


//    //提醒卖家发货
//    public function remind_seller_delivery(){
//        $root =array();
//        $user_id = intval($GLOBALS['user_info']['id']);
//        if ($user_id == 0) {
//            $root['status'] = 10007;
//            $root['error']  = "请先登录";
//            api_ajax_return($root);
//        }
//
//        $order_sn = strim($_REQUEST['order_sn']);
//        $head_args['orderNo']=$order_sn;
//
//        $ret=third_interface($user_id,'http://gw1.yimile.cc/V1/Order.json?action=OrderRemindConsignment',$head_args);
//        if($ret['code'] == 0){
//            $root['status']=1;
//            $root['error']="提醒成功";
//        }else{
//            $root['status']=0;
//            $root['error']="消息推送失败";
//        }
//
//        return $root;
//
//    }

//    //第三方商城--支付成功修改订单状态
//    public function pay_success_remind($order_sn){
//        $root =array();
//        $user_id = intval($GLOBALS['user_info']['id']);
//        if ($user_id == 0) {
//            $root['status'] = 10007;
//            $root['error']  = "请先登录";
//            api_ajax_return($root);
//        }
//
//        //$goods_id = strim($_REQUEST['order_sn']);
//        $head_args['orderNo']=$order_sn;
//
//        $ret=third_interface($user_id,'http://gw1.yimile.cc/V1/Order.json?action=OrderPaySuccess',$head_args);
//        if($ret['code'] == 0){
//            $root['error']="提醒成功";
//        }else{
//            $root['error']="消息推送失败";
//        }
//
//        return $root;
//
//    }

//    //第三方商城--确认收货
//    public function confirm_the_goods($order_sn){
//        $root =array();
//        $user_id = intval($GLOBALS['user_info']['id']);
//        if ($user_id == 0) {
//            $root['status'] = 10007;
//            $root['error']  = "请先登录";
//            api_ajax_return($root);
//        }
//
//        //$goods_id = strim($_REQUEST['order_sn']);
//        $head_args['orderNo']=$order_sn;
//
//        $ret=third_interface($user_id,'http://gw1.yimile.cc/V1/Order.json?action=OrderConfirmStete',$head_args);
//        if($ret['code'] == 0){
//            $root['error']="提醒成功";
//        }else{
//            $root['error']="消息推送失败";
//        }
//
//        return $root;
//    }


//    //第三方商城---个人中心收入明细（商品）接口
//    public function commodity_profitlist($page,$page_size,$time,$year,$month,$type){
//        $root = array();
//        $user_id = intval($GLOBALS['user_info']['id']);
//        if ($user_id == 0) {
//            $root['status'] = 10007;
//            $root['error']  = "请先登录";
//            api_ajax_return($root);
//        }
//
//        $head_args['pageNo'] = $page;
//        $head_args['pageRows'] = $page_size;
//        $head_args['refreshDate'] = $time;
//        $head_args['year'] = $year;
//        $head_args['month'] = $month;
//        $head_args['orderType'] = $type;
//
//        $ret=third_interface($user_id,'http://gw1.yimile.cc/v1/User.json?action=CommodityProfitList',$head_args);
//        if($ret['code'] == 0){
//            $root['status'] = 1;
//            if($ret['profitOrder'] == ''){
//                $ret['profitOrder'] =array();
//            }else{
//                foreach($ret['profitOrder'] as $key => $vaule){
//                    $time=strtotime($vaule['orderTimeSpan']);
//                    $ri=date('m-d',$time);
//                    $zhou=date('N',$time);
//                    switch ($zhou) {
//                        case '1':
//                            $zhou = '星期一';
//                            break;
//                        case '2':
//                            $zhou = '星期二';
//                            break;
//                        case '3':
//                            $zhou = '星期三';
//                            break;
//                        case '4':
//                            $zhou = '星期四';
//                            break;
//                        case '5':
//                            $zhou = '星期五';
//                            break;
//                        case '6':
//                            $zhou = '星期六';
//                            break;
//                        default:
//                            $zhou = '星期日';
//                            break;
//                    }
//                    $root['profitOrder'][$ri.'-'.$zhou]['total']+=$vaule['commissionCount'];
//
//                    $data['order_sn'] = $vaule['orderNo'];
//                    $data['name'] = $vaule['commodityName'];
//                    $data['diamond'] = $vaule['commissionCount'];
//                    $root['profitOrder'][$ri.'-'.$zhou]['goods_list'][] = $data;
//                }
//                foreach($root['profitOrder'] as $k => $v){
//                    $root['profitOrder'][$k]['time'] = $k;
//                }
//                $root['profitOrder'] = array_values($root['profitOrder']);
//            }
//            //$root['profitOrder'] = $ret['profitOrder'];
//            $root['orderIncome'] = $ret['orderIncome'];
//
//        }else{
//            $root['error']="获取失败";
//        }
//
//        return $root;
//    }


//    //第三方商城---竟拍商品详情图片接口
//    public function getauction_commodity_detail(){
//        $root=array();
//        $user_id = intval($GLOBALS['user_info']['id']);
//        if ($user_id == 0) {
//            $root['status'] = 10007;
//            $root['error']  = "请先登录";
//            api_ajax_return($root);
//        }
//
//        $goods_id = intval($_REQUEST['goods_id']);
//        $head_args['commodityId']=$goods_id;
//
//        $ret=third_interface($user_id,'http://gw1.yimile.cc/V1/Commodity.json?action=GetAuctionCommodityDetail',$head_args);
//        if($ret['code'] == 0){
//            $root['status'] = 1;
//            $root['pai_goods'] = array();
//            if($ret['data']['detailImages'] != ''){
//                foreach($ret['data']['detailImages'] as $key => $vaule){
//                    $goods_detail = array();
//                    $goods_detail['image_width'] =$vaule['detailImageWidth'];
//                    $goods_detail['image_height'] =$vaule['detailImageHeight'];
//                    $goods_detail['image_url'] =$vaule['detailImageUrl'];
//                    $root['goods_detail'][] = $goods_detail;
//                }
//            }else{
//                $root['goods_detail'][] = array();
//            }
//
//        }else{
//            $root['error'] = '获取失败';
//        }
//
//        api_ajax_return($root);
//    }


    /*
      * 主播下架商品
      * */
    public function podcasr_shelves_goods(){

        $goods_id = intval($_REQUEST['goods_id']);
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $res = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".DB_PREFIX."user_goods WHERE is_effect=1 and goods_id=".$goods_id." and user_id=".$user_id);
        if($res){
            $sql = "update ".DB_PREFIX."user_goods set is_effect=0 where user_id=".$user_id." and goods_id=".$goods_id;
            $user_goods_info = $GLOBALS['db']->query($sql);
            if($user_goods_info){
                $root['status']= 1;
                $root['error']= '商品下架成功';
            }else{
                $root['status']= 0;
                $root['error']= '商品下架失败';
            }
        }else{
            $root['status']= 0;
            $root['error']= '无此商品';
        }

        api_ajax_return($root);
    }


    /*
     * 主播删除下架商品
     * */
    public function podcasr_delete_goods(){

        $goods_id = intval($_REQUEST['goods_id']);
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $res = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".DB_PREFIX."user_goods WHERE is_effect=0 and goods_id=".$goods_id." and user_id=".$user_id);
        if($res){
            $sql = "delete from ".DB_PREFIX."user_goods where is_effect=0 and user_id=".$user_id." and goods_id=".$goods_id;
            $user_goods_info = $GLOBALS['db']->query($sql);
            if($user_goods_info){
                $root['status']= 1;
                $root['error']= '商品删除成功';
            }else{
                $root['status']= 0;
                $root['error']= '商品删除失败';
            }
        }else{
            $root['status']= 0;
            $root['error']= '无此商品';
        }

        api_ajax_return($root);
    }

    /*
     * 主播清空下架商品
     * */
    public function podcasr_empty_goods(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $res = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".DB_PREFIX."user_goods WHERE is_effect=0 and user_id=".$user_id);
        if($res){
            $sql = "delete from ".DB_PREFIX."user_goods where is_effect=0 and user_id=".$user_id;
            $user_goods_info = $GLOBALS['db']->query($sql);
            if($user_goods_info){
                $root['status']= 1;
                $root['error']= '清空成功';
            }else{
                $root['status']= 0;
                $root['error']= '清空失败';
            }
        }else{
            $root['status']= 0;
            $root['error']= '主播无下架商品';
        }

        api_ajax_return($root);
    }


}

?>