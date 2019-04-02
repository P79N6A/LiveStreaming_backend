<?php

/* API 文档
 * 点播 https://www.qcloud.com/doc/api/257/1965
 */

class VideoQlvb2
{
    public function __construct()
    {
        fanwe_require(APP_ROOT_PATH . 'system/QcloudApi/QcloudApi.php');
    }

    private function loadService($module_name, $config = null)
    {
        $m_config = load_auto_cache("m_config");

        if ($config == null) {
            $config = array(
                'SecretId' => $m_config['qcloud_secret_id'],
                'SecretKey' => $m_config['qcloud_secret_key'],
                'RequestMethod' => 'GET',
                'DefaultRegion' => 'gz'
            );
        }

        return QcloudApi::load($module_name, $config);
    }

    /**
     * 创建一个直播频道
     * @param unknown_type $video_id
     * @param unknown_type $user_id
     * @param unknown_type $is_record
     * @return Ambigous <multitype:string, multitype:string multitype:string  >
     */
    public function Create($user_id, $video_id)
    {
        $flow_user_id = $user_id;
        $is_record = 1;
        return $this->GetChannelInfo($video_id, 'b', $video_id, $user_id, $is_record, $flow_user_id);
    }

    /**
     * 关闭频道推流
     * @param string $channel_id 有些早期提供的API中直播码参数被定义为channel_id，新的API则称直播码为stream_id，仅历史原因而已
     * @return multitype:number unknown
     */
    public function Stop($stream_id)
    {
        $m_config = load_auto_cache('m_config');
        $key = $m_config['qcloud_auth_key'];
        $t = get_gmtime() + 86400;

        $url = "http://fcgi.video.qcloud.com/common_access?" . http_build_query(array(
            'appid' => $m_config['vodset_app_id'],
            'interface' => 'Live_Channel_SetStatus',
            't' => $t,
            'sign' => md5($key . $t),
            'Param.s.channel_id' => $stream_id,
            'Param.n.status' => 0 //0:关闭； 1:开启
        ));

        $ret = $this->accessService($url);
        //ret    返回码    int    0:成功；其他值:失败
        //message    错误信息    string    错误信息
        if ($ret['ret'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取直播录制文件
     * @param string $channel_id
     * @return multitype:number NULL multitype:multitype:unknown
     *
     * https://www.qcloud.com/document/product/267/5960
     */
    public function GetRecord($stream_id)
    {
        $m_config = load_auto_cache('m_config');
        $key = $m_config['qcloud_auth_key'];
        $t = get_gmtime() + 86400;

        $url = "http://fcgi.video.qcloud.com/common_access?" . http_build_query(array(
            'appid' => $m_config['vodset_app_id'],
            'interface' => 'Live_Tape_GetFilelist',
            't' => $t,
            'sign' => md5($key . $t),
            'Param.n.page_size' => 100,
            'Param.s.channel_id' => $stream_id
        ));

        $res = $this->accessService($url);
        $filesInfo = array();
        foreach ($res['output']['file_list'] as $file) {
            $filesInfo[] = array(
                'fileId' => $file['file_id']
            );
        }
        return array('status' => 1, 'totalCount' => $res['output']['all_count'], 'filesInfo' => $filesInfo, 'file_list' => $res['output']['file_list']);
    }

    /**
     * 视频拼接 https://www.qcloud.com/document/product/266/7821
     * @param unknown_type $channel_id
     * @return multitype:number NULL multitype:multitype:unknown
     */
    public function ConcatVideo($channel_id, $new_file_name)
    {
        $res = $this->GetRecord($channel_id);

        if ($res['totalCount'] > 1) {
            $params = array();
            $params['name'] = $new_file_name;
            $params['dstType.0'] = 'mp4';

            $i = 0;
            foreach ($res['filesInfo'] as $file) {
                $params['srcFileList.' . $i . '.fileId'] = $file['fileId'];
                $i = $i + 1;
            }

            $service = $this->loadService(QcloudApi::MODULE_VOD);
            $ret = $service->ConcatVideo($params);
            /*
            array (
            'codeDesc' => 'Success',
            'vodTaskId' => 'concat-d0cef54c78075e5657dc934fc1b38d98',
            )
             */
            if ($ret['codeDesc'] != 'Success') {
                // 请求失败，解析错误信息
                $error = $service->getError();
                return array(
                    'status' => 0,
                    'channel_id' => $channel_id,
                    'error' => $error->getMessage()
                );
            } else {
                //code 错误码, 0: 成功, 其他值: 失败
                //vodTaskId 描述拼接任务的唯一id，可以通过此id查询任务状态
                return array(
                    'status' => 1,
                    'channel_id' => $channel_id,
                    'error' => '合并任务已提交,请等待合并，大致需要5分钟',
                    'data' => $ret
                );
            }
        } else {
            //只有一个文件时,不需要调用：合并视频功能
            return array(
                'status' => 1,
                'error' => '单文件视频不需要合并'
            );
        }

    }

    /**
     * 删除视频文件 https://www.qcloud.com/document/product/266/1324
     * @param string $channel_id
     * @return multitype:NULL
     */
    public function DeleteVodFile($channel_id)
    {

        $res = $this->GetRecord($channel_id);

        $delvodset = array();

        if ($res['totalCount'] > 0) {
            $service = $this->loadService(QcloudApi::MODULE_VOD);

            foreach ($res['file_list'] as $file) {
                $delvodset[$file['file_id']] = $service->DeleteVodFile(array('fileId' => $file['file_id'], 'priority' => 0));
            }
        }

        return $delvodset;
    }

    /** URL拉取视频上传 文档地址 https://www.qcloud.com/document/product/266/1393
     * @param $url
     */
    public function MultiPullVodFile($flow_user_id, $url, $file_name)
    {
        //$config

        $service = $this->loadService(QcloudApi::MODULE_VOD, $config);
        $ret = $service->MultiPullVodFile(array(
            'pullset.1.url' => $url,
            'pullset.1.fileName' => $file_name
        ));
        if ($ret === false) {
            // 请求失败，解析错误信息
            $error = $service->getError();
            return array(
                'status' => 0,
                'error' => $error->getMessage()
            );
        }

        return $ret;
    }

    /**
     *  拉取事件通知【点播API】
    视频拼接、URL拉取视频上传 通过可靠回调，获得执行结果；
    PullVodEvent：https://www.qcloud.com/document/product/266/7818
    5.1 监听到 视频拼接完 事件则执行第5步，把视频文件保存到用户的腾讯云帐户下，同时调用第4步删除源文件
    5.2 监听到【用户腾讯云帐户】下的 URL拉取视频上传完 事件；则删除千秀腾讯云下的合并后的视频文件；并通知用户的appserver，点播视频生成；

     * @param int $flow_user_id
     * @return multitype:number NULL |unknown
     */
    public function PullEvent($flow_user_id = 0)
    {
        //$config
        $config = null;

        $m_config = load_auto_cache("m_config");
        $service = $this->loadService(QcloudApi::MODULE_VOD, $config);
        //print_r($service);
        //echo $service->getLastRequest();
        $ret = $service->PullEvent();
        if ($ret === false) {
            //print_r($service);

            // 请求失败，解析错误信息
            $error = $service->getError();
            return array(
                'status' => 0,
                'error' => $error->getMessage()
            );

        }

        return $ret;
    }

    /**
     * 确认事件通知【点播API】
    ConfirmVodEvent：https://www.qcloud.com/document/product/266/7819
     * @param unknown_type $flow_user_id
     * @param unknown_type $msgHandle
     * @return multitype:number NULL |unknown
     */
    public function ConfirmVodEvent($flow_user_id = 0, $msgHandle = array())
    {
        //$config
        $params = array();
        $i = 1;
        foreach ($msgHandle as $key => $val) {
            $params['msgHandle.' . $i] = $val;
            $i = $i + 1;
        }
        //https://vod.api.qcloud.com/v2/index.php?Action=ConfirmEvent&msgHandle.0=XXXX&msgHandle.1=YYYY&COMMON_PARAMS
        $service = $this->loadService(QcloudApi::MODULE_VOD, $config);
        $ret = $service->ConfirmEvent($params);
        if ($ret === false) {
            // 请求失败，解析错误信息
            $error = $service->getError();
            return array(
                'status' => 0,
                'error' => $error->getMessage()
            );
        }

        return $ret;
    }

    /**
     * 获取播放统计历史信息【直播API】
     * @param string $stream_id 直播码也是$channel_id值
     * @param int $start_time 查询起始时间 3天内的数据 时间戳
     * @param int $end_time 查询终止时间 建议查询跨度不大于2小时 时间戳
     * @return multitype:number NULL
     */
    public function LivePlayStatHistory($stream_id, $start_time, $end_time)
    {
        $m_config = load_auto_cache('m_config');
        $key = $m_config['qcloud_auth_key'];
        $t = get_gmtime() + 86400;

        $url = "http://statcgi.video.qcloud.com/common_access?" . http_build_query(array(
            'appid' => $m_config['vodset_app_id'],
            'interface' => 'Get_LivePlayStatHistory',
            't' => $t,
            'sign' => md5($key . $t),
            'Param.n.start_time' => $start_time,
            'Param.n.end_time' => $end_time,
            'Param.s.stream_id' => $stream_id
        ));

        $res = $this->accessService($url);

        return $res;
    }

    /**
     *
     * @param int $app_video_id  fanwe_video.id
     * @param string $layer    b:主播; s:小主播
     * @param int $session_id
     * @param int $app_user_id    fanwe_user.id
     * @param int $is_record    是否记录文件;1:记录;0:不记录
     * @param int $flow_user_id    流量平台用户id;
     * @param int $flow_video_id 流量平台频道id;
     * @return multitype:string multitype:string
     */
    public function GetChannelInfo(
        $app_video_id,
        $layer = 'b',
        $session_id = 0,
        $app_user_id = 0,
        $is_record = 0,
        $flow_user_id = 0,
        $flow_video_id = 0
    ) {
        $m_config = load_auto_cache('m_config');
        $bizId = $m_config['qcloud_bizid'];
        $key = $m_config['qcloud_security_key'];

        //$stream_id = $session_id . $layer . $app_user_id . "_" . substr(md5($app_video_id . microtime_float()), 12);
        //$stream_id = $bizId . "_" . $stream_id; //直播码

        if ($flow_video_id == 0) {
            $flow_video_id = $app_video_id;
        }

        //if ($flow_user_id == 0) $flow_user_id = $bizId;

        //直播码生成规则： 商户ID."_".商户会员id.$layer.商户直播ID."_".平台直播ID."_".to_date(NOW_TIME, 'Y-m-d H:i:s')
        //直播码:用作识别不同推流的ID标示，唯一的要求就是以“bizid+下划线”作为前缀，剩下的部分您可以自由指定，只要确保不跟已经分配过的直播码冲突就行了，所以很多客户会选择用主播的用户id来作为直播码使用。
        $stream_id = $bizId . "_" . $flow_user_id . "_" . $app_user_id . $layer . $app_video_id . "_" . $flow_video_id . "_" . microtime_format(NOW_TIME, 'YmdHisx');

        if ($session_id == 0) {
            $session_id = $flow_video_id;
        }

        $time = to_date(get_gmtime() + 86400, 'Y-m-d H:i:s');
        $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
        //$stream_id = bizid+"_"+stream_id  如 8888_test123456

        // 24小时失效
        $ext_str = $this->get_acc_sign($key, $stream_id, 86400);

        $qcloud_liveplay_url = !empty($m_config['qcloud_liveplay_url']) ? $m_config['qcloud_liveplay_url'] : "{$bizId}.liveplay.myqcloud.com";
        $qcloud_livepush_url = !empty($m_config['qcloud_livepush_url']) ? $m_config['qcloud_livepush_url'] : "{$bizId}.livepush.myqcloud.com";

        $ext_str = "?bizid=" . $bizId . "&" . $ext_str; //. "&mix=layer:{$layer};session_id:{$session_id};t_id:1";
        $upstream_address = "rtmp://{$qcloud_livepush_url}/live/" . $stream_id . $ext_str;
        //后台开启录制 或者 非私密直播 录制视频
        $has_save_video = intval($m_config['has_save_video']);
        $save_video = '';
        if ($has_save_video && $is_private != 1) {
            $save_video = "&record=" . $record . "&record_interval=5400";
        }
        return array(
            'channel_id' => $stream_id,
            'upstream_address' => $upstream_address . $save_video,
            'downstream_address' => array(
                'rtmp' => "rtmp://{$qcloud_liveplay_url}/live/" . $stream_id,
                'flv' => "http://{$qcloud_liveplay_url}/live/" . $stream_id . ".flv",
                'hls' => "http://{$qcloud_liveplay_url}/live/" . $stream_id . ".m3u8"
            )
        );
    }

    private function get_acc_sign($key, $stream_id, $len = 300)
    {
        $time = to_date(get_gmtime() + $len, 'Y-m-d H:i:s');
        //$time = '2017-01-22 23:59:59';
        $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
        //txSecret的生成方法是 = MD5(KEY+ stream_id + txTime)
        $txSecret = md5($key . $stream_id . $txTime);
        $ext_str = http_build_query(array(
            "txSecret" => $txSecret,
            "txTime" => $txTime
        ));

        return $ext_str;
    }

    private function accessService($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false); //对body进行输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $package = curl_exec($ch);
        $res = json_decode($package, true);

        return $res;
    }
    //查询直播状态
    public function Query($stream_id)
    {
        $res = $this->GetRecord($stream_id);

        $stream_info = array();

        if ($res['totalCount'] > 0) {
            $service = $this->loadService(QcloudApi::MODULE_VOD);
            foreach ($res['filesInfo'] as $k => $file) {
                $stream_info[$k] = $service->GetVideoInfo(array('fileId' => $file['fileId'], 'infoFilter.0' => 'basicInfo'));
            }
        }
        return $stream_info;
    }

}
