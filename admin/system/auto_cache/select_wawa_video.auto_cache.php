<?php

class select_wawa_video_auto_cache extends auto_cache
{
    private $key = "select:wawa:video:";
    public function load($param)
    {
        $order = strim($param['order']); //排序字段
        $sort = strim($param['sort']); //排序方式
        $is_classify = intval($_REQUEST['classified_id']); //分类

        if ($order) {
            $this->key .= '_' . $order;
        }
        if ($sort) {
            $this->key .= '_' . $sort;
        }
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false || true) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (false) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $m_config = load_auto_cache("m_config"); //初始化手机端配置

                $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.create_type, v.room_type,
						(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.live_image, v.xpoint,v.ypoint,
						v.is_live_pay,v.live_pay_type,v.live_fee FROM " . DB_PREFIX . "video v where v.live_in in (1,3)";

                if ($is_classify) {
                    $sql .= " and v.classified_id = " . $is_classify; //分类
                }

                if ($order) {
                    $sql .= " order by v." . $order;
                    if ($sort) {
                        $sql .= " " . $sort;
                    }
                } else {
                    $sql .= "  order by v.sort_num desc,v.sort desc";
                }

                $list = $GLOBALS['db']->getAll($sql, true, true);

                $GLOBALS['cache']->set($this->key, $list, 10, true);

                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                //echo $this->key;
            }
        }
        if (empty($list)) {
            $list = array();
        }
        return $list;
    }

    public function rm()
    {

        //$GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {

        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
