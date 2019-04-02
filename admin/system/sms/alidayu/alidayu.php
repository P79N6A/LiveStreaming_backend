<?php

require_once __DIR__ . "/TopSdk.php";

/**
 * 阿里短信Api
 * @author 李航
 */
class Alidayu
{
    /**
     * 短信发送
     * @author lihang 李航
     * @param int $mobile 发送号码
     * @param strint $templateId 模板ID
     * @param array $content 发送内容
     * @return int/bool/object/array
     */
    public static function send($appId, $secret, $mobile, $sms_type = 'normal', $sign_name, $templateId, $param, $content)
    {
        $smsParam = json_encode(array($param => $content));
        $c = new TopClient;
        $c->appkey = $appId;
        $c->secretKey = $secret;
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType($sms_type);
        $req->setSmsFreeSignName($sign_name);
        $req->setSmsParam($smsParam);
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode($templateId);
        return json_decode(json_encode($c->execute($req)), true);
    }
}
