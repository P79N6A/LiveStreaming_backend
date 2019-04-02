<?php

/**
 *
 */
class CourseSeasonAction extends CommonAction
{
    public function index()
    {
        $_REQUEST['type'] = intval($_REQUEST['type']);
        $id               = intval($_REQUEST['id']);
        $sid              = intval($_REQUEST['sid']);
        $title            = trim($_REQUEST['title']);
        $map              = array('pid' => $id);
        if ($sid) {
            $map['id'] = $sid;
        }
        if ($title) {
            $map['title'] = array('like', '%' . trim($title) . '%');
        }
        $model = D(MODULE_NAME);
        if (!empty($model)) {
            $this->_list($model, $map, 'season');
        }
        $this->assign('id', $id);
        $this->assign('course', M('course')->find($id));
        $this->display();
    }

    public function edit()
    {
        $_REQUEST['type'] = intval($_REQUEST['type']);
        $id               = intval($_REQUEST['id']);
        $pid              = intval($_REQUEST['pid']);
        $vo               = M(MODULE_NAME)->find($id);
        if ($vo) {
            $pid = $vo['pid'];
        }
        $m_config          = load_auto_cache("m_config");
        $qcloud_secret_id  = $m_config['qcloud_secret_id'];
        $qcloud_secret_key = $m_config['qcloud_secret_key'];
        if ($vo['video_url'] && !intval($vo['video_url'])) {
            $video_url = $vo['video_url'];
        } else {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
            $video_factory = new VideoFactory();
            $video         = $video_factory->DescribeVodPlayUrls($vo['file_id']);
            $video_url     = $video['urls'][min(array_keys($video['urls']))];
        }
        $this->assign('max_size', conf('MAX_IMAGE_SIZE'));
        $this->assign('video_url', $video_url);
        $this->assign('pid', $pid);
        $this->assign('course', M('course')->find($pid));
        $this->assign('vo', $vo);
        $this->assign('qcloud_secret_id', $qcloud_secret_id);
        $this->assign('qcloud_secret_key', $qcloud_secret_key);
        $this->display();
    }

    public function update()
    {
        if (!trim($_REQUEST['video_url'])) {
            ajax_return(array(
                'status' => 0,
                'error'  => '请上传视频或等待视频加载完成！',
            ));
        }
        $_REQUEST['type'] = intval($_REQUEST['type']);

        $data        = M(MODULE_NAME)->create();
        $pid         = $data['pid'];
        $data['img'] = $_REQUEST['image'];
        //开始验证有效性
        $this->assign("jumpUrl", u('Course' . "/viewSeason", array("id" => $data['id'], 'type' => $_REQUEST['type'])));
        if (!check_empty($data['title'])) {
            ajax_return(array('status' => 0, 'error' => '请输入分集名称'));
        }
        if (!check_empty($data['img'])) {
            ajax_return(array('status' => 0, 'error' => '请上传封面'));
        }
        $data['is_order'] = 0;
        // 更新数据
        if ($data['id']) {
            $res = M(MODULE_NAME)->save($data);
        } else {
            $data['create_time'] = NOW_TIME;
            $res                 = M(MODULE_NAME)->add($data);
        }
        if (false !== $res) {
            self::seasonOrder($pid);
            ajax_return(array('status' => 1, 'error' => '更新成功'));
        } else {
            ajax_return(array('status' => 0, 'error' => '更新错误'));
        }
    }
    public function uploadHeart()
    {
        ajax_return(array(
            'status' => 1,
            'error'  => '',
        ));
    }
    public function getVideoUrlById()
    {
        $id = trim($_REQUEST['id']);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_factory = new VideoFactory();
        $video         = $video_factory->DescribeVodPlayUrls($id);
        $video_url     = $video['urls'][min(array_keys($video['urls']))];
        if ($video_url) {
            ajax_return(array(
                'status' => 1,
                'error'  => '',
                'url'    => $video_url,
            ));
        } else {
            ajax_return(array(
                'status' => 0,
                'error'  => '正在加载视频，请稍后',
            ));
        }
    }
    protected static function seasonOrder($pid)
    {
        $mod  = M(MODULE_NAME);
        $list = $mod->where(array('pid' => $pid))->order('season,is_order')->select();
        if ($list) {
            $order_list = array();
            foreach ($list as $value) {
                if ($value['season']) {
                    $order_list[] = $value;
                }
            }
            foreach ($list as $value) {
                if (!$value['season']) {
                    $order_list[] = $value;
                }
            }
            $max = $mod->where(array('pid' => $pid))->max('season');
            foreach ($order_list as $key => $value) {
                if (!($key + 1 == $value['season'] && $value['is_order'])) {
                    $mod->where(array(
                        'id' => $value['id'],
                    ))->save(array(
                        'season'   => $key + 1,
                        'is_order' => 1,
                    ));
                }
            }
        }
    }
    public function delete()
    {
        //删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id   = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data  = M(MODULE_NAME)->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['title'];
            }
            if ($info) {
                $info = implode(",", $info);
            }
            $list = M(MODULE_NAME)->where($condition)->setField('is_delete', 1);
            if ($list !== false) {
                save_log($info . l("DELETE_SUCCESS"), 1);
                clear_auto_cache("get_help_cache");
                clear_auto_cache("article_notice");
                $this->success(l("DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("DELETE_FAILED"), 0);
                $this->error(l("DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }
}
