<?php

/* API 文档
 * 点播 https://www.qcloud.com/doc/api/257/1965
 */

class VideoFanwe2
{
    private $m_config = null;

    public function __construct($m_config)
    {
        $this->m_config = $m_config;
    }

    public function Create($user_id, $video_id)
    {
        $result = $this->invoke(array(
            'act' => 'create',
            'user_id' => $user_id,
            'video_id' => $video_id
        ));
        if (!$result['status']) {
            ajax_return(array(
                'status' => 0,
                'error' => $result['error']
            ));
        }
        log_file('Create_result', 'VideoQlvb1');
        log_file($result, 'VideoQlvb1');
        $data = $result['data'];
        return array(
            'channel_id' => $data['stream_id'],
            'upstream_address' => $data['push_rtmp'],
            'downstream_address' => array(
                'rtmp' => $data['play_rtmp'],
                'flv' => $data['play_flv'],
                'hls' => $data['play_hls']
            )
        );
    }

    public function Query($stream_id)
    {
        $result = $this->invoke(array(
            'act' => 'query',
            'stream_id' => $stream_id
        ));
        $num = count($result);
        if ($num) {
            $k = $num - 1;
        }
        if (!$result[$k]['code']) {
            return array(
                'status' => 0,
                'error' => $result[$num]['message']
            );
        }
        log_file('Create_Query', 'VideoQlvb1');
        log_file($result, 'VideoQlvb1');
        $data = $result[$k]['basicInfo'];
        return array(
            'channel_id' => $stream_id,
            'status' => $data['status']
        );
    }

    public function Stop($stream_id)
    {
        $result = $this->invoke(array(
            'act' => 'stop',
            'stream_id' => $stream_id
        ));
        if (!$result['status']) {
            return array(
                'status' => 0,
                'error' => $result['error']
            );
        }

        return $result['data'];
    }
    //查询查询
    public function GetRecord($stream_id)
    {
        $result = $this->invoke(array(
            'act' => 'get_record',
            'stream_id' => $stream_id
        ));
        log_file('Create_GetRecord', 'VideoQlvb1');
        log_file($result, 'VideoQlvb1');
        if (!$result['status']) {
            return array(
                'status' => 0,
                'error' => $result['error']
            );
        }
        return array('totalCount' => $result['data']['total_count'], 'filesInfo' => $result['data']['filesInfo'], 'urls' => $result['data']['file_list']);

    }
    //删除视频
    public function DeleteVodFile($stream_id)
    {
        $result = $this->invoke(array(
            'act' => 'delete',
            'stream_id' => $stream_id
        ));
        log_file('Create_DeleteVodFile', 'VideoQlvb1');
        log_file($result, 'VideoQlvb1');
        if (!$result['status']) {
            return array(
                'status' => 0,
                'error' => $result['error']
            );
        }
        return array(
            'status' => 1,
            'delvodset' => $result['delvodset']
        );
    }
    //获取未消费事件通知
    public function PullEvent()
    {
        $result = $this->invoke(array(
            'act' => 'pullevent'
        ));
        log_file('Create_PullEvent', 'VideoQlvb1');
        log_file($result, 'VideoQlvb1');
        if (!$result['status']) {
            return array(
                'status' => 0,
                'error' => $result['message']
            );
        }
        return array(
            'status' => 1,
            'data' => $result['data']
        );
    }
    //确认事件通知
    public function ConfirmEvent($MsgHandle)
    {
        $result = $this->invoke(array(
            'act' => 'confirmevent',
            'msghandle' => $MsgHandle
        ));
        log_file('Create_ConfirmEvent', 'VideoQlvb1');
        log_file($result, 'VideoQlvb1');
        if (!$result['status']) {
            return array(
                'status' => 0,
                'error' => $result['message']
            );
        }
        return array(
            'status' => 1,
            'data' => $result['data']
        );
    }

    /*private function invoke($params)
    {
    $url = "http://fwyun.fanwe.net/video";
    fanwe_require(APP_ROOT_PATH . 'system/saas/SAASAPIClient.php');
    $client = new \SAASAPIClient($this->m_config['fwyun_access_key'], $this->m_config['fwyun_secret_key']);
    return $client->invoke($url, $params);
    }*/
    private function invoke($params)
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_qlvb2.php');
        $act = strim($params['act']);
        $factory = new VideoQlvb2();
        $user_id = $params['user_id'];
        $video_id = $params['video_id'];
        try {
            switch ($act) {
                case 'create':
                    $stream_info = $factory->Create($user_id, $video_id);
                    return array('status' => 1, 'data' => $stream_info);

                    break;
                case 'query':
                    $stream_id = strim($params['stream_id']);
                    $stream_info = $factory->Query($stream_id);
                    return array('status' => 1, 'data' => $stream_info);

                    break;
                case 'stop':
                    $stream_id = strim($params['stream_id']);
                    $status = $factory->Stop($stream_id);
                    return array('status' => $status ? 1 : 0);

                    break;
                case 'get_record':
                    $stream_id = strim($params['stream_id']);
                    $stream_info = $factory->GetRecord($stream_id);

                    return array('status' => 1, 'delvodset' => $stream_info);
                    break;
                case 'delect':
                    $stream_id = strim($params['stream_id']);
                    $stream_info = $factory->DeleteVodFile($stream_id);

                    return array('status' => 1, 'data' => $stream_info);
                    break;
                case 'pullevent':
                    $msgHandle = $factory->PullEvent();
                    return array('status' => 1, 'data' => $msgHandle);
                    break;
                case 'confirmevent':
                    $msghandle = strim($params['msghandle']);
                    $stream_info = $factory->ConfirmVodEvent($msghandle);
                    return array('status' => 1, 'data' => $stream_info);
                    break;
                default:
                    return array('status' => 0, 'error' => 'unknown service');
                    break;
            }
        } catch (Exception $e) {
            return array('status' => 0, 'error' => $e->getMessage());
        }
    }

}
