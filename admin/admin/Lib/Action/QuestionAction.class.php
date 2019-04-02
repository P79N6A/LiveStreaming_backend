<?php

class QuestionAction extends CommonAction{
    public function index(){
        $model = M('question');

        $is_answered = $_REQUEST['is_answered'];
        $condition['is_question'] = 1;
        if ($is_answered != ''){
            $condition['is_answered'] = intval($is_answered);
        }

        $list = $model->where($condition)->order('create_time desc')->select();
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
        $condition=array('is_question'=>0);
        $condition['pid']=$pid;
        $data['answer_list'] = $model->where($condition)->select();

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
        $result_Q = $model->where('id='.$id)->field('question_user_id,question_user_name,pid')->find();
        $data['question_user_id'] = $result_Q['question_user_id'];
        $data['question_user_name'] = htmlspecialchars($result_Q['question_user_name']);
        $data['answer_user_id'] = 0;
        $data['answer_user_name'] = '老余';
        $data['content'] = trim($_REQUEST['answer']);
        $data['is_question'] = 0;
        $data['create_time'] = NOW_TIME;
        $data['praise_count'] = 0;
        $data['count'] = 0;
        $data['qid'] = $id;
        if (intval($result_Q['pid']) != 0){
            $data['pid'] = intval($result_Q['pid']);
        }else{
            $data['pid'] = $id;
        }

        $model->add($data);
        $model->where('id='.$id)->setField('is_answered',1);//问题设置为已回答

        $this->success("回答成功");
    }
}