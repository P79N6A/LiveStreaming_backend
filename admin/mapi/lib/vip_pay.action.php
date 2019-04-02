<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class vip_payModule extends baseModule
{
    /**
     * 用户会员购买页面
     */
    public function purchase()
    {

        $root = array();
        $root['status'] = 1;
        $root['error'] = '';

        //$GLOBALS['user_info']['id'] = 320;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {

            $user_id = intval($GLOBALS['user_info']['id']); //用户ID

            $sql = "select id,is_vip,vip_expire_time from " . DB_PREFIX . "user where id = " . $user_id;
            $user = $GLOBALS['db']->getRow($sql, true, true);
            $vip_expire_time = intval($user['vip_expire_time']);

            $root['is_vip'] = intval($user['is_vip']);
            $root['vip_expire_time'] = to_date(intval($user['vip_expire_time']), 'Y-m-d H:i');
            if ($vip_expire_time > 0) {
                if ($vip_expire_time < NOW_TIME) {
                    $root['is_vip'] = 0;
                    $root['error'] = '已过期';
                    $root['vip_expire_time'] = '已过期';
                    $sql = "update " . DB_PREFIX . "user set is_vip = 0 where id = " . $user_id;
                    $GLOBALS['db']->query($sql);
                    user_deal_to_reids(array($user_id));
                } else {
                    if (intval($user['is_vip']) == 0) {
                        $root['vip_expire_time'] = '未开通';
                    }
                }
            } else {
                $root['vip_expire_time'] = '未开通';
                if (intval($user['is_vip']) == 1) {
                    $root['error'] = '永久会员';
                    $root['vip_expire_time'] = '永久';
                }
            }

            $pay_list = load_auto_cache("pay_list");
            $m_config = load_auto_cache("m_config");

            $list = array();
            //客服端手机类型dev_type=android;dev_type=ios
            $dev_type = strim($_REQUEST['sdk_type']);

            if (isios() || $dev_type == 'ios') {
                //正在审核的版本,只显示：苹果支付
                //审核帐户,只显示苹果应用内支付
                if ($GLOBALS['user_info']['mobile'] == '13888888888' || $GLOBALS['user_info']['mobile'] == '13999999999') {
                    $sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where class_name = 'Iappay' limit 0,1";
                    $pay_list = $GLOBALS['db']->getAll($sql, true, true);
                    $list = $pay_list;
                } else {
                    $ios_open_pay = intval($m_config['ios_open_pay']); //IOS默认只支持支持应用内支付,需要开放其它支付选是;
                    $sdk_version_name = strim($_REQUEST['sdk_version_name']);
                    if ($m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name || $ios_open_pay == 0) {
                        foreach ($pay_list as $k => $v) {
                            if ($v['class_name'] != 'Iappay') {
                                unset($pay_list[$k]);
                                //$pay_list[$k]['name'] = $v['class_name'].'aa';
                            } else {
                                //$pay_list[$k]['name'] = $v['class_name'];
                                $list[] = $v;
                            }
                        }
                    } else {
                        $list = $pay_list;
                    }
                }
            } else {
                //过滤苹果支付
                foreach ($pay_list as $k => $v) {
                    if ($v['class_name'] == 'Iappay') {
                        unset($pay_list[$k]);
                    } else {
                        $list[] = $v;
                    }
                }
            }

            if ($list) {
                $root['pay_list'] = $list;
            }

            $rule_list = load_auto_cache("vip_rule_list");

            foreach ($pay_list as $k => $v) {
                if ($v['class_name'] == 'Iappay') {
                    if (intval($m_config['iap_recharge']) == 0) {
                        foreach ($rule_list as $k => $v) {
                            $rule_list[$k]['money'] = $v['iap_money'];
                        }
                    }
                }

            }
            $root['rule_list'] = $rule_list;
        }

        ajax_return($root);
    }

    /**
     * 用户充值支付
     */
    public function pay()
    {

        $root = array();
        $root['status'] = 1;
        //$GLOBALS['user_info']['id'] = 1;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID

            $pay_id = intval($_REQUEST['pay_id']); //支付id
            $rule_id = intval($_REQUEST['rule_id']); //支付项目id

            if ($pay_id == 0) {
                $root['error'] = "支付id无效";
                $root['status'] = 0;
            } elseif ($rule_id == 0) {
                $root['error'] = "项目id无效或充值金额不能为0";
                $root['status'] = 0;
            } else {
                $sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where is_effect = 1 and online_pay = 3 and id =" . $pay_id;
                $pay = $GLOBALS['db']->getRow($sql, true, true);

                if ($rule_id > 0) {
                    $sql = "select money,name,iap_money,product_id,day_num from " . DB_PREFIX . "vip_rule where is_effect = 1 and id =" . $rule_id;
                    $rule = $GLOBALS['db']->getRow($sql, true, true);

                    if ($pay['class_name'] == 'Iappay') {
                        $money = $rule['iap_money'];
                    } else {
                        $money = $rule['money'];
                    }
                } else {
                    $pay = null;
                    $money = 0;
                }

                if (!$pay || $money == 0) {
                    $root['error'] = "支付id或 项目id无效";
                    $root['status'] = 0;
                } else {
                    if ($pay['class_name'] != 'Iappay') {
                        $payment_notice['create_time'] = NOW_TIME;
                        $payment_notice['user_id'] = $user_id;
                        $payment_notice['payment_id'] = $pay_id;
                        $payment_notice['money'] = $money;
                        $payment_notice['diamonds'] = $rule['day_num']; //获得的会员天数
                        $payment_notice['recharge_id'] = $rule_id;
                        $payment_notice['recharge_name'] = $rule['name'];
                        $payment_notice['product_id'] = $rule['product_id'];
                        $payment_notice['type'] = 1;
                        do {
                            $payment_notice['notice_sn'] = to_date(NOW_TIME, "YmdHis") . rand(100, 999);
                            $GLOBALS['db']->autoExecute(DB_PREFIX . "payment_notice", $payment_notice, "INSERT", "", "SILENT");
                            $notice_id = $GLOBALS['db']->insert_id();
                        } while ($notice_id == 0);
                    } else {
                        $notice_id = $rule['product_id'];
                    }

                    $class_name = $pay['class_name'] . "_payment";
                    fanwe_require(APP_ROOT_PATH . "system/payment/" . $class_name . ".php");
                    $o = new $class_name;
                    $pay = $o->get_payment_code($notice_id);

                    $root['pay'] = $pay;
                }
            }
        }

        ajax_return($root);
    }

    /**
     * 苹果应用内支付成功后，回调
     */
    public function iappay()
    {
        $root = array();
        $root['status'] = 1;

        //$GLOBALS['user_info']['id'] = 320;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $receipt_data = strim($_REQUEST['receipt-data']);
            //$receipt_data = 'ewoJInNpZ25hdHVyZSIgPSAiQXhjNnZsQWYybnRXYWJTODZTTDFBaXZYUG93VStHNkVCak9Bd0pWT2pHVzNlK0RzRUN3V3NQZHVZZ28vUmx1b3c2Y0RPcUdKRlhGbzUxS0orZGFLdDZUREN1R2tTRG8vQUpJL1FTdGgrVldBQ3lGQlNDSElOUVNSQm9Oa3UxRi8yaHFOWjFCRFc2MjFrOTc3M05SWWdZLzN4MFc3Q2NmUFhiQjJzVjRuV051WkVqY0hONWV2RGVEL2t4UFpvby8xQThBT0g0WEpKZFR1Sy9iUmpDeTd2OGF0RkVkSFpLQ1ZoSDRlbXcwQjBrU1hKaEhtLzUwWUhQc1N2ZTV3UGxubitGbWl5NnIyMnFwY0x3UEl5NStuNzYyYjNvUGkxcXZrQllCTlRHbXozeXRzVTlPdFZMNGNxU0Y0WUZVb1U0UDQrRDdYQXFNSG1uZnFyL1JCN1dKVDZSY0FBQVdBTUlJRmZEQ0NCR1NnQXdJQkFnSUlEdXRYaCtlZUNZMHdEUVlKS29aSWh2Y05BUUVGQlFBd2daWXhDekFKQmdOVkJBWVRBbFZUTVJNd0VRWURWUVFLREFwQmNIQnNaU0JKYm1NdU1Td3dLZ1lEVlFRTERDTkJjSEJzWlNCWGIzSnNaSGRwWkdVZ1JHVjJaV3h2Y0dWeUlGSmxiR0YwYVc5dWN6RkVNRUlHQTFVRUF3dzdRWEJ3YkdVZ1YyOXliR1IzYVdSbElFUmxkbVZzYjNCbGNpQlNaV3hoZEdsdmJuTWdRMlZ5ZEdsbWFXTmhkR2x2YmlCQmRYUm9iM0pwZEhrd0hoY05NVFV4TVRFek1ESXhOVEE1V2hjTk1qTXdNakEzTWpFME9EUTNXakNCaVRFM01EVUdBMVVFQXd3dVRXRmpJRUZ3Y0NCVGRHOXlaU0JoYm1RZ2FWUjFibVZ6SUZOMGIzSmxJRkpsWTJWcGNIUWdVMmxuYm1sdVp6RXNNQ29HQTFVRUN3d2pRWEJ3YkdVZ1YyOXliR1IzYVdSbElFUmxkbVZzYjNCbGNpQlNaV3hoZEdsdmJuTXhFekFSQmdOVkJBb01Da0Z3Y0d4bElFbHVZeTR4Q3pBSkJnTlZCQVlUQWxWVE1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBcGMrQi9TV2lnVnZXaCswajJqTWNqdUlqd0tYRUpzczl4cC9zU2cxVmh2K2tBdGVYeWpsVWJYMS9zbFFZbmNRc1VuR09aSHVDem9tNlNkWUk1YlNJY2M4L1cwWXV4c1FkdUFPcFdLSUVQaUY0MWR1MzBJNFNqWU5NV3lwb041UEM4cjBleE5LaERFcFlVcXNTNCszZEg1Z1ZrRFV0d3N3U3lvMUlnZmRZZUZScjZJd3hOaDlLQmd4SFZQTTNrTGl5a29sOVg2U0ZTdUhBbk9DNnBMdUNsMlAwSzVQQi9UNXZ5c0gxUEttUFVockFKUXAyRHQ3K21mNy93bXYxVzE2c2MxRkpDRmFKekVPUXpJNkJBdENnbDdaY3NhRnBhWWVRRUdnbUpqbTRIUkJ6c0FwZHhYUFEzM1k3MkMzWmlCN2o3QWZQNG83UTAvb21WWUh2NGdOSkl3SURBUUFCbzRJQjF6Q0NBZE13UHdZSUt3WUJCUVVIQVFFRU16QXhNQzhHQ0NzR0FRVUZCekFCaGlOb2RIUndPaTh2YjJOemNDNWhjSEJzWlM1amIyMHZiMk56Y0RBekxYZDNaSEl3TkRBZEJnTlZIUTRFRmdRVWthU2MvTVIydDUrZ2l2Uk45WTgyWGUwckJJVXdEQVlEVlIwVEFRSC9CQUl3QURBZkJnTlZIU01FR0RBV2dCU0lKeGNKcWJZWVlJdnM2N3IyUjFuRlVsU2p0ekNDQVI0R0ExVWRJQVNDQVJVd2dnRVJNSUlCRFFZS0tvWklodmRqWkFVR0FUQ0IvakNCd3dZSUt3WUJCUVVIQWdJd2diWU1nYk5TWld4cFlXNWpaU0J2YmlCMGFHbHpJR05sY25ScFptbGpZWFJsSUdKNUlHRnVlU0J3WVhKMGVTQmhjM04xYldWeklHRmpZMlZ3ZEdGdVkyVWdiMllnZEdobElIUm9aVzRnWVhCd2JHbGpZV0pzWlNCemRHRnVaR0Z5WkNCMFpYSnRjeUJoYm1RZ1kyOXVaR2wwYVc5dWN5QnZaaUIxYzJVc0lHTmxjblJwWm1sallYUmxJSEJ2YkdsamVTQmhibVFnWTJWeWRHbG1hV05oZEdsdmJpQndjbUZqZEdsalpTQnpkR0YwWlcxbGJuUnpMakEyQmdnckJnRUZCUWNDQVJZcWFIUjBjRG92TDNkM2R5NWhjSEJzWlM1amIyMHZZMlZ5ZEdsbWFXTmhkR1ZoZFhSb2IzSnBkSGt2TUE0R0ExVWREd0VCL3dRRUF3SUhnREFRQmdvcWhraUc5Mk5rQmdzQkJBSUZBREFOQmdrcWhraUc5dzBCQVFVRkFBT0NBUUVBRGFZYjB5NDk0MXNyQjI1Q2xtelQ2SXhETUlKZjRGelJqYjY5RDcwYS9DV1MyNHlGdzRCWjMrUGkxeTRGRkt3TjI3YTQvdncxTG56THJSZHJqbjhmNUhlNXNXZVZ0Qk5lcGhtR2R2aGFJSlhuWTR3UGMvem83Y1lmcnBuNFpVaGNvT0FvT3NBUU55MjVvQVE1SDNPNXlBWDk4dDUvR2lvcWJpc0IvS0FnWE5ucmZTZW1NL2oxbU9DK1JOdXhUR2Y4YmdwUHllSUdxTktYODZlT2ExR2lXb1IxWmRFV0JHTGp3Vi8xQ0tuUGFObVNBTW5CakxQNGpRQmt1bGhnd0h5dmozWEthYmxiS3RZZGFHNllRdlZNcHpjWm04dzdISG9aUS9PamJiOUlZQVlNTnBJcjdONFl0UkhhTFNQUWp2eWdhWndYRzU2QWV6bEhSVEJoTDhjVHFBPT0iOwoJInB1cmNoYXNlLWluZm8iID0gImV3b0pJbTl5YVdkcGJtRnNMWEIxY21Ob1lYTmxMV1JoZEdVdGNITjBJaUE5SUNJeU1ERTJMVEEzTFRNd0lEQXlPalV6T2pFMElFRnRaWEpwWTJFdlRHOXpYMEZ1WjJWc1pYTWlPd29KSW5WdWFYRjFaUzFwWkdWdWRHbG1hV1Z5SWlBOUlDSXdOakprWTJKaU1qUTVNV0V5TWpZNVptVm1NalUxWkdNMFpXUmpPVFl5T0dVMU16YzVObUU1SWpzS0NTSnZjbWxuYVc1aGJDMTBjbUZ1YzJGamRHbHZiaTFwWkNJZ1BTQWlNVEF3TURBd01ESXlOalk0TURFMU1DSTdDZ2tpWW5aeWN5SWdQU0FpTkM0eElqc0tDU0owY21GdWMyRmpkR2x2YmkxcFpDSWdQU0FpTVRBd01EQXdNREl5TmpZNE1ERTFNQ0k3Q2draWNYVmhiblJwZEhraUlEMGdJakVpT3dvSkltOXlhV2RwYm1Gc0xYQjFjbU5vWVhObExXUmhkR1V0YlhNaUlEMGdJakUwTmprNE56SXpPVFEzTVRZaU93b0pJblZ1YVhGMVpTMTJaVzVrYjNJdGFXUmxiblJwWm1sbGNpSWdQU0FpUVVGR1FUTXdNalV0T1VVM05TMDBOalk1TFVJNE1FVXRRMFZCT1VSRU56RkZOek5HSWpzS0NTSndjbTlrZFdOMExXbGtJaUE5SUNJeE1EQXdNREVpT3dvSkltbDBaVzB0YVdRaUlEMGdJakV4TXpreE16ZzNNeklpT3dvSkltSnBaQ0lnUFNBaVkyOXRMbVpoYm5kbExteHBkbVVpT3dvSkluQjFjbU5vWVhObExXUmhkR1V0YlhNaUlEMGdJakUwTmprNE56SXpPVFEzTVRZaU93b0pJbkIxY21Ob1lYTmxMV1JoZEdVaUlEMGdJakl3TVRZdE1EY3RNekFnTURrNk5UTTZNVFFnUlhSakwwZE5WQ0k3Q2draWNIVnlZMmhoYzJVdFpHRjBaUzF3YzNRaUlEMGdJakl3TVRZdE1EY3RNekFnTURJNk5UTTZNVFFnUVcxbGNtbGpZUzlNYjNOZlFXNW5aV3hsY3lJN0Nna2liM0pwWjJsdVlXd3RjSFZ5WTJoaGMyVXRaR0YwWlNJZ1BTQWlNakF4Tmkwd055MHpNQ0F3T1RvMU16b3hOQ0JGZEdNdlIwMVVJanNLZlE9PSI7CgkiZW52aXJvbm1lbnQiID0gIlNhbmRib3giOwoJInBvZCIgPSAiMTAwIjsKCSJzaWduaW5nLXN0YXR1cyIgPSAiMCI7Cn0=';

            $m_config = load_auto_cache("m_config");
            $sdk_version_name = strim($_REQUEST['sdk_version_name']);
            if ($m_config['ios_check_version'] != '') {
                //请求验证
                $data = $this->acurl($receipt_data, 0);
                //如果是沙盒数据 则验证沙盒模式
                if ($data['status'] == '21007') {
                    //请求验证
                    $data = $this->acurl($receipt_data, 1);
                }
            } else {
                //请求验证
                $data = $this->acurl($receipt_data, 0);

            }

            if ($data['status'] == 0) {
                $notice_sn = $data['receipt']['transaction_id'];

                $payment_notice = $GLOBALS['db']->getRow("select id from " . DB_PREFIX . "payment_notice where notice_sn = '" . $notice_sn . "'");
                if ($payment_notice) {
                    $root['status'] = 1;
                    $root['error'] = '支付成功';
                } else {
                    $pay_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "payment where class_name='Iappay'", true, true);

                    $product_id = $data['receipt']['product_id'];

                    $sql = "select id,money,name,iap_money,product_id,day_num from " . DB_PREFIX . "vip_rule where product_id ='" . $product_id . "'";
                    $rule = $GLOBALS['db']->getRow($sql, true, true);

                    $payment_notice = array();
                    $payment_notice['create_time'] = NOW_TIME;
                    $payment_notice['user_id'] = $user_id;
                    $payment_notice['payment_id'] = $pay_id;
                    $payment_notice['money'] = $rule['iap_money'];
                    $payment_notice['recharge_id'] = $rule['id'];
                    $payment_notice['recharge_name'] = $rule['name'];
                    $payment_notice['product_id'] = $rule['product_id'];
                    $payment_notice['notice_sn'] = $notice_sn;
                    $payment_notice['iap_receipt'] = print_r($data['receipt'], 1);
                    $payment_notice['diamonds'] = $rule['day_num'];
                    $payment_notice['type'] = 1;

                    $GLOBALS['db']->autoExecute(DB_PREFIX . "payment_notice", $payment_notice, "INSERT", "", "SILENT");
                    $notice_id = $GLOBALS['db']->insert_id();

                    //if ($notice_id > 0){
                    require_once APP_ROOT_PATH . "system/libs/cart.php";
                    $root = payment_paid($payment_notice['notice_sn'], $data['receipt']['original_transaction_id']);
                    //}
                }
            } else {
                $root['status'] = 0;
                $root['error'] = print_r($data, 1);
            }
        }

        ajax_return($root);
    }

    /**
     * 21000 App Store不能读取你提供的JSON对象
     * 21002 receipt-data域的数据有问题
     * 21003 receipt无法通过验证
     * 21004 提供的shared secret不匹配你账号中的shared secret
     * 21005 receipt服务器当前不可用
     * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
     * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
     * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
     *
    Array
    (
    [receipt] => Array
    (
    [original_purchase_date_pst] => 2016-07-30 02:53:14 America/Los_Angeles
    [purchase_date_ms] => 1469872394716
    [unique_identifier] => 062dcbb2491a2269fef255dc4edc9628e53796a9
    [original_transaction_id] => 1000000226680150
    [bvrs] => 4.1
    [transaction_id] => 1000000226680150
    [quantity] => 1
    [unique_vendor_identifier] => AAFA3025-9E75-4669-B80E-CEA9DD71E73F
    [item_id] => 1139138732
    [product_id] => 100001
    [purchase_date] => 2016-07-30 09:53:14 Etc/GMT
    [original_purchase_date] => 2016-07-30 09:53:14 Etc/GMT
    [purchase_date_pst] => 2016-07-30 02:53:14 America/Los_Angeles
    [bid] => com.fanwe.live
    [original_purchase_date_ms] => 1469872394716
    )

    [status] => 0
    )
    );
     */

    private function acurl($receipt_data, $sandbox)
    {
        //正式购买地址 沙盒购买地址
        $url_buy = "https://buy.itunes.apple.com/verifyReceipt";
        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        $url = $sandbox ? $url_sandbox : $url_buy;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("receipt-data" => $receipt_data))); //$this->encodeRequest());
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        if ($errno != 0) {
            //throw new Exception($errmsg, $errno);
            $data = array();
            $data['status'] = $errno;
            $data['error'] = $errmsg;

            return $data;
        } else {
            return json_decode($response, 1);
        }
    }

}
