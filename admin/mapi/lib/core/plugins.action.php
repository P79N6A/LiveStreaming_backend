<?php
class pluginsModule extends baseModule
{
    public function init()
    {
        $table = DB_PREFIX . 'games';
        $count = $GLOBALS['db']->getOne("SELECT count(1) as count FROM $table", 1, 1);
        $list  = array();
        if ($count) {
            $field = '`id`,`name`,`image`,`principal`';
            $list  = $GLOBALS['db']->getRow("SELECT $field FROM $table", 1, 1);
        }
        api_ajax_return(array(
            'status'   => 1,
            'rs_count' => $count,
            'list'     => $list,
        ));
    }
}
