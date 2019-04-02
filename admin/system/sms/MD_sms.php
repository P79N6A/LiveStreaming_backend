<?php


/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'MD';
    /* 名称 */
    $module['name'] = "秒滴云短信API";

    if (ACTION_NAME == "install" || ACTION_NAME == "edit") {
        $module['lang'] = $payment_lang;
        $module['config'] = $config;
        $module['is_effect'] = 1;
    }
    return $module;
}

// 企信通短信平台
require_once APP_ROOT_PATH . "system/libs/sms.php";  //引入接口

class MD_sms implements sms
{
    public $sms;
    public $message = "";

    public function __construct($smsInfo = '')
    {
        if (!empty($smsInfo)) {
            $this->sms = $smsInfo;
        }
    }

    public function sendSMS($mobile_number, $content)
    {


        $ACCOUNT_SID = $this->sms['user_name'];
        $AUTH_TOKEN = $this->sms['password'];
        $Sms_Sign = $this->sms['description'];

        if (is_array($mobile_number)) {
            $mobile_number = implode(",", $mobile_number);
        }
        date_default_timezone_set("Asia/Shanghai");
        $timestamp = date("YmdHis");
        $sig = md5($ACCOUNT_SID . $AUTH_TOKEN . $timestamp);
        preg_match('/\d+/', $content, $arr);
        $body = array("accountSid" => $ACCOUNT_SID, "templateid" => "546393977", "param" => $arr[0], "timestamp" => $timestamp, "sig" => $sig, "respDataType" => "JSON");
        //$scontent = "验证码为" . $arr[0];
        $body['to'] = $mobile_number;
        //$body['smsContent'] = urlencode($Sms_Sign . $scontent);

        $moduleUrl = "industrySMS/sendSMS";
        $return = $this->post($body, $moduleUrl);
        $return = json_decode($return, true);
        if ($return["respCode"] == "00000") {
            $result['status'] = 1;
            $result['msg'] = '发送成功！';
        } else {
            $result['status'] = 0;
            $result['msg'] = $return["respDesc"];
        }

        return $result;
    }

    public function getSmsInfo()
    {

        return "秒滴云短信";

    }

    public function check_fee()
    {
        $ACCOUNT_SID = $this->sms['user_name'];
        $AUTH_TOKEN = $this->sms['password'];

        date_default_timezone_set("Asia/Shanghai");
        $timestamp = date("YmdHis");
        $sig = md5($ACCOUNT_SID . $AUTH_TOKEN . $timestamp);
        $body = array("accountSid" => $ACCOUNT_SID, "timestamp" => $timestamp, "sig" => $sig, "respDataType" => "JSON");
        $moduleUrl = "query/accountInfo";
        $return = $this->post($body, $moduleUrl);
        $return = json_decode($return, true);

        if ($return["respCode"] == "00000") {
            return $return['balance'];
        } else {
            return "查询失败！";
        }
    }

    function post($body, $moduleUrl)
    {
        $BASE_URL = "https://api.miaodiyun.com/20150822/";
        $CONTENT_TYPE = "application/x-www-form-urlencoded";
        $SMS_ACCEPT = "application/json";
        // 构造请求数据
        $url = $BASE_URL . $moduleUrl;
        $headers = array('Content-type: ' . $CONTENT_TYPE, 'Accept: ' . $SMS_ACCEPT);
        // 要求post请求的消息体为&拼接的字符串，所以做下面转换
        $fields_string = "";
        foreach ($body as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        // 提交请求
        $con = curl_init();
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($con, CURLOPT_HEADER, 0);
        curl_setopt($con, CURLOPT_POST, 1);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($con, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($con);
        curl_close($con);

        return "" . $result;
    }
}

?>