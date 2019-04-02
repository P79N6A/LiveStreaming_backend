<?php

class medal_id_auto_cache extends auto_cache
{
    public function load($param)
    {
        $id = intval($param['id']);
        $key = "medal:" . $id;
        $medal = $GLOBALS['cache']->get($key);
        if ($medal === false) {
            $sql = "SELECT * FROM " . DB_PREFIX . "medals where id=" . $id;
            $medal = $GLOBALS['db']->getRow($sql, true, true); //以后需要缓存
            if (!empty($medal)) {
                $medal['icon'] = get_spec_image($medal['icon']);
                $medal['pc_icon'] = get_spec_image($medal['pc_icon']);
                $medal['pc_gif'] = get_spec_image($medal['pc_gif']);
            } else {
                $medal = array();
            }
            $GLOBALS['cache']->set($key, $medal);
        }

        if (empty($medal)) {
            $medal = array();
        }
        return $medal;
    }

    public function rm($param)
    {
        $id = intval($param['id']);
        $key = "medal:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
    }

    public function clear_all($param)
    {
        $id = intval($param['id']);
        $key = "medal:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
    }
}
