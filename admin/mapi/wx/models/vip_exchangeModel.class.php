<?php
/**
 *
 */
class vip_exchangeModel extends NewModel
{
    /**
     * VIP兑换码兑换VIP
     * @param  [type] $code    [description]
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function exchange($code, $user_id)
    {
        $exchange = $this->field('id,vip_id,effect_time')->selectOne(array('code' => $code, 'is_effect' => 1, 'is_delete' => 0));
        if (!$exchange) {
            return false;
        }
        if ($exchange['effect_time'] && $exchange['effect_time'] < NOW_TIME) {
            $this->update(array('is_effect' => 0), array('id' => $exchange['id']));
            return false;
        }
        $model = Model::build('vip');
        $res   = $model->addVip($user_id, $exchange['vip_id']);
        if (!$res) {
            return false;
        }
        return $this->update(array('is_effect' => 0, 'to_user_id' => $user_id), array('id' => $exchange['id']));
    }
    /**
     * 添加兑换码
     * @param [type] $num       生成兑换码条数
     * @param [type] $vip_id    VIP类型id
     * @param [type] $user_id   用户id
     * @param [type] $num_time  兑换码有效期数量（默认：1）
     * @param string $unit_time 兑换码有效期单位（默认为月：'month'）
     */
    public function addExchange($num, $vip_id, $user_id, $num_time = 1, $unit_time = 'month')
    {
        $num     = intval($num);
        $vip_id  = intval($vip_id);
        $user_id = intval($user_id);
        $num     = intval($num);
        if (!($vip_id && $user_id && $num)) {
            return false;
        }
        if ($num < 1) {
            return false;
        }
        $data = array(
            'vip_id'      => $vip_id,
            'admin_id'    => 0,
            'user_id'     => $user_id,
            'is_effect'   => 1,
            'effect_time' => strtotime("+$num_time $unit_time", NOW_TIME),
        );
        Connect::beginTransaction();
        for ($i = 0; $i < $num; $i++) {
            $data['code'] = substr(md5(uniqid('', 1)), 0, 16);
            $res          = $this->insert($data);
            if (!$res) {
                Connect::rollback();
                return false;
                break;
            }
        }
        Connect::commit();
        return true;
    }
}
