<?php

class testService
{
    public function test()
    {
        return self::getPropTbales();
    }

    protected static function getPropTbales($all = false)
    {
        // return array(createPropTable());
        if ($all) {
            $table = DB_PREFIX . 'video_prop%';
            $data  = $GLOBALS['db']->getAll("SHOW TABLES LIKE'$table'");
            $res   = array();
            foreach ($data as $value) {
                foreach ($value as $v) {
                    $res[] = $v;
                }
            }
            return $res;
        } else {
            $res       = array(createPropTable());
            $pre_table = DB_PREFIX . 'video_prop_' . date('Ym', strtotime("-1 month"));
            if ($GLOBALS['db']->getRow("SHOW TABLES LIKE'$pre_table'")) {
                $res[] = $pre_table;
            }
            return $res;
        }
    }

    protected static function getUnionTable($tables, $field, $where)
    {
        $table_union = "";
        foreach ($tables as $value) {
            $table_union .= "SELECT $field FROM $value WHERE $where UNION ALL ";
        }
        return '(' . substr_replace($table_union, '', -strlen(' UNION ALL')) . ')';
    }

    /**
     * 个人中心秀票贡献榜
     * @param $data
     * @return mixed
     */
    public function contribution_list($data)
    {
        $user_id = intval($data['user_id']);
        $type    = trim($data['type']);

        $field = 'u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication';
        $table = DB_PREFIX . 'user as u';

        $field_union = "total_ticket,from_user_id,to_user_id";
        $where_union = "to_user_id=$user_id";

        switch ($type) {
            case 'day':
                $date = date('Y-m-d');
                $field_union .= ',create_d';
                $where_union .= " and create_date = '$date' ";
                break;
            case 'week':
                $year = date('Y');
                $week = date('W');
                $field_union .= ',create_w';
                $where_union .= " and create_y = '$year' and create_w = '$week'";
                break;
            case 'month':
                $where_union .= " and TO_DAYS(NOW())-TO_DAYS(create_date) <=30 ";
                break;
            default:
                $tables = self::getPropTbales(1);
                break;
        }
        $tables = isset($tables) ? $tables : self::getPropTbales();

        $table_union = self::getUnionTable($tables, $field_union, $where_union) . ' as v';

        return $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id=v.from_user_id GROUP BY from_user_id ORDER BY use_ticket DESC",
            true, true);
    }

    /**
     * 贡献排行榜
     * @param $data
     * @return mixed
     */
    public function contribution($data)
    {
        $rank_name = trim($data['rank_name']);
        $page      = intval($data['page']);
        $page_size = intval($data['page_size']);

        $page  = $page ? $page : 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        $table = DB_PREFIX . 'user as u';
        $field = 'u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket ,u.is_authentication';

        $field_union = "total_diamonds,from_user_id";

        switch ($rank_name) {
            case 'day':
                $where_union = 'create_y=' . to_date(NOW_TIME, 'Y') .
                ' and create_m=' . to_date(NOW_TIME, 'm') .
                ' and create_d=' . to_date(NOW_TIME, 'd');
                break;
            case 'month':
                $where_union = 'create_y=' . to_date(NOW_TIME, 'Y') . ' and create_m=' . to_date(NOW_TIME, 'm');
                break;
            default:
                $where_union = '1';
                $tables      = self::getPropTbales(1);
                break;
        }
        $tables = isset($tables) ? $tables : self::getPropTbales();

        $table_union = self::getUnionTable($tables, $field_union, $where_union) . ' as v';

        return $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id=v.from_user_id GROUP BY from_user_id order BY ticket desc LIMIT $limit",
            true, true);
    }

    /**
     * 收入排行榜
     * @param $data
     * @return mixed
     */
    public function consumption($data)
    {
        $rank_name = trim($data['rank_name']);
        $page      = intval($data['page']);
        $page_size = intval($data['page_size']);

        $page  = $page ? $page : 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        $table = DB_PREFIX . 'user as u';
        $field = 'u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as ticket ,u.is_authentication';

        $field_union = "total_ticket,to_user_id";
        $where_union = 'is_red_envelope = 0';

        switch ($rank_name) {
            case 'day':
                $where_union .= ' and create_y=' . to_date(NOW_TIME, 'Y') .
                ' and create_m=' . to_date(NOW_TIME, 'm') .
                ' and create_d=' . to_date(NOW_TIME, 'd');
                break;
            case 'month':
                $where_union .= ' and v.create_y=' . to_date(NOW_TIME, 'Y') .
                ' and create_m=' . to_date(NOW_TIME, 'm');
                break;
            default:
                $tables = self::getPropTbales(1);
                break;
        }
        $tables = isset($tables) ? $tables : self::getPropTbales();

        $table_union = self::getUnionTable($tables, $field_union, $where_union) . ' as v';

        return $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id=v.to_user_id GROUP BY to_user_id order BY ticket desc LIMIT $limit",
            true, true);
    }

    /**
     * 魅力排行榜
     * @return mixed
     */
    public function charm_ceil()
    {
        $limit = " 0,10 ";
        $table = DB_PREFIX . 'user as u';

        $field       = 'u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication';
        $field_union = 'to_user_id,total_ticket';
        $tables      = self::getPropTbales();

        $where       = " create_d = day(curdate()) and is_red_envelope = 0";
        $table_union = self::getUnionTable($tables, $field_union, $where) . ' as v';
        $root['day'] = $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id = v.to_user_id GROUP BY to_user_id order BY use_ticket desc LIMIT $limit",
            1, 1);

        $where         = " create_w = week(curdate()) and is_red_envelope = 0";
        $table_union   = self::getUnionTable($tables, $field_union, $where) . ' as v';
        $root['weeks'] = $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id = v.to_user_id GROUP BY to_user_id order BY use_ticket desc LIMIT $limit",
            1, 1);

        $where         = " TO_DAYS(NOW())-TO_DAYS(create_date) <=30 and is_red_envelope = 0";
        $table_union   = self::getUnionTable($tables, $field_union, $where) . ' as v';
        $root['month'] = $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id = v.to_user_id GROUP BY to_user_id order BY use_ticket desc LIMIT $limit",
            1, 1);

        $tables      = self::getPropTbales(1);
        // $where       = "1";
        $where       = "is_red_envelope = 0";
        $table_union = self::getUnionTable($tables, $field_union, $where) . ' as v';
        $root['all'] = $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id = v.to_user_id GROUP BY to_user_id order BY use_ticket desc LIMIT $limit",
            1, 1);
        return $root;
    }

    /**
     * 财富榜
     * @return mixed
     */
    public function rich_ceil()
    {
        $limit = " 0,10 ";
        $table = DB_PREFIX . 'user as u';
        $field = 'u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as use_ticket ,u.is_authentication';

        $tables      = self::getPropTbales();
        $field_union = 'from_user_id,total_diamonds';

        $where         = "TO_DAYS(NOW())-TO_DAYS(create_date) <=30";
        $table_union   = self::getUnionTable($tables, $field_union, $where) . ' as v';
        $root['month'] = $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id = v.from_user_id GROUP BY from_user_id order BY use_ticket desc LIMIT $limit",
            1, 1);

        $where         = "create_w = week(curdate())";
        $table_union   = self::getUnionTable($tables, $field_union, $where) . ' as v';
        $root['weeks'] = $GLOBALS['db']->getAll(
            "SELECT $field FROM $table INNER JOIN $table_union ON u.id = v.from_user_id GROUP BY from_user_id order BY use_ticket desc LIMIT $limit",
            1, 1);

        return $root;
    }



    public function contribution_list_old($data)
    {
        // contribution_list();
        $user_id = intval($data['user_id']);
        $type    = trim($data['type']);

        if ($type == 'day') {
            $where = " create_d = day(curdate()) ";
        } elseif ($type == 'month') {
            $where = " TO_DAYS(NOW())-TO_DAYS(create_date) <=30 ";
        } elseif ($type == 'week') {
            $where = " create_w = week(curdate())";
        }
        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN  " . DB_PREFIX . "video_prop as v ON u.id=v.from_user_id where v.to_user_id=" . $user_id . " and " . $where . " GROUP BY v.from_user_id order BY use_ticket desc ";

        return $GLOBALS['db']->getAll($sql, true, true);
    }
    public function contribution_old($data)
    {
        // contribution();
        $rank_name = trim($data['rank_name']);
        $page      = intval($data['page']);
        $page_size = intval($data['page_size']);

        $page  = $page ? $page : 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $table = createPropTable();

        switch ($rank_name) {
            case 'day':
                return $GLOBALS['db']->getAll("select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . $table . " as v on u.id = v.from_user_id where v.create_y=" . to_date(NOW_TIME, 'Y') . " and v.create_m=" . to_date(NOW_TIME, 'm') . " and v.create_d=" . to_date(NOW_TIME, 'd') . " GROUP BY from_user_id order BY sum(v.total_diamonds) desc limit " . $limit, true, true);
                break;
            case 'month':
                return $GLOBALS['db']->getAll("select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . $table . " as v on u.id = v.from_user_id where v.create_y=" . to_date(NOW_TIME, 'Y') . " and v.create_m=" . to_date(NOW_TIME, 'm') . " GROUP BY from_user_id order BY sum(v.total_diamonds) desc limit " . $limit, true, true);
                break;

            default:
                return $GLOBALS['db']->getAll("select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . $table . " as v on u.id = v.from_user_id GROUP BY from_user_id order BY sum(v.total_diamonds) desc limit " . $limit, true, true);
                break;
        }
    }
    public function consumption_old($data)
    {
        // consumption();
        $rank_name = trim($data['rank_name']);
        $page      = intval($data['page']);
        $page_size = intval($data['page_size']);

        $page  = $page ? $page : 1;
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $table = createPropTable();

        switch ($rank_name) {
            case 'day':
                return $GLOBALS['db']->getAll("select u.id as user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as ticket,u.is_authentication from ".DB_PREFIX."user as u INNER JOIN ".$table." as v on u.id = v.to_user_id where v.create_y=".to_date(NOW_TIME,'Y')." and v.create_m=".to_date(NOW_TIME,'m')." and v.create_d=".to_date(NOW_TIME,'d')." and v.is_red_envelope = 0 GROUP BY to_user_id order BY ticket desc limit ".$limit, true, true);
                break;
            case 'month':
                return $GLOBALS['db']->getAll("select u.id as user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as ticket,u.is_authentication from ".DB_PREFIX."user as u INNER JOIN ".$table." as v on u.id = v.to_user_id where v.create_y=".to_date(NOW_TIME,'Y')." and v.create_m=".to_date(NOW_TIME,'m')." and v.is_red_envelope = 0 GROUP BY to_user_id order BY ticket desc limit ".$limit, true, true);
                break;

            default:
                return $GLOBALS['db']->getAll("select u.id as user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as ticket,u.is_authentication from ".DB_PREFIX."user as u INNER JOIN ".$table." as v on u.id = v.to_user_id where  v.is_red_envelope = 0 GROUP BY to_user_id order BY ticket desc limit ".$limit, true, true);
                break;
        }
    }
    public function charm_ceil_old()
    {
        // charm_ceil();
        $limit = " 0,10 ";//取前十

        $where = " create_d = day(curdate()) and is_red_envelope = 0";//日榜条件
        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . DB_PREFIX . "video_prop as v on u.id = v.to_user_id where " . $where . " GROUP BY to_user_id order BY use_ticket desc limit " . $limit;

        $root['day'] = $GLOBALS['db']->getAll($sql);

        $where = " create_w = week(curdate()) and is_red_envelope = 0";//周榜条件

        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . DB_PREFIX . "video_prop as v on u.id = v.to_user_id where " . $where . " GROUP BY to_user_id order BY use_ticket desc limit " . $limit;

        $root['weeks'] = $GLOBALS['db']->getAll($sql);

        $where = " TO_DAYS(NOW())-TO_DAYS(create_date) <=30 and is_red_envelope = 0";//月榜条件
        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . DB_PREFIX . "video_prop as v on u.id = v.to_user_id where " . $where . " GROUP BY to_user_id order BY use_ticket desc limit " . $limit;

        $root['month'] = $GLOBALS['db']->getAll($sql);
        //总榜
        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . DB_PREFIX . "video_prop as v on u.id = v.to_user_id GROUP BY to_user_id order BY use_ticket desc limit " . $limit;
        $root['all'] = $GLOBALS['db']->getAll($sql);
        return $root;
    }
    public function rich_ceil_old()
    {
        // rich_ceil();
        $limit = " 0,10 ";
        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . DB_PREFIX . "video_prop as v on u.id = v.from_user_id where TO_DAYS(NOW())-TO_DAYS(create_date) <=30 GROUP BY from_user_id order BY use_ticket desc limit " . $limit;
        $root['month'] = $GLOBALS['db']->getAll($sql);

        $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN " . DB_PREFIX . "video_prop as v on u.id = v.from_user_id where create_w = week(curdate()) GROUP BY from_user_id order BY use_ticket desc limit " . $limit;
        $root['weeks'] = $GLOBALS['db']->getAll($sql);


        return $root;
    }
}
