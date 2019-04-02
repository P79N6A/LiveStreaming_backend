<?php

fanwe_require(APP_ROOT_PATH.'mapi/lib/society_user.action.php');

class society_userCModule  extends society_userModule
{
    //签约的主播列表
    public function user_list()
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );

        //搜索的主播id
        $id = intval($_REQUEST['user_id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;

        //        $sign_status = intval($_REQUEST['sign_status']);//签约状态

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
        if ($society_info['society_chieftain'] != 1) {
            api_ajax_return(array('status' => 0, 'error' => '您不是公会长'));
        }

        //公会成员总数
        //        $rs_count     = $GLOBALS['db']->getOne("select user_count as rs_count from ".DB_PREFIX."society where id=".$society_info['society_id']);

        $where = "sa.status=1 and sa.society_id=" . $society_info['society_id'];
        if ($id > 0) {
            $where .= " and sa.user_id=" . $id;
        }
        $apply_count = $GLOBALS['db']->getOne("select count(id) from " . DB_PREFIX . "society_apply sa where $where");

        $field = "u.id as user_id,u.nick_name,u.sex,u.v_type,u.v_icon,u.head_image,u.signature,u.user_level,u.society_chieftain,
                        u.society_settlement_type,sa.status,sa.deal_time";
        $start = ($page - 1) * $page_size;
        $end = $page_size;
        $rs_count = $apply_count ? $apply_count : 0;
        $table = DB_PREFIX . "user u";
        $left_join = DB_PREFIX . "society_apply sa on u.id=sa.user_id";
        $sql = "select $field from $table left join $left_join where $where limit $start,$end";
        $list = $GLOBALS['db']->getAll($sql);
        foreach ($list as $k => $v) {
            $list[$k]['head_image'] = get_spec_image($v['head_image']);
            $list[$k]['deal_time'] = to_date($v['deal_time'], 'Y-m-d H:i:s');

        }
        $root['url'] = SITE_DOMAIN.'/index.php?ctl=society&act=mobile_login&society_id='.$society_info['society_id'];

        $page = new Page($rs_count, $page_size);
        $root['page'] = $page->show();
        $root['rs_count'] = $rs_count;
        $root['list'] = $list;
        //        $root['sign_status'] = $sign_status;
        api_ajax_return($root);
    }
}
?>