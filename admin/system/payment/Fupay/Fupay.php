<?php

class JSON
{
    const JSVAL_TEXT = 12001;
    const JSVAL_STRING = 12002;
    const JSVAL_REGEXP = 12003;
    const JSVAL_COMMT1 = 12004;
    const JSVAL_COMMT2 = 12005;
    private $fields;
    private $assoc = false;
    public function __construct($type, $assoc = false)
    {
        $this->assoc = $assoc;
        $this->fields = ($type == '[') || $this->assoc ? [] : new stdClass();
    }
    public function add_name(&$text)
    {
        $this->name = $text;
        $text = '';
    }
    public function add_value(&$text)
    {
        if (!isset($this->name)) {
            $this->fields[] = $text;
        } else {
            if ($this->assoc) {
                $this->fields[$this->name] = $text;
            } else {
                $this->fields->{$this->name} = $text;
            }
        }
        $text = '';
    }
    public static function decode($json, $assoc = false)
    {
        try {
            if ($data = json_decode($json, $assoc)) {
                return $data;
            }
            $stack = [];
            $text = "";
            $state = self::JSVAL_TEXT;
            $len = strlen($json);
            for ($i = 0; $i != $len; $i++) {
                $c = $json[$i];
                switch ($state) {
                    case self::JSVAL_TEXT:
                        switch ($c) {
                            case '{':
                            case '[':
                                array_unshift($stack, new self($c, $assoc));
                                break;
                            case '}':
                            case ']':
                                if (isset($stack[0])) {
                                    $stack[0]->add_value($text);
                                    $text = array_shift($stack)->fields;
                                }
                                break;
                            case ':':
                                if (isset($stack[0])) {
                                    $stack[0]->add_name($text);
                                }
                                break;
                            case ',':
                                if (isset($stack[0])) {
                                    $stack[0]->add_value($text);
                                }
                                break;
                            case '"':
                            case "'":
                                $closer = $c;
                                $state = self::JSVAL_STRING;
                                break;
                            case '/':
                                // assert($i != ($len - 1));
                                switch ($json[$i + 1]) {
                                    case '/':
                                        $state = self::JSVAL_COMMT1;
                                        break;
                                    case '*':
                                        $state = self::JSVAL_COMMT2;
                                        break;
                                    default:
                                        $state = self::JSVAL_REGEXP;
                                        $text .= $c;
                                }
                                break;
                            case "\r":
                            case "\n":
                            case "\t":
                            case ' ':break;
                            default:
                                $text .= $c;
                        }
                        break;
                    case self::JSVAL_STRING:
                        if ($c != $closer) {
                            $text .= $c;
                        } else {
                            $state = self::JSVAL_TEXT;
                        }
                        break;
                    case self::JSVAL_REGEXP:
                        if (($c != ',') && ($c != '}')) {
                            $text .= $c;
                        } else {
                            $i--;
                            $state = self::JSVAL_TEXT;
                        }
                        break;
                    case self::JSVAL_COMMT1:
                        if (($c == "\r") || ($c == "\n")) {
                            $state = self::JSVAL_TEXT;
                        }
                        break;
                    case self::JSVAL_COMMT2:
                        if ($c != '*') {
                            break;
                        }
                        // assert($i != ($len - 1));
                        if ($json[$i + 1] == '/') {
                            $i++;
                            $state = self::JSVAL_TEXT;
                        }
                }
            }
            // assert($state == self::JSVAL_TEXT);
            return is_object($text) || is_array($text) ? $text : null;
        } catch (Exception $e) {
            return null;
        }
    }
    public static function encode($data)
    {
        return json_encode($data);
    }
}
/**
 * summary
 */
class Fupay
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
        $config['version'] = !empty($config['version']) ? $config['version'] : '2.3';
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
        $tmp = ['mchId' => '', 'mchOrderNo' => '', 'mchOrderTime' => '', 'channelId' => '', 'amount' => '', 'notifyUrl' => '', 'returnUrl' => '', 'memberName' => '', 'version' => '', 'key' => $this->config['key']];
        return md5(urldecode(http_build_query(array_intersect_key($data, $tmp) + $tmp)));
    }

    public function create($option = [])
    {
        $url = 'http://pay.gateway8.com/api/pay/create_order';
        $data = ['mchId' => $this->config['mchid'], 'mchOrderNo' => $option['out_trade_no'], 'mchOrderTime' => date('YmdHis'), 'channelId' => '1001', 'amount' => bcmul($option['amount'], 100), 'goodsName' => isset($option['goods_name']) ? $option['goods_name'] : 'none', 'goodsNum' => isset($option['goods_num']) ? $option['goods_num'] : 1, 'goodsDesc' => isset($option['goods_desc']) ? $option['goods_desc'] : 'none', 'notifyUrl' => $this->config['notify_url'], 'returnUrl' => $this->config['return_url'], 'memberName' => isset($option['member']) ? $option['member'] : 'none', 'remark' => isset($option['remark']) ? $option['remark'] : 'none', 'version' => $this->config['version']];
        $sign = $this->sign($data);
        // $result = $this->request(['url' => $url, 'method' => 'POST', 'data' => ['param' => json_encode($data), 'sign' => $sign]]);
        return vsprintf('%s%s%s', [$url, (strpos($url, '?') !== false ? '&' : '?'), http_build_query(['param' => json_encode($data), 'sign' => $sign])]);
        // return $result;
    }

    private function verify_sign($data)
    {
        $tmp = ['mchId' => '', 'mchOrderNo' => '', 'orderCode' => '', 'result' => '', 'amount' => '', 'memberName' => '', 'key' => $this->config['key']];
        return md5(urldecode(http_build_query(array_intersect_key($data, $tmp) + $tmp)));
    }

    public function verify($data = [])
    {
        if (!isset($data['param'])) {
            throw new Exception('The parameter param does not exist.');
        }
        $param = JSON::decode($data['param'], true);
        if (!is_array($param)) {
            throw new Exception('Parameter param parsing failed');
        }
        if (!isset($data['sign'])) {
            throw new Exception('The parameter sign does not exist.');
        }
        if (hash_equals($this->verify_sign($param), strtolower($data['sign']))) {
            return $param;
        }
        return false;
    }
}
