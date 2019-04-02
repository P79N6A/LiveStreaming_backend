<?php
//底部文章
class user_mounts_auto_cache extends auto_cache
{
    private $key = "user_mounts:list";
    public function load($param = array())
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';

        $list = $GLOBALS['cache']->get($this->key, true);

        $user_id = isset($param['user_id']) ? intval($param['user_id']) : 0;
        $no_expired = isset($param['no_expired']) ? $param['no_expired'] : false;
        $condition = '';
        if (!empty($param['mount_id'])) {
            $mount_id = intval($param['mount_id']);
            $condition .= " AND um.mount_id={$mount_id}";
        }
        if ($no_expired) {
            $condition .= " AND um.end_time>UNIX_TIMESTAMP()";
        }

        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                // if ($list === false) {
                $sql = "SELECT um.*,m.`name`,m.`icon`,m.`desc` FROM " . DB_PREFIX . "user_mounts AS um INNER JOIN " . DB_PREFIX . "mounts AS m ON m.id=um.mount_id WHERE um.user_id={$user_id} {$condition} ORDER BY um.end_time DESC";
                if (!empty($mount_id)) {
                    $list = $GLOBALS['db']->getRow($sql, true, true);
                    if (!empty($list['icon'])) {
                        $list['icon'] = get_spec_image($list['icon']);
                    }
                } else {
                    $list = $GLOBALS['db']->getAll($sql, true, true);
                    foreach ($list as &$value) {
                        $value['icon'] = get_spec_image($value['icon']);
                    }
                }
                if (empty($list)) {
                    $list = array();
                }
                $GLOBALS['cache']->set($this->key, $list, 10, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
        }
        if (empty($list)) {
            $list = array();
        }
        return $list;
    }

    public function rm($param = array())
    {
        $this->key .= md5(serialize($param));
        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
