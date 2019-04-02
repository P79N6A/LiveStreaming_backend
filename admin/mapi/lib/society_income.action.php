<?php
class society_incomeModule  extends baseModule
{
    protected static function getUserId($is_debug=false){
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($is_debug){
            return 101674;
        }
        if (!$user_id) {
            ajax_return(array(
                'status' => 0,
                'error'  => '未登录',
            ));
        }
        if ($is_debug){

        }
        return $user_id;
    }

    //将时间戳差值转换为x时x分x秒
    protected static function timelen_change($timelen){
        if($timelen > 0){
            $hour   = intval($timelen / 3600);
            $res    = $timelen % 3600;
            $minute = intval($res / 60);
            $second = $res % 60;
            return $hour."时".$minute."分".$second."秒";
        }else{
            return "";
        }
    }

    //公会月收入
    public function society_income_month(){
        $root = array('status'=>1,'error'=>'');
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id);

        $date_str = $_REQUEST['date_str'];
        if ($date_str == ''){
            $date_str = date('Y-m',NOW_TIME);
        }
        $year  = intval(substr($date_str,0,4));
        $month = intval(substr($date_str,5,2));

        $m_config = load_auto_cache('m_config');
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            $where  = "society_id=".$society_info['society_id']." and end_Y=$year and end_m=$month";
            $sql    = "select sum(vote_number) from ".DB_PREFIX."society_earning where $where";
            $ticket = $GLOBALS['db']->getOne($sql);
            if (!$ticket){
                $ticket = 0;
            }

            $data = array();
            $data['date']   = $date_str;
            $data['ticket'] = $ticket;

            $refund_rate = $GLOBALS['db']->getOne("select refund_rate from ".DB_PREFIX."society where id=".$society_info['society_id']);
            if (floatval($refund_rate) == 0){
                $refund_rate = floatval($m_config['society_public_rate']);
            }
            $data['total_money'] = $ticket * floatval($refund_rate);
            $data['society_rate'] = strval(floatval($refund_rate) * 100)."%";
            $data['society_money'] = $ticket * floatval($refund_rate);

            $root['list'] = $data;
            $root['list'] = $data;
        }
        api_ajax_return($root);
    }

    //主播月收入
    public function user_income_month(){
        $root = array('status'=>0,'error'=>'');
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain,society_settlement_type from ".DB_PREFIX."user where id=".$user_id);

        //搜索的月份
        $date_str = $_REQUEST['date_str'];
        if ($date_str == ''){
            $date_str = date('Y-m',NOW_TIME);
        }
        $year  = intval(substr($date_str,0,4));
        $month = intval(substr($date_str,5,2));

        //分页
        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page  ? $page : 1;
        $page_size    = 10;

        //搜索的主播id
        $id = intval($_REQUEST['id']);
        $m_config = load_auto_cache('m_config');
        $society_rate = floatval($m_config['society_user_public_rate']);
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            if($society_rate>0){
                $field     = "s.user_id,u.nick_name,u.society_settlement_type,sum(s.vote_number) as ticket,sum(s.vote_number)*".$society_rate." as society_private_money";
                $table     = DB_PREFIX."society_earning s";
                $where     = "s.society_id=".$society_info['society_id']." and s.end_Y=$year and s.end_m=$month";
                if ($id > 0){
                    $where .= " and s.user_id=".$id;
                }
                $left_join = DB_PREFIX."user u on s.user_id=u.id";
                $start     = ($page - 1) * $page_size;
                $sql       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id limit $start,$page_size";
                $list      = $GLOBALS['db']->getAll($sql);
                $sql_count  = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id ";
                $list_count  =  $GLOBALS['db']->getAll($sql_count);
                if ($list){
                    $rs_count  = count($list_count);
                    $page = new Page($rs_count,$page_size);
                    $root['page'] = $page->show();
                    $root['date'] = $date_str;
                    $root['rs_count'] = $rs_count;
                    $root['list'] = $list;
                    $root['status'] = 1;
                }
            }else{
                $root['status'] =0;
                $root['error'] = '收益参数错误';
            }

        }
        api_ajax_return($root);
    }

    //主播日收入
    public function user_income(){
        $root = array(
            'status'=>1,
            'error'=>'',
        );

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain,society_settlement_type from ".DB_PREFIX."user where id=".$user_id);
        $date_str = $_REQUEST['date_str'];

        //搜索的日期
        if ($date_str == ''){
            $date_str = date('Y-m-d',NOW_TIME);
        }
        $year  = intval(substr($date_str,0,4));
        $month = intval(substr($date_str,5,2));
        $day   = intval(substr($date_str,8,2));

        //搜索的主播id
        $id = intval($_REQUEST['user_id']);

        //分页
        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page  ? $page : 1;
        $page_size    = 10;

        $m_config = load_auto_cache('m_config');
        $society_rate = floatval($m_config['society_user_public_rate']);
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            if($society_rate>0){
                $field     = "s.user_id,u.nick_name,u.society_settlement_type,sum(s.vote_number) as ticket,sum(s.vote_number)*".$society_rate." as society_money";
                $table     = DB_PREFIX."society_earning s";
                $where     = "s.society_id=".$society_info['society_id']." and s.end_Y=$year and s.end_m=$month and s.end_d=$day";
                if ($id > 0){
                    $where .= " and s.user_id=".$id;
                }
                $left_join = DB_PREFIX."user u on s.user_id=u.id";
                $start     = ($page - 1) * $page_size;
                $sql       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id limit $start,$page_size";
                $list      = $GLOBALS['db']->getAll($sql);
                $sql_count       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id ";
                $list_count      = $GLOBALS['db']->getAll($sql_count);
                $now_date = to_date(NOW_TIME,'Y-m-d');
                if ($list){
                    $rs_count = count($list_count);
                    $page = new Page($rs_count,$page_size);
                    $root['page'] = $page->show();
                    $root['date'] = $date_str;
                    $root['rs_count'] = $rs_count;
                    $root['now_date'] = $now_date;
                    $root['list'] = $list;
                }
            }else{
                $root['status'] =0;
                $root['error'] = '收益参数错误';
            }

        }
        api_ajax_return($root);
    }

    //主播直播时长
    public function user_live_length(){
        $root = array(
            'status'=>0,
            'error'=>'',
        );
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id);

        //搜索的主播id
        $id = intval($_REQUEST['id']);

        //搜索的日期
        $date_str = $_REQUEST['date_str'];

        //分页
        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page    = intval($_REQUEST['p']);
        $page    = $page ? $page : 1;
        $page_size = 10;

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            $field     = "s.user_id,u.nick_name,sum(s.timelen) as timelen";
            $table     = DB_PREFIX."society_earning s";
            $where     = "s.society_id=".$society_info['society_id'];
            if ($id > 0){
                $where .= " and s.user_id=".$id;
            }
            if ($date_str != ''){
                $date_arr = explode('-',$date_str);
                $where     .= " and s.end_Y>=".$date_arr[0].". and s.end_m>=".$date_arr[1]." and s.end_d>=".$date_arr[2]." 
                                and s.end_Y<=".$date_arr[3].". and s.end_m<=".$date_arr[4]." and s.end_d<=".$date_arr[5];
            }
            $left_join = DB_PREFIX."user u on s.user_id=u.id";
            $start     = ($page - 1) * $page_size;
            $sql       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id limit $start,$page_size";
            $list      = $GLOBALS['db']->getAll($sql);
            $sql_count       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id ";
            $list_count      = $GLOBALS['db']->getAll($sql_count);
            if ($list){
                foreach ($list as $k=>$v){
                    $list[$k]['timelen'] = self::timelen_change($v['timelen']);
                }

//                $rs_count = $GLOBALS['db']->getOne("select count(*) from $table where $where GROUP BY s.user_id");
                $rs_count = count($list_count);
                $month_e = date('Y-m-d',time());//2017-03-19 结束时间
                $month_s = date("Y-m-d",strtotime($month_e." -1 month -1 day")); //2017-03-19 开始时间
                $month_date = $month_s.' - '.$month_e;
                $page = new Page($rs_count,$page_size);
                $root['page'] = $page->show();
                $root['rs_count'] = $rs_count;
                $root['list'] = $list;
//                $root['month_date'] = $month_date;
                $root['status'] = 1;
            }
        }
        api_ajax_return($root);
    }

    //导出公会月收入
    public function society_csv(){
        $root = array(
            'status'=>1,
            'error'=>'',
        );

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id);

        //搜索的月份
        $date_str = $_REQUEST['date_str'];
        if ($date_str == ''){
            $date_str = to_date(NOW_TIME,'Y-m');
        }
        $year  = intval(substr($date_str,0,4));
        $month = intval(substr($date_str,5,2));

        $m_config = load_auto_cache('m_config');
        $content = iconv('utf-8','gbk','日期,总秀票,分成比例,结算金额,实际收入');
        $content .= "\n";

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            $where  = "society_id=".$society_info['society_id']." and end_Y=$year and end_m=$month";
            $sql    = "select sum(vote_number) from ".DB_PREFIX."society_earning where $where";
            $ticket = $GLOBALS['db']->getOne($sql);

            if (!$ticket) {
                $ticket = 0;
            }
            $society_rate = $GLOBALS['db']->getOne("select refund_rate from ".DB_PREFIX."society where id=".$society_info['society_id']);
            if (floatval($society_rate) == 0){
                $society_rate = floatval($m_config['society_public_rate']);
            }
            $society_money = $ticket * floatval($society_rate);
            $society_rate = strval(floatval($society_rate) * 100)."%";
            $time ='1970-01-01 16:00:00';
            $data   = array();
            $data['date'] = '"' . iconv('utf-8','gbk',$date_str) . '"';
            $data['date'] = str_replace($time,'0',$date_str);
            $data['ticket'] = '"' . iconv('utf-8','gbk',$ticket) . '"';
            $data['society_rate']  = '"' . iconv('utf-8','gbk',$society_rate) . '"';
            $data['society_money'] = '"' . iconv('utf-8','gbk',$society_money) . '"';
            $data['total_money'] = '"' . iconv('utf-8','gbk',$society_money) . '"';

            $content .= implode(",", $data) . "\n";

            header("Content-Disposition: attachment; filename=society_income.csv");
            echo $content ;
        }else{
            header("Content-Disposition: attachment; filename=society_income.csv");
            echo $content ;
        }
    }

    //导出主播月收入
    public function user_month_csv(){
            $root = array('status'=>0,'error'=>'');
            $user_id = self::getUserId();
            $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain,society_settlement_type from ".DB_PREFIX."user where id=".$user_id);

            //搜索的月份
            $date_str = $_REQUEST['date_str'];
            if ($date_str == ''){
                $date_str = date('Y-m',NOW_TIME);
            }
            $year  = intval(substr($date_str,0,4));
            $month = intval(substr($date_str,5,2));

            //搜索的主播id
            $id = intval($_REQUEST['id']);

            $content = iconv('utf-8','gbk','日期,主播ID,主播昵称,总秀票,结算金额,实际收入');
            $content .= "\n";

            $m_config = load_auto_cache('m_config');
            if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
                $society_rate = floatval($m_config['society_user_public_rate']);
                if($society_rate>0){
                    $field     = "s.user_id,u.nick_name,u.society_settlement_type,sum(s.vote_number) as ticket,sum(s.vote_number)*".$society_rate." as society_private_money";
                    $table     = DB_PREFIX."society_earning s";
                    $where     = "s.society_id=".$society_info['society_id']." and s.end_Y=$year and s.end_m=$month";
                    if ($id > 0){
                        $where .= " and s.user_id=".$id;
                    }
                    $left_join = DB_PREFIX."user u on s.user_id=u.id";
                    $sql       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id";
                    $list      = $GLOBALS['db']->getAll($sql);
                    if ($list){
                        $data = array();
                        foreach ($list as $k=>$v){
                            $data['date'] = '"' . iconv('utf-8','gbk',$date_str) . '"';
                            $data['user_id'] = '"' . iconv('utf-8','gbk',$list[$k]['user_id']) . '"';
                            $data['nick_name'] = '"' . iconv('utf-8','gbk',$list[$k]['nick_name']) . '"';
                            $data['ticket'] = '"' . iconv('utf-8','gbk',$list[$k]['ticket']) . '"';
                            $data['society_private_money'] = '"' . iconv('utf-8','gbk',$list[$k]['society_private_money']) . '"';
                            $data['total_money'] = '"' . iconv('utf-8','gbk',$list[$k]['society_private_money']) . '"';
                            $content .= implode(",", $data) . "\n";
                        }
                        header("Content-Disposition: attachment; filename=user_month_income.csv");
                        echo $content ;
                    }else{
                        header("Content-Disposition: attachment; filename=user_month_income.csv");
                        echo $content ;
                    }
                }else{
                    $root['status'] =0;
                    $root['error'] = '收益参数错误';
                }

            }else{
                header("Content-Disposition: attachment; filename=user_month_income.csv");
                echo $content ;
            }
        }

    //导出主播日收入
    public function user_day_csv()
    {
        $root = array(
            'status' => 0,
            'error' => '',
        );

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
        $date_str = $_REQUEST['date_str'];

        //搜索的日期
        if ($date_str == '') {
            $date_str = date('Y-m-d',NOW_TIME);
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));
        $day = intval(substr($date_str, 8, 2));

        //搜索的主播id
        $id = intval($_REQUEST['id']);

        $content = iconv('utf-8', 'gbk', '日期,主播ID,主播昵称,结算方式,总秀票,结算金额,实际收入');
        $content .= "\n";

        $m_config = load_auto_cache('m_config');
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $society_rate = floatval($m_config['society_user_public_rate']);
            if($society_rate>0){
                $field = "s.user_id,u.nick_name,u.society_settlement_type,sum(s.vote_number) as ticket,sum(s.vote_number)*" . $society_rate . " as society_private_money";
                $table = DB_PREFIX . "society_earning s";
                $where = "s.society_id=" . $society_info['society_id'] . " and s.end_Y=$year and s.end_m=$month and s.end_d=$day";
                if ($id > 0) {
                    $where .= " and s.user_id=" . $id;
                }
                $left_join = DB_PREFIX . "user u on s.user_id=u.id";
                $sql = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id";
                $list = $GLOBALS['db']->getAll($sql);
                if ($list) {


                    $data = array();
                    foreach ($list as $k => $v) {
                        $data['date'] = '"' . iconv('utf-8', 'gbk', $date_str) . '"';
                        $data['user_id'] = '"' . iconv('utf-8', 'gbk', $list[$k]['user_id']) . '"';
                        $data['nick_name'] = '"' . iconv('utf-8', 'gbk', $list[$k]['nick_name']) . '"';
                        $data['ticket'] = '"' . iconv('utf-8', 'gbk', $list[$k]['ticket']) . '"';
                        $data['society_private_money'] = '"' . iconv('utf-8', 'gbk', $list[$k]['society_private_money']) . '"';
                        $data['total_money'] = '"' . iconv('utf-8', 'gbk', $list[$k]['society_private_money']) . '"';
                        $content .= implode(",", $data) . "\n";
                    }
                    header("Content-Disposition: attachment; filename=user_month_income.csv");
                    echo $content;
                }else {
                    header("Content-Disposition: attachment; filename=user_month_income.csv");
                    echo $content;
                }
            }else{
                echo '参数错误';
            }
        }else{
            header("Content-Disposition: attachment; filename=user_month_income.csv");
            echo $content;
        }
        }

    //导出主播直播时长
    public function user_live_length_csv(){
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id);

        //搜索的主播id
        $id = intval($_REQUEST['id']);

        //搜索的日期
        $date_str = $_REQUEST['date_str'];

        $content = iconv('utf-8','gbk','主播ID,主播昵称,直播时长');
        $content .= "\n";

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            $field     = "s.user_id,u.nick_name,sum(s.timelen) as timelen";
            $table     = DB_PREFIX."society_earning s";
            $where     = "s.society_id=".$society_info['society_id'];
            if ($id > 0){
                $where .= " and s.user_id=".$id;
            }
            if ($date_str != ''){
                $date_arr = explode('-',$date_str);
                $where     .= " and s.end_Y>=".$date_arr[0].". and s.end_m>=".$date_arr[1]." and s.end_d>=".$date_arr[2]." 
                            and s.end_Y<=".$date_arr[3].". and s.end_m<=".$date_arr[4]." and s.end_d<=".$date_arr[5];
            }
            $left_join = DB_PREFIX."user u on s.user_id=u.id";
            $sql       = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id";
            $list      = $GLOBALS['db']->getAll($sql);
            if ($list){
                foreach ($list as $k=>$v){
                    $list[$k]['timelen'] = self::timelen_change($v['timelen']);
                    $data['user_id']   = '"' . iconv('utf-8','gbk',$list[$k]['user_id']) . '"';
                    $data['nick_name'] = '"' . iconv('utf-8','gbk',$list[$k]['nick_name']) . '"';
                    $data['timelen']   = '"' . iconv('utf-8','gbk',$list[$k]['timelen']) . '"';
                    $content .= implode(",", $data) . "\n";
                }
                header("Content-Disposition: attachment; filename=user_month_income.csv");
                echo $content ;
            }else{
                header("Content-Disposition: attachment; filename=user_month_income.csv");
                echo $content ;
            }
        }else{
            header("Content-Disposition: attachment; filename=user_month_income.csv");
            echo $content ;
        }
    }
}