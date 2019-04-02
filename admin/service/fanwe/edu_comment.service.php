<?php

class edu_commentService
{

    public function comment_list($param)
    {
        $type = intval($param['type']);//评论类型：1表课堂 2机构（线下预约） 3讲师（线上约课)
        $ext_id = intval($param['id']);//关联 id,type1时表课堂，type2机构id , type3时教师id
        $page = intval($param['page']);

        $where = " reply_id = 0";
        if ($type > 0) {
            $where .= " and type=" . $type . "";
        }
        if ($ext_id > 0) {
            $where .= " and ext_id=" . $ext_id . "";
        }


        if ($page > 0) {
            $page_size = $param['page_size'];
            $limit = " limit " . $page_size * ($page - 1) . "," . $page_size;
        } else {
            if ($param['limit'] > 0) {
                $param['limit'] = intval($param['limit']);
                $limit = " limit " . $param['limit'] . " ";
            } else {
                $limit = '';
            }
        }

        $sql = "select c.id,c.content,c.user_id,c.type,c.ext_id,c.reply_id,c.reply_user_id,c.create_time,c.likes" .
            ",u.nick_name as nickname,u.head_image" .
            " from " . DB_PREFIX . "edu_comment as c " .
            " left join " . DB_PREFIX . "user as u on u.id=c.user_id " .
            " where " . $where . " order by c.id desc " . $limit . "";
        $comment_list = $GLOBALS['db']->getAll($sql);
        $comments = array();
        foreach ($comment_list as $k => $v) {
            $v['liked'] = false;
            $v['head_image'] = get_spec_image($v['head_image']);
            $v['create_time'] = pass_date($v['create_time']);
            $v['comments_reply'] = array();
            $comments[$v['id']] = $v;
            $comments_ids[] = $v['id'];
        }

        //回复
        if ($comments_ids) {
            $sql2 = "select c.id,c.content,c.user_id,c.type,c.ext_id,c.reply_id" .
                ",u.nick_name as nickname" .
                " from " . DB_PREFIX . "edu_comment as c " .
                " left join " . DB_PREFIX . "user as u on u.id=c.user_id " .
                " where reply_id >0 and reply_id in (" . implode(',', $comments_ids) . ")";
            $comments_reply = $GLOBALS['db']->getAll($sql2);
            foreach ($comments_reply as $k => $v) {
                $reply['nickname'] = $v['nickname'];
                $reply['content'] = $v['content'];
                $comments[$v['reply_id']]['comments_reply'][] = $reply;
            }
        }

        return $comments;
    }

    public function add($param)
    {
        $GLOBALS['db']->autoExecute(DB_PREFIX . 'edu_comment', $param);
        return $GLOBALS['db']->insert_id();
    }

    public function detail($id)
    {
        return $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "edu_comment where id =" . $id);
    }
}

//获取已过时间
function pass_date($time)
{
    $time_span = NOW_TIME - $time;
    if ($time_span > 3600 * 24 * 365) {
        $time_span_lang = to_date($time, "Y-m-d");
    } elseif ($time_span > 3600 * 24 * 30) {
        $time_span_lang = to_date($time, "Y-m-d");
    } elseif ($time_span > 3600 * 24) {
        //一天
        $time_span_lang = round($time_span / (3600 * 24)) . "天前";

    } elseif ($time_span > 3600) {
        //一小时
        $time_span_lang = round($time_span / (3600)) . "小时前";
    } elseif ($time_span > 60) {
        //一分
        $time_span_lang = round($time_span / (60)) . "分钟前";

    } else {
        //一秒
        $time_span_lang = "刚刚";
    }
    return $time_span_lang;
}


?>