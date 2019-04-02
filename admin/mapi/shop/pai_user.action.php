<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/pai_user.action.php');

class pai_userCModule extends pai_userModule
{
    /*
     * 购物订单结算页面--买给主播
     * */
    public function order_settlement(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $shop_info = $_REQUEST['shop_info'];
        $shop_info=json_decode($shop_info,true);

        foreach($shop_info as $key =>$value){
            $user_name = $GLOBALS['db']->getOne("SELECT nick_name FROM ".DB_PREFIX."user WHERE id=".$value['podcast_id']);
            if(!$user_name){
                $user_name = '';
            }

            $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.price,gs.kd_cost,gs.inventory';
            $where = 'gs.id=ug.goods_id and ug.is_effect=1 and ug.user_id='.$value['podcast_id'].' and gs.id='.$value['goods_id'];
            $user_goods_info = $GLOBALS['db']->getRow("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE $where ");
            if($user_goods_info){
                $user_goods_info['imgs'] = json_decode(get_spec_image($user_goods_info['imgs']),true)[0];
            }
            $user_goods_info['number'] = intval($value['number']);
            $user_goods_info['nick_name'] = $user_name;
            $user_goods_info['podcast_id'] = $value['podcast_id'];
            $user_goods_info['total_price'] = ($user_goods_info['price']*intval($value['number']))+$user_goods_info['kd_cost'];
            $user_goods_info['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
            $goods_info[] = $user_goods_info;
            $root['all_total_price']+=$user_goods_info['total_price'];
            $root['all_number']+=intval($value['number']);
        }

        if($goods_info){
            $root['status']= 1;
            $root['goods'] = $goods_info;
            $root['page_title'] ='确认订单';

        }else{
            $root['status']= 0;
            $root['error']= '暂无分销商品';
            $root['goods'] = array();
        }

        api_ajax_return($root);
    }

    /*
     * 购物订单结算页面--买给自己
     * */
    public function order_settlement_user(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $shop_info = $_REQUEST['shop_info'];
        $shop_info=json_decode($shop_info,true);

        foreach($shop_info as $key =>$value){
            $user_name = $GLOBALS['db']->getOne("SELECT nick_name FROM ".DB_PREFIX."user WHERE id=".$value['podcast_id']);
            if(!$user_name){
                $user_name = '';
            }

            $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.price,gs.kd_cost,gs.inventory';
            $where = 'gs.id=ug.goods_id and ug.is_effect=1 and ug.user_id='.$value['podcast_id'].' and gs.id='.$value['goods_id'];
            $user_goods_info = $GLOBALS['db']->getRow("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE $where ");
            if($user_goods_info){
                $user_goods_info['imgs'] = json_decode(get_spec_image($user_goods_info['imgs']),true)[0];
            }
            $user_goods_info['number'] = intval($value['number']);
            $user_goods_info['nick_name'] = $user_name;
            $user_goods_info['podcast_id'] = $value['podcast_id'];
            $user_goods_info['total_price'] = ($user_goods_info['price']*intval($value['number']))+$user_goods_info['kd_cost'];
            $user_goods_info['order_sn'] = to_date(NOW_TIME,"Ymdhis").rand(10,99);
            $goods_info[] = $user_goods_info;
            $root['all_total_price']+=$user_goods_info['total_price'];
            $root['all_number']+=intval($value['number']);
        }

        $user_address = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE is_default=1 and user_id=".$user_id);
        if(!$user_address){
            $user_address = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE is_default=0 and user_id=".$user_id);
        }
        if($user_address){
            $address = array();
            $address['id'] = $user_address['id'];
            $address['consignee'] = $user_address['consignee'];
            $address['consignee_mobile'] = $user_address['consignee_mobile'];
            $user_address['consignee_district']=json_decode(htmlspecialchars_decode($user_address['consignee_district']),true);
            $address['consignee_address'] = $user_address['consignee_district']['province'].$user_address['consignee_district']['city'].$user_address['consignee_district']['area'].$user_address['consignee_address'];
        }

        if($goods_info){
            $root['status']= 1;
            $root['goods'] = $goods_info;
            $root['user_address'] = $address;
            $root['page_title'] ='确认订单';

        }else{
            $root['status']= 0;
            $root['error']= '暂无分销商品';
            $root['goods'] = array();
        }

        api_ajax_return($root);
    }

    /*
     * 观众直播间打开商品列表
     * */
    public function open_goods()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            ajax_return(array(
                'status' => 0,
                'error'  => '未登录',
            ));
        }

        $podcast_id = intval($_REQUEST['podcast_id']);
        $count = $GLOBALS['db']->getRow("SELECT COUNT(1) FROM  ".DB_PREFIX."user WHERE id=".$podcast_id);
        if(!$count){
            ajax_return(array(
                'status' => 0,
                'error'    => '主播不存在'
            ));
        }

        $url = SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=shop&act=shop_goods_list&page=1&podcast_id='.$podcast_id;

        ajax_return(array(
            'status' => 1,
            'url'    => $url
        ));
    }

    /*
    * 观众直播间打开商品详情链接
    * */
    public function open_goods_detail()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            ajax_return(array(
                'status' => 0,
                'error'  => '未登录',
            ));
        }

        $goods_id = intval($_REQUEST['goods_id']);
        $podcast_id = intval($_REQUEST['podcast_id']);
        $count = $GLOBALS['db']->getRow("SELECT COUNT(1) FROM  ".DB_PREFIX."user WHERE id=".$podcast_id);
        if(!$count){
            ajax_return(array(
                'status' => 0,
                'error'    => '主播不存在'
            ));
        }

        $url = SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=shop&act=shop_goods_details&podcast_id='.$podcast_id."&goods_id=".$goods_id;

        ajax_return(array(
            'status' => 1,
            'url'=> $url,
        ));
    }

}
