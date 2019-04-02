<?php

/**
 * 文档地址 https://v.ksyun.com/doc.html#/doc/livesdk.md
 */
class VideoUcloud
{
    private $m_config;

    public function __construct($m_config)
    {
        $this->m_config = $m_config;
    }

    private function build_auth_key($uri, $time)
    {
        $time = dechex($time);
        $auth_key = md5("{$this->m_config['uc_secret']}{$uri}{$time}");
        return "{$uri}?t={$time}&k={$auth_key}";
    }

    public function Create($video_id)
    {
        // 流时长6小时之内，且主动正常断流会触发拼接
        $stream_id = $video_id . "_" . substr(md5($video_id . microtime_float()), 12);
        $time = time();

        return array(
            'stream_id' => $stream_id,
            'push_rtmp' => "rtmp://{$this->m_config['uc_push_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}", $time),
            'play_rtmp' => "rtmp://{$this->m_config['uc_rtmp_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}", $time),
            'play_flv' => "",
            'play_hls' => "http://{$this->m_config['uc_hls_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}/playlist.m3u8", $time),
        );
    }
    /**
     * [Get 非标鉴权实时获取推拉流地址]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-05-24T13:23:54+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     [type] $stream_id [description]
     */
    public function Get($stream_id)
    {
        // 流时长6小时之内，且主动正常断流会触发拼接
        $time = time();

        return array(
            'stream_id' => $stream_id,
            'push_rtmp' => "rtmp://{$this->m_config['uc_push_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}", $time),
            'play_rtmp' => "rtmp://{$this->m_config['uc_rtmp_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}", $time),
            'play_flv' => "",
            // 'play_flv' => "http://{$this->m_config['uc_hls_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}.flv", $time),
            'play_hls' => "http://{$this->m_config['uc_hls_url']}" . $this->build_auth_key("/{$this->m_config['uc_key']}/{$stream_id}/playlist.m3u8", $time),
        );
    }

    public function Query($stream_id)
    {
        return array(
            'stream_status' => 1,
        );
    }

    public function Stop($stream_id)
    {

        $url = "https://api.ucloud.cn/?Action=ForbidLiveStream&Domain={$this->m_config['uc_push_url']}&Application={$this->m_config['uc_key']}&StreamId={$stream_id}";

        $client = new Client();
        $response = $client->request('GET', $url);
        return $response->getStatuscode() == 200;
    }

    public function GetRecord($stream_id, $video_id)
    {
        return array();
    }
}
