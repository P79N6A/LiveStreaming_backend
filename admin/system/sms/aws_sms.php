<?php
// +----------------------------------------------------------------------
// | Zi Ping Fango2o商业系统 最新版V3.03.3285  含4个手机APP。
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'aws';
    /* 名称 */
    $module['name'] = "亚马逊短信";
    $module['server_url'] = 'https://aws.amazon.com/cn/sns/';

    if (ACTION_NAME == "install" || ACTION_NAME == "edit") {
        $module['lang'] = array(
            'region' => '地区（默认美国东部 (弗吉尼亚北部)）',
            'content' => '短信内容(每条 SMS 消息最多能包含 140 字节)'
        );
        $module['config'] = array(
            'region' => array(
                'INPUT_TYPE' => '0',
                'INPUT_VALUE' => 'us-east-1'
            ),
            'content' => array(
                'INPUT_TYPE' => '4',
                'INPUT_VALUE' => '验证码${code}，您正在进行${product}身份验证，打死不要告诉别人哦！'
            )
        );
    }

    return $module;
}

// 企信通短信平台
require_once APP_ROOT_PATH . "system/libs/sms.php"; //引入接口

class aws_sms implements sms
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
        require_once APP_ROOT_PATH . 'vendor/autoload.php';
        if (!is_array($mobile_number)) {
            $mobile_number = array($mobile_number);
        }
        $m_config = load_auto_cache('m_config');
        try {
            $sms = new \Aws\Sns\SnsClient([
                'region' => $this->sms['config']['region'] ?: 'us-east-1', //这是亚马逊在新加坡的服务器，具体要根据情况决定
                'credentials' => [
                    'key' => $this->sms['user_name'],
                    'secret' => $this->sms['password']
                ],
                'version' => 'latest',
                'debug' => false
            ]);
            $message = strtr($this->sms['config']['content'], array('${code}' => $content, '${product}' => $m_config['program_title']));
            foreach ($mobile_number as &$value) {
                $result = $sms->Publish([
                    "SenderID" => 'SendBySystem',
                    "SMSType" => $is_adv ? "Promotional" : "Transactional",
                    "Message" => $message,
                    "PhoneNumber" => '+' . str_replace('+', '', $value)
                ])->toArray();
            }
            if (!empty($result['MessageId'])) {
                return array('status' => 1, 'msg' => '发送成功');
            } else {
                return array('status' => 0, 'msg' => '发送失败');
            }
        } catch (\Exception $e) {
            return array('status' => 0, 'msg' => '发送失败');
        }
    }

    public function getSmsInfo()
    {
        return "亚马逊短信";
    }

    public function check_fee()
    {
        return '暂无该功能';
    }
}
