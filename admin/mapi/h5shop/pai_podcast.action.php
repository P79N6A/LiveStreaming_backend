<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/pai_podcast.action.php');
class pai_podcastCModule extends pai_podcastModule
{
    //不需要判断是否登陆，但是需要判断用户是否存在
    protected static function checkSN($sn, $viewer_id, $podcast_id)
    {
        $table = '`' . DB_PREFIX . 'goods_order`';
        $field = '`id`';
        $where = '`order_sn`=' . intval($sn) . ' and `viewer_id`=' . intval($viewer_id) . ' and `podcast_id`=' . intval($podcast_id);
        $info  = $GLOBALS['db']->getRow("SELECT $field FROM $table WHERE $where");
        if (!$info) {
            ajax_return(array(
                'status' => 0,
                'error'  => '订单信息错误',
            ));
        }
    }
    /**
     * 主播提醒买家付款
     * @return [type] [description]
     */
    public function remind_buyer_pay()
    {
        $user_id     = intval($_REQUEST['user_id']);
        $order_sn    = strim($_REQUEST['order_sn']);
        $to_buyer_id = intval($_REQUEST['to_buyer_id']);
        self::checkSN($order_sn, $to_buyer_id, $user_id);
        $data = array(
            'podcast_id'  => $user_id,
            'to_buyer_id' => $to_buyer_id,
            'order_sn'    => $order_sn,
        );
        $rs = FanweServiceCall("pai_podcast", "remind_buyer_pay", $data);
        switch ($rs['status']) {
            case 10021:
                $error = "消息类型为空";
                break;
            case 10033:
                $error = "推送会员为空";
                break;
            case 10022:
                $error = "消息推送失败";
                break;
            case 1:
                $error = "提醒成功";
                break;
        }
        ajax_return(array(
            'status' => intval($rs['status']),
            'error'  => $error,
        ));
    }
    /**
     * 主播提醒买家收货
     * @return [type] [description]
     */
    public function remind_buyer_receive()
    {
        $user_id     = intval($_REQUEST['user_id']);
        $order_sn    = strim($_REQUEST['order_sn']);
        $to_buyer_id = intval($_REQUEST['to_buyer_id']);
        self::checkSN($order_sn, $to_buyer_id, $user_id);
        $data = array(
            'podcast_id'  => $user_id,
            'to_buyer_id' => $to_buyer_id,
            'order_sn'    => $order_sn,
        );
        $rs = FanweServiceCall("pai_podcast", "remind_buyer_receive", $data);
        switch ($rs['status']) {
            case 10021:
                $error = "消息类型为空";
                break;
            case 10033:
                $error = "推送会员为空";
                break;
            case 10022:
                $error = "消息推送失败";
                break;
            case 1:
                $error = "提醒成功";
                break;
        }
        ajax_return(array(
            'status' => intval($rs['status']),
            'error'  => $error,
        ));
    }
    /**
     * 主播提醒买家约会
     * @return [type] [description]
     */
    public function remind_buyer_to_date()
    {
        $user_id     = intval($_REQUEST['user_id']);
        $order_sn    = strim($_REQUEST['order_sn']);
        $to_buyer_id = intval($_REQUEST['to_buyer_id']);
        self::checkSN($order_sn, $to_buyer_id, $user_id);
        $data = array(
            'podcast_id'  => $user_id,
            'to_buyer_id' => $to_buyer_id,
            'order_sn'    => $order_sn,
        );
        $rs = FanweServiceCall("pai_podcast", "remind_buyer_to_date", $data);
        switch ($rs['status']) {
            case 10021:
                $error = "消息类型为空";
                break;
            case 10033:
                $error = "推送会员为空";
                break;
            case 10022:
                $error = "消息推送失败";
                break;
            case 1:
                $error = "提醒成功";
                break;
        }
        ajax_return(array(
            'status' => intval($rs['status']),
            'error'  => $error,
        ));
    }
    /**
     * 买家提醒主播约会
     * @return [type] [description]
     */
    public function remind_podcast_to_date()
    {
        $user_id       = intval($_REQUEST['user_id']);
        $order_sn      = strim($_REQUEST['order_sn']);
        $to_podcast_id = intval($_REQUEST['to_podcast_id']);
        self::checkSN($order_sn, $user_id, $to_podcast_id);
        $data = array(
            'podcast_id'    => $user_id,
            'order_sn'      => $order_sn,
            'to_podcast_id' => $to_podcast_id,
        );
        $rs = FanweServiceCall("pai_podcast", "remind_podcast_to_date", $data);
        switch ($rs['status']) {
            case 10021:
                $error = "消息类型为空";
                break;
            case 10033:
                $error = "推送会员为空";
                break;
            case 10022:
                $error = "消息推送失败";
                break;
            case 1:
                $error = "提醒成功";
                break;
        }

        ajax_return(array(
            'status' => intval($rs['status']),
            'error'  => $error,
        ));
    }
    /**
     * 买家提醒主播确认约会
     * @return [type] [description]
     */
    public function remind_podcast_to_confirm_date()
    {
        $user_id       = intval($_REQUEST['user_id']);
        $order_sn      = strim($_REQUEST['order_sn']);
        $to_podcast_id = intval($_REQUEST['to_podcast_id']);
        self::checkSN($order_sn, $user_id, $to_podcast_id);
        $data = array(
            'podcast_id'    => $user_id,
            'order_sn'      => $order_sn,
            'to_podcast_id' => $to_podcast_id,
        );
        $rs = FanweServiceCall("pai_podcast", "remind_podcast_to_confirm_date", $data);
        switch ($rs['status']) {
            case 10021:
                $error = "消息类型为空";
                break;
            case 10033:
                $error = "推送会员为空";
                break;
            case 10022:
                $error = "消息推送失败";
                break;
            case 1:
                $error = "提醒成功";
                break;
        }

        ajax_return(array(
            'status' => intval($rs['status']),
            'error'  => $error,
        ));
    }

    //远程更新订单状态
    public function deal_order(){

        $user_id = intval($_REQUEST['user_id']);//主播ID
        $order_sn = strim($_REQUEST['order_sn']);//订单编号
        $order_status = intval($_REQUEST['order_status']);//订单状态

        $sql = "select * from ".DB_PREFIX."goods_order where order_status!=".$order_status." and podcast_id=".$user_id." and order_sn=".$order_sn;
        $goods_order_info=$GLOBALS['db']->getRow($sql);//查询是否有此订单
        if (!$goods_order_info) {
            $root['status'] = 0;
            $root['error'] = "无此订单";
            api_ajax_return($root);
        }

        $order_info=array();
        $order_info['order_status']=$order_status;
        $order_info['order_status_time']=NOW_TIME;
        $status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_type='pai' and podcast_id='".$user_id."' and order_sn='".$order_sn."'");//修改订单表状态
        if($status){
            $sql = "SELECT pg.*,go.viewer_id,go.pai_id FROM ".DB_PREFIX."pai_goods as pg,".DB_PREFIX."goods_order as go  WHERE go.pai_id = pg.id and go.order_type='".pai."' and go.order_sn='".$order_sn."'";
            $order_data = $GLOBALS['db']->getRow($sql);
            $viewer_id=intval($order_data['viewer_id']);
            $pai_id=intval($order_data['pai_id']);

            $sql = "update ".DB_PREFIX."pai_goods set order_status=".$order_status." where is_true=1 podcast_id=".$user_id." and id=".$pai_id." ";
            $GLOBALS['db']->query($sql);//修改商品表状态

            $sql = "update ".DB_PREFIX."pai_join set order_status=".$order_status." where user_id=".$viewer_id." and pai_id=".$pai_id." ";
            $GLOBALS['db']->query($sql);//修改参与竞拍表状态

            if($order_data['is_true'] == 1){
                $content="主播‘".$order_data['podcast_name']."’已确认‘".$order_data['name']."’发货";
            }
            $rs=FanweServiceCall("message","send",array("send_type"=>'podcast_to_over_tryst',"user_ids"=>$viewer_id,"content"=>$content));

            $root['status'] = 1;
            $root['error'] = "提醒成功";
        }else{
            $root['status']=10028;
            $root['error'] = "提醒失败";
        }

        api_ajax_return($root);

    }






}
