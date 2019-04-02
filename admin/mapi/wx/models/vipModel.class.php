<?php
/**
 *
 */
class vipModel extends NewModel
{
    public function getList($where = array())
    {
        $where['is_effect'] = 1;
        $where['is_delete'] = 0;
        return $this->where($where)->select();
    }
    /**
     * 根据VIP添加VIP时间
     * @param [type] $user_id [description]
     * @param [type] $vip_id  [description]
     */
    public function addVip($user_id, $vip_id)
    {
        $vip = $this->field('month,vip_lv,is_repeatedly,gift_num,gift_vip_id,gift_num_time,gift_unit_time')->selectOne(array('id' => $vip_id));
        if (!$vip) {
            return false;
        }
        if (!$vip['is_repeatedly'] && $this->isVip($user_id, $vip['vip_lv']) && $vip['vip_lv'] < $this->getVip($user_id)) {
            return false;
        }
        $num_time = $vip['month'];
        $model    = Model::build('user');
        $where    = array('id' => $user_id);
        $user     = $model->field('vip_date,vip')->selectOne($where);
        $now_time = date('Y-m-d', NOW_TIME);
        if ($vip['vip_lv'] == $user['vip'] && $now_time < $user['vip_date']) {
            $now_time = $user['vip_date'];
        }
        if ($vip['gift_num'] && $vip['gift_vip_id'] && $vip['gift_num_time']) {
            Model::build('vip_exchange')->addExchange($vip['gift_num'], $vip['gift_vip_id'], $user_id, $vip['gift_num_time'], $vip['gift_unit_time']);
        }
        $time = strtotime("+$num_time month", strtotime($now_time));
        return $model->update(array('vip' => $vip['vip_lv'], 'vip_date' => date('Y-m-d', $time)), $where);
    }
    public function isVip($user_id, $vip_lv = 1)
    {
        if (!$user_id) {
            return false;
        }
        $vip = $this->getVip($user_id);
        if (!$vip) {
            return false;
        }
        return $vip >= $vip_lv;
    }
    public function getVip($user_id)
    {
        $user = Model::build('user')->field('vip,vip_date')->selectOne(array('id' => $user_id));
        return strtotime($user['vip_date'] . ' 23:59:59') > NOW_TIME ? $user['vip'] : 0;
    }
}
