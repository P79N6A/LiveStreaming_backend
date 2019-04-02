<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class VideoCollect1Action extends CommonAction
{
    private $key = "zzq1314520";

    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH . "/admin/Lib/Action/VideoCommonAction.class.php";
        require_once APP_ROOT_PATH . "/admin/Lib/Action/UserCommonAction.class.php";
    }

    /**
     * 视频采集
     *
     */
    public function index()
    {
        $url = 'http://api.hclyz.cn:81/mf/json.txt';
        $UserAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:59.0) Gecko/20100101 Firefox/59.0';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        $resp = $resp['pingtai'];
        foreach ($resp as $item) {
            //  $itemArray = explode("|", $item);
            $newArray = [
                "name" => $item['title'],
                "img" => $item['xinimg'],
                "url" => $item['address']
            ];
            $data[] = $newArray;
        }
        if (empty(S('gatherData'))) {
            S('gatherData', $data, '300');
        } else {
            $data = S('gatherData');
        }

        $this->assign('data', $data);
        $this->display();
    }

    //详细页
    public function two()
    {
        header("Content-type: text/json; charset=utf-8");
        $url = 'http://api.hclyz.cn:81/mf/' . $_GET['url'];
        $UserAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:59.0) Gecko/20100101 Firefox/59.0';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        $resp = $resp['zhubo'];
        foreach ($resp as $item) {
            //  $itemArray = explode("|", $item);
            $url = $item['address'];
            if (stripos($url, 'http') === 0) {
                $headers = get_headers($url, true);
                $url = !empty($headers['Location']) ? $headers['Location'] : $url;
            }
            $data[] = [
                "name" => $item['title'],
                "img" => $item['img'],
                "url" => $url
            ];
        }
        if (empty(S($_GET['url']))) {
            S($_GET['url'], $data, '300');
        } else {
            $data = S($_GET['url']);
        }
        $this->assign('data', $data);
        $this->display();
    }

    //选择房间类型
    public function change_roomtype()
    {
        $this->display();
    }

    //加入直播
    public function add_video()
    {
        $play_url = $_REQUEST['url'];
        if (empty($play_url)) {
            $this->error("播流地址为空！");
        }

        $m_config = load_auto_cache("m_config");
        //增加虚拟会员
        $userRobot = array();
        $userRobot['nick_name'] = $_REQUEST['nickname'];
        $userRobot['head_image'] = $_REQUEST['logourl'];
        $userRobot['is_admin'] = "0";
        $userRobot['mobile'] = "";
        $userRobot['province'] = "";
        $userRobot["city"] = "";
        $userRobot["sex"] = "1";
        $userRobot['user_level'] = "1";
        $userRobot['signature'] = "";
        $userRobot['is_effect'] = "1";
        $userRobot['is_ban'] = "0";
        $userRobot["ban_time"] = "";
        $userRobot["is_authentication"] = "0";
        $userRobot["authentication_type"] = "0";
        $userRobot["v_explain"] = "0";
        $userRobot['identify_positive_image'] = "";
        $userRobot["identify_nagative_image"] = "";
        $userRobot["identify_hold_image"] = "";
        $userRobot["identify_number"] = "";
        $userRobot['member_type'] = "1";
        $userRobot['is_robot'] = "1";
        $userRobot['v_icon'] = "";
        $userRobot["score"] = "10";

        $common = new UserCommon();
        filter_request($userRobot);
        $res = save_user($userRobot, 'INSERT', $update_status = 1);
        $user_id = intval($res['data']);

        //添加采集流
        $sql = "select id,video_type from " . DB_PREFIX . "video where live_in =2 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {

            //更新心跳时间，免得被删除了
            $sql = "update " . DB_PREFIX . "video set monitor_time = '" . to_date(NOW_TIME, 'Y-m-d H:i:s') . "' where id =" . $video['id'];
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                $this->success("加入成功");
            }
        }

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from " . DB_PREFIX . "video where live_in =1 and user_id = " . $user_id;
        $list = $GLOBALS['db']->getAll($sql, true, true);
        foreach ($list as $k => $v) {
            //结束直播
            do_end_video($v, $v['video_vid'], 1, $v['cate_id']);
        }

        require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
        $video_id = get_max_room_id(0);
        $data = array();
        $data['id'] = $video_id;
        //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
        $data['room_type'] = 3;

        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']); //允许添加的最大机器人数;

        //图片,应该从客户端上传过来,如果没上传图片再用会员头像

        $data['head_image'] = $_REQUEST['logourl'];
        $data['thumb_head_image'] = $_REQUEST['logourl'];
        $data['sex'] = "2"; //性别 0:未知, 1-男，2-女
        $data['video_type'] = intval($m_config['video_type']); //0:腾讯云互动直播;1:腾讯云直播

        if ($data['video_type'] > 0) {
            require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
            $api = createTimAPI();
            $ret = $api->group_create_group('AVChatRoom', (string) $user_id, (string) $user_id, (string) $video_id);
            if ($ret['ActionStatus'] != 'OK') {
                $this->error("加入失败[" . $ret['ErrorCode'] . $ret['ErrorInfo'] . "]");
            }

            $data['group_id'] = $ret['GroupId'];

        }

        //生成随机省市
        $cityArr = ['北京市', '天津市', '河北省', '山西省', '内蒙', '辽宁省', '吉林省', '黑龙江省', '上海市', '江苏省', '浙江省', '安徽省', '福建省', '江西省', '山东省', '河南省', '湖北省', '湖南省', '广东省', '广西省', '海南省', '重庆市', '四川省', '贵州省', '云南省', '陕西省', '甘肃省', '青海省', '宁夏', '新疆'];
        $index = rand(0, 29);

        $data['monitor_time'] = to_date(NOW_TIME, 'Y-m-d H:i:s'); //主播心跳监听
        $data['title'] = $_REQUEST['nickname'];
        $data['cate_id'] = 1;
        $data['user_id'] = $user_id;
        $data['live_in'] = 1; //live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = ''; //'当前观看人数';
        $data['vote_number'] = ''; //'获得票数';
        $data['province'] = "火星"; //'省';
        $data['city'] = $cityArr[$index]; //'城市';
        $data['xpoint'] = "";
        $data['ypoint'] = "";

        $data['create_time'] = NOW_TIME; //'创建时间';
        $data['begin_time'] = NOW_TIME; //'开始时间';
        $data['end_time'] = ''; //'结束时间';
        $data['is_hot'] = 1; //'1热门; 0:非热门';
        $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1; //主播在线状态;1:在线(默认); 0:离开

        //sort_init(初始排序权重) = (用户可提现秀票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留秀票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
        $sort_init = 0;

        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];

        // 1、创建视频时检查表是否存在，如不存在创建礼物表，表命名格式 fanwe_ video_ prop_201611、格式同fanwe_ video_ prop相同
        // 2、将礼物表名称写入fanwe_video 中，需新建字段
        // 3、记录礼物发送时候读取fanwe_video 的礼物表名，写入对应的礼物表
        // 4、修改所有读取礼物表的地方，匹配数据
        $data['prop_table'] = createPropTable();
        //直播分类
        $data['classified_id'] = 8;
        if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && intval($m_config['switch_public_pay']) == 1 && intval($m_config['public_pay']) > 0) {
            $data['is_live_pay'] = 1;
            $data['live_pay_type'] = 1;
            $data['public_screen'] = 1;
            $data['live_fee'] = intval($m_config['public_pay']);
            $data['live_pay_time'] = intval(NOW_TIME);
        }

        $data['is_live_pay'] = $_REQUEST['is_live_pay'];
        $data['live_pay_type'] = $_REQUEST['live_pay_type'];
        $data['live_fee'] = $_REQUEST['live_fee'];
        $data['live_pay_time'] = intval(NOW_TIME);

        //判断流媒体格式
        //rtmp格式
        if (preg_match("/^rtmp:/", $play_url)) {
            //RTMP格式
            $data['play_rtmp'] = $play_url;
            $data['play_flv'] = $play_url;
        }
        if (preg_match("/flv$/", $play_url)) {
            //FLV
            $data['play_flv'] = $play_url;
        }
        if (preg_match("/mp4$/", $play_url)) {
            //MP4
            $data['play_mp4'] = $play_url;
        }
        if (preg_match("/m3u8$/", $play_url)) {
            //HLS 仅此格式能播放
            $data['play_hls'] = $play_url;
        }

        $data['channelid'] = "gather";

        $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
        //$video_id =  $GLOBALS['db']->insert_id();

        if ($GLOBALS['db']->affected_rows()) {
            sync_video_to_redis($video_id, '*', false);
            $this->success("加入成功");
        } else {
            $this->error("加入失败");
        }
    }

    public function getAction($url = '')
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;

    }

    //rc4加解密  rc4b解密  rc4a加密
    private function rc4($pwd, $data)
    {
        $key[] = "";
        $box[] = "";
        $pwd_length = strlen($pwd);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            //$cipher = "";
            @$cipher .= chr(ord($data[$i]) ^ $k);
        }
        return $cipher;
    }

    private function hexToStr($hex)
    {
        $string = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $string;
    }

    private function strToHex($string)
    {
        return substr(chunk_split(bin2hex($string)), 0, -2);
    }

    private function rc4a($string)
    {
        return $this->strToHex($this->rc4($this->key, $string));
    }

    private function rc4b($string)
    {
        return @$this->rc4($this->key, pack('H*', $string));
    }
}
