<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH.'mapi/lib/pai_user.action.php');
class pai_userCModule  extends pai_userModule
{

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
        $user_info = $GLOBALS['db']->getRow("SELECT is_shop,shop_user_id,store_url FROM  ".DB_PREFIX."user WHERE id=".$podcast_id);
        if(!$user_info){
            ajax_return(array(
                'status' => 0,
                'error'    => '主播不存在'
            ));
        }

        $head_args['page']=1;
        $head_args['user_id']=$user_info['shop_user_id'];
        $head_args['goods_id']=$goods_id;
        $head_args['ctl']='mystore';
        $head_args['act']='shop';
        $info = third_o2o_mall('http://www.513zhibo.com/saas_api_server.php',$head_args);
        if($info['list']){
            $url = $info['list']['url'];
        }

        ajax_return(array(
            'status' => 1,
            'url'=> $url,
        ));
    }

}
?>