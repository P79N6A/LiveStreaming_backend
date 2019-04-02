<?php

class DollQuestionLogAction extends CommonAction{
	public function index()
	{
		$user_id = $_REQUEST['user_id'];
		if(trim($user_id)!='')
        {
            $map['_string'] = " user_id =" . $user_id . "";
        }
		$doll_id = $_REQUEST['doll_id'];
        $room_id = $_REQUEST['room_id'];
        $begin_time = $_REQUEST['create_time_1'];
        $end_time = $_REQUEST['create_time_2'];
        if(trim($doll_id)!='')
        {
        	$map['doll_id'] = $doll_id ;
        }

        if(trim($room_id)!='')
        {
            $map['room_id'] = $room_id ;
        }
        $begin_time_span = to_timespan($begin_time);
        $end_time_span   = to_timespan($end_time);
        if ($begin_time != '' && $end_time == '') {
            $map['_string'] = "create_time >=" . $begin_time_span . "";
        } elseif ($begin_time != '' && $end_time != '') {
            $map['_string'] = "create_time >=" . $begin_time_span . " and create_time <=" . $end_time_span . "";
        } elseif ($begin_time == '' && $end_time != '') {
            $map['_string'] = "create_time <=" . $end_time_span . "";
        }
		
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }
        $name=$this->getActionName();
        $model = D ($name);
        if (! empty ( $model )) {
            $this->_list ( $model, $map );
        }
        $this->display ();
	}

	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            $list = M(MODULE_NAME)->where ( $condition )->delete();
            if ($list!==false) {
                $result['status'] = 1;
                $result['info'] = '删除成功';
            } else {
                $result['status'] = 0;
                $result['info'] = '删除失败';
            }
        } else {
            $result['status'] = 0;
            $result['info'] = '请选择要删除的选项';
		}
		admin_ajax_return($result);
	}

}
?>