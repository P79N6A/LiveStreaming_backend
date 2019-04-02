<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class testModule extends baseModule
{
    /**
     * 获得礼物列表
     */
    public function test()
    {

        $info = "<xml>
<return_code><![CDATA[SUCCESS]]></return_code>
<return_msg><![CDATA[]]></return_msg>
<nonce_str><![CDATA[qyzf308199]]></nonce_str>
<result_code><![CDATA[SUCCESS]]></result_code>
<partner_trade_no><![CDATA[wx4330149334058335714]]></partner_trade_no>
<payment_no><![CDATA[1000018301201704287354747565]]></payment_no>
<payment_time><![CDATA[2017-04-28 08:49:44]]></payment_time>
</xml>";

        $return = (array) simplexml_load_string($info, 'SimpleXMLElement', LIBXML_NOCDATA);
        print_r($return);
        echo "<hr/>";
        print_r($return['return_code']);
        echo "<hr/>";
        print_r($return['result_code']);
        echo "<hr/>";

        print_r($return);
        echo "<hr/>";

        if ($return['return_code'] == 'SUCCESS' && $return['result_code'] == 'SUCCESS') {
            $sql = "update " . DB_PREFIX . "user_refund set pay_log='已付款',is_pay = 3,partner_trade_no = '" . $partner_trade_no . "',pay_time = " . get_gmtime() . " where id = " . $order_id;
            print_r($sql);
            echo "<hr/>";
            //$GLOBALS['db']->query($sql);
            $sql = "update " . DB_PREFIX . "payment set total_amount = total_amount + " . $msg_item['money'] . " where id = " . $payment_info['id'];
            print_r($sql);
            echo "<hr/>";
            //$GLOBALS['db']->query($sql);
            return true;
        }
    }
}
