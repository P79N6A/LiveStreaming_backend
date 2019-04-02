<?php


/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'HY';
    /* 名称 */
    $module['name']    = "互忆短信API";
  
    if(ACTION_NAME == "install" || ACTION_NAME == "edit"){  
	    $module['lang']  = $payment_lang;
	    $module['config'] = $config;
		$module['is_effect']=1;
    }
    return $module;
}

// 企信通短信平台
require_once APP_ROOT_PATH."system/libs/sms.php";  //引入接口

class HY_sms implements sms
{
	public $sms;
	public $message = "";
	
    public function __construct($smsInfo = '')
    { 	    	
		if(!empty($smsInfo))
		{			
			$this->sms = $smsInfo;
		}
    }
	
	public function sendSMS($mobile_number,$content)
	{
		
	
		$ACCOUNT_SID=$this->sms['user_name'];
		$AUTH_TOKEN=$this->sms['password'];
		$Sms_Sign=$this->sms['description'];

		if(is_array($mobile_number))
		{
			$mobile_number = implode(",",$mobile_number);
		}
        $rs=array();
        /* 互亿无线 */
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        preg_match('/\d+/',$content,$arr);
        $post_data = "account=".$ACCOUNT_SID."&password=".$AUTH_TOKEN."&mobile=".$mobile_number."&content=".rawurlencode("您的验证码是：".$arr[0]."。请不要把验证码泄露给其他人。");
        //密码可以使用明文密码或使用32位MD5加密
        $gets = $this->xml_to_array($this->Post($post_data, $target));

        if($gets['SubmitResult']['code']==2){
            $rs['status']=1;
            $rs['msg']='发送成功！';
        }else{
            $rs['status']=0;
            $rs['msg']=$gets['SubmitResult']['msg'];
        }
        return $rs;
	}
	
	public function getSmsInfo()
	{	

		return "互忆无线";
		
	}
	
	public function check_fee()
	{
        $ACCOUNT_SID=$this->sms['user_name'];
        $AUTH_TOKEN=$this->sms['password'];
        /* 互亿无线 */
        $target = "http://106.ihuyi.com/webservice/sms.php?method=GetNum";
        $post_data = "account=".$ACCOUNT_SID."&password=".$AUTH_TOKEN;
        $gets = $this->xml_to_array($this->Post($post_data, $target));
        if($gets['GetNumResult']['code']==2){
            $rs['info']=$gets['GetNumResult']['num'];
        }else{
            $rs['info']=$gets['GetNumResult']['msg'];
        }
        return $rs['info'];

	}

    protected function Post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }

    protected function xml_to_array($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = $this->xml_to_array( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }
}
?>