<?php

class addressService{

    /**
     * 收货地址列表
     * $data = array("user_id"=>$user_id,"page"=>$page,"page_size"=>$page_size);
     * return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$page);
     */
    public function address($data){

        $user_id = (int)$data['user_id'];
        $page = (int)$data['page'];
        $page_size = (int)$data['page_size'];

        $limit = (($page-1)*$page_size).",".$page_size;

        $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_address WHERE user_id=".$user_id,true,true);
        $list = array();

        $pages['page'] = $page;
        $pages['has_next'] = 0;

        if($rs_count > 0){
            $list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."user_address WHERE user_id=".$user_id." ORDER BY is_default desc,id desc limit ".$limit,true,true);
            foreach($list as $k=>$v){
                if($v['consignee_district']!=''){
                    $list[$k]['consignee_district'] = json_decode($v['consignee_district'],true);
                    if($list[$k]['consignee_district']==''){
                        $list[$k]['consignee_district'] = array();
                    }
                }else{
                    $list[$k]['consignee_district'] = array();
                }
            }
            $total = ceil($rs_count/$page_size);
            if($total > $page)
                $pages['has_next'] = 1;
        }

        return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);
    }

    /**
     * 添加收货地址
     * $data = array("user_id"=>$user_id,"consignee"=>$consignee,"consignee_mobile"=>$consignee_mobile,"consignee_district"=>$consignee_district,"consignee_address"=>$consignee_address,"is_default"=>$is_default);
     * return array("status"=>$status,"data"=>$data);
     */
    public function addaddress($data){

        $user_id = (int)$data['user_id'];//所有用户id
        $consignee = trim($data['consignee']);//收货人姓名
        $consignee_mobile = trim($data['consignee_mobile']);//收货人手机号
        $consignee_district = trim($data['consignee_district']);//收货人所在地行政地区信息,json格式
        $consignee_address = trim($data['consignee_address']);//收货人详细地址
        $is_default = (int)($data['is_default']);//是否默认 1为默认
        if(mb_strlen($consignee) > 8){
            $root['status'] = 0;
            return $root;
        }
        if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE id=".$user_id)<=0){
            $root['status'] = 10009;
            return $root;
        }
        if(empty($consignee)){
            $root['status'] = 10017;
            return $root;
        }
        if(empty($consignee_mobile)){
            $root['status'] = 10018;
            return $root;
        }
        if(!check_mobile($consignee_mobile)){
            $root['status'] = 10019;
            return $root;
        }

        $address['user_id'] = $user_id;
        $address['consignee'] = $consignee;
        $address['consignee_mobile'] = $consignee_mobile;
        $address['consignee_district'] = $consignee_district;
        $address['consignee_address'] = $consignee_address;
        $address['is_default'] = $is_default;
        $address['create_time'] = to_date(get_gmtime(),'Y-m-d H:i:s');

        if($GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE user_id=".$user_id)){
            $root['data'] = array();
            $root['status'] = 10015;
            return $root;
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX."user_address",$address,"INSERT");
        $address_id = $GLOBALS['db']->insert_id();
        if($address_id){
            if($is_default){
                $GLOBALS['db']->query("update ".DB_PREFIX."user_address set is_default = 0 where is_default = 1 and id <> ".$address_id." and user_id = ".$user_id);
            }
            $root['status'] = 1;
            $data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE id=".$address_id);
            if($data){
                if($data['consignee_district']!=''){
                    $data['consignee_district'] = json_decode($data['consignee_district'],true);
                    if($data['consignee_district']==''){
                        $data['consignee_district'] = array();
                    }
                }else{
                    $data['consignee_district'] = array();
                }
                $root['data'] = $data;
                
            }else{
                $root['data'] = array();
            }
        }else{
            $root['status'] = 10015;
        }
        return $root;
    }

    /**
     * 编辑收货地址
     * $data = array("id"=>$id,"user_id"=>$user_id,"consignee"=>$consignee,"consignee_mobile"=>$consignee_mobile,"consignee_district"=>$consignee_district,"consignee_address"=>$consignee_address,"is_default"=>$is_default);
     * return array("status"=>$status,"data"=>$data);
     */
    public function editaddress($data){

        $id = (int)$data['id'];//收货地址id 0为添加 非0为编辑
        if(!$id){
            $root = $this->addaddress($data);
            return $root;
        }
        $data['user_id'] = (int)$data['user_id'];//所有用户id
        $data['consignee'] = trim($data['consignee']);//收货人姓名
        $data['consignee_mobile'] = trim($data['consignee_mobile']);//收货人手机号
        $data['consignee_district'] = trim($data['consignee_district']);//收货人所在地行政地区信息,json格式
        $data['consignee_address'] = trim($data['consignee_address']);//收货人详细地址
        $data['is_default'] = (int)($data['is_default']);//是否默认 1为默认
        if(mb_strlen($data['consignee']) > 8){
            $root['status'] = 0;
            return $root;
        }
        if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE id=".$data['user_id'])<=0){
            $root['status'] = 10009;
            return $root;
        }
        if(empty($data['consignee'])){
            $root['status'] = 10017;
            return $root;
        }
        if(empty($data['consignee_mobile'])){
            $root['status'] = 10018;
            return $root;
        }
        if(!check_mobile($data['consignee_mobile'])){
            $root['status'] = 10019;
            return $root;
        }
        //$GLOBALS['db']->affected_rows()
        if($GLOBALS['db']->autoExecute(DB_PREFIX."user_address", $data,'UPDATE','id='.$id)){
            if($data['is_default']){
                $GLOBALS['db']->query("update ".DB_PREFIX."user_address set is_default = 0 where is_default = 1 and id <> ".$id." and user_id = ".$data['user_id']);
            }
            $root['status'] = 1;
            if($data['consignee_district']!=''){
                $data['consignee_district'] = json_decode($data['consignee_district'],true);
                if($data['consignee_district']==''){
                    $data['consignee_district'] = array();
                }
            }else{
                $data['consignee_district'] = array();
            }
            $root['data'] = $data;
            
        }else{
            $root['status'] = 10020;
        }
        return $root;
    }

    /**
     * 删除收货地址
     * $data = array("id"=>$id,"user_id"=>$user_id);
     * return array("status"=>$status);
     */
    public function del($data){

        $id = (int)$data['id'];//收货地址id
        $user_id = (int)$data['user_id'];//所有用户id

        $GLOBALS['db']->query("delete from ".DB_PREFIX."user_address where id = ".$id." and user_id = ".$user_id);
        if($GLOBALS['db']->affected_rows()>0){
            $root['status'] = 1;
        }else{
            $root['status'] = 10016;
        }
        return $root;
    }

    /**
     * 设置默认地址
     * $data = array("id"=>$id,"user_id"=>$user_id);
     * return array("status"=>$status);
     */
    public function setdefault($data){

        $id = (int)$data['id'];//收货地址id
        $user_id = (int)$data['user_id'];//所有用户id

        $info = $GLOBALS['db']->query("update ".DB_PREFIX."user_address set is_default = 1 where id = ".$id." and user_id = ".$user_id);
        if($info){
            $GLOBALS['db']->query("update ".DB_PREFIX."user_address set is_default = 0 where is_default = 1 and id <> ".$id." and user_id = ".$user_id);
            $root['status'] = 1;
        }else{
            $root['status'] = 10024;
        }
        return $root;
    }

}


?>