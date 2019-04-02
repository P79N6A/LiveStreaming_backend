<?php
class gfpay
{
    private $signature;
    private $API_Pay_Url = 'http://api.gf-info.com/wapPay';

    public function __construct($partner_id,$merchantPrivateKey){
        $this->account = $partner_id;
        $this->signature = $merchantPrivateKey;
    }

    public  function get_reveiveData($post_data){
        $temp='';
        ksort($post_data);//对数组进行排序
        //遍历数组进行字符串的拼接
        foreach ($post_data as $x=>$x_value){
            if ($x_value != null){
                $temp = $temp.$x."=".iconv('UTF-8','GBK//IGNORE',$x_value)."&";
            }
        }
        //MD5转码
        $md5=md5($temp.$this->signature);
        $reveiveData = $temp.'signature'.'='.$md5;
        return $reveiveData;
    }

    public  function  get_url($reveiveData){
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $this->API_Pay_Url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, false);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $reveiveData);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        return $data;
    }

    public  function get_md5($request){
        $result = array('status'=>1,'error'=>'');
        if($request){
            $post_data = array();
            foreach ($request as $key=>$value){
                $post_data = array_merge($post_data,array($key=>$value));
            }
            $temp='';
            ksort($post_data);//对数组进行排序
            //遍历数组进行字符串的拼接
            foreach ($post_data as $x=>$x_value){
                if ($x != 'signature'&& $x_value != null){
                    $temp = $temp.$x."=".$x_value."&";
                }
            }
            $md5=strtoupper(md5($temp.$this->signature));
            $result['md5']= $md5;
        }else{
            $result['status']= 0;
            $result['error']= '无接收参数';
        }
        return $result;
    }

}