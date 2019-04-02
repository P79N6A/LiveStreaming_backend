<?php
// +----------------------------------------------------------------------
// | Zi Ping Fango2o商业系统 最新版V3.03.3285  含4个手机APP。
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'alisms';
    /* 名称 */
    $module['name'] = "阿里云短信";
    $module['server_url'] = 'http://www.alisms.com/sms';

    if (ACTION_NAME == "install" || ACTION_NAME == "edit") {
        $module['lang'] = array(
            'sign_name' => '短信签名',
            'tpl' => '签名模板ID',
            'param' => '短信内的变量',
        );
        $module['config'] = array(
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
require_once APP_ROOT_PATH . "system/sms/alisms/ali_sms.php";

class alisms_sms implements sms
{
    public $sms;

    public function __construct($smsInfo = '')
    {
        if (!empty($smsInfo)) {
            $this->sms = $smsInfo;
        }
    }
    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    private function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false)
    {
        $apiParams = array_merge(array(
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http') . "://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";
        $sms = new ali_sms(-1, -1, -1, true);
        try {
            $result = $sms->request($url, null, 'GET', array(
                "x-sdk-client" => "php/2.0.0",
            ));
            return json_decode($result['body'], true);
        } catch (\Exception $e) {
            return false;
        }
    }
    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    public function sendSMS($mobile_number, $content, $is_adv = 0)
    {
        $params = array();
        // fixme 必填: 待发送手机号。支持JSON格式的批量调用，批量上限为100个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $params["PhoneNumberJson"] = is_array($mobile_number) ? $mobile_number : array(
            $mobile_number,
        );

        // fixme 必填: 短信签名，支持不同的号码发送不同的短信签名，每个签名都应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignNameJson"] = array(
            $this->sms['config']['sign_name'],
        );

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $this->sms['config']['tpl'];

        // fixme 必填: 模板中的变量替换JSON串,如模板内容为"亲爱的${name},您的验证码为${code}"时,此处的值为
        // 友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $params["TemplateParamJson"] = array(
            array(
                $this->sms['config']['param'] => preg_replace('/[^\d]+/', '', $content),
            ),
        );

        // todo 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        // $params["SmsUpExtendCodeJson"] = json_encode(array("90997","90998"));

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        $params["TemplateParamJson"] = json_encode($params["TemplateParamJson"], JSON_UNESCAPED_UNICODE);
        $params["SignNameJson"] = json_encode($params["SignNameJson"], JSON_UNESCAPED_UNICODE);
        $params["PhoneNumberJson"] = json_encode($params["PhoneNumberJson"], JSON_UNESCAPED_UNICODE);

        if (!empty($params["SmsUpExtendCodeJson"]) && is_array($params["SmsUpExtendCodeJson"])) {
            $params["SmsUpExtendCodeJson"] = json_encode($params["SmsUpExtendCodeJson"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求

        // 此处可能会抛出异常，注意catch
        $content = $this->request(
            $this->sms['user_name'],
            $this->sms['password'],
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendBatchSms",
                "Version" => "2017-05-25",
            ))
        );
        if (!empty($content) && is_array($content) && array_key_exists('Code', $content) && ($content['Code'] == 'OK')) {
            $result['status'] = 1;
            $result['msg'] = '发送成功！';
        } else {
            $result['status'] = 0;
            $result['msg'] = $content['Message'];
        }
        return $result;
    }

    public function getSmsInfo()
    {
        return "阿里云短信";
    }

    public function check_fee()
    {
        return '暂无该功能';
    }
}
