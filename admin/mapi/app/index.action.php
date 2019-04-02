<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//fanwe_require(APP_ROOT_PATH.'mapi/lib/index.action.php');
class indexCModule extends baseModule
{
    public function index()
    {

        $root = array();
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $root['page_title'] = "首页";
        //首页轮播
        $root['banner'] = load_auto_cache("bannerpc_list", array('show_position' => 3));

        $live_num = 6;
        //主播推荐
        $live_video = load_auto_cache("selectpc_live_video", array('index_recommend' => $m_config['index_recommend'], 'page_size' => $live_num));
        $live_num -= count($live_video);
        $is_recommend_list = load_auto_cache("selectpc_video", array('is_recommend' => 1, 'pc' => 1));
        if ($live_num > 0) {
            // 直播推荐
            $root['live_video'] = array_merge($live_video, array_slice($is_recommend_list, 0, $live_num));
        } else {
            $root['live_video'] = $live_video;
        }
        //推荐
        $root['is_recommend'] = $is_recommend_list;
        // 演示版推荐位按照扣掉直播后顺延
        $recommend_offset = count($is_recommend_list) - $live_num - 4;
        $root['recommend_offset'] = $recommend_offset > 0 ? $live_num : $live_num + $recommend_offset;

        $root['is_recommend_more_url'] = url("video#video_list", array('is_recommend' => 1));
        //热门
        $is_hot_list = load_auto_cache("selectpc_video", array('is_hot' => 1, 'pc' => 1));
        $root['is_hot'] = $is_hot_list;
        $root['is_hot_more_url'] = url("video#video_list", array('is_hot' => 1));
        //最新话题
        $root['is_new']['cate_top'] = load_auto_cache("cate_top");
        $root['is_new']['cate_top_more_url'] = url("video#video_list", array('is_hot_cate' => 1));
        //最新列表
        $is_new_list = load_auto_cache("selectpc_video", array('is_new' => 1, 'pc' => 1));
        $root['is_new']['list'] = $is_new_list;
        $root['is_new']['is_new_more_url'] = url("video#video_list", array('is_new' => 1));

        //热门家族

        $is_family_hot_list = load_auto_cache("selectpc_video", array('is_family_hot' => 1, 'pc' => 1));
        $root['is_family_hot'] = $is_family_hot_list;
        $root['is_family_hot_more_url'] = url("video#video_list", array('is_family_hot' => 1));
        //魅力
        $root['charm_podcast'] = $this->charm_podcast();
        $charm_podcast['charm_podcast'] = $this->charm_podcast();
        //财富
        $root['rich_list'] = $this->rich_list();
        $rich_list['rich_list'] = $this->rich_list();
        //新闻公告
        $param = array('page' => 1, 'page_size' => 10);
        $article_list = load_auto_cache("article", $param);
        // 主播推荐
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();

        $recommend_anchor = $this->get_recommend_anchor($charm_podcast['charm_podcast'], $rich_list['rich_list']);
        shuffle($recommend_anchor);
        foreach ($recommend_anchor as $key => $value) {
            if ($value['live_in']) {
                $fields = array(
                    'title',
                    'watch_number'
                );
                $room = $video_redis->getRow_db($value['room_id'], $fields);
                $recommend_anchor[$key]['watch_number'] = empty($recommend_anchor[$key]['watch_number']) ? $room['watch_number'] : $recommend_anchor[$key]['watch_number'];
                $recommend_anchor[$key]['title'] = empty($recommend_anchor[$key]['title']) ? $room['title'] : $recommend_anchor[$key]['title'];
            }
        }
        $root['recommend_anchor'] = $recommend_anchor;

        // 广告列表
        $place_id = 1;
        $root['ad_list'] = load_auto_cache("ad_list", $place_id);

        $root['news']['news_list'] = $article_list['listmsg'];
        $root['news']['news_more'] = url("article#news");

        $root['appid'] = $m_config['vodset_app_id'];
        $root['status'] = 1;
        // if ($_REQUEST['test']) {
        //     echo '<pre>';
        //     var_dump($root);
        //     die;
        // }
        api_ajax_return($root);
    }

    private function get_recommend_anchor($charm_podcast, $rich_list)
    {
        $recommend_anchor = array();
        foreach (array('day', 'weeks', 'month', 'all') as $key) {
            foreach ($charm_podcast[$key] as $item) {
                if (isset($recommend_anchor[$item['user_id']])) {
                    continue;
                }
                $recommend_anchor[$item['user_id']] = $item;
            }

            foreach ($rich_list[$key] as $item) {
                if (isset($recommend_anchor[$item['user_id']])) {
                    continue;
                }
                $recommend_anchor[$item['user_id']] = $item;
            }

            if (count($recommend_anchor) >= 15) {
                break;
            }
        }
        return array_values($recommend_anchor);
    }

    //魅力
    public function charm_podcast()
    {
        $list = load_auto_cache("charm_podcast");
        return $list;
    }

    //财富
    public function rich_list()
    {
        $list = load_auto_cache("rich_list");
        return $list;
    }

    public function is_live($data, $live_list)
    {
        foreach ($data as $k => $v) {
            foreach ($live_list as $kk => $vv) {
                if ($vv['user_id'] == $v['user_id']) {
                    $data[$k]['live_in'] = $vv['live_in'];
                    $data[$k]['video_url'] = url("live#show", array("room_id" => $vv['room_id']));
                } else {
                    $data[$k]['live_in'] = 0;
                    $data[$k]['video_url'] = '';
                }
            }
            $data[$k]['user_level_ico'] = get_spec_image("./public/images/rank/rank_" . $v['user_level'] . ".png");
        }
        return $data;
    }

    public function get_live()
    {
        $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type,
                        (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.head_image,v.thumb_head_image, v.xpoint,v.ypoint,
                        u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
                    LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) order by v.sort_num desc,v.sort desc";
        $live_list = $GLOBALS['db']->getAll($sql, true, true);

        return $live_list;
    }

}
