<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class shopCModule extends baseModule
{

    /**
     * 我的小店接口
     * http://doc.fanwe.net/index.php?s=/fanwelive&page_id=552
     * @return [type] [description]
     */
    public function mystore()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $page_size = 20;
        $page      = intval($_REQUEST['page']);
        $page      = $page > 0 ? $page : 1;
        if (OPEN_GOODS == 1) {

            $head_args['pageNo']=$page;
            $head_args['pageRows']=$page_size;
            $res      = third_interface($user_id, 'http://gw1.yimile.cc/V1/Commodity.json?action=UserDistributionCommodityList', $head_args);
            $goods = array();
            $has_next = 0;

            foreach($res['data'] as $key => $value){
                $data = array();
                $data['id'] = intval($value['commodityId']);
                $data['name'] = strim($value['commodityName']);
                $data['imgs'] = array($value['commodityLogo']);;
                $data['price'] = intval($value['salePrice']);
                $data['url'] = go_h5(intval($GLOBALS['user_info']['id']),'http://gw1.yimile.cc/Web/AnchorCentre/CommodityList.aspx');
                $data['description'] = '';
                $data['kd_cost'] = 0;
                $goods[] = $data;
            }

            $podcast['store_url'] = go_h5($user_id, 'http://gw1.yimile.cc/Web/AnchorCentre/CommodityList.aspx');
        }else {

            $podcast_id = intval($_REQUEST['podcast_user_id']);
            $classified_id = $GLOBALS['db']->getOne("SELECT classified_id FROM ".DB_PREFIX."video WHERE live_in=1 and user_id=".$podcast_id);

            $table      = DB_PREFIX . 'user';
            $podcast    = $GLOBALS['db']->getRow("SELECT id FROM $table WHERE id = $podcast_id");
            if (!$podcast) {
                self::returnErr(10009);
            }

            $field    = 'count(1) as count';
            if($classified_id > 0){
                $where = "ug.user_id = $user_id and ug.goods_id=gs.id and gs.cate_id=".$classified_id." and ug.is_effect = 1 and gs.inventory >0";
            }else{
                $where = "ug.user_id = $user_id and ug.goods_id=gs.id and ug.is_effect = 1 and gs.inventory >0";
            }

            $count    = $GLOBALS['db']->getRow("SELECT $field FROM ".DB_PREFIX."user_goods as ug,".DB_PREFIX."goods as gs WHERE $where");

            $has_next = ($count['count'] - ($page_size * $page)) > 0?1:0;

            $goods    = array();
            if ($count['count']) {
                $field = 'gs.id,gs.name,gs.imgs,gs.price,gs.description,gs.kd_cost';
                $limit = ($page - 1) * $page_size . ',' . $page_size;
                $goods = $GLOBALS['db']->getAll("SELECT $field FROM  ".DB_PREFIX."user_goods as ug,".DB_PREFIX."goods as gs WHERE $where LIMIT $limit");
                foreach ($goods as $k => $lv) {
                    if($lv['id'] != 0 || $lv['inventory'] != 0){
                        $goods[$k]['imgs'] = json_decode(get_spec_image($lv['imgs']),true);
                    }else{
                        $goods[$k]['imgs'] = array();
                    }
                }
            }
            $podcast['store_url'] ='';

        }
        if ($goods === false) {
            self::returnErr(10001);
        } else {

            api_ajax_return(array(
                'status' => 1,
                'error'  => '',
                'list'   => $goods,
                'url'    => $podcast['store_url'],
                'page'   => array('page' => $page, 'has_next' => intval($has_next)),
            ));
        }
    }

    /*
     * 主播小店商品
     * */
    public function podcast_mystore(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $page_size = 20;
        $page      = intval($_REQUEST['page']);
        $page      = $page > 0 ? $page : 1;

        $podcast_user_id = intval($_REQUEST['podcast_user_id']);

        if($podcast_user_id == ''){
            $field    = 'count(1) as count';
            $where = "user_id = $user_id and is_effect=1";
            $count    = $GLOBALS['db']->getRow("SELECT $field FROM ".DB_PREFIX."podcast_goods WHERE $where");

            $has_next = ($count['count'] - ($page_size * $page)) > 0?1:0;

            $goods =array();
            if($count['count']){
                $limit = ($page - 1) * $page_size . ',' . $page_size;
                $goods = $GLOBALS['db']->getAll("SELECT * FROM  ".DB_PREFIX."podcast_goods WHERE $where order by id desc LIMIT $limit");
                foreach ($goods as $k => $lv) {
                    if($lv['id'] != 0){
                        $goods[$k]['url'] = htmlspecialchars_decode($lv['url']);
                        $goods[$k]['imgs'] = json_decode(get_spec_image($lv['imgs']),true);
                    }else{
                        $goods[$k]['imgs'] = array();
                    }
                }
            }
        }else{
            $field    = 'count(1) as count';
            $where = "user_id = $podcast_user_id and is_effect=1";
            $count    = $GLOBALS['db']->getRow("SELECT $field FROM ".DB_PREFIX."podcast_goods WHERE $where");

            $has_next = ($count['count'] - ($page_size * $page)) > 0?1:0;

            $goods =array();
            if($count['count']){
                $limit = ($page - 1) * $page_size . ',' . $page_size;
                $goods = $GLOBALS['db']->getAll("SELECT * FROM  ".DB_PREFIX."podcast_goods WHERE $where order by id desc LIMIT $limit");
                foreach ($goods as $k => $lv) {
                    if($lv['id'] != 0){
                        $goods[$k]['url'] = htmlspecialchars_decode($lv['url']);
                        $goods[$k]['imgs'] = json_decode(get_spec_image($lv['imgs']),true);
                    }else{
                        $goods[$k]['imgs'] = array();
                    }
                }
            }
        }

        api_ajax_return(array(
            'status' => 1,
            'error'  => '',
            'list'   => $goods,
            'page'   => array('page' => $page, 'has_next' => intval($has_next)),
        ));

    }

    //监测URL是否有效
    public function curl_status($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,1);
        $output = curl_exec($ch);
        $root = $output ? true : false;
        return $root;
    }

    /**
     * 添加商品
     */
    public function add_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods = array();
        $goods['user_id'] = $user_id; //主播ID
        $goods['name'] = strim($_REQUEST['name']); //商品名称
        $imgs = json_decode($_REQUEST['imgs']);
        $result_imgs=array();
        foreach($imgs as $k=>$v){
            $result_imgs[]=$v;
        }
        $goods['imgs'] = json_encode($result_imgs);//商品图片JSON数据
        $goods['price'] = strim($_REQUEST['price']); //商品价钱

        $url = strim($_REQUEST['url']);
        if (!preg_match("/^(http|https):/",$url)){
            $url = 'http://'.$url;
        }
        $goods['url'] = $url; //购买商品URL
        $goods['description'] = strim($_REQUEST['description']); //商品描述

        if ($goods['name'] == '') {
            $root['status'] = 10038;
            $root['error']  = "名称不能为空";
            api_ajax_return($root);
        } elseif ($goods['imgs'] == '') {
            $root['status'] = 10059;
            $root['error']  = "商品图片不能为空";
            api_ajax_return($root);
        } elseif ($goods['price'] == '') {
            $root['status'] = 10061;
            $root['error']  = "商品价格不能为0";
            api_ajax_return($root);
        } elseif ($goods['url'] == '') {
            $root['status'] = 10060;
            $root['error']  = "商品链接地址不能为空";
            api_ajax_return($root);
        }elseif($this->curl_status($url) == false){
            $root['status'] = 0;
            $root['error']  = "商品链接无效";
            api_ajax_return($root);
        }elseif($goods['description'] == ''){
            $goods['description'] = '主播很懒，没有写商品描述。'; //商品描述
        }elseif(mb_strlen($goods['description']) > 50){
            $root['status'] = 0;
            $root['error']  = "商品描述太长.";
            api_ajax_return($root);
        }

        $rs = FanweServiceCall("shop", "add_goods", $goods);

        $root['status'] = intval($rs['status']);
        if ($root['status'] == 1) {
            $root['error'] = "添加商品成功";
            $goods = $rs['info'];
            if($goods['imgs'] != ''){
                $goods['imgs'] = json_decode($goods['imgs']);
                if ($goods['imgs'] == "") {
                    $goods['imgs'] = array();
                } else {
                    foreach ($goods['imgs'] as $k => $v) {
                        $goods['imgs'][$k]=get_spec_image($v);
                    }
                }
            }else {
                $goods['imgs'] = array();
            }
            $root['info'] = $goods;

        } elseif ($root['status'] == 10057) {
            $root['error'] = "添加商品失败";
        }

        api_ajax_return($root);
    }

    /**
     * 编辑商品
     */
    public function edit_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods            = array();
        $goods['id']      = intval($_REQUEST['id']); //商品ID
        $goods['user_id'] = $user_id; //主播ID
        $goods['name']    = strim($_REQUEST['name']); //商品名称
        $imgs = json_decode($_REQUEST['imgs']);
        $result_imgs=array();
        foreach($imgs as $k=>$v){
            $result_imgs[]=$v;
        }
        $goods['imgs'] = json_encode($result_imgs);//商品图片JSON数据
        $goods['price']       = strim($_REQUEST['price']); //商品价钱
        $url = strim($_REQUEST['url']);
        if (!preg_match("/^(http|https):/",$url)){
            $url = 'http://'.$url;
        }
        $goods['url'] = $url; //购买商品URL
        $goods['description'] = strim($_REQUEST['description']); //商品描述

        if ($goods['name'] == '') {
            $root['status'] = 10038;
            $root['error']  = "名称不能为空";
            api_ajax_return($root);
        } elseif ($goods['imgs'] == '') {
            $root['status'] = 10059;
            $root['error']  = "商品图片不能为空";
            api_ajax_return($root);
        } elseif ($goods['price'] == '') {
            $root['status'] = 10061;
            $root['error']  = "商品价格不能为0";
            api_ajax_return($root);
        } elseif ($goods['url'] == '') {
            $root['status'] = 10060;
            $root['error']  = "商品链接地址不能为空";
            api_ajax_return($root);
        }elseif($this->curl_status($url) === false){
            $root['status'] = 0;
            $root['error']  = "商品链接无效";
            api_ajax_return($root);
        }elseif($goods['description'] == ''){
            $goods['description'] = '主播很懒，没有写商品描述。'; //商品描述
        }elseif(mb_strlen($goods['description']) > 50){
            $root['status'] = 0;
            $root['error']  = "商品描述太长.";
            api_ajax_return($root);
        }

        $rs = FanweServiceCall("shop", "edit_goods", $goods);

        $root['status'] = intval($rs['status']);
        if ($root['status'] == 1) {
            $root['error'] = "修改商品成功";
            $goods         = $rs['info'];

            if ($goods['imgs'] != '') {
                $goods['imgs'] = json_decode($goods['imgs']);
                if ($goods['imgs'] == "") {
                    $goods['imgs'] = array();
                } else {
                    foreach ($goods['imgs'] as $k => $v) {
                        $goods['imgs'][$k] = get_spec_image($v);
                    }
                }
            } else {
                $goods['imgs'] = array();
            }

            $root['info'] = $goods;
        } else if ($root['status'] == 10008) {
            $root['error'] = "商品不存在";
        } else if ($root['status'] == 10057) {
            $root['error'] = "添加商品失败";
        }
        api_ajax_return($root);
    }

    /**
     * 删除商品
     */
    public function del_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods = array();
        $goods['user_id'] = $user_id; //主播ID
        $goods['id'] = intval($_REQUEST['id']); //商品ID

        $rs = FanweServiceCall("shop", "del_goods", $goods);

        $root['status'] = intval($rs['status']);
        if (intval($root['status']) == 1) {
            $root['error'] = "删除成功";

        } elseif ($root['status'] == 10008) {
            $root['error'] = "商品不存在";

        } else {
            $root['status'] = 10058;
            $root['error']  = "删除商品失败";
        }

        api_ajax_return($root);
    }

    /**
     * 主播小店商品推送
     */
    public function push_podcast_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods_id   = intval($_REQUEST['goods_id']);

        $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $user_id . " and live_in =1");
        //tim推送
        $ext = array();
        $ext['type']    = 31;
        $ext['room_id'] = intval($video_info['id']);
        $ext['post_id'] = $user_id;

        $goods_info = $GLOBALS['db']->getRow("select id as goods_id,name,imgs,price,url,description from ".DB_PREFIX ."podcast_goods where id=".$goods_id ." and is_effect=1");
        if (!$goods_info) {
            $root['status'] = 10008;
            $root['error']  = "商品不存在";
            api_ajax_return($root);
        }

        $ext['desc']    = "主播推送了商品“" . $goods_info['name'] . "”";
        if ($goods_info['imgs'] != '') {
            $goods_info['imgs'] = json_decode($goods_info['imgs'],1)[0];
        }else {
            $goods_info['imgs'] = array();
        }
        $goods_info["type"] =1;//1=小店推送
        $goods_info['url'] = htmlspecialchars_decode($goods_info['url']);
        $ext['goods'] = $goods_info;

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis                = new UserRedisService();
        $fields                    = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
        $ext['user']               = $user_redis->getRow_db($user_id, $fields);
        $ext['user']['user_id']    = $user_id;
        $ext['user']['head_image'] = get_abs_img_root($ext['user']['head_image']);

        #构造高级接口所需参数
        $tim_data               = array();
        $tim_data['ext']        = $ext;
        $tim_data['podcast_id'] = strim($user_id);
        $tim_data['group_id']   = strim($video_info['group_id']);
        get_tim_api($tim_data);

        $root['status'] = 1;
        $root['error']  = "推送成功";
        api_ajax_return($root);
    }

    /**
     * 平台商品推送
     */
    public function push_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods_id   = intval($_REQUEST['goods_id']);

        $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $user_id . " and live_in =1");
        //tim推送
        $ext = array();
        $ext['type']    = 31;
        $ext['room_id'] = intval($video_info['id']);
        $ext['post_id'] = $user_id;

        if(OPEN_GOODS == 1){
            $head_args['commodityId']=$goods_id;
            $ret=third_interface($user_id,'http://gw1.yimile.cc/V1/Commodity.json?action=SetAuctionCommodityDetail',$head_args);
            $goods_info = array();
            if($ret['code'] == 0){
                $ext['desc']    = "主播推送了商品“" . $ret['data']['commodityName'] . "”";
                $goods_info['name'] = $ret['data']['commodityName'];
                $goods_info['imgs'] = $ret['data']['commodityLogo'];
                $goods_info['price'] = $ret['data']['salePrice'];
                $goods_info['url'] = '';
                $goods_info['description'] = '';
                $goods_info['kd_cost'] = 0;
            }
            $ext['goods'] = $goods_info;

        }else{
            $goods_info = $GLOBALS['db']->getRow("select id as goods_id,name,imgs,price,description,kd_cost from " . DB_PREFIX . "goods where id=" . $goods_id . " and is_effect=1");
            if (!$goods_info) {
                $root['status'] = 10008;
                $root['error']  = "商品不存在";
                api_ajax_return($root);
            }
            $goods_info['url'] = SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=shop&act=goods_details&goods_id='.$goods_info['goods_id'];
            $ext['desc']    = "主播推送了商品“" . $goods_info['name'] . "”";
            if ($goods_info['imgs'] != '') {
                $goods_info['imgs'] = json_decode($goods_info['imgs'],1)[0];
            } else {
                $goods_info['imgs'] = array();
            }
            $ext['goods'] = $goods_info;
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis                = new UserRedisService();
        $fields                    = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
        $ext['user']               = $user_redis->getRow_db($user_id, $fields);
        $ext['user']['user_id']    = $user_id;
        $ext['user']['head_image'] = get_abs_img_root($ext['user']['head_image']);

        #构造高级接口所需参数
        $tim_data               = array();
        $tim_data['ext']        = $ext;
        $tim_data['podcast_id'] = strim($user_id);
        $tim_data['group_id']   = strim($video_info['group_id']);
        get_tim_api($tim_data);

        $root['status'] = 1;
        $root['error']  = "推送成功";
        api_ajax_return($root);
    }

    /**
     * 编辑小店URL
     */
    public function edit_store_url()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $user              = array();
        $user['id']        = $user_id; //主播ID
        $user['store_url'] = strim($_REQUEST['store_url']); //商品详情URL地址

        if ($user['store_url'] == '') {
            $root['status'] = 10060;
            $root['error']  = "商品详情不能为空";
            api_ajax_return($root);
        }

        $rs = FanweServiceCall("shop", "edit_store_url", $user);

        $root['status'] = intval($rs['status']);
        if ($root['status'] == 1) {
            $root['error'] = "修改成功";
        } elseif ($root['status'] == 10009) {
            $root['status'] = 10009;
            $root['error']  = "主播不存在";
        }
        api_ajax_return($root);
    }

    /**
     * 错误返回
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    protected static function returnErr($code)
    {
        $error = array(
            10001 => '查询的业务数据不存在',
            10002 => '操作的业务动作失败',
            10003 => '分润失败，需做日志处理或重新发起请求',
            10004 => '订单支付失败',
            10005 => '接口不存在',
            10006 => '接口下的方法不存在',
            10007 => '服务端未登陆',
            10008 => '商品不存在',
            10009 => '主播不存在',
            10010 => '竞拍商品不存在',
            10011 => '竞拍人不存在',
            10012 => '下单单失败',
            10013 => '提交保证金失败',
            10014 => '竞拍失败',
            10015 => '添加收货地址失败',
            10016 => '删除收货地址失败',
            10017 => '姓名为空',
            10018 => '手机号码为空',
            10019 => '手机号码格式错误',
            10020 => '编辑收货地址失败',
            10021 => '消息类型为空',
            10022 => '消息推送失败',
            10023 => '消息删除失败',
            10024 => '设置默认收货地址失败',
            10025 => '创建竞拍失败',
            10026 => '编辑竞拍失败',
            10027 => '关闭竞拍失败',
            10028 => '确认完成虚拟竞拍失败',
            10029 => '确认竞拍退款失败',
            10030 => '申诉竞拍失败',
            10031 => '确认约会失败',
            10032 => '撤销失败',
            10033 => '推送会员为空',
            10034 => '区域数据错误',
            10035 => '收货地址为空',
            10036 => '竞拍已结束',
            10037 => '订单号错误',
            10038 => '名称不能为空',
            10039 => '描述不能为空',
            10040 => '时间不能为空',
            10041 => '地点不能为空',
            10042 => '联系人不能为空',
            10043 => '请输入正确的联系电话',
            10044 => '竞拍价格不能为0',
            10045 => '每次加价幅度不能为',
            10046 => '竞拍时长不能为0',
            10047 => '每次竞拍延时不能为0',
            10048 => '最大延时不能为0',
            10049 => '存在未完成的竞拍，创建竞拍失败',
            10050 => '已提交过保证金',
            10051 => '禁止发起竞拍，创建竞拍失败',
            10052 => '未提交保证金',
            10053 => '出价非最高价',
            10054 => '订单已付款',
            10055 => '直播间已关闭，无法创建竞拍',
            10056 => '约会时间早于竞拍完成时间，请重新选择约会时间',
            10057 => '添加商品失败',
            10058 => '删除商品失败',
            10059 => '商品图片不能为空',
            10060 => '商品详情不能为空',
            10061 => '商品价格不能为0',
        );
        api_ajax_return(array('status' => $code, 'error' => isset($error[$code]) ? $error[$code] : '未知错误'));
    }

    //实物竞拍商品列表
    public function pai_goods(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $page_size = 20;
        $page      = intval($_REQUEST['page']);
        $page      = $page > 0 ? $page : 1;
        $limit = ($page - 1) * $page_size . ',' . $page_size;

        $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.imgs_details,gs.pai_diamonds,gs.description,gs.kd_cost,gs.score,gs.inventory';
        $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."user_goods as ug,".DB_PREFIX."goods as gs WHERE gs.id=ug.goods_id and gs.inventory>0 and ug.is_effect=1 and ug.user_id=".$user_id." LIMIT $limit");

        $where = "ug.user_id = $user_id and ug.goods_id=gs.id and ug.is_effect = 1 and gs.inventory >0";
        $count    = $GLOBALS['db']->getRow("SELECT count(1) as count FROM ".DB_PREFIX."user_goods as ug,".DB_PREFIX."goods as gs WHERE $where");
        $has_next = ($count['count'] - ($page_size * $page)) > 0?1:0;

        if(!$user_goods_info){
            $root['status']=0;
            $root['error']="主播暂无商品";
            api_ajax_return($root);
        }
        $root['status'] = 1;
        $root['error'] = '';
        $root['url'] = '';
        $list=array();
        foreach($user_goods_info as $key => $lv){
            if($lv['id'] != 0 || $lv['inventory'] != 0){
                $data = array();
                $data['id'] = $lv['goods_id'];
                $data['name'] = $lv['name'];
                $data['imgs'] = json_decode(get_spec_image($lv['imgs']),true);
                $data['url'] = '';
                $data['price'] = $lv['pai_diamonds'];
                $data['description'] = $lv['description'];
                $data['kd_cost'] = $lv['kd_cost'];
                $list[] = $data;
            }
        }
        $root['list'] = $list;
        $page = array();
        $page['page'] = 1;
        $page['has_next'] = $has_next;
        $root['page'] = $page;

        api_ajax_return($root);
    }

    /*
     * 分销商品列表
     * */
    public function distribution_goods_list(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $content = strim($_REQUEST['content']);//搜索字段
        $cate_id = intval($_REQUEST['cate_id']);//分类ID
        $options = intval($_REQUEST['options']);//1按销售量，2按商品价格最多排，3按商品价格最少排
        $page = intval($_REQUEST['page']);
        $page_size = 20;
        if ($page == 0) {
            $page = 1;
        }
        $limit = (($page - 1) * $page_size).",".$page_size;

        $m_config =  load_auto_cache("m_config");
        $user_info = $GLOBALS['db']->getRow("SELECT is_authentication FROM ".DB_PREFIX."user WHERE id=".$user_id);
        if($m_config['must_authentication']==0 || ($m_config['must_authentication']==1&&$user_info['is_authentication'] == 2)){

            $classified_id = $GLOBALS['db']->getOne("SELECT classified_id FROM ".DB_PREFIX."video WHERE live_in=1 and user_id=".$user_id);
            $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."goods WHERE is_effect=1");
            $pages['page'] = $page;
            $total = ceil($rs_count/$page_size);
            $pages['count'] = $total;

            if($classified_id > 0){
                if($content == ''){
                    if($cate_id != 0){
                        if($options == 1){
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and cate_id=".$cate_id." and is_effect=1 order by sales desc limit ".$limit);
                        }elseif($options == 2){
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and cate_id=".$cate_id." and is_effect=1 order by price desc limit ".$limit);
                        }elseif($options == 3){
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and cate_id=".$cate_id." and is_effect=1 order by price asc limit ".$limit);
                        }else{
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and cate_id=".$cate_id." and is_effect=1 limit ".$limit);
                        }
                    }elseif($options == 1){
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and is_effect=1 order by sales desc limit ".$limit);
                    }elseif($options == 2){
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and is_effect=1 order by price desc limit ".$limit);
                    }elseif($options == 3){
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and is_effect=1 order by price asc limit ".$limit);
                    }else{
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$classified_id." and is_effect=1 limit ".$limit);
                    }
                }else{
                    $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE name LIKE '%$content%' and inventory>0 and cate_id=".$classified_id." and is_effect=1 limit ".$limit);
                }
            }else{
                if($content == ''){
                    if($cate_id != 0){
                        if($options == 1){
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$cate_id." and is_effect=1 order by sales desc limit ".$limit);
                        }elseif($options == 2){
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$cate_id." and is_effect=1 order by price desc limit ".$limit);
                        }elseif($options == 3){
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$cate_id." and is_effect=1 order by price asc limit ".$limit);
                        }else{
                            $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and cate_id=".$cate_id." and is_effect=1 limit ".$limit);
                        }
                    }elseif($options == 1){
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and is_effect=1 order by sales desc limit ".$limit);
                    }elseif($options == 2){
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and is_effect=1 order by price desc limit ".$limit);
                    }elseif($options == 3){
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and is_effect=1 order by price asc limit ".$limit);
                    }else{
                        $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE inventory>0 and is_effect=1 limit ".$limit);
                    }
                }else{
                    $goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods WHERE name LIKE '%$content%' and inventory>0 and is_effect=1 limit ".$limit);
                }
            }


            $m_config =  load_auto_cache("m_config");//手机端配置
            $commission = $m_config['platform_on_commission'];
            $ticket_name = $m_config['ticket_name'];

            $user_goods_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."user_goods WHERE user_id=".$user_id." and is_effect=1");
            if($goods_info){
                foreach($goods_info as $key => $value){
                    $goods_name = mb_substr($value['name'],0,10);
                    $goods_info[$key]['name'] = mb_strlen($goods_name) < 10?$goods_name:$goods_name."......";
                    if(floatval($value['podcast_ticket']) == 0){
                        $goods_info[$key]['commission'] = round($value['price']*($commission/100),2).$ticket_name;
                    }else{
                        $goods_info[$key]['commission'] = floatval($value['podcast_ticket']).$ticket_name;
                    }
                    $goods_info[$key]['imgs'] = get_spec_image(json_decode($value['imgs'],true)[0]);
                    $goods_info[$key]['has']=0;
                    foreach($user_goods_info as $value1){
                        if($value['id'] == $value1['goods_id']){
                            $goods_info[$key]['has'] = 1;
                            break;
                        }
                    }
                }
            }else{
                $goods_info = array();
            }
        }

        $goods_cate_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods_cate WHERE is_effect=1");

        $root['goods_cate'] = $goods_cate_info;
        $root['content'] = $content;
        $root['cate_id'] = $cate_id;
        $root['goods'] = $goods_info;
        $root['options'] = $options;
        $root['page'] = $pages;
        $root['page_title'] ='分销商品';

        api_ajax_return($root);
    }


    /*
     * 添加分销商品
     * */
    public function add_distribution_goods(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }
        $goods_id = intval($_REQUEST['goods_id']);

        $user_goods_info = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".DB_PREFIX."user_goods WHERE user_id=".$user_id." and is_effect=1 and goods_id=".$goods_id);
        if($user_goods_info){
            $root['status']=0;
            $root['error']="已添加此商品";
            api_ajax_return($root);
        }

        $fields = 'id as goods_id,name,imgs,imgs_details,price,pai_diamonds,description,kd_cost,score,inventory,sales,number,bz_diamonds,jj_diamonds,pai_time';
        $goods_info = $GLOBALS['db']->getRow("SELECT $fields FROM ".DB_PREFIX."goods WHERE is_effect=1 and inventory>0 and id=".$goods_id);
        if($goods_info){

            $user_goods_info = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".DB_PREFIX."user_goods WHERE user_id=".$user_id." and is_effect=0 and goods_id=".$goods_id);
            $goods_info['is_effect'] = 1;
            if($user_goods_info){
                unset($goods_info['goods_id']);
                $ret = $GLOBALS['db']->autoExecute(DB_PREFIX."user_goods", $goods_info, $mode = 'UPDATE', "user_id=".$user_id." and goods_id=".$goods_id."");
            }else{
                $goods_info['user_id'] = $user_id;
                $ret = $GLOBALS['db']->autoExecute(DB_PREFIX."user_goods",$goods_info,"INSERT");
            }

            if($ret){
                $sql = "update ".DB_PREFIX."goods set number=number+1 where id=".$goods_id."";
                $GLOBALS['db']->query($sql);//售卖人数增加
                $root['status']= 1;
                $root['error']= '添加成功';
            }else{
                $root['status']= 0;
                $root['error']= '添加失败';
            }
        }else{
            $root['status']= 0;
            $root['error']= '无此商品';
        }

        api_ajax_return($root);

    }

    /*
     * 商品详情页面
     * */
    public function goods_details(){

        $root['page_title'] ='商品详情';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }
        $goods_id = intval($_REQUEST['goods_id']);

        $fields = 'id as goods_id,name,imgs,imgs_details,price,pai_diamonds,description,kd_cost,score,inventory,sales,number,bz_diamonds,jj_diamonds,pai_time,podcast_ticket,tags_id';
        $goods_info = $GLOBALS['db']->getRow("SELECT $fields FROM ".DB_PREFIX."goods WHERE is_effect=1 and id=".$goods_id);

        $user_goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_goods WHERE user_id=".$user_id." and goods_id=".$goods_id." and is_effect=1");

        if($goods_info){
            $m_config =  load_auto_cache("m_config");//手机端配置
            $commission = $m_config['platform_on_commission'];
            $ticket_name = $m_config['ticket_name'];
            if(floatval($goods_info['podcast_ticket']) == 0){
                $goods_info['commission'] = round($goods_info['price']*($commission/100),2).$ticket_name;
            }else{
                $goods_info['commission'] = floatval($goods_info['podcast_ticket']).$ticket_name;
            }
            $goods_info['imgs'] = get_spec_image(json_decode($goods_info['imgs'],true)[0]);
            $goods_info['imgs_details'] = get_spec_image(json_decode($goods_info['imgs_details'],true));

            if($goods_info['goods_id'] == $user_goods_info['goods_id']){
                $goods_info['has'] = 1;
            }else{
                $goods_info['has']=0;
            }

            $goods_info['tags_id'] = json_decode($goods_info['tags_id']);
            foreach($goods_info['tags_id'] as $key => $value){
                $goods_tags[] = $GLOBALS['db']->getRow("select image,name from ".DB_PREFIX."goods_tags where id=".$value);
            }
            foreach($goods_tags as $key => $vuale){
                $goods_tags[$key]['image'] = get_spec_image($vuale['image']);
            }

            $root['status']= 1;
            $root['goods'] = $goods_info;
            $root['goods_tags'] = $goods_tags;
        }else{
            $root['status']= 0;
            $root['error']= '无此商品';
            $root['goods'] = array();
            $root['goods_tags'] = array();
        }

        api_ajax_return($root);
    }

    /*
     * 主播商品管理列表页面
     * */
    public function podcasr_goods_management(){

        $root['page_title'] ='商品管理';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $content = strim($_REQUEST['content']);//搜索字段
        $state = intval($_REQUEST['state']);//0下架商品  1出售中商品
        $page = intval($_REQUEST['page']);
        $page_size = 20;
        if ($page == 0) {
            $page = 1;
        }
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $m_config =  load_auto_cache("m_config");
        $user_info = $GLOBALS['db']->getRow("SELECT is_authentication FROM ".DB_PREFIX."user WHERE id=".$user_id);
        if($m_config['must_authentication']==0 || ($m_config['must_authentication']==1&&$user_info['is_authentication'] == 2)) {

            $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.imgs_details,gs.price,gs.pai_diamonds,gs.description,gs.kd_cost,gs.score,gs.inventory,gs.sales,gs.number,gs.podcast_ticket';
            if ($content == '') {
                if ($state == 0) {
                    $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM " . DB_PREFIX . "goods as gs," . DB_PREFIX . "user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=0 and gs.is_effect=1 and ug.user_id=" . $user_id . " limit " . $limit);
                    $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "user_goods WHERE is_effect=0 and user_id=" . $user_id);
                } else {
                    $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM " . DB_PREFIX . "goods as gs," . DB_PREFIX . "user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=1 and gs.is_effect=1 and gs.inventory>0 and ug.user_id=" . $user_id . " limit " . $limit);
                    $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "user_goods WHERE inventory>0 and is_effect=1 and user_id=" . $user_id);
                }
            } else {
                if ($state == 0) {
                    $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM " . DB_PREFIX . "goods as gs," . DB_PREFIX . "user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=0 and gs.is_effect=1 and gs.inventory>0 and ug.user_id=" . $user_id . " limit " . $limit);
                    $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "goods as gs," . DB_PREFIX . "user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=0 and gs.inventory>0 and ug.user_id=" . $user_id);
                }else{
                    $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM " . DB_PREFIX . "goods as gs," . DB_PREFIX . "user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=1 and gs.is_effect=1 and gs.inventory>0 and ug.user_id=" . $user_id . " limit " . $limit);
                    $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "goods as gs," . DB_PREFIX . "user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=1 and gs.inventory>0 and ug.user_id=" . $user_id);
                }
            }
            $total = ceil($rs_count / $page_size);
            $pages['count'] = $total;
            $pages['page'] = $page;
        }

        if($user_goods_info){

            $m_config =  load_auto_cache("m_config");//手机端配置
            $commission = $m_config['platform_on_commission'];
            $ticket_name = $m_config['ticket_name'];

            foreach($user_goods_info as $key => $value){
                if(floatval($value['podcast_ticket']) == 0){
                    $user_goods_info[$key]['commission'] = round($value['price']*($commission/100),2).$ticket_name;
                }else{
                    $user_goods_info[$key]['commission'] = floatval($value['podcast_ticket']).$ticket_name;
                }
                $user_goods_info[$key]['imgs'] = get_spec_image(json_decode($value['imgs'],true)[0]);
            }

            $root['status']= 1;
            $root['content'] = $content;
            $root['goods'] = $user_goods_info;
            $root['state'] = $state;
            $root['page'] = $pages;

        }else{
            $root['status']= 0;
            $root['content'] = $content;
            $root['error']= '暂无分销商品';
            $root['goods'] = array();
            $root['state'] = $state;
        }

        api_ajax_return($root);
    }

    /*
     * 观众端购物选商品列表页面
     * */
    public function shop_goods_list(){

        $root['page_title'] ='选购商品';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $content = strim($_REQUEST['content']);//搜索字段
        $podcast_id = intval($_REQUEST['podcast_id']);//主播ID
        $page = intval($_REQUEST['page']);
        $page_size = 20;
        if ($page == 0) {
            $page = 1;
        }
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_goods WHERE inventory>0 and is_effect=1 and user_id=".$podcast_id);
        $total = ceil($rs_count/$page_size);
        $pages['count'] = $total;
        $pages['page'] = $page;

        $classified_id = $GLOBALS['db']->getOne("SELECT classified_id FROM ".DB_PREFIX."video WHERE live_in=1 and user_id=".$podcast_id);
        $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.imgs_details,gs.price,gs.pai_diamonds,gs.description,gs.kd_cost,gs.score,gs.inventory,gs.sales,gs.number';
        if($classified_id > 0){
            if($content == ''){
                $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and ug.is_effect=1 and gs.is_effect=1 and gs.inventory>0 and gs.cate_id=".$classified_id." and ug.user_id=".$podcast_id." limit ".$limit);
            }else{
                $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=1 and gs.is_effect=1 and gs.inventory>0 and gs.cate_id=".$classified_id." and ug.user_id=".$podcast_id." limit ".$limit);
            }
        }else{
            if($content == ''){
                $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and ug.is_effect=1 and gs.is_effect=1 and gs.inventory>0 and ug.user_id=".$podcast_id." limit ".$limit);
            }else{
                $user_goods_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and gs.name LIKE '%$content%' and ug.is_effect=1 and gs.is_effect=1 and gs.inventory>0 and ug.user_id=".$podcast_id." limit ".$limit);
            }
        }


        if($user_goods_info){

            foreach($user_goods_info as $key => $value){
                $user_goods_info[$key]['imgs'] = json_decode($value['imgs'],true)[0];
            }

            $root['status']= 1;
            $root['content'] = $content;
            $root['goods'] = $user_goods_info;
            $root['podcast_id'] = $podcast_id;
            $root['page'] = $pages;

        }else{
            $root['status']= 0;
            $root['content'] = $content;
            $root['error']= '暂无分销商品';
            $root['goods'] = array();
            $root['podcast_id'] = $podcast_id;
        }

        api_ajax_return($root);
    }

    /*
     * 观众端购物商品详情页面
     * */
    public function shop_goods_details(){

        $root['page_title'] ='选购商品详情';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $podcast_id = intval($_REQUEST['podcast_id']);//主播ID
        $goods_id = intval($_REQUEST['goods_id']);

        $fields = 'gs.id as goods_id,gs.name,gs.imgs,gs.imgs_details,gs.price,gs.pai_diamonds,gs.description,gs.kd_cost,gs.score,gs.inventory,gs.sales,gs.number,gs.tags_id';
        $user_goods_info = $GLOBALS['db']->getRow("SELECT $fields FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and ug.is_effect=1 and ug.goods_id=".$goods_id." and ug.user_id=".$podcast_id);// and gs.inventory>0

        if($user_goods_info){

            $user_goods_info['imgs'] = json_decode($user_goods_info['imgs'],true)[0];
            $user_goods_info['imgs_details'] = json_decode($user_goods_info['imgs_details'],true);

            $user_goods_info['tags_id'] = json_decode($user_goods_info['tags_id']);
            foreach($user_goods_info['tags_id'] as $key => $value){
                $goods_tags[] = $GLOBALS['db']->getRow("select image,name from ".DB_PREFIX."goods_tags where id=".$value);
            }
            foreach($goods_tags as $key => $vuale){
                $goods_tags[$key]['image'] = get_spec_image($vuale['image']);
            }

            $root['goods_tags'] = $goods_tags;
            $root['status']= 1;
            $root['goods'] = $user_goods_info;
            $root['podcast_id'] = $podcast_id;//主播ID

        }else{
            $root['status']= 0;
            $root['error']= '暂无分销商品';
            $root['goods'] = array();
            $root['goods_tags'] = array();
        }

        api_ajax_return($root);
    }

    /*
     * 查看物流信息
     * */
    public function see_boring(){
        $root['page_title'] ='物流信息';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $time = NOW_TIME;
        $order_sn = intval($_REQUEST['order_sn']);

        $courier = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."courier WHERE order_sn=".$order_sn);

        if(!$courier){
            $root['status'] = 0;
            $root['error'] = '暂无物流信息';
            api_ajax_return($root);
        }

        if($courier['view_time']+24*3600 < $time ){

            fanwe_require(APP_ROOT_PATH . 'mapi/shop/Express.class.php');
            $express = new Express();
            $result  = $express -> getorder($courier['courier_number']);

            $data = $result['data'];
            foreach($data as $k => $v){
                unset($data[$k]['ftime'],$data[$k]['location']);
                $data[$k] = $v['time'].$v['context'];
            }

            $data = json_encode($data,true);
            $sql = "update ".DB_PREFIX."courier set courier_details ='".addslashes($data)."',view_time =".$time." where order_sn=".$order_sn;
            $GLOBALS['db']->query($sql);

            $root['data'] = json_decode($data,true);
            $root['courier_number'] = strim($courier['courier_number']);
            $root['courier_offic'] = strim($courier['courier_offic']);
        }else{
            $root['data'] = json_decode($courier['courier_details'],true);
            $root['courier_number'] = strim($courier['courier_number']);
            $root['courier_offic'] = strim($courier['courier_offic']);
        }

        api_ajax_return($root);
    }

    /*
     * 购物个人中心我的订单列表页面
     * */
    public function shop_order(){
        $root['page_title'] ='我的订单';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $state = intval($_REQUEST['state']);// 0全部  1待付款  2待发货  3待收货
        $page = intval($_REQUEST['page']);
        $page_size = 20;
        if ($page == 0) {
            $page = 1;
        }
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."goods_order WHERE order_type='shop' and viewer_id=".$user_id);
        $pages['page'] = $page;
        $total = ceil($rs_count/$page_size);
        $pages['count'] = $total;

        $fields = "us.nick_name,gs.name,gs.imgs,gs.price,go.id as order_id,go.order_sn,go.number,go.total_diamonds,go.podcast_ticket,go.order_status,go.podcast_id,go.is_p,go.create_time";
        if($state == 1){
            $goods_order_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.goods_id=gs.id and ((go.pid = 0 && go.order_status=1) OR go.is_p=1 ) and go.viewer_id=".$user_id." order by go.create_date desc limit ".$limit );
            foreach($goods_order_info as $key => $value){
                $goods_order_info[$key]['imgs'] = json_decode(get_spec_image($value['imgs']),true)[0];
                $goods_order_info[$key]['expire_time'] = $value['create_time']+MAX_PAI_PAY_TIME-NOW_TIME;
                if($value['is_p'] == 1 && $value['order_status'] == 1){
                    $goods_info = $GLOBALS['db']->getAll("SELECT us.nick_name,gs.name,gs.price,gs.imgs,go.number FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.goods_id=gs.id and go.pid=".$value['order_id']." and go.viewer_id=".$user_id);
                    foreach($goods_info as $k => $v){
                        $goods_info[$k]['imgs'] = json_decode(get_spec_image($v['imgs']),true)[0];
                    }
                    foreach($goods_info as $k => $v){
                        unset($v['nick_name']);
                        $goods_info[$goods_info[$k]['nick_name']][] =$v;
                        unset($goods_info[$k]);
                    }
                    $goods_order_info[$key]['goods_info'] = $goods_info;
                }
                if($value['is_p'] == 1 && $value['order_status'] > 1){
                    unset($goods_order_info[$key]);
                }
            }
        }elseif($state == 2){
            $goods_order_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.order_status=2 and go.goods_id=gs.id and is_p=0 and go.viewer_id=".$user_id." order by go.create_date desc limit ".$limit );
            foreach($goods_order_info as $k => $v){
                $goods_order_info[$k]['imgs'] = json_decode(get_spec_image($v['imgs']),true)[0];
            }

        }elseif($state == 3){
            $goods_order_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.order_status=3 and go.goods_id=gs.id and is_p=0 and go.viewer_id=".$user_id." order by go.create_date desc limit ".$limit );
            foreach($goods_order_info as $k => $v){
                $goods_order_info[$k]['imgs'] = json_decode(get_spec_image($v['imgs']),true)[0];
            }

        }else{
            $goods_order_info = $GLOBALS['db']->getAll("SELECT $fields FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.goods_id=gs.id and ((go.pid = 0 || go.order_status>1) OR go.is_p=1 ) and go.viewer_id=".$user_id." order by go.create_date desc limit ".$limit );
            foreach($goods_order_info as $key => $value){
                $goods_order_info[$key]['imgs'] = json_decode(get_spec_image($value['imgs']),true)[0];
                $goods_order_info[$key]['expire_time'] = $value['create_time']+MAX_PAI_PAY_TIME-NOW_TIME;
                if($value['is_p'] == 1 && $value['order_status'] == 1){
                    $goods_info = $GLOBALS['db']->getAll("SELECT us.nick_name,gs.name,gs.price,gs.imgs,go.number FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."goods as gs,".DB_PREFIX."user as us where go.order_type='shop' and go.podcast_id=us.id and go.goods_id=gs.id and go.pid=".$value['order_id']." and go.viewer_id=".$user_id);
                    foreach($goods_info as $k => $v){
                        $goods_info[$k]['imgs'] = json_decode(get_spec_image($v['imgs']),true)[0];
                    }
                    foreach($goods_info as $k => $v){
                        unset($v['nick_name']);
                        $goods_info[$goods_info[$k]['nick_name']][] =$v;
                        unset($goods_info[$k]);
                    }
                    $goods_order_info[$key]['goods_info'] = $goods_info;
                }
                if($value['is_p'] == 1 && $value['order_status'] > 1){
                    unset($goods_order_info[$key]);
                }
            }
        }
        if($goods_order_info){
            $root['goods'] = $goods_order_info;
        }else{
            $root['goods'] = array();
        }

        $root['state'] = $state;
        $root['page'] = $pages;

        api_ajax_return($root);
    }

    /*
     * 购物个人中心我的订单列表页面删除订单
     * */
    public function shop_order_del(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $order_id = intval($_REQUEST['order_id']);
        $order = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE id=".$order_id);
        if($order['is_p'] == 1){
            if($order["order_status"]==1||$order["order_status"]==6){
                $is_p_info = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."goods_order WHERE pid=".$order_id);
                foreach($is_p_info as $key => $value){
                    $sql = "UPDATE ".DB_PREFIX."goods SET inventory=inventory+".intval($value['number'])." WHERE id=".intval($value['goods_id']);
                    $GLOBALS['db']->query($sql);//增加库存
                }
                $ret = $GLOBALS['db']->query("delete from " . DB_PREFIX . "goods_order where viewer_id = " . $user_id . ' and id=' . $order_id);
                $ret1 = $GLOBALS['db']->query("delete from " . DB_PREFIX . "goods_order where viewer_id = " . $user_id . ' and pid=' . $order_id);
                if ($ret || $ret1) {
                    $root['status'] = 1;
                    $root['error'] = '订单删除成功！';
                }else{
                    $root['status'] = 0;
                    $root['error'] = '订单删除失败！';
                }
            }
        }else{
            if($order["order_status"]==1||$order["order_status"]==6){

                $ret = $GLOBALS['db']->query("delete from " . DB_PREFIX . "goods_order where viewer_id = " . $user_id . ' and id=' . $order_id);
                if ($ret) {

                    $sql = "UPDATE ".DB_PREFIX."goods SET inventory=inventory+".intval($order['number'])." WHERE id=".intval($order['goods_id']);
                    $GLOBALS['db']->query($sql);//增加库存

                    $root['status'] = 1;
                    $root['error'] = '订单删除成功！';
                }else{
                    $root['status'] = 0;
                    $root['error'] = '订单删除失败！';
                }
            }else{
                $root['status'] = 0;
                $root['error'] = '订单状态不允许删除！';
            }
        }

        ajax_return($root);

    }

    /*
     * 新增收货地址页面
     * */
    public function new_address(){

        $root['page_title'] ='收货地址';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $address_id = intval($_REQUEST['address_id']);

        $user_address = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE user_id=".$user_id." and id=".$address_id);
        $address = array();
        if($user_address){
            $address['id'] = $user_address['id'];
            $address['consignee'] = $user_address['consignee'];
            $address['consignee_mobile'] = $user_address['consignee_mobile'];
            $user_address['consignee_district']=json_decode(htmlspecialchars_decode($user_address['consignee_district']),true);
            $address['province'] = $user_address['consignee_district']['province'];
            $address['city'] = $user_address['consignee_district']['city'];
            $address['area'] = $user_address['consignee_district']['area'];
            $address['consignee_address'] = $user_address['consignee_address'];

            $root['status'] = 1;
            $root['address'] = $address;
        }else{
            $root['status'] = 0;
            $root['error']  = "暂无收货地址";
            $address['province'] = '北京市';
            $address['city'] = '东城区';
            $address['area'] = '';
            $root['address'] = $address;
        }

        api_ajax_return($root);
    }

    /*
     * 查看购物订单详情
     * */
    function virtual_shop_order_details(){

        $root['page_title'] = '订单详情';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $order_sn = strim($_REQUEST['order_sn']);//要查询的订单编号
        $order_id = intval($_REQUEST['order_id']);//订单ID

        if($order_sn == ''){
            $root['status']='10037';
            $root['error']="订单号错误";
            api_ajax_return($root);
        }

        $ret = FanweServiceCall("shop","virtual_shop_order_details",array("order_sn"=>$order_sn,"order_id"=>$order_id));

        if($ret){
            $root['status'] = 1;
            $root['data'] = $ret;

        }elseif($ret == ''){
            $root['status'] = 0;
            $root['error'] = '订单异常';
        }elseif($ret['status'] == 10037){
            $root['status']='10037';
            $root['error']="订单号错误";
        }

        api_ajax_return($root);
    }

    /*
     * 购物车
     * */
    public function shop_shopping_cart(){

        $root['page_title'] = '购物车';
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $page = intval($_REQUEST['page']);
        $page_size = 20;
        if ($page == 0) {
            $page = 1;
        }
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."shopping_cart WHERE is_effect=1 and user_id=".$user_id);
        $total = ceil($rs_count/$page_size);
        $pages['count'] = $total;
        $pages['page'] = $page;

        if(intval($rs_count) > 0){
            $goods_info =$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."shopping_cart WHERE is_effect=1 and user_id=".$user_id." limit ".$limit );
            if($goods_info){
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                foreach($goods_info as $key => $value){
                    $goods_info[$key]['imgs'] = json_decode(get_spec_image($value['imgs']),true)[0];
                    $podcast_name = $user_redis->getOne_db(intval($value['podcast_id']),'nick_name');
                    $goods_info[$key]['podcast_name'] = $podcast_name;
                }
                $root['status'] = 1;
                $root['goods'] = $goods_info;
                $root['page'] = $pages;
            }

        }else{
            $root['status'] = 0;
            $root['goods'] = array();
            $root['page'] = $pages;
        }

        api_ajax_return($root);

    }

    /*
     * 加入购物车
     * */
    public function join_shopping(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $data['user_id'] = $user_id;
        $data['goods_id'] = intval($_REQUEST['goods_id']);//商品ID
        $data['podcast_id'] = intval($_REQUEST['podcast_id']);//主播ID
        $data['number'] = intval($_REQUEST['number']);//商品数量

        $ret = FanweServiceCall("shop","join_shopping",$data);
        if($ret['status'] == 1){
            $root['status'] = 1;
            $root['error']  = "成功加入购物车";
        }else{
            $root['status'] = 0;
            $root['error']  = $ret['error'];
        }

        api_ajax_return($root);
    }

    /*
     * 修改购物车商品
     * */
    public function update_shopping_goods(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods_id = intval($_REQUEST['goods_id']);//商品ID
        $podcast_id = intval($_REQUEST['podcast_id']);//主播ID
        $number = intval($_REQUEST['number']);//商品数量

        if($number > 0){
            $user_shopping_number = $GLOBALS['db']->getOne("SELECT number FROM ".DB_PREFIX."shopping_cart WHERE user_id=".$user_id." and goods_id=".$goods_id." and podcast_id=".$podcast_id);
            if($user_shopping_number == $number){
                $root['status'] =0;
                $root['error']  = "修改成功";
            }else{
                $sql = "update ".DB_PREFIX."shopping_cart set number=".$number." where goods_id=".$goods_id." and user_id=".$user_id." and podcast_id=".$podcast_id;
                $user_shopping = $GLOBALS['db']->query($sql);//修改购物车里商品数量
                if($user_shopping){
                    $root['status'] =1;
                    $root['error']  = "修改成功";
                }else{
                    $root['status'] =0;
                    $root['error']  = "修改失败";
                }
            }

        }elseif($number == 0){
            $sql = "delete FROM ".DB_PREFIX."shopping_cart where goods_id=".$goods_id." and user_id=".$user_id." and podcast_id=".$podcast_id;
            $user_shopping = $GLOBALS['db']->query($sql);//删除商品
            if($user_shopping){
                $root['status'] =1;
                $root['error']  = "成功删除商品";
            }
        }else{
            $root['status'] =0;
            $root['error']  = "商品数量异常";
        }

        api_ajax_return($root);
    }

    /*
     * 删除购物车商品
     * */
    public function delete_shopping_goods(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods_id = intval($_REQUEST['goods_id']);//商品ID
        $podcast_id = intval($_REQUEST['podcast_id']);//主播ID

        $user_shopping_number = $GLOBALS['db']->getOne("SELECT number FROM ".DB_PREFIX."shopping_cart WHERE user_id=".$user_id." and goods_id=".$goods_id." and podcast_id=".$podcast_id);
        if($user_shopping_number){
            $sql = "delete FROM ".DB_PREFIX."shopping_cart where goods_id=".$goods_id." and user_id=".$user_id." and podcast_id=".$podcast_id;
            $user_shopping = $GLOBALS['db']->query($sql);//删除商品
            if($user_shopping){
                $root['status'] =1;
                $root['error']  = "删除成功";
            }else{
                $root['status'] =0;
                $root['error']  = "删除失败";
            }
        }else{
            $root['status'] =0;
            $root['error']  = "购物车无此商品";
        }

        api_ajax_return($root);
    }

}
