<?php

fanwe_require(APP_ROOT_PATH . 'vendor/autoload.php');

use luoyy\Wowza\Stream;

/**
 * 文档地址 https://sandbox.cloud.wowza.com/api/v1.2/docs
 */
class VideoWawza
{
    private $live = null;

    public function __construct($m_config)
    {
        try {
            $this->live = new Stream([
                'broadcast_location' => $m_config['wowza_broadcast_location'],
                'api_key' => $m_config['wowza_api_key'],
                'access_key' => $m_config['wowza_access_key']
            ]);
        } catch (\Exception $e) {}
    }

    public function Create($video_id)
    {
        try {
            $create = $this->live->create();
            $this->live->start($create['stream_target']['id']);
            return array(
                'stream_id' => $create['stream_target']['id'],
                'push_rtmp' => $create['stream_target']['primary_url'],
                'play_rtmp' => $create['stream_target']['playback_urls']['ws'][0],
                'play_flv' => $create['stream_target']['playback_urls']['wowz'][0],
                'play_hls' => $create['stream_target']['playback_urls']['hls'][0]
            );
        } catch (\Exception $e) {
            return array('status' => 0, "error" => $e->getMessage());
        }
    }

    public function Query($stream_id)
    {
        try {
            $state = $this->live->self($stream_id);
            return array(
                'stream_status' => ($state['stream_target']['state'] != 'stopped') ? 1 : 0
            );
        } catch (\Exception $e) {
            return array(
                'stream_status' => 0
            );
        }
    }

    public function Get($stream_id)
    {
        try {
            $self = $this->live->self($stream_id);
            return $self;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function Delete($stream_id)
    {
        try {
            $delete = $this->live->delete($stream_id);
            return $delete;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function Stop($stream_id)
    {
        try {
            $stop = $this->live->update($stream_id, ['enabled' => false]);
            return $stop['stream_target']['state'] == 'stopped';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function GetRecord($stream_id, $video_id)
    {
        return array();
    }
}
