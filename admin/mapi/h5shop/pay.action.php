<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/pay.action.php');

class payCModule extends payModule
{

    /**
     * 支付页面
     * 1、接口安全验证
     * 2、插入对应订单
     * 3、返回界面信息：1)订单金额，2)账号余额
     */
    function pay(){
        $root = array('status' => 1,'error'=>'',"data"=>array(),"page_title"=>"付款");
        // 1、接口安全验证
        $_saas_params=$_REQUEST['_saas_params'];
        $_REQUEST=base64_decode($_saas_params);

        fanwe_require(APP_ROOT_PATH."system/saas/SAASAPIServer.php");
        $appid = FANWE_APP_ID_YM;
        $appsecret = FANWE_AES_KEY_YM;
        $server = new SAASAPIServer($appid, $appsecret);
        $ret = $server->verifyRequestParameters();
        if ($ret['errcode'] != 0) {
//            log_result($ret);
            die($server->toResponse($ret));
        }

        // 2、插入对应订单
        //插入order表

        print_r($_REQUEST);
        $order_data=array();
        $order_data['viewer_id']=$_REQUEST['user_id'];
        $order_data['goods_diamonds']=$_REQUEST['goods_diamonds'];
        $order_data['total_diamonds']=$_REQUEST['goods_diamonds'];       
        $order_data['order_sn']=$_REQUEST['order_sn'];
        $order_data['podcast_ticket']=$_REQUEST['podcast_ticket'];
        $order_data['podcast_id']=$_REQUEST['podcast_id'];

        $order_data['order_source']='remote';
        $order_data['order_type']='shop';
        if (!$GLOBALS['db']->autoExecute(DB_PREFIX."goods_order",$order_data,"INSERT")) {
            $root['data'] = $order_data;
            $root['data']['order_id'] = $GLOBALS['db']->insert_id();
        }else{
            $root['status']=0;
            $root['error']='插入信息失败';
            api_ajax_return($root);
        }

        // 3、返回界面信息：1)订单金额，2)账号余额
        //返回order界面，显示支付金额
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $diamonds   = $user_redis->getOne_db(intval($_REQUEST['user_id']), 'diamonds');
        
        $root['data']['diamonds'] = $diamonds;
        $root['data']['order_type'] = 'h5shop';
        api_ajax_return($root);
    }
}
