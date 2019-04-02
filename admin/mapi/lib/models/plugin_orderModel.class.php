<?php
/**
 *
 */
class plugin_orderModel extends NewModel
{
    public function buyPlugin($user_id, $plugin_id)
    {
        $user_id   = intval($user_id);
        $plugin_id = intval($plugin_id);
        if (defined('BUY_PLUGIN_ONCE') && BUY_PLUGIN_ONCE) {
            $has_plugin = $this->select(['user_id' => $user_id, 'plugin_id' => $plugin_id]);
            if ($has_plugin) {
                return '已购买插件';
            }
        }
        $plugin_model = self::build('plugin');
        $plugin       = $plugin_model->field('price,name')->selectOne(['id' => $plugin_id]);

        $money      = $plugin['price'];
        $user_model = self::build('user');
        if ($money > 0) {

            $res = $user_model->coin($user_id, -$money, 'diamonds');
            if (!$res) {
                return '余额不足';
            }
            $account_diamonds = $user_model->coin($user_id, false, 'diamonds');
            self::build('coin_log')->addLog($user_id, -1, -$money, $account_diamonds, $plugin['name'] . '插件购买');
        } else {
            $account_diamonds = $user_model->coin($user_id, false, 'diamonds');
        }
        $create_time = NOW_TIME;
        $this->insert(compact('plugin_id', 'user_id', 'create_time'));
        return $account_diamonds;
    }
}
