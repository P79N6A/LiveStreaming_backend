<?php

class shopService{


    /**
     * 添加商品
     */
    function add_goods($data){

        $goods['user_id']  = intval($data['user_id']);//主播ID
        $goods['name']  = htmlspecialchars_decode($data['name']);//商品名称
        $goods['imgs']  = $data['imgs'];//商品图片
        $goods['price']  = floatval($data['price']);//商品价钱
        $goods['url']  = htmlspecialchars_decode($data['url']);//购买商品URL
        $goods['description']  = strim($data['description']);//商品描述

        $status = $GLOBALS['db']->autoExecute(DB_PREFIX."podcast_goods",$goods,"INSERT");
        if($status){
            $status=1;
            $goods_id = $GLOBALS['db']->insert_id();
            $info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."podcast_goods WHERE id=".$goods_id);
            $result_data=$info;
        }else{
            $status=10057;
        }

        return array("status"=>$status,"info"=>$result_data);
    }

    /**
     * 编辑商品
     */
    function edit_goods($data){

        $id  = intval($data['id']);
        $user_id = intval($data['user_id']);
        $info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."podcast_goods WHERE id=".$id." and is_effect=1 and user_id=".$user_id);

        if($info){

            $goods['name']  = htmlspecialchars_decode($data['name']);//商品名称
            $goods['imgs']  = $data['imgs'];//商品图片
            $goods['price']  = strim($data['price']);//商品价钱
            $goods['url']  = htmlspecialchars_decode($data['url']);//商品详情URL地址
            $goods['description']  = strim($data['description']);//商品描述

            $status = $GLOBALS['db']->autoExecute(DB_PREFIX."podcast_goods", $goods, $mode = 'UPDATE', "id=".$id);
            if($status){
                $goods['id']  = $id;
                $goods['user_id']  = $user_id;
                return array("status"=>1,"info"=>$goods);

            }else{
                return array("status"=>10057);
            }

        }else{
            $root['status'] = 10008;
            $root['error']  = "商品不存在";
            return $root;
        }

    }

    /**
     * 删除商品
     */
    public function del_goods($data){
        $id  = intval($data['id']);
        $user_id = intval($data['user_id']);
        $info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."podcast_goods WHERE id=".$id." and is_effect=1 and user_id=".$user_id);

        if($info){
            $goods['is_effect']  = 0;//商品状态
            $GLOBALS['db']->autoExecute(DB_PREFIX."podcast_goods", $goods, $mode = 'UPDATE', "id=".$id);
            $root['status'] = 1;
            return $root;

        }else{
            $root['status'] = 10008;
            $root['error']  = "商品不存在";
            return $root;
        }

    }

    /**
     * 编辑小店URL
     */
    function edit_store_url($data){
        $id  = intval($data['id']);

        $info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$id);
        if($info){
            $user['store_url'] = htmlspecialchars_decode($data['store_url']);
            $GLOBALS['db']->autoExecute(DB_PREFIX."user", $user, $mode = 'UPDATE', "id=".$id);
            $root['status'] = 1;
        }else{
            $root['status'] = 10009;
            $root['error']  = "主播不存在";
        }
        return $root;
    }


    /*
    * 查看购物订单详情
    * */
    function virtual_shop_order_details($data){

        $order_id = intval($data['order_id']);
        $order_sn = intval($data['order_sn']);

        $order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE id=".$order_id." and order_sn=".$order_sn);
        if(!$order_info){
            $root['status']='10037';
            $root['error']="订单号错误";
            api_ajax_return($root);
        }

        $return_data=array();
        if($order_info){

            $return_data['order_id']=$order_id;
            $return_data['order_sn']=$order_sn;

            if($order_info['is_p'] == 1 && $order_info['order_status']==1){
                $goods_info = $GLOBALS['db']->getAll("SELECT us.nick_name,gs.name,gs.price,gs.imgs,go.number,go.freight_diamonds,go.memo FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.goods_id=gs.id and go.pid=".$order_id);
                foreach($goods_info as $k => $v){
                    $goods_info[$k]['imgs'] = json_decode(get_spec_image($v['imgs']),true)[0];
                }
                foreach($goods_info as $k => $v){
                    unset($v['nick_name']);
                    $goods_info[$goods_info[$k]['nick_name']][] =$v;
                    unset($goods_info[$k]);
                }
                $return_data['goods_imgs'] = $goods_info;

            }else{
                $goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE id=".$order_info['goods_id']);
                $podcast_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$order_info['podcast_id']);//卖家（主播）
                $return_data['supplier_name']=$podcast_info['nick_name'];//主播昵称
                $return_data['goods_id']=$goods_info['id'];//商品ID
                $return_data['goods_name']=$goods_info['name'];//商品名称
                $goods_info['imgs'] = json_decode(get_spec_image($goods_info['imgs']),true)[0];
                $return_data['goods_imgs']=$goods_info['imgs'];//商品图片
                $return_data['goods_price']=$goods_info['price'];//商品单价
                $return_data['number']=$order_info['number'];
                $return_data['memo']=$order_info['memo'];
            }
            $return_data['order_status']=$order_info['order_status'];
            $return_data['no_refund']=$order_info['no_refund'];
            $return_data['refund_buyer_status']=$order_info['refund_buyer_status'];
            $return_data['order_status_time']=date('Y-m-d H:i:s',$order_info['order_status_time']+8*3600);
            $return_data['refund_buyer_delivery']=$order_info['refund_buyer_delivery'];
            $return_data['refund_seller_status']=$order_info['refund_seller_status'];
            $return_data['refund_platform']=$order_info['refund_platform'];
            $return_data['refund_over_time']=$order_info['refund_over_time'];
            $return_data['refund_reason']=$order_info['refund_reason'];
            $return_data['total_diamonds']=floatval($order_info['total_diamonds']);
            $return_data['goods_diamonds']=floatval($order_info['goods_diamonds']);
            $return_data['pay_diamonds']=floatval($order_info['pay_diamonds']);
            $return_data['podcast_ticket']=floatval($order_info['podcast_ticket']);
            $return_data['refund_diamonds']=$order_info['refund_diamonds'];
            $return_data['freight_diamonds']=$order_info['freight_diamonds'];
            $return_data['consignee']=$order_info['consignee'];
            $return_data['consignee_mobile']=$order_info['consignee_mobile'];
            $return_data['consignee_district']=json_decode(htmlspecialchars_decode($order_info['consignee_district']),true);
            $return_data['consignee_address']=$return_data['consignee_district']['province'].$return_data['consignee_district']['city'].$return_data['consignee_district']['area'].$order_info['consignee_address'];
            $return_data['create_time']=$order_info['create_time'];
            $return_data['create_date']=$order_info['create_date'];
            $return_data['podcast_id']=$order_info['podcast_id'];
            $return_data['user_id']=$order_info['viewer_id'];
            $return_data['goods_id']=$order_info['goods_id'];
            $return_data['pay_time']=$order_info['pay_time'];
            $return_data['time'] = $order_info['delivery_time'];//发货时间
            $return_data['courier_number']=$order_info['courier_number'];//物流单号
            $return_data['courier_offic']=$order_info['courier_offic'];//物流公司
            $return_data['buy_type'] = intval($order_info['buy_type']);//0购买给自己 1购买给主播
            $return_data['is_p'] = intval($order_info['is_p']);
            $return_data['pid'] = intval($order_info['pid']);
        }

        return $return_data;

    }

    /*
     * 加入购物车
     * */
    public function join_shopping($data){


        $user_id = intval($data['user_id']);
        $goods_id = intval($data['goods_id']);//商品ID
        $podcast_id = intval($data['podcast_id']);//主播ID
        $number = intval($data['number']);//商品数量

        if($number==0){
            $root['status'] = 0;
            $root['error']  = "添加数量有误";
            return $root;
        }

        $goods_inventory = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."goods WHERE inventory<".$number." and id=".$goods_id);
        if($goods_inventory){
            $root['status'] = 0;
            $root['error']  = "库存不足";
            return $root;
        }

        $user_shopping = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."shopping_cart WHERE user_id=".$user_id." and goods_id=".$goods_id." and podcast_id=".$podcast_id);
        if($user_shopping){
            $sql = "update ".DB_PREFIX."shopping_cart set number=number+".$number." where goods_id=".$goods_id." and user_id=".$user_id." and podcast_id=".$podcast_id;
            $user_update = $GLOBALS['db']->query($sql);//增加购物车里商品数量
            if($user_update){
                $root['status'] = 1;
                $root['error']  = "成功加入购物车";
            }else{
                $root['status'] = 0;
                $root['error']  = "加入失败";
            }

        }else{

            $goods_ingo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE is_effect=1 and id=".$goods_id);

            $info = array();
            $info['user_id'] = $user_id;
            $info['goods_id'] = $goods_id;
            $info['name'] = strim($goods_ingo['name']);
            $info['imgs'] = $goods_ingo['imgs'];
            $info['attr'] = '';
            $info['unit_price'] = floatval($goods_ingo['price']);
            $info['number'] = $number;
            $info['total_price'] = floatval($goods_ingo['price'])*$number;
            $info['verify_code'] = '';
            $time = NOW_TIME+28800;
            $create_time = date("Y-m-d H:i:s",$time);
            $info['create_time'] = $create_time;
            $info['update_time'] = 0;
            $info['return_money'] = 0;
            $info['return_total_money'] = 0;
            $info['return_score'] = intval($goods_ingo['score']);
            $info['return_total_score'] = intval($goods_ingo['score'])*$number;
            $info['cate_id'] = intval($goods_ingo['cate_id']);
            $info['sub_name'] = '';
            $info['podcast_id'] = $podcast_id;
            $info['attr_str'] = '';
            $info['is_effect'] = 1;

            $shopping_cart = $GLOBALS['db']->autoExecute(DB_PREFIX."shopping_cart",$info,"INSERT");
            if($shopping_cart){
                $root['status'] = 1;
                $root['error']  = "成功加入购物车";
            }else{
                $root['status'] = 0;
                $root['error']  = "新增失败";
            }

        }

        return $root;
    }



}


?>