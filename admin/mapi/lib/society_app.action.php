<?php
class society_appModule extends baseModule
{
    //获取登录用户的ID
    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            ajax_return(array(
                'status' => 0,
                'error' => '未登录'
            ));
        }
        return $user_id;
    }

    //公会详情
    public function society_details()
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        if ($society_id != 0) {
            //查询公会信息
            $sql2 = "select s.id,s.logo,s.manifesto,s.name,u.nick_name,u.id as uid,s.user_id,s.status,s.society_code,u.province,u.city,IF(u.luck_num=0,u.id,u.luck_num) as luck_id from " . DB_PREFIX . "society s inner join " . DB_PREFIX . "user u on s.user_id=u.id where s.id=" . $society_id . ";";
            $res2 = $GLOBALS['db']->getRow($sql2);
            if (!empty($res2)) {
                $root['society_id'] = intval($res2['id']);
                $root['society_image'] = get_spec_image($res2['logo']);
                $root['society_name'] = ($res2['name']);
                $root['society_explain'] = ($res2['manifesto']);
                $root['society_chairman'] = ($res2['nick_name']);
                //判断是否显示公会邀请码
                $m_config = load_auto_cache("m_config");
                if ($m_config['open_society_code'] == 1 && $res2['status'] == 1) {
                    $root['open_society_code'] = 1;
                } else {
                    $root['open_society_code'] = 0;
                }
                $root['society_code'] = $res2['society_code'];
                $root['user_count'] = intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user where society_id=" . $res2['id'] . " and is_authentication=2;"));
                $root['fans_count'] = intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user where society_id=" . $res2['id'] . " and is_authentication!=2;"));
                //$root['apply_count']      = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."society_apply where society_id=".$res2['id'].";"));
                $root['join_count'] = intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "society_apply where society_id=" . $res2['id'] . " and apply_type=0 and status=0;"));
                $root['out_count'] = intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "society_apply where society_id=" . $res2['id'] . " and apply_type=1 and status=3;"));
                $root['gh_status'] = intval($res2['status']);
            } else {
                $root['status'] = 0;
                $root['error'] = "公会信息获取失败";
            }
            //判断访问用户的身份
            $root['type'] = $this->member_identity($user_id, $res2['uid'], $society_id);

            $root['province'] = $res2['province'] ? $res2['province'] : '火星';
            $root['city'] = $res2['city'] ? $res2['city'] : '火星';
            $root['luck_id'] = $res2['luck_id']; //靓号

            $status = intval($_REQUEST['society_status'] ? $_REQUEST['society_status'] : 0); //请求列表ID  0公会主播，1公会粉丝，2成员申请
            $page = $_REQUEST['page'] ? $_REQUEST['page'] : 1; //当前页
            $page_size = 20; //分页数量

            if ($status == 0) {
//公会主播列表
                $root = $this->anchor_fans($root, $society_id, 0, $page, $page_size);
            } elseif ($status == 1) {
//公会粉丝列表
                $root = $this->anchor_fans($root, $society_id, 1, $page, $page_size);
            } elseif ($status == 2) {
//加入申请列表
                $root = $this->out_join($root, $society_id, 0, 1, $page, $page_size);
            } elseif ($status == 3) {
//退出申请列表
                $root = $this->out_join($root, $society_id, 1, 4, $page, $page_size);
            } else {
                $root['status'] = 0;
                $root['error'] = "列表数据获取失败";
            }
        } else {
            $root['status'] = 0;
            $root['error'] = "公会ID获取失败";
        }
        ajax_return($root);
    }

    //-------------判断成员身份-------------
    /**
     * @ param int $user_id     登录用户ID
     * @ param int $chairman_id 会长ID
     * @ param int $society_id  公会ID
     */
    public function member_identity($user_id, $chairman_id, $society_id)
    {
        //获取用户的公会ID
        $sql = "select society_id from " . DB_PREFIX . "user where id=" . $user_id . ";";
        $res = $GLOBALS['db']->getOne($sql);
        if ($user_id == $chairman_id) {
            $identity = 1; //会长
        } elseif ($res == $society_id) {
            $sql2 = "select id from " . DB_PREFIX . "society_apply where user_id=" . $user_id . " and society_id=" . $society_id . " and apply_type=1 and status=3;";
            $res2 = $GLOBALS['db']->getOne($sql2);
            if (empty($res2)) {
                $identity = 0; //会员
            } else {
                $identity = 5; //申请退出公会人员
            }
        } else {
            if ($res != 0) {
                $identity = 2; //其他公会成员
            } else {
                $sql4 = "select id from " . DB_PREFIX . "society_apply where user_id=" . $user_id . " and society_id=" . $society_id . " and apply_type=0 and status=0;";
                $res4 = $GLOBALS['db']->getOne($sql4);
                if (empty($res4)) {
                    $identity = 3; //无公会人员
                } else {
                    $identity = 4; //申请入会人员
                }

            }
        }
        return $identity;
    }

    //-------------公会主播 与 公会粉丝  列表-------------
    /**
     * @ param array $root          接收的变量
     * @ param int $society_id      公会ID
     * @ param int $authentication  是否认证:1非认证2认证
     * @ param int $page            当前页
     * @ param int $page_size       显示页
     */
    public function anchor_fans($root, $society_id, $authentication, $page, $page_size)
    {
        //获取总条数
        if ($authentication == 0) {
//已认证
            $count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user u where is_authentication=2 and society_id=" . $society_id, true, true);
        } else {
//非认证（未认证、待认证、认证不通过）
            $count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user u where is_authentication!=2 and society_id=" . $society_id, true, true);
        }

        $page_total = ceil($count / $page_size); //总页数
        $limit = (($page - 1) * $page_size) . "," . $page_size; //分页

        //该公会成员信息
        if ($authentication == 0) {
//公会主播
            $is_aut = '=';
        } else {
//公会粉丝
            //$sql3 = "select is_authentication,society_chieftain,id,head_image,nick_name,user_level,sex from ".DB_PREFIX."user where is_authentication!=2 and society_id=".$society_id;
            $is_aut = '!=';
        }

        $sql3 = "select * from (select v.room_type,u.is_authentication,u.society_chieftain,u.id,u.head_image,u.nick_name,u.user_level,u.sex,v.live_in,v.id as vid,v.group_id,u.province,u.city,IF(u.luck_num=0,u.id,u.luck_num) as luck_id from " . DB_PREFIX . "user u left join " . DB_PREFIX . "video v on u.id=v.user_id where u.is_authentication" . $is_aut . "2 and u.society_id=" . $society_id . " ORDER BY FIND_IN_SET(v.live_in,1) DESC) as t group by id order by society_chieftain DESC,live_in DESC limit " . $limit . ";";
        $res3 = $GLOBALS['db']->getAll($sql3);
        if (empty($res3)) {
            $root['list'] = [];
        } else {
            foreach ($res3 as $key3 => $val3) {
                if ($val3['is_authentication'] == 2) {
//判断是否认证
                    $root['list'][$key3]['is_authentication'] = 1;
                } else {
                    $root['list'][$key3]['is_authentication'] = 0;
                }
                $root['list'][$key3]['user_status'] = 1;
                $root['list'][$key3]['user_id'] = intval($val3['id']);
                $root['list'][$key3]['user_image'] = get_spec_image($val3['head_image']);
                $root['list'][$key3]['user_name'] = ($val3['nick_name']);
                if ($val3['society_chieftain'] == 1) {
//判断成员职位  0会员，1会长
                    $root['list'][$key3]['user_position'] = 1;
                } else {
                    $root['list'][$key3]['user_position'] = 0;
                }
                $root['list'][$key3]['user_lv'] = intval($val3['user_level']);
                $root['list'][$key3]['user_sex'] = intval($val3['sex']);

                if ($val3['live_in'] == 1 && $val3['room_type'] != 1) {
//判断是否在直播
                    $root['list'][$key3]['live_in'] = intval($val3['live_in']);
                    $root['list'][$key3]['room_id'] = intval($val3['vid']);
                    $root['list'][$key3]['group_id'] = intval($val3['group_id']);
                } else {
                    $root['list'][$key3]['live_in'] = 0;
                    $root['list'][$key3]['room_id'] = 0;
                    $root['list'][$key3]['group_id'] = 0;
                }
                $root['list'][$key3]['province'] = $val3['province'] ? $val3['province'] : '火星';
                $root['list'][$key3]['city'] = $val3['city'] ? $val3['city'] : '火星';
                $root['list'][$key3]['luck_id'] = $val3['luck_id']; //靓号
            }
        }

        $has_next = ($count > $page * $page_size) ? '1' : '0'; //是否有下一页
        $root['page'] = array('page' => $page, 'page_total' => $page_total, 'has_next' => $has_next);
        return $root;
    }

    //-------------加入公会申请  与退出公会申请  列表-------------
    /**
     * @ param array $root      接收的变量
     * @ param int $society_id  公会ID
     * @ param int $apply_type  申请状态：0加入申请1退出申请
     * @ param int $s_status    区分加入成功与退会成功：1加入成功4退会成功
     * @ param int $page        当前页
     * @ param int $page_size   显示页
     */
    public function out_join($root, $society_id, $apply_type, $s_status, $page, $page_size)
    {
        if ($root['type'] == 1) {
//会长才可以查看
            //获取总条数
            $count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "society_apply s inner join " . DB_PREFIX . "user u on s.user_id=u.id where s.society_id=" . $society_id . " and s.apply_type=" . $apply_type . " and s.status !=" . $s_status . ";", true, true);
            $page_total = ceil($count / $page_size); //总页数
            $limit = (($page - 1) * $page_size) . "," . $page_size; //分页

            //查询公会申请表
            //$sql5 = "select s.status,s.user_id,u.head_image,u.nick_name,u.user_level,u.sex,u.is_authentication,v.live_in,v.id as vid,v.group_id from ".DB_PREFIX."society_apply s inner join ".DB_PREFIX."user u on s.user_id=u.id left join ".DB_PREFIX."video v on u.id=v.user_id where s.society_id=".$society_id." and s.apply_type=".$apply_type." and s.status !=".$s_status." limit ".$limit.";";
            $sql5 = "select * from (select v.room_type,v.live_in,v.id as vid,v.group_id,s.status,s.user_id,s.head_image,s.nick_name,s.user_level,s.sex,s.is_authentication from (select s.status,s.user_id,u.head_image,u.nick_name,u.user_level,u.sex,u.is_authentication from fanwe_society_apply s inner join fanwe_user u on s.user_id=u.id where s.society_id=$society_id and s.status !=$s_status and s.apply_type=$apply_type) as s left join fanwe_video v on s.user_id=v.user_id order by v.live_in desc ) as res group by res.user_id limit $limit";
            $res5 = $GLOBALS['db']->getAll($sql5);
            if (empty($res5)) {
                $root['list'] = [];
            } else {
                $list = array();
                foreach ($res5 as $key5 => $val5) {
                    $list['user_status'] = intval($val5['status']);
                    $list['user_id'] = intval($val5['user_id']);
                    $list['user_image'] = get_spec_image($val5['head_image']);
                    $list['user_name'] = ($val5['nick_name']);
                    $list['user_position'] = 3;
                    $list['user_sex'] = intval($val5['sex']);
                    $list['user_lv'] = intval($val5['user_level']);
                    if ($val5['live_in'] == 1 && $val5['room_type'] != 1) {
                        $list['live_in'] = intval($val5['live_in']);
                        $list['room_id'] = intval($val5['vid']);
                        $list['group_id'] = intval($val5['group_id']);
                    } else {
                        $list['live_in'] = 0;
                        $list['room_id'] = 0;
                        $list['group_id'] = 0;
                    }
                    if ($val5['is_authentication'] == 2) {
//判断是否认证
                        $list['is_authentication'] = 1;
                    } else {
                        $list['is_authentication'] = 0;
                    }
                    $root['list'][] = $list;
                }

            }
            $has_next = ($count > $page * $page_size) ? '1' : '0'; //是否有下一页
            $root['page'] = array('page' => $page, 'page_total' => $page_total, 'has_next' => $has_next);
        } else {
            $root['status'] = 0;
            $root['error'] = "你不是会长无法显示加入申请列表";
        }
        return $root;
    }

    //判断操作人员身份
    //公会ID，操作人员ID
    public function judge_user($society_id, $user_id)
    {
        //查询公会信息
        $sql2 = "select s.id,s.logo,s.manifesto,s.name,u.nick_name,u.id as uid,s.user_id from " . DB_PREFIX . "society s inner join " . DB_PREFIX . "user u on s.user_id=u.id where s.id=" . $society_id . ";";
        $res2 = $GLOBALS['db']->getRow($sql2);
        if (!empty($res2)) {
            //获取用户的公会ID
            $sql = "select society_id from " . DB_PREFIX . "user where id=" . $user_id . ";";
            $res = $GLOBALS['db']->getOne($sql);
            //判断访问公会人员的身份
            $dateR = '';
            if ($res == 0) {
//未加入公会成员
                $dateR = 0;
            } elseif ($res == $society_id) {
//本公会成员
                if ($user_id == $res2['uid']) { //会长
                    $dateR = 2;
                } else {
//会员
                    $dateR = 1;
                }
            } else {
//其他公会成员
                $dateR = 3;
            }
        } else {
            $dateR = 4;
        }
        return $dateR;
    }

    //入会和退会的数据写入
    //等人用户ID，公会ID，0入会1退会
    public function outJoinR($user_id, $society_id, $type)
    {
        $root['status'] = 1;
        $root['error'] = "";
        $oTime = time(); //获取当前时间戳
        //先查询一下申请是否已存在
        if ($type == 0) {
//入会
            $res = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=" . $user_id . " and society_id=" . $society_id . ";");
            if (empty($res)) {
                $res1 = $GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply', array('society_id' => $society_id, 'user_id' => $user_id, 'create_time' => $oTime, 'apply_type' => 0, 'status' => 0));
            }
        } else {
//退会
            $res = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=" . $user_id . " and status =1;");
            if (!empty($res)) {
                $res1 = $GLOBALS['db']->query("update " . DB_PREFIX . "society_apply set apply_type=1,status=3,deal_time=" . $oTime . " where user_id=" . $user_id . " and society_id=" . $society_id);
            }
        }
        if ($res1) {
            $root['error'] = "申请已提交,等待审核";
        }

        return $root;
    }

    //入会和退会 操作
    //操作用户的id，公会id，1退会0入会操作
    public function op_outJoin($user_id, $society_id, $type)
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );
        if ($society_id != 0) {
            //判断是否开启车行定制 - 用户是否有权限加入公会
            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                $user = $GLOBALS['db']->getOne("SELECT open_sociaty FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
                if (intval($user) == 1) {
                    $root['error'] = "您没有权限申请加入公会，请联系客服";
                }
            }
            $dateR = $this->judge_user($society_id, $user_id); //身份校验
            if ($type == 0) {
//入会申请
                if ($dateR == 0) {
                    $root = $this->outJoinR($user_id, $society_id, $type);
                } elseif ($dateR == 3) {
                    $root['error'] = "抱歉您已经有公会了，无法申请加入";
                } else {
                    $root['error'] = "您已经是本公会成员了，无需申请";
                }
            } elseif ($type == 1) {
//退会申请
                if ($dateR == 1) {
                    $root = $this->outJoinR($user_id, $society_id, $type);
                } elseif ($dateR == 2) {
                    $root['error'] = "抱歉您是会长，无法申请退出";
                } else {
                    $root['error'] = "您不是本公会成员，无法申请";
                }
            }

        } else {
            $root['status'] = 0;
            $root['error'] = "公会ID获取失败";
        }
        return $root;
    }

    //申请加入公会
    public function society_join()
    {
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        $root = $this->op_outJoin($user_id, $society_id, 0); //申请操作
        ajax_return($root);
    }

    //申请退出公会
    public function society_out()
    {
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        $root = $this->op_outJoin($user_id, $society_id, 1); //退会操作
        ajax_return($root);
    }

    //会长操作入会和退会
    //公会ID，被操作人员ID，0入会1退会
    public function President($society_id, $user_id, $type)
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );
        $dateR = $this->judge_user($society_id, $user_id); //身份校验
        if ($dateR == 2) {
            $applyFor_id = intval($_REQUEST['applyFor_id']); //被操作人员id
            if ($applyFor_id != 0) {
                $is_agree = intval($_REQUEST['is_agree']); //是否同意
                if ($is_agree == 1) {
//同意
                    if ($type == 0) { //入会
                        //判断该成员是否有公会
                        $res1 = $GLOBALS['db']->getOne("select society_id from " . DB_PREFIX . "user where id=" . $applyFor_id . ";");
                        if (empty($res1)) {
                            //查询是否已经被拒绝，防止卡顿现象导致操作异常
                            $res2 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=$applyFor_id and society_id=$society_id");
                            if (!empty($res2)) {
                                $status = 1;
                                //删除其他公会申请
                                $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=" . $applyFor_id . " and society_id!=" . $society_id);
                                $GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply', array('status' => 1), 'UPDATE', 'user_id=' . $applyFor_id);
                                $GLOBALS['db']->autoExecute(DB_PREFIX . 'user', array('society_id' => $society_id), 'UPDATE', "id=" . $applyFor_id . ";");
                                //更新redis
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                                $user_redis = new UserRedisService();
                                $user_redis->update_db($applyFor_id, array('society_id' => $society_id));
                                //公会总人数+1
                                $update_society = "UPDATE " . DB_PREFIX . "society set user_count=user_count+1 where id=" . $society_id;
                                $GLOBALS['db']->query($update_society);

                                //写入用户日志
                                $data = array();
                                $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                                $param['type'] = 12; //类型12表示公会相关操作
                                $log_msg = '成功加入【' . $society_name . '】公会';
                                account_log_com($data, $applyFor_id, $log_msg, $param);

                                $root['error'] = "申请已通过";
                            } else {
                                $root['error'] = "请勿频繁操作";
                            }

                        } else {
                            $root['error'] = "操作失败，该成员已有公会";
                        }

                    } else {
//退会
                        //防止已经被拒绝退会
                        $res2 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where society_id=$society_id and user_id=$applyFor_id and status=3 and apply_type=1");
                        if (!empty($res2)) {
                            $status = 4;
                            $GLOBALS['db']->autoExecute(DB_PREFIX . 'user', array('society_id' => 0), 'UPDATE', "id=" . $applyFor_id . ";");
                            //更新redis
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                            $user_redis = new UserRedisService();
                            $user_redis->update_db($applyFor_id, array('society_id' => 0));
                            //公会总人数-1
                            $update_society = "UPDATE " . DB_PREFIX . "society set user_count=user_count-1 where id=" . $society_id;
                            $GLOBALS['db']->query($update_society);
                            $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=" . $applyFor_id);

                            //写入用户日志
                            $data = array();
                            $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                            $param['type'] = 12; //类型12表示公会相关操作
                            $log_msg = '成功退出【' . $society_name . '】公会';
                            account_log_com($data, $applyFor_id, $log_msg, $param);

                            $root['error'] = "申请已通过";
                        } else {
                            $root['error'] = "请勿频繁操作";
                        }
                    }
                    //$res = $GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply', array('status' => $status), 'UPDATE', "society_id=".$society_id." and user_id=".$applyFor_id." and apply_type=".$type.";");
                    //$GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=".$applyFor_id);

                } elseif ($is_agree == 0) {
//拒绝
                    if ($type == 1) { //退会
                        //查询是否已退会，防止卡顿现象导致操作异常
                        $res2 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=$applyFor_id and society_id=$society_id");
                        if (!empty($res2)) {
                            $status = 2;
                            $res = $GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply', array('status' => 1, 'apply_type' => 0), 'UPDATE', "society_id=" . $society_id . " and user_id=" . $applyFor_id . ";");

                            //写入用户日志
                            $data = array();
                            $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                            $param['type'] = 12; //类型12表示公会相关操作
                            $log_msg = '退出【' . $society_name . '】公会被拒绝';
                            account_log_com($data, $applyFor_id, $log_msg, $param);
                        } else {
                            $root['error'] = "请勿频繁操作";
                        }
                    } else {
//入会
                        //查询是否已同意入会，防止卡顿现象导致操作异常
                        $res2 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=$applyFor_id and society_id=$society_id and status=1");
                        if (empty($res2)) {
                            $status = 5;
                            $res = $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=" . $applyFor_id . " and society_id=" . $society_id);

                            //写入用户日志
                            $data = array();
                            $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                            $param['type'] = 12; //类型12表示公会相关操作
                            $log_msg = '加入【' . $society_name . '】公会被拒绝';
                            account_log_com($data, $applyFor_id, $log_msg, $param);
                        } else {
                            $root['error'] = "请勿频繁操作";
                        }
                    }
                    //$res = $GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply', array('status' => $status), 'UPDATE', "society_id=".$society_id." and user_id=".$applyFor_id." and apply_type=".$type.";");

                    if ($res) {
                        $root['error'] = "申请被拒绝了";
                    } else {
                        $root['status'] = 0;
                        $root['error'] = "数据更新失败";
                    }
                }
            } else {
                $root['status'] = 0;
                $root['error'] = "需要操作的成员id获取失败";
            }
        } else {
            $root['status'] = 0;
            $root['error'] = "抱歉您不是会长无法操作";
        }
        return $root;
    }

    //加入公会审核（会长操作）
    public function join_check()
    {
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        $root = $this->President($society_id, $user_id, 0); //执行操作
        ajax_return($root);
    }

    //退出公会审核（会长操作）
    public function out_check()
    {
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        $root = $this->President($society_id, $user_id, 1); //执行操作
        ajax_return($root);
    }

    //踢出公会成员（会长操作）
    public function member_del()
    {
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        $dateR = $this->judge_user($society_id, $user_id); //身份校验
        if ($dateR == 2) {
//会长
            $member_id = intval($_REQUEST['member_id'] ? $_REQUEST['member_id'] : 0); //被踢成员ID
            if ($member_id != 0) {
                $society_chieftain = $GLOBALS['db']->getOne("select society_chieftain from " . DB_PREFIX . "user where id=" . $member_id);
                if ($society_chieftain == 1) {
//防止出现会长将自己踢出
                    $root['status'] = 0;
                    $root['error'] = "抱歉你是会长无法踢出自己";
                } else {
                    $res = $GLOBALS['db']->autoExecute(DB_PREFIX . 'user', array('society_id' => 0), 'UPDATE', "id=" . $member_id . ";");
                    //更新redis
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_redis->update_db($member_id, array('society_id' => 0));

                    if ($res) {
                        $res1 = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society_apply where user_id=" . $member_id . " and society_id=" . $society_id . ";");
                        if (!empty($res1)) {
//踢出公会，判断该成员是否曾经申请退会，是，同时删除
                            $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=" . $member_id . " and society_id=" . $society_id);
                        }
                        //公会总人数-1
                        $update_society = "UPDATE " . DB_PREFIX . "society set user_count=user_count-1 where id=" . $society_id;
                        $GLOBALS['db']->query($update_society);

                        //写入用户日志
                        $data = array();
                        $society_name = $GLOBALS['db']->getOne("select name from " . DB_PREFIX . "society where id=" . $society_id);
                        $param['type'] = 12; //类型12表示公会相关操作
                        $log_msg = '被【' . $society_name . '】公会踢出';
                        account_log_com($data, $member_id, $log_msg, $param);

                        $root['status'] = 1;
                        $root['error'] = "该公会成员已移除";
                    } else {
                        $root['status'] = 0;
                        $root['error'] = "踢出成员数据更新失败";
                    }
                }
            } else {
                $root['status'] = 0;
                $root['error'] = "被操作成员id获取失败";
            }
        } else {
//不是会长
            $root['status'] = 0;
            $root['error'] = "抱歉您不是会长无法操作";
        }
        ajax_return($root);
    }

    //重新提交公会申请
    public function society_agree()
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );
        $user_id = self::getUserId(); //获取当前登录的用户ID
        $society_id = intval($_REQUEST['society_id'] ? $_REQUEST['society_id'] : 0); //请求公会的ID
        if ($society_id != 0) {
            $res = $GLOBALS['db']->autoExecute(DB_PREFIX . 'society', array('status' => 0), 'UPDATE', "id=" . $society_id . ";");
            if ($res) {
                $root['error'] = "提交成功等待审核";
            } else {
                $root['status'] = 0;
                $root['error'] = "更新数据失败";
            }
        } else {
            $root['status'] = 0;
            $root['error'] = "公会ID获取失败";
        }
        ajax_return($root);
    }

    //创建公会
    public function create()
    {
        $user_id = self::getUserId();
        $root = array(
            'status' => 0,
            'error' => ''
        );
        //判断是否开启车行定制 - 用户是否有权限创建公会
        if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
            $user = $GLOBALS['db']->getOne("SELECT open_sociaty FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
            if (intval($user) == 1) {
                api_ajax_return(array(
                    'status' => '0',
                    'error' => '您没有权限创建公会，请联系客服'
                ));
            }
        }
        //判断用户是否认证
        $is_authentication = $GLOBALS['db']->getOne("select is_authentication from " . DB_PREFIX . "user where id=" . $user_id);
        if ($is_authentication != 2) {
            $root['error'] = '抱歉您需要先认证才能创建公会';
            api_ajax_return($root);
        }
        $data = array();
        $data['logo'] = trim($_REQUEST['logo']);
        $data['name'] = trim($_REQUEST['name']);
        $data['name'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['name']);
        $data['manifesto'] = trim($_REQUEST['manifesto']);
        $data['manifesto'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['manifesto']);
        $data['notice'] = trim($_REQUEST['notice']);
        $data['notice'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['notice']); //过滤表情
        $data['status'] = 0;
        $data['create_time'] = NOW_TIME;
        $data['memo'] = '无';
        $data['user_count'] = 1;
        $data['user_id'] = $user_id;
        if ($data['logo'] == '') {
            $root['error'] = '公会logo不能为空';
            api_ajax_return($root);
        }
        if ($data['name'] == '') {
            $root['error'] = '公会名称不能为空';
            api_ajax_return($root);
        }
        if ($data['manifesto'] == '') {
            $root['error'] = '公会宣言不能为空';
            api_ajax_return($root);
        }

        if (strlen($data['name']) > 48) {
            api_ajax_return(array(
                'status' => '0',
                'error' => '公会名称限制15字以内'
            ));
        }

        $user = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
        $society_status = $GLOBALS['db']->getOne("SELECT status FROM " . DB_PREFIX . "society WHERE id =" . $user['society_id'] . " and user_id=" . $user_id);
        if ($user['society_id'] > 0 && $society_status != 2) {
            if ($user['society_chieftain'] == 1) {
//用户是公会长
                if ($society_status == 1) {
                    $root['error'] = '您已有创建成功的公会';
                }
                if ($society_status == 0) {
                    $root['error'] = '您已创建的公会正在审核';
                }
            } else {
//用户是公会成员
                $root['error'] = '您已加入公会，请退出后再创建';
            }
            $root['status'] = 0;
        } else {
            $name_exist = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society where name='" . $data['name'] . "' and (status=0 or status=1)");
            if ($name_exist > 0) {
                $root['error'] = '公会名已存在';
            } else {
                $data['society_code'] = substr(uniqid(), -5);
                $m_config = load_auto_cache('m_config');
                $data['refund_rate'] = $m_config['society_public_rate'];
                $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "society", $data);
                if ($res) {
                    $society_id = $GLOBALS['db']->insert_id();
                    $user_data = array();
                    if ($society_id) {
                        $user_data['society_id'] = $society_id;
                        $user_data['society_chieftain'] = 1;
                        $user_data['society_code'] = '';
                    }
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $user_data, $mode = 'UPDATE', "id=" . $data['user_id']);
                    //更新redis
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_redis->update_db($data['user_id'], array('society_id' => $society_id, 'society_chieftain' => 1));
                    //更新申请表
                    $apple_count = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "society_apply WHERE status=0 and  user_id = " . $user_id);
                    if ($apple_count > 0) {
                        $join['status'] = 2;
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "society_apply", $join, $mode = 'UPDATE', "user_id=" . $user_id);
                    }
                    //删除在其他公会的申请记录
                    $GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where user_id=" . $user_id . " and society_id!=" . $society_id);
                    //将会长信息写入公会申请表
                    $society_apply['user_id'] = $user_id;
                    $society_apply['society_id'] = $society_id;
                    $society_apply['create_time'] = time();
                    $society_apply['status'] = 1;
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "society_apply", $society_apply, $mode = 'INSERT', "user_id=" . $user_id);
                    $root['society_id'] = $society_id;
                    $root['status'] = 1;
                    $root['error'] = '公会创建成功';

                } else {
                    $root['status'] = 0;
                    $root['error'] = '公会创建失败';
                }
            }
        }

        api_ajax_return($root);
    }

    //保存公会信息
    public function save()
    {
        $user_id = self::getUserId();
        $root = array(
            'status' => 0,
            'error' => ''
        );

        $data = array();
        $data['id'] = intval($_REQUEST['id']);
        if ($data['id'] == 0) {
            $root['error'] = '公会ID不能为空';
            api_ajax_return($root);
        }

        //数据处理
        if (!empty($_REQUEST['logo'])) {
            $data['logo'] = $_REQUEST['logo'];
        }
        if (!empty($_REQUEST['name'])) {
            $data['name'] = (trim($_REQUEST['name']));
            $data['name'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['name']);
            if (strlen($data['name']) > 48) {
                api_ajax_return(array(
                    'status' => '0',
                    'error' => '公会名称限制15字以内'
                ));
            }
        }
        if (!empty($_REQUEST['manifesto'])) {
            $data['manifesto'] = (trim($_REQUEST['manifesto']));
            $data['manifesto'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['manifesto']);
        }
        $data['notice'] = trim($_REQUEST['notice']);
        $data['notice'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['notice']);

        //权限和异常判断
        $user = $GLOBALS['db']->getRow("select user_id from " . DB_PREFIX . "society where id=" . $data['id']);
        if ($user_id != $user['user_id']) {
            $root['error'] = "您没有权限";
            api_ajax_return($root);
        } else {
            $is_refuse = $GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "society WHERE user_id = " . $user_id . " and status=2 ");
            if ($is_refuse > 0) {
                if (!empty($_REQUEST['name'])) {
                    $data['name'] = (trim($_REQUEST['name']));
                    $data['name'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {return '';}, $data['name']);
                    if (strlen($data['name']) > 48) {
                        api_ajax_return(array(
                            'status' => '0',
                            'error' => '公会名称限制15字以内'
                        ));
                    }
                }

                $name_exist = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society where name='" . $data['name'] . "' and (status=0 or status=1)");
                if ($name_exist > 0) {
                    $root['error'] = '公会名已存在';
                    api_ajax_return($root);
                } else {
                    $data['status'] = 0;
                    $update = $GLOBALS['db']->autoExecute(DB_PREFIX . "society", $data, $mode = 'UPDATE', 'id=' . $is_refuse); //如果是被拒绝状态重新编辑更新
                    if ($update) {
                        $root['error'] = '公会信息修改成功';
                        $root['status'] = 1;
                        $root['society_id'] = $is_refuse;
                        api_ajax_return($root);
                    } else {
                        $root['error'] = '公会信息修改失败';
                        $root['status'] = 0;
                        api_ajax_return($root);
                    }
                }
            } else {

                $exist_name = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society where status=1 and name='%" . $data['name'] . "%'");
                if ($exist_name > 0 && $exist_name != $data['id']) {
                    $root['error'] = '公会名已存在';
                    api_ajax_return($root);
                }
                //更新数据
                $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "society", $data, 'UPDATE', 'id=' . $data['id']);
                if ($res) {
                    $root['status'] = 1;
                    $root['error'] = '公会信息修改成功';
                    $root['society_id'] = $data['id'];
                    api_ajax_return($root);
                } else {
                    $root['status'] = 0;
                    $root['error'] = '公会信息修改失败';
                    api_ajax_return($root);
                }
            }
        }
    }

}
