<?php

namespace luoyy;

use luoyy\JSObject;
use \Exception;
use Requests;

/**
 * summary
 */
class KjPay
{
    // 支付宝即时到账
    const DIRECT_PAY_URL = 'http://api.kj-pay.com/alipay/direct_pay';
    // 支付宝WAP支付
    const WAP_PAY_URL = 'http://api.kj-pay.com/alipay/wap_pay';
    // 支付宝APP支付
    const APP_PAY_URL = 'http://api.kj-pay.com/alipay/app_pay';
    // 支付宝JS支付
    const JS_PAY_URL = 'http://api.kj-pay.com/alipay/js_pay';
    // 支付宝扫码支付
    const SCAN_PAY_URL = 'http://api.kj-pay.com/alipay/scan_pay';
    // 订单查询
    const QUERY_PAY_URL = 'http://api.kj-pay.com/alipay/query_pay';
    // 退款
    const TRADE_REFUND_URL = 'http://api.kj-pay.com/alipay/trade_refund';
    /**
     * [$merchantid 配置文件]
     * @var [type]
     */
    private $config;
    /**
     * @var mixed
     */
    private static $request;

    /**
     * [$gateways 驱动]
     * @var [type]
     */
    private $gateways;
    /**
     * 初始化配置
     */
    public function __construct($config = [])
    {
        $config = array_merge(['merchantid' => '', 'merchantkey' => '', 'notify_url' => '', 'return_url' => ''], $config);
        if (empty($config['merchantid'])) {
            throw new Exception('Missing Config -- [merchantid]');
        }
        if (empty($config['merchantkey'])) {
            throw new Exception('Missing Config -- [merchantkey]');
        }
        $this->config = $config;
    }
    /**
     * [paraFilters 过滤参数用作与签名]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T11:23:08+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     [type]                   $para      [description]
     * @return    [type]                              [description]
     */
    private function paraFilters($para = [])
    {
        unset($para['sign']);
        return array_filter($para, function ($v) {
            return $v !== '';
        });
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    private function argSorts($para)
    {
        ksort($para);
        return $para;
    }

    /**
     * 签名验证-快接支付
     * $datas 数据数组
     * $key 密钥
     */
    private function sign($data = [])
    {
        return md5(sprintf('%s&key=%s', urldecode(http_build_query($this->argSorts($this->paraFilters($data)))), $this->config['merchantkey']));
    }

    /**
     * [gateway 设置网关]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T11:10:16+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     [type]                   $gateway    [description]
     * @return    [type]                              [description]
     */
    public function gateway($gateway)
    {
        if (!method_exists($this, $gateway)) {
            throw new Exception('Driver is not defined.');
        }
        $this->gateways = $gateway;
        return $this;
    }

    /**
     * 验证支付宝支付宝通知
     * @param array $data 通知数据
     * @param null $sign 数据签名
     * @return array|bool
     */
    public function verify($data, $sign = null)
    {
        if (empty($this->config['merchantkey'])) {
            throw new Exception('Missing Config -- [merchantkey]');
        }
        $sign = is_null($sign) ? $data['sign'] : $sign;
        return $this->sign($data) === $sign ? $data : false;
    }

    /**
     * [apply 调用网关]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:43:24+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    public function apply($data = [])
    {
        $data['merchant_no'] = $this->config['merchantid'];
        $data['notify_url'] = $this->config['notify_url'];
        $data['sign_type'] = '1';
        $result = call_user_func_array([$this, $this->gateways], [$data]);
        if ($result->status_code == 200) {
            return JSObject::decode($result->body, true);
        }
        throw new Exception('Response Error.');
    }
    /**
     * [direct 即时到账]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T11:46:53+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function direct($data = [])
    {
        $data['return_url'] = $this->config['return_url'];
        $data['sign'] = $this->sign($data);
        return Requests::post(self::DIRECT_PAY_URL, [], $data);
    }
    /**
     * [wap wap支付]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:32:19+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function wap($data = [])
    {
        $data['return_url'] = $this->config['return_url'];
        $data['sign'] = $this->sign($data);
        return Requests::post(self::WAP_PAY_URL, [], $data);
    }
    /**
     * [app APP支付]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:34:01+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function app($data = [])
    {
        $data['sign'] = $this->sign($data);
        return Requests::post(self::APP_PAY_URL, [], $data);
    }
    /**
     * [js JS支付]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:36:02+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function js($data = [])
    {
        $data['sign'] = $this->sign($data);
        return Requests::post(self::JS_PAY_URL, [], $data);
    }
    /**
     * [scan 扫码支付]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:36:59+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function scan($data = [])
    {
        $data['sign'] = $this->sign($data);
        return Requests::post(self::SCAN_PAY_URL, [], $data);
    }
    /**
     * [query 订单查询]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:37:48+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function query($data = [])
    {
        $data['sign'] = $this->sign($data);
        return Requests::post(self::QUERY_PAY_URL, [], $data);
    }
    /**
     * [trade 退款]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2017-12-19T13:39:08+0800
     * @copyright (c)                      ZiShang520 All           Rights Reserved
     * @param     array                    $data      [description]
     * @return    [type]                              [description]
     */
    private function trade($data = [])
    {
        $data['sign'] = $this->sign($data);
        return Requests::post(self::TRADE_REFUND_URL, [], $data);
    }
}
