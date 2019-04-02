<?php

class questionCModule  extends baseModule
{
	public function index(){
        $type = trim($_REQUEST['type']);
        $page      = intval($_REQUEST['page']);
        $page_size = intval($_REQUEST['page_size']);
        $page      = $page ? $page : 1;
        $page_size = $page_size ? $page_size : 20;
        $table = DB_PREFIX . 'question';
        $field = 'id,create_time,content,title,count,praise_count,is_answered';
        $where = 'is_question=1 and pid=0';
        $order = 'create_time desc';
        $limit = (($page - 1) * $page_size) . ',' . ($page_size);
        $root['type'] = $type;
        switch ($type) {
            case 'hot':
                $where .= ' and is_open=1';
                $order = 'count desc';
                break;
            case 'mine':
                $user_id = intval($GLOBALS['user_info']['id']);
                $where .=  ' and question_user_id=' . $user_id;
                break;
            case 'new':
                $where .= ' and is_open=1';
            default:
                $type = 'new';
                break;
        }
        $sql = "SELECT $field FROM $table WHERE $where ORDER BY $order LIMIT $limit";
        $question_list = $this->time_change($GLOBALS['db']->getAll($sql));
        $sql = "SELECT count(1) FROM $table WHERE $where";
        $count = $GLOBALS['db']->getOne($sql);
        $total_page = ceil($count / $page_size);
        $status = 1;
        $error = '';
        $page_title = '企业特诊';
        $head_img = $GLOBALS['db']->getOne("select head_image from ".DB_PREFIX."user where id=1");
        $head_img = get_spec_image($head_img);
        api_ajax_return(compact('status','error','type','question_list','total_page','page_title','head_img'));
    }

    public function detail(){
        $root = array();
        $id = intval($_REQUEST['id']);
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($id == 0){
            $root['error'] = "id不能为空";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        //取问答的详情
        $field = "q.id,q.answer_user_id,q.question_user_id,q.create_time,q.content,q.title,q.count,q.praise_count,u1.nick_name
                as answer_user_name,u1.v_explain,u2.nick_name as question_user_name";
        $table = DB_PREFIX."question as q ";
        $left_join1 = DB_PREFIX."user as u1 on q.answer_user_id=u1.id ";
        $left_join2 = DB_PREFIX."user as u2 on q.question_user_id=u2.id where q.id=".$id;
        $sql = "select $field from $table left join $left_join1 left join $left_join2";
        $data = $GLOBALS['db']->getRow($sql);
        $data['create_time'] = $this->row_time_change($data['create_time']);

        //取问答回复列表，用到三个left join，分别取出回复者昵称，被回复者昵称以及当前用户是否点赞了这条评论
        $field_answer = "q.*,u1.nick_name as answer_user_name,u2.nick_name as question_user_name,u1.v_explain,count(p.id) 
                         as is_praised";
        $table_answer = DB_PREFIX."question as q";
        $left_join_one = DB_PREFIX."user as u1 on q.answer_user_id=u1.id";
        $left_join_two = DB_PREFIX."user as u2 on q.question_user_id=u2.id";
        $left_join_three = DB_PREFIX."question_praise as p on (p.question_id=q.id and p.user_id=".$user_id.")";
        $where = "q.pid=".$id;
        $sql_answer = "select $field_answer from $table_answer left join $left_join_one left join $left_join_two left join 
                        $left_join_three where $where group by q.id order by create_time";

        $data['answer_list'] = $GLOBALS['db']->getAll($sql_answer);
        $data['answer_list'] = $this->time_change($data['answer_list']);//处理时间格式

        $reply_count = count($data['answer_list']);
        $data['reply_count'] = $reply_count;

        $sql_add = "update ".DB_PREFIX."question set count=count+1 where id=".$id;//阅读数加1
        $GLOBALS['db']->query($sql_add);

        $root = $data;
        $root['status'] = 1;
        $root['page_title'] = '问答详情';
        api_ajax_return($root);
    }

    public function time_change($list){
        foreach ($list as $k=>&$v){
            $time = NOW_TIME;
            $sub = $time - $v['create_time'];
            if ($sub < 3600){
                $v['create_time'] = floor($sub / 60)."分钟前";
            }elseif ($sub < 86400){
                $v['create_time'] = floor($sub /3600)."小时前";
            }elseif ($sub < 604800){
                $v['create_time'] = floor($sub /86400)."天前";
            }else{
                $v['create_time'] = date('Y-m-d',$v['create_time']);
            }
        }
        unset($v);
        return $list;
    }

    public function row_time_change($create_time){
        $time = NOW_TIME;
        $sub = $time - $create_time;
        if ($sub < 3600){
            $result = floor($sub / 60)."分钟前";
        }elseif ($sub < 86400){
            $result = floor($sub /3600)."小时前";
        }elseif ($sub < 604800){
            $result = floor($sub /86400)."天前";
        }else{
            $result = date('Y-m-d',$time);
        }

        return $result;
    }

    //在线咨询
    public function form_question(){
        $root = array();
        $root['page_title'] = '在线咨询';
        api_ajax_return($root);
    }

    //提问
    public function question(){
        $root = array();
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] =0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);//intval($GLOBALS['user_info']['id'])
            $data = array();

            $_REQUEST['question'] = filterEmoji($_REQUEST['question']);
            $_REQUEST['title'] = filterEmoji($_REQUEST['title']);

            if ($_REQUEST['question'] == ''){
                $root['status'] = 0;
                $root['error'] = "内容不能为空";
                api_ajax_return($root);
            }
            if ($_REQUEST['title'] == ''){
                $root['status'] = 0;
                $root['error'] = "标题不能为空";
                api_ajax_return($root);
            }
            $data['title'] = trim($_REQUEST['title']);
            $len = $data['title']-1;
            if ($data['title'][$len] != '?'){
                $data['title'] .= "?";
            }
            if ($_REQUEST['is_open'] == 'true'){
                $data['is_open'] = 1;
            }elseif($_REQUEST['is_open'] == 'false'){
                $data['is_open'] = 0;
            }

            $data['question_user_id'] = $user_id;
            $data['user_id'] = $user_id;
            $data['answer_user_id'] = 1;
            $data['type'] = intval($_REQUEST['type']);
            $data['content'] = trim($_REQUEST['question']);
            $data['is_question'] = 1;
            $data['create_time'] = NOW_TIME;
            $data['praise_count'] = 0;
            $data['count'] = 0;
            $data['pid'] = 0;//主问题id

            $GLOBALS['db']->autoExecute(DB_PREFIX.'question',$data,'INSERT');
            $sql_id = "select max(id) as id from ".DB_PREFIX."question";
            $qid = $GLOBALS['db']->getOne($sql_id);

            $root['status'] = 1;
            $root['id'] = $qid;
            api_ajax_return($root);
        }
    }

    //回答
    public function answer(){
        $root = array();
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] =0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);//intval($GLOBALS['user_info']['id']) 临时修改

            if ($_REQUEST['id'] == ''){
                $root['status'] = 0;
                $root['error'] = "id不能为空";
                api_ajax_return($root);
            }

            $_REQUEST['answer'] = filterEmoji($_REQUEST['answer']);

            if ($_REQUEST['answer'] == ''){
                $root['status'] = 0;
                $root['error'] = "内容不能为空";
                api_ajax_return($root);
            }

            $id = intval($_REQUEST['id']);

            $data = array();

            //取被回复者的ID和pid
            $sql_reply = "select user_id,pid from ".DB_PREFIX."question where id=".$id;
            $result_R = $GLOBALS['db']->getRow($sql_reply);

            $data['question_user_id'] = $result_R['user_id'];
            $data['answer_user_id'] = $user_id;
            $data['user_id'] = $user_id;
            $data['content'] = trim($_REQUEST['answer']);
            $data['is_question'] = 0;
            $data['create_time'] = NOW_TIME;
            $data['praise_count'] = 0;
            $data['count'] = 0;

            //主问题id,如果取出的pid为0，说明回答的问题是主问题，pid=id;否则回答的是回复，主问题需取出
            if (intval($result_R['pid']) == 0){
                $data['pid'] = $id;//主问题id
            }else{
                $data['pid'] = intval($result_R['pid']);
            }

            $GLOBALS['db']->autoExecute(DB_PREFIX.'question',$data,'INSERT');//插入回答
            $sql_id = "select max(id) as id from ".DB_PREFIX."question";
            $new_id = $GLOBALS['db']->getOne($sql_id);//取得新插入的记录id
            $GLOBALS['db']->query("update ".DB_PREFIX."question set is_answered=1 where id=".$id);//将问题设置为已回答

            $root['status'] = 1;
            $root['id'] = $new_id;
            api_ajax_return($root);
        }
    }

    //点赞
    public function praise(){
        $root = array();
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] =0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);
            $id = intval($_REQUEST['id']);

            //查询是否已经点赞过了
            $sql_get = "select id from ".DB_PREFIX."question_praise where user_id=".$user_id." and question_id=".$id;
            $praise_id = $GLOBALS['db']->getOne($sql_get);

            //取主问题id
            $sql_pid = "select pid from ".DB_PREFIX."question where id=".$id;
            $pid = $GLOBALS['db']->getOne($sql_pid);

            if (intval($praise_id) > 0){
                //如果已经赞过了，就取消点赞，从点赞表中删除
                $sql_del = "delete from ".DB_PREFIX."question_praise where id=".$praise_id;
                $GLOBALS['db']->query($sql_del);
                //问题点赞数减1
                $sql_sub = "update ".DB_PREFIX."question set praise_count=IF(praise_count<1,0,praise_count-1) where id=".$pid;
                $GLOBALS['db']->query($sql_sub);
                $root['is_praise'] = 0;//点赞状态改为未点赞
            }else{
                //如果没赞过，则加入点赞表
                $data['user_id'] = $user_id;
                $data['question_id'] = $id;
                $GLOBALS['db']->autoExecute(DB_PREFIX.question_praise,$data,'INSERT');
                $sql_add = "update ".DB_PREFIX."question set praise_count=praise_count+1 where id=".$pid;
                $GLOBALS['db']->query($sql_add);
                $root['is_praise'] = 1;//点赞状态改为已点赞
            }

            $root['status'] = 1;
            api_ajax_return($root);
        }
    }

    //删除问题
    public function delete(){
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] =0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $id = intval($_REQUEST['id']);
            $sql = "DELETE FROM ".DB_PREFIX."question WHERE id=".$id." or pid=".$id;
            $GLOBALS['db']->query($sql);

            $root = array();
            $root['status'] = 1;
            api_ajax_return($root);
        }
    }

}

?>
