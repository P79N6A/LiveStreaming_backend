<?php
    /**
     * 
     * @author Jupiter
     * 
     * 应用配置类
     */
    class Config{
        static $timezone="Asia/Shanghai";
        static $app_id="1408709961320306";//该处配置您的APPID
        static $secure_key="0nqIDgkOnNBD6qoqO5U68RO1fNqiaisg";//该处配置您的应用秘钥
        static $query_url="http://api.ipaynow.cn";
        
        const VERIFY_HTTPS_CERT=false;
        const QUERY_FUNCODE_KEY="MQ002";
        const SIGNATURE_KEY="signature";
        const SIGNTYPE_KEY="signType";
        const MHT_SIGN_TYPE_KEY="mhtSignType";
        const MHT_SIGNATURE_KEY="mhtSignature";
        const CHARSET="UTF-8";
        const SIGN_TYPE="MD5";
        const QSTRING_EQUAL="=";
        const QSTRING_SPLIT="&";
    }