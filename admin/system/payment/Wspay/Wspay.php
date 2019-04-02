<?php
/**
 * summary
 */
class Wspay
{

    private $config = array();

    public function __construct($config = [])
    {
        if (!isset($config['mchid'])) {
            throw new Exception('Missing Config -- [mchid]');
        }
        if (!isset($config['key'])) {
            throw new Exception('Missing Config -- [key]');
        }
        // if (!isset($config['notify_url'])) {
        //     throw new Exception('Missing Config -- [notify_url]');
        // }
        // if (!isset($config['return_url'])) {
        //     throw new Exception('Missing Config -- [return_url]');
        // }
        $config['model'] = !empty($config['model']) ? $config['model'] : 'ZFBZF';
        $this->config = $config;
    }

    public function request($option = [])
    {
        $option += ['url' => null, 'method' => 'GET', 'gzip' => true, 'data' => null, 'header' => []];
        $curl = curl_init();
        if ($option['gzip']) {
            $option['header']['accept-encoding'] = 'gzip, deflate, identity';
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate,identity');
        }
        if ((strtoupper($option['method']) === 'GET') && (!is_null($option['data']) && $option['data'] != '')) {
            $option['url'] = vsprintf('%s%s%s', [$option['url'], (strpos($option['url'], '?') !== false ? '&' : '?'), is_array($option['data']) ? http_build_query($option['data']) : $option['data']]);
        }
        foreach ($option['header'] as $key => &$value) {
            $value = is_int($key) ? $value : $key . ': ' . $value;
        }
        curl_setopt_array($curl, [CURLOPT_URL => $option['url'], CURLOPT_CUSTOMREQUEST => $option['method'], CURLOPT_HTTPHEADER => $option['header'], CURLOPT_AUTOREFERER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false, CURLOPT_NOBODY => false, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false]);

        if (in_array(strtoupper($option['method']), ['POST', 'PATCH', 'PUT']) && !is_null($option['data'])) {
            $post = is_array($option['data']) ? http_build_query($option['data']) : $option['data'];
            curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post]);
        }
        list($data, $errno, $error) = [(object) ['body' => curl_exec($curl), 'header' => curl_getinfo($curl), 'http_code' => curl_getinfo($curl, CURLINFO_HTTP_CODE)], curl_errno($curl), curl_error($curl), curl_close($curl)];
        if ($errno !== 0) {
            throw new Exception($error, $errno);
        }
        return $data;
    }

    private function sign($data)
    {
        $tmp = ['merchantId' => '', 'totalAmount' => '', 'corp_flow_no' => '', 'key' => $this->config['key']];
        return md5(vsprintf('%spay%s%s%s', array_intersect_key($data, $tmp) + $tmp));
    }

    public function create($option = [])
    {
        $url = 'https://merchants.wszzff.com:8081/trade/doPay.do';
        $data = [
            'merchantId' => $this->config['mchid'],
            'totalAmount' => $option['amount'],
            'desc' => isset($option['goods_desc']) ? $option['goods_desc'] : 'none',
            'corp_flow_no' => $option['out_trade_no'],
            'model' => $this->config['model'],
            'notify_url' => $this->config['notify_url'],
            'client_ip' => isset($option['client_ip']) ? $option['client_ip'] : '127.0.0.1'
        ];
        $data['sign'] = $this->sign($data);
        $result = $this->request(['url' => $url, 'method' => 'POST', 'data' => $data]);
        if ($data = json_decode($result->body, true)) {
            if ($data['Result']) {
                return $data['Msg'];
            }
            throw new Exception($data['Msg']);
        }
        throw new Exception('Request api error.');
    }

    private function verify_sign($data)
    {
        $tmp = ['merchantId' => '', 'corp_flow_no' => '', 'reqMsgId' => '', 'respType' => '', 'key' => $this->config['key']];
        return md5(implode('', array_intersect_key($data, $tmp) + $tmp));
    }

    public function verify($data = [])
    {
        if (!isset($data['sign'])) {
            throw new Exception('The parameter sign does not exist.');
        }
        if (hash_equals($this->verify_sign($data), strtolower($data['sign']))) {
            return $data;
        }
        return false;
    }
}