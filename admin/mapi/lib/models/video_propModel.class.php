<?php
/**
 *
 */
class video_propModel extends NewModel
{
    public function getNewestOne($field = '', $where = '', $order = 'id desc')
    {
        return $this->table('video_prop_' . date('Ym'))->field($field)->order($order)->selectOne($where);
    }
}
