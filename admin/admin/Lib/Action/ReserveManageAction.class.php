<?php

class ReserveManageAction extends CommonAction
{
	//查看预约列表
    public function index()
    {
     	$user_id = $_REQUEST['user_id'];
        $dolls_id = $_REQUEST['dolls_id'];
        $room_id = $_REQUEST['room_id'];
        $is_effect = $_REQUEST['is_effect'];
        if($_REQUEST['is_effect'] == '')
        {
            $_REQUEST['is_effect'] = -1;
        }
     	$create_time_1 = $_REQUEST['create_time_1'];

     	if(trim($user_id)!='')
        {
            $parameter.= "user_id=" . intval($user_id). "&";
            $sql_w .= "user_id=".intval($user_id)." and ";
        }

        if(trim($dolls_id)!='')
        {
            $parameter.= "dolls_id=" . intval($dolls_id). "&";
            $sql_w .= "dolls_id=".intval($dolls_id)." and ";
        }

        if(trim($room_id)!='')
        {
            $parameter.= "room_id=" . intval($room_id). "&";
            $sql_w .= "room_id=".intval($room_id)." and ";
        }

        if(trim($is_effect)!='' && trim($is_effect) != -1)
        {
            $parameter.= "is_effect=" . intval($is_effect). "&";
            $sql_w .= "is_effect=".intval($is_effect)." and ";
        }

        $create_time_2=empty($_REQUEST['create_time_2'])?to_date(get_gmtime(),'Y-m-d'):strim($_REQUEST['create_time_2']);
		$create_time_2=to_timespan($create_time_2);
        if(trim($create_time_1)!='' )
		{
			$parameter.="create_time between '". to_timespan($create_time_1) . "' and '". $create_time_2 ."'&";
			$sql_w .=" (create_time between '". to_timespan($create_time_1). "' and '". $create_time_2 ."' ) and ";

		}


        
        $model = D ();

        $sql_str = "SELECT *," .
        " id,dolls_id,user_id,room_id,create_time,begin_time,end_time,is_effect" .
        " FROM ".DB_PREFIX."dolls_reserve WHERE 1=1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM ".DB_PREFIX."dolls_reserve WHERE 1=1";
        $sql_str .= " and ".$sql_w." 1=1 ";
        $count_sql .= " and ".$sql_w." 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'id',0,$count_sql);
        $this->assign ( 'list', $voList );
        $this->display ();
    }





}