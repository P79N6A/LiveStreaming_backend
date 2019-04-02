<?php
// +----------------------------------------------------------------------
// | Zi Ping Fango2o商业系统 最新版V3.03.3285  含4个手机APP。
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'alidayu';
    /* 名称 */
    $module['name'] = "阿里短信企业版";
    $module['server_url'] = 'http://www.alisms.com/sms';

    if (ACTION_NAME == "install" || ACTION_NAME == "edit") {
        $module['lang'] = array(
            'sign_type' => '短信类型(默认normal)',
            'sign_name' => '短信签名',
            'tpl' => '签名模板ID',
            'param' => '短信内的变量',
        );
        $module['config'] = array(
            'sign_type' => array(
                'INPUT_TYPE' => '0',
                'INPUT_VALUE' => 'normal',
            ), //合作者身份ID
            'sign_name' => array(
                'INPUT_TYPE' => '0',
            ), //合作者身份ID
            'tpl' => array(
                'INPUT_TYPE' => '0',
            ), //支付宝帐号:
            'param' => array(
                'INPUT_TYPE' => '0',
            ), //支付宝帐号:
        );
    }

    return $module;
}

// 企信通短信平台
require_once APP_ROOT_PATH . "system/libs/sms.php"; //引入接口
require_once APP_ROOT_PATH . "system/sms/alidayu/alidayu.php";

class alidayu_sms implements sms
{
    public $sms;

    public function __construct($smsInfo = '')
    {
        if (!empty($smsInfo)) {
            $this->sms = $smsInfo;
        }
    }

    public function sendSMS($mobile_number, $content, $is_adv = 0)
    {

        $mobile_number = is_array($mobile_number) ? null : $mobile_number;
        $content = Alidayu::send($this->sms['user_name'], $this->sms['password'], $mobile_number, $this->sms['config']['sign_type'], $this->sms['config']['sign_name'], $this->sms['config']['tpl'], $this->sms['config']['param'], preg_replace('/[^\d]+/', '', $content));
        if (!empty($content) && is_array($content) && array_key_exists('code', $content) && array_key_exists('msg', $content)) {
            $result['status'] = 0;
            $result['msg'] = $content['msg'];
        } else {
            $result['status'] = 1;
            $result['msg'] = '发送成功！';
        }
        return $result;
    }

    public function getSmsInfo()
    {
        return "阿里短信企业版";
    }

    public function check_fee()
    {
        return '暂无该功能';
    }
}
