<?php
    fanwe_require(APP_ROOT_PATH."system/payment/Nowpay/services/Core.php");
    fanwe_require(APP_ROOT_PATH."system/payment/Nowpay/services/Net.php");
    fanwe_require(APP_ROOT_PATH."system/payment/Nowpay/conf/Config.php");
    fanwe_require(APP_ROOT_PATH."system/payment/Nowpay/utils/Log.php");
    if(function_exists("date_default_timezone_set")){
        date_default_timezone_set(Config::$timezone);
    }
    /**
     * 
     * @author Jupiter
     * 接口服务类
     * 
     * 用于处理通知以及查询等请求
     */
    class Services{
        /**
         * 查询处理方法
         * @param array $req
         * @param array $para
         */
        public static function queryOrder(Array $req,Array &$para){
            //组合报文
            $req_str=self::buildReq($req);
            Log::outLog("订单查询(商户->中怡同创)", $req_str);
            //推送给中小开发者支付系统
            $resp_str=Net::sendMessage($req_str, Config::$query_url);
            Log::outLog("订单查询(商户->中怡同创)", $resp_str);
            //验证签名
            return self::verifyResponse($resp_str, $para);
        }
        
        private static function buildReq(Array $req){
            return Core::createLinkString($req, false, true);
        }
        
        public static function verifySignature($para){
            $respSignature=$para[Config::SIGNATURE_KEY];
            Log::outLog("原签名", $respSignature);
            $filteredReq=Core::paraFilter($para);
            $signature=Core::buildSignature($filteredReq);
            Log::outLog("核对签名", $signature);
            if ($respSignature!=""&&$respSignature==$signature) {
                return TRUE;
            }else {
                return FALSE;
            }
        }
        
        public static function buildSignature(Array $para){
            $filteredReq=Core::paraFilter($para);
            return Core::buildSignature($filteredReq);
        }
        
        public static function verifyResponse($resp_str,&$resp){
            if ($resp_str!="") {
                parse_str($resp_str,$para);
                
                $signIsValid=self::verifySignature($para);
                $resp=$para;
                if ($signIsValid) {
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }