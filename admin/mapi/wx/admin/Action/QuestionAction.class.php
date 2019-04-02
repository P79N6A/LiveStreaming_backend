<?php

class QuestionAction extends CommonAction{
    public function index(){
        if ($_REQUEST['is_answered'] != ''){
            $parameter = "is_answered=" . intval($_REQUEST['is_answered']). "&";
            $sql_w = "is_answered=".intval($_REQUEST['is_answered']). " and ";
        }

        $sql = "select q.id,u.nick_name,q.title,q.content,q.create_time,q.is_answered from ".DB_PREFIX."question as q 
                 left join ".DB_PREFIX."user as u on q.question_user_id=u.id where q.is_question=1 and ".$sql_w."  
                 1=1 order by q.create_time desc";
        $model = D();
        $list  = $this->_Sql_list($model,$sql,'&'.$parameter);
        //转换时间
        foreach ($list as $k=>&$v){
            $v['create_time'] = date('Y-m-d',$v['create_time']);
        }
        $this->assign("list",$list);
        $this->display();
    }

    public function detail(){
        $model = M('question');

        //取出问题详情
        $id = intval($_REQUEST['id']);
        $pid= $model->where('id='.$id)->getField('pid');
        if (intval($pid) == 0){
            $pid = $id;
        }
        //主问题
        $data = $model->where('id='.$pid)->find();
        $data['create_time'] = date('Y-m-d',$data['create_time']);
        //需回答问题
        $data_question = $model->where('id='.$id)->find();
        //取出问题相关回答
        $sql_answer = "select q.*,u1.nick_name as answer_user_name,u2.nick_name as question_user_name from ".DB_PREFIX."question 
                        as q left join ".DB_PREFIX."user as u1 on q.answer_user_id=u1.id left join ".DB_PREFIX."user as u2 on 
                      q.question_user_id=u2.id where q.pid=".$pid;

        $data['answer_list'] = $model->query($sql_answer);

        $this->assign('answer_list',$data['answer_list']);
        $this->assign('data',$data);
        $this->assign('data_question',$data_question);
        $this->display();
    }

    public function answer(){
        if ($_REQUEST['id'] == ''){
            $this->error("id不能为空");
        }

        if ($_REQUEST['answer'] == ''){
            $this->error("内容不能为空");
        }
        $model = M('question');

        $id = intval($_REQUEST['id']);//问题ID
        $result_Q = $model->where('id='.$id)->field('user_id,pid')->find();
        $data['question_user_id'] = $result_Q['user_id'];//回复上一个回复时，上一个的回答人变被回答人
        $data['answer_user_id'] = 1;
        $data['content'] = trim($_REQUEST['answer']);
        $data['is_question'] = 0;
        $data['create_time'] = NOW_TIME;
        $data['praise_count'] = 0;
        $data['count'] = 0;
        if (intval($result_Q['pid']) != 0){
            $data['pid'] = intval($result_Q['pid']);
        }else{
            $data['pid'] = $id;
        }

        $data['user_id'] = 1;
        $model->add($data);
        $model->where('id='.$id)->setField('is_answered',1);//问题设置为已回答

        $this->success("回答成功");
    }

    //每日新增问题、回答统计
    public function day_count(){
        $model = M('question');
        //取出7日内新增问题数与日期
        $question_sql = "select FROM_UNIXTIME(create_time,'%m月%d日') as day,count(id) as count from ".DB_PREFIX."question where
                 is_question=1 and (create_time+7*24*3600)>=unix_timestamp(NOW()) group by FROM_UNIXTIME(create_time,'%m %d')";
        $data = $model->query($question_sql);

        $data_question = array();
        //去掉key名，只保留value
        foreach ($data as $k=>$v){
            $data_question[] = array_values($v);
        }
        //构造一个数组，表示7日内，问题数初始化为0
        $dateList = array();
        for($i=6;$i>=0;$i--){
            $dateList[6-$i][0] = date('m月d日',strtotime("-".$i." day"));
            $dateList[6-$i][1] = 0;
            $dateList[6-$i][2] = 0;
        }
        //如果某个日期有新增问题，将其数量赋值给dataList
        $len = count($data_question);
        for ($i=0;$i<7;$i++){
            for ($k=0;$k<$len;$k++){
                if ($dateList[$i][0] == $data_question[$k][0]){
                    $dateList[$i][1] = $data_question[$k][1];
                }
            }
        }

        //取出7日内新增回答数与日期
        $answer_sql = "select FROM_UNIXTIME(create_time,'%m月%d日') as day,count(id) as count from ".DB_PREFIX."question where
                 is_question=0 and (create_time+7*24*3600)>=unix_timestamp(NOW()) group by FROM_UNIXTIME(create_time,'%m %d')";
        $data_ans = $model->query($answer_sql);

        $data_answer = array();
        //去掉key名，只保留value
        foreach ($data_ans as $k=>$v){
            $data_answer[] = array_values($v);
        }

        //如果某个日期有新增回答，将其数量赋值给dataList.
        $len_ans = count($data_answer);
        for ($i=0;$i<7;$i++){
            for ($k=0;$k<$len_ans;$k++){
                if ($dateList[$i][0] == $data_answer[$k][0]){
                    $dateList[$i][2] = $data_answer[$k][1];
                }
            }
        }

        $this->assign('dataValue',$dateList);
        $this->display();
    }

    public function foreverdelete(){
        if (!isset($_REQUEST['id']) || $_REQUEST['id']==''){
            $this->error (l("INVALID_OPERATION"));
        }
        $id = intval($_REQUEST['id']);
        $condition = array();
        $condition['id']=$id;
        $condition['pid']=$id;
        $condition['_logic']='OR';

        $rid = M(MODULE_NAME)->where ( $condition )->Field('id')->select();
        //需删除的点赞id
        $praise_id = array();
        foreach ($rid as $k=>$v){
            $praise_id[] = $v['id'];
        }
        $list = M(MODULE_NAME)->where ( $condition )->delete();
        if ($list != false){
            //删除点赞表中相关数据
            $map = array('question_id'=>array('in',$praise_id));
            $list_reply = M('question_praise')->where($map)->delete();

            $this->success ($id.l("FOREVER_DELETE_SUCCESS"),1);
        }else{
            $this->error ($id.l("FOREVER_DELETE_FAILED"),0);
        }
    }
}