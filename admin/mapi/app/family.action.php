<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . "/mapi/app/page.php");

class familyCModule extends baseModule
{
    /*
     * [index description]
     * @return [type] [description]
     */
    public function index()
    {
        //家族首页轮播
        $banner = load_auto_cache("bannerpc_list", array('type' => 1));
        $new = load_auto_cache("family_rank_all", array('sort' => 'create_time', 'limit' => 6,'type'=>'new'));
        $hot = load_auto_cache("family_rank_all", array('sort' => 'user_count', 'limit' => 12,'type'=>'hot'));
        $ranking = load_auto_cache("family_rank_all",array('sort'=>'user_count','limit'=>10,'type'=>'all'));
        $hot_family_video=load_auto_cache("selectpc_video",array("is_family_hot"=>1,"page_size"=>6,"page"=>1));
        api_ajax_return(array(
            'banner' => $banner,
            'new' => $new,
            'hot' => $hot,
            'ranking' => $ranking,
            'is_family_hot'=>$hot_family_video,
            'is_family_hot_more_url'=>url("video#video_list",array('is_family_hot'=>1)),
            'status' => 1,
        ));
    }

    //家族更多接口
    public function lists()
    {
        $root = array();
        $type = strim($_REQUEST['type']);//请求类型：hot-热门；new-最新
        $p = intval($_REQUEST['p']) > 0 ? intval($_REQUEST['p']) : 1;//页码
        $page_size = 12;//分页数量
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $root['type'] = $type;
        if ($type == 'new') {
            $list = load_auto_cache("family_rank_all", array('sort' => 'create_time', 'limit' => $limit));
        } elseif ($type == 'hot') {
            $list = load_auto_cache("family_rank_all", array('sort' => 'user_count', 'limit' => $limit));
        }

        $ranking = load_auto_cache("family_rank_all", array('sort' => 'user_count', 'limit' => 10));
        if ($list) {
            $rs_count = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "family WHERE status=1", true, true);
            $page = new Page($rs_count, $page_size);
            $root['list'] = $list;
            $root['status'] = 1;
            $root['page'] = $page->show();
            $root['ranking'] = $ranking;
        } else {
            $root['list'] = $list;
            $root['status'] = 0;
            $root['error'] = '';
        }
        api_ajax_return($root);

    }

    // 家族列表（会员中心）
    public function family_list()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";// es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            //分页
            $user_id = intval($GLOBALS['user_info']['id']);
            $p = intval($_REQUEST['p']);//取第几页数据
            if ($p == 0 || $p == '') {
                $p = 1;
            }
            //每次20条
            $page_size = 8;
            $limit = (($p - 1) * $page_size) . "," . $page_size;
            //搜索
            $family_id = intval($_REQUEST['family_id']);
            $family_name = strim($_REQUEST['family_name']);

            if (($family_id != '' && $family_id != 0) || $family_name != '') {
                if ($family_name == '') {
                    $family_name = 'null';
                }else{
                    $root['keyword'] = $family_name;
                }
                //搜索列表
                $family_list = $GLOBALS['db']->getAll("SELECT j.id as family_id,j.logo as family_logo,j.name as family_name,j.user_id,u.nick_name,j.create_time,(SELECT COUNT(id) FROM " . DB_PREFIX . "user c WHERE c.family_id=j.id) as user_count,IF ((select count(id) as is_apply from " . DB_PREFIX . "family_join as jo where jo.user_id=" . $user_id . " and jo.family_id=j.id and jo.status=0 )>0,1,IF ((select count(id) as is_apply from " . DB_PREFIX . "family_join as jo where jo.user_id=" . $user_id . " and jo.family_id=j.id and jo.status=1 )>0,2,0)) as is_apply FROM " . DB_PREFIX . "family as j left join " . DB_PREFIX . "user as u on j.user_id=u.id where j.status=1 and ( j.id = '" . $family_id . "' or j.name like '%" . $family_name . "%') limit " . $limit, true, true);
                $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "family  where status=1 and ( id = '" . $family_id . "' or name like '%" . $family_name . "%')", true, true);//家族数量
            } else {
                //默认列表
                $family_list = $GLOBALS['db']->getAll("SELECT j.id as family_id,j.logo as family_logo,j.name as family_name,j.user_id,u.nick_name,j.create_time,(SELECT COUNT(id) FROM " . DB_PREFIX . "user c WHERE c.family_id=j.id) as user_count,IF ((select count(id) as is_apply from " . DB_PREFIX . "family_join as jo where jo.user_id=" . $user_id . " and jo.family_id=j.id and jo.status=0 )>0,1,IF ((select count(id) as is_apply from " . DB_PREFIX . "family_join as jo where jo.user_id=" . $user_id . " and jo.family_id=j.id and jo.status=1 )>0,2,0)) as is_apply FROM " . DB_PREFIX . "family as j left join " . DB_PREFIX . "user as u on j.user_id=u.id where j.status=1 limit " . $limit, true, true);
                $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "family where status=1", true, true);//家族数量
            }
            foreach ($family_list as $k => $v) {
                $family_list[$k]['family_logo'] = get_spec_image($v['family_logo'], 50, 50);
                $family_list[$k]['name'] = htmlspecialchars_decode($family_list[$k]['name']);
                $family_list[$k]['nick_name'] = htmlspecialchars_decode($family_list[$k]['nick_name']);
                $family_list[$k]['create_time'] = htmlspecialchars_decode($family_list[$k]['create_time']);
            }
            if ($family_list) {
                $root['list'] = $family_list;
                $page = new Page($rs_count, $page_size);
                $root['page'] = $page->show();
                $root['rs_count'] = $rs_count;
                $root['status'] = 1;
                $root['error'] = '';
            } else {
                $root['list'] = $family_list;
                $rs_count = count($family_list);
                $page = new Page($rs_count, $page_size);
                $root['page'] = $page->show();
                $root['rs_count'] = $rs_count;
                $root['status'] = 1;
                $root['error'] = '';
            }
        }
        api_ajax_return($root);
    }

    //家族详情
    public function info()
    {
        $page = intval($_REQUEST['p']); //第几页
        $family_id = intval($_REQUEST['family_id']);//家族ID

        //家族信息
        $count = "SELECT COUNT(1) FROM " . DB_PREFIX . "user AS u WHERE u.family_id=f.id";
        $info = "id as family_id,user_id,logo as family_logo,name as family_name,create_time,manifesto as family_manifesto,memo,status,contribution,family_level,video_time,score,({$count}) as user_count";
        $family_info = $GLOBALS['db']->getRow("SELECT " . $info . " FROM " . DB_PREFIX . "family f WHERE id=" . $family_id, true, true);
        if($family_info['status'] != 1 && $family_info['user_id'] != $GLOBALS['user_info']['id'])
        {
            return app_redirect(url('index#index'));
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $family_user = $user_redis->getRow_db($family_info['user_id'], array('nick_name'));
        $family_info['user'] = $family_user;

        if ($family_info["contribution"] > 10000) {
            $family_info["contribution"] = round($family_info["contribution"] / 10000, 2);
            $family_info["contribution"] .= "万";
        }

        if ($family_info['video_time'] > 86400) {
            $family_info["video_time"] = intval($family_info["video_time"] / 86400);
            $family_info["video_time"] .= "天";
        } else if ($family_info['video_time'] > 3600) {
            $family_info["video_time"] = intval($family_info["video_time"] / 3600);
            $family_info["video_time"] .= "小时";
        } else if ($family_info['video_time'] > 60) {
            $family_info["video_time"] = intval($family_info["video_time"] / 60);
            $family_info["video_time"] .= "分钟";
        } else {
            $family_info["video_time"] .= "秒";
        }

        $level = family_level_syn($family_info);
        $level['u_score'] = $family_info['score'];
        $level['level_ico'] = get_domain() . '/public/images/rank/rank_' . $level["level"] . '.png';
        $p_score = $family_info['score'] - $level['score'];
        if ($p_score < 0) {
            $p_score = 0;
        }
        if(empty($level['next_level'])) {
            $level['progress'] = 100;
        } else {
            $next_level = $level['next_level'];
            $level['next_level_ico'] = get_domain() . '/public/images/rank/rank_' . $next_level["level"] . '.png';
            $level['next_level'] = $next_level['level'];
            $level['progress'] = intval($p_score / ($next_level['score'] - $level['score']) * 100);
        }

        //家族直播信息
        $count = $GLOBALS['db']->getOne("SELECT COUNT(1) as video_count FROM " . DB_PREFIX . "video as v," . DB_PREFIX . "user as u WHERE v.user_id=u.id and u.family_id=" . $family_id, true, true);
        $page_size = 20;//分页数量
        if ($page == 0) {
            $page = 1;
        }
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        $field = "a.id as room_id,a.sort_num,a.group_id,a.user_id,a.city,a.title,a.cate_id,a.live_in,a.video_type,a.room_type,(a.robot_num + a.virtual_watch_number + a.watch_number) as watch_number,a.head_image,a.live_image,a.thumb_head_image,a.xpoint,a.ypoint,b.v_type,b.v_icon,b.nick_name,b.user_level ";
        $list = $GLOBALS['db']->getAll("SELECT " . $field . " FROM " . DB_PREFIX . "video as a," . DB_PREFIX . "user as b WHERE a.live_in in (1,3) and a.user_id=b.id and b.family_id=" . $family_id . " limit " . $limit, true, true);//家族直播信息
        foreach ($list as $k => $v) {
            $list[$k]['head_image'] = get_spec_image($v['head_image']);
            if($list[$k]['thumb_head_image']==''){
                $list[$k]['thumb_head_image']=get_spec_image($v['head_image']);
            }else{
                $list[$k]['thumb_head_image'] = get_spec_image($v['thumb_head_image']);
            }
            if(empty($v['live_image'])) {
                $list[$k]['live_image'] = $list[$k]['head_image'];
            }else{
                $list[$k]['live_image']=get_spec_image($v['live_image'],320,180,1);
            }
            $list[$k]['video_url'] = get_video_url($v['room_id'], $v['live_in']);
        }

        //推荐主播
        $is_recommend = $GLOBALS['db']->getAll("SELECT " . $field . " FROM " . DB_PREFIX . "video as a," . DB_PREFIX . "user as b WHERE a.user_id=b.id and b.family_id= " . $family_id . " and a.is_recommend = 1  and a.live_in in (1,3) order by a.is_recommend desc,a.sort_num desc,a.sort desc limit 0,6", true, true);//家族直播信息
        foreach ($is_recommend as $k => $v) {
            $is_recommend[$k]['head_image'] = get_spec_image($v['head_image']);
            if($is_recommend[$k]['thumb_head_image']==''){
                $is_recommend[$k]['thumb_head_image']=get_spec_image($v['head_image']);
            }else{
                $is_recommend[$k]['thumb_head_image'] = get_spec_image($v['thumb_head_image']);
            }
            $is_recommend[$k]['video_url'] = get_video_url($v['room_id'], $v['live_in']);
        }
        //家族数量
        $ranking = load_auto_cache("family_rank_all", array('sort' => 'user_count', 'limit' => 10));
        $page_m = new Page($count, $page_size);   //初始化分页对象
        api_ajax_return(array(
            'family_info' => $family_info,//家族信息
            'level' => $level, // 等级信息
            'is_recommend' => $is_recommend,//家族推荐直播
            'list' => $list,//家族直播信息
            'ranking' => $ranking,//家族数量
            'status' => 1,//状态
            'page' => $page_m->show(),//分页输出
        ));

    }

    // 创建家族（弹窗）
    function edit()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";// es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $res = $GLOBALS['db']->getRow("SELECT id,logo,name,manifesto,status FROM " . DB_PREFIX . "family WHERE user_id = " . $user_id);
            if (empty($res)) {
                $root['error'] = "";
                $root['status'] = 0;
                $root['family_info'] = $res;
            } else {
                $root['error'] = "";
                $root['status'] = 1;
                $root['family_info'] = array(
                    'family_logo' => replace_public($res['logo']),//get_spec_image($jiainfo['family_logo'],150,150);
                    'family_name' => strim($res['name']),
                    'family_manifesto' => strim($res['manifesto']),
                    'family_id' => intval($res['id']),
                    'status' => $res['status'],
                );
            }
        }
        api_ajax_return($root);
    }

    //创建家族
    function create()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";// es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        } else {
            $data['user_id'] = intval($GLOBALS['user_info']['id']);//创建人id
            $data['logo'] = strim($_REQUEST['family_logo']);//家族logo
            $data['name'] = strim($_REQUEST['family_name']);//家族名称
            $data['manifesto'] = strim($_REQUEST['family_manifesto']);//家族宣言
            if ($data['name'] == '' || $data['manifesto'] == '' || $data['logo'] == '') {
                $root['error'] = "请完善信息";// es_session::id();
                $root['status'] = 0;
                api_ajax_return($root);
            }
            if ($_REQUEST['family_notice']) {
                $data['notice'] = strim($_REQUEST['family_notice']);//家族公告
            }
            $data['status'] = 0;//状态 0：未审核，1：审核通过，2：拒绝通过
            $data['create_time'] = NOW_TIME;//创建时间
            $data['memo'] = "无";//备注
            //
            $data['create_date'] = date('Y-m-d', NOW_TIME);
            $data['create_y'] = date('Y');
            $data['create_m'] = date('m');
            $data['create_d'] = date('d');
            $data['create_w'] = date('W');
            if(strlen($data['name']) > 48){
                    api_ajax_return(array(
                        'status' => '0',
                        'error' => '家族名称限制15字以内'
                    ));
            }

            $user = $GLOBALS['db']->getRow("SELECT family_id,family_chieftain FROM " . DB_PREFIX . "user WHERE id =" . $data['user_id']);
            $family_status = $GLOBALS['db']->getRow("SELECT status FROM " . DB_PREFIX . "family WHERE id =" . $user['family_id'] . " and user_id=" . $data['user_id']);
            if ($user['family_id'] > 0) {
                if ($family_status['status'] == 0) {
                    $root['error'] = '您已创建的家族正在审核';
                }
                $root['status'] = 0;
            } else {
                // 名称校验
                $jia_name = $GLOBALS['db']->getRow("SELECT count(id) as jia_count FROM " . DB_PREFIX . "family WHERE name = '" . $data['name'] . "' and (status=1 or status=0)");
                if ($jia_name['jia_count'] > 0) {
                    $root['error'] = '家族名已存在';
                    $root['status'] = 0;
                } else {
                    $is_refuse = $GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "family WHERE user_id = '" . $data['user_id'] . "' and status=2 ");
                    if ($is_refuse > 0) {
                        $update = $GLOBALS['db']->autoExecute(DB_PREFIX . "family", $data, $mode = 'UPDATE', 'id=' . $is_refuse); //如果是被拒绝状态重新编辑更新
                        if ($update) {
                            $root['error'] = '家族创建成功';
                            $root['status'] = 1;
                            $root['family_id'] = $is_refuse['id'];
                        } else {
                            $root['error'] = '家族创建失败';
                            $root['status'] = 0;
                        }
                    } else {
                        $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "family", $data, "INSERT");//插入数据
                        if ($res) {
//						$jia_info=$GLOBALS['db']->getRow("SELECT id FROM ".DB_PREFIX."family WHERE user_id = ".$data['user_id']." AND name=".$data['name']);//查询创建成功的家族编号
                            $family_id = $GLOBALS['db']->insert_id();
                            if ($family_id) {
                                $userdata['family_id'] = $family_id;
                                $userdata['family_chieftain'] = 1;
                            }
                            $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $userdata, $mode = 'UPDATE', "id=" . $data['user_id']);
//						$GLOBALS['db']->query("update ".DB_PREFIX."user set family_id=".$jia_info['id'].",family_chieftain=1 where id=".$data['user_id']);
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                            $user_redis = new UserRedisService();
                            $user_redis->update_db($data['user_id'], array('family_id' => $family_id, 'family_chieftain' => 1));
                            //更新申请表
                            $apple_count = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "family_join WHERE user_id = " . $data['user_id']);
                            if ($apple_count > 0) {
                                $join['status'] = 2;
                                $GLOBALS['db']->autoExecute(DB_PREFIX . "family_join", $join, $mode = 'UPDATE', "user_id=" . $data['user_id']);
                            }
                            $user_info = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "user WHERE id=" . $data['user_id'] . " and family_chieftain=0", true, true);
                            $root['error'] = '家族创建成功';
                            $root['status'] = 1;
                            $root['family_id'] = $family_id;
                            es_session::set("user_info", $user_info);
                            $GLOBALS['user_info'] = $user_info;
                        } else {
                            $root['error'] = '家族创建失败';
                            $root['status'] = 0;
                        }
                    }
                }
            }
        }
        api_ajax_return($root);
    }

//修改家族信息
    function save()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";// es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $jid = intval($_REQUEST['family_id']);
            $res = $GLOBALS['db']->getRow("SELECT logo,notice,manifesto,status FROM " . DB_PREFIX . "family WHERE id = " . $jid,true,true);
            if (!empty($_REQUEST['family_logo'])) {
                $data['logo'] = strim($_REQUEST['family_logo']);
            }
            if (!empty($_REQUEST['family_notice'])) {
                //家族公告
                if (strim($_REQUEST['family_notice']) != $res['notice']) {
                    $data['notice'] = strim($_REQUEST['family_notice']);
                    $data['notice'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {
                        return '';
                    }, $data['notice']);
                } else {
                    $data['notice'] = $res['notice'];
                }
            }

            if (!empty($_REQUEST['family_manifesto'])) {
                if (strim($_REQUEST['family_manifesto']) != $res['manifesto']) {
                    $data['manifesto'] = strim($_REQUEST['family_manifesto']);//家族宣言
                    $data['manifesto'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {
                        return '';
                    }, $data['manifesto']);
                } else {
                    $data['manifesto'] = $res['manifesto'];
                }
            }

            $user_id = intval($GLOBALS['user_info']['id']);//用户ID
            $user = $GLOBALS['db']->getRow("SELECT family_id,family_chieftain FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
            $family_chieftain = $user['family_chieftain'];//族长标志：0不是族长。1是组长
            if ($res['status'] == 0) {
                $root['error'] = '家族正在审核中不能修改';
                $root['status'] = 0;
                $root['family_id'] = $jid;
            } else {
                if ($family_chieftain != 1) {//判断是否为族长
                    $root['error'] = '没有权限';
                    $root['status'] = 0;
                    $root['family_id'] = $jid;
                } else {
                    $is_refuse = $GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "family WHERE user_id = " . $user_id . " and status=2 ");
                    if ($is_refuse > 0) {
                        $data['name'] = strim($_REQUEST['family_name']);
                        $data['name'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {
                            return '';
                        }, $data['name']);
                        $jia_name = $GLOBALS['db']->getRow("SELECT count(id) as jia_count FROM " . DB_PREFIX . "family WHERE name = '" . $data['name'] . "' and status=1 ");
                        if ($jia_name['jia_count'] > 0) {
                            $root['error'] = '家族名已存在';
                            $root['status'] = 0;
                        } else {
                            $data['status'] = 0;
                            $update = $GLOBALS['db']->autoExecute(DB_PREFIX . "family", $data, $mode = 'UPDATE', 'id=' . $is_refuse); //如果是被拒绝状态重新编辑更新
                            if ($update) {
                                $root['error'] = '家族信息修改成功';
                                $root['status'] = 1;
                                $root['family_id'] = $is_refuse;
                            } else {
                                $root['error'] = '家族信息修改失败';
                                $root['status'] = 0;
                            }
                        }

                    } else {
                        $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "family", $data, "UPDATE", 'id=' . $jid);//更新信息
                        if ($res) {
                            $root['error'] = '家族信息修改成功';
                            $root['status'] = 1;
                            $root['family_id'] = $jid;
                        } else {
                            $root['error'] = '家族信息修改失败';
                            $root['status'] = 0;
                            $root['family_id'] = $jid;
                        }
                    }
                }
            }
            api_ajax_return($root);
        }
    }
}
