<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class baseCModule  extends baseModule
{
	public function __construct()
	{/*
		parent::__construct();

		//require_once APP_ROOT_PATH."system/saas/SAASAPIServer.php";
		fanwe_require(APP_ROOT_PATH."system/saas/SAASAPIServer.php");

		// 初始化操作：
		// 1) 设置应用开发的APPID和APP密钥，正常系统会为每个企业用户随机分配一对APPID和APP密钥，这里设置为系统通用的值
		// 2) 构造API服务类对象
		$appid = FANWE_APP_ID;
		$appsecret = FANWE_AES_KEY;
		$server = new SAASAPIServer($appid, $appsecret);

		// 验证客户端请求参数（时间戳、参数验证等）
		$ret = $server->verifyRequestParameters();
		if ($ret['errcode'] != 0) {
			die($server->toResponse($ret));
		}
	*/}
		
	public function checkSaas() {
		
		fanwe_require ( APP_ROOT_PATH . "system/saas/SAASAPIServer.php" );
		
		// 初始化操作：
		// 1) 设置应用开发的APPID和APP密钥，正常系统会为每个企业用户随机分配一对APPID和APP密钥，这里设置为系统通用的值
		// 2) 构造API服务类对象
		$appid = FANWE_APP_ID;
		$appsecret = FANWE_AES_KEY;
		$server = new SAASAPIServer ( $appid, $appsecret );
		
		// 验证客户端请求参数（时间戳、参数验证等）
		$ret = $server->verifyRequestParameters ();
		if ($ret ['errcode'] != 0) {
			die ( $server->toResponse ( $ret ) );
		}
	}
	
	
	public function checkSession() {
		$session_id = strim ( $_REQUEST ['session_id'] );
		if ($session_id == null || es_session::id () != $session_id) {
			api_ajax_return ( array (
					"status" => 10007,
					"error" => '服务端未登陆' 
			) );
		}
	}
		
	/**
	 * 登录检查
	 * @return [type] [description]
	 */
    protected static function checkLogin()
    {
        $user_id = intval(isset($GLOBALS['user_info']['id']) ? $GLOBALS['user_info']['id'] : 0);
        if (!$user_id) {
            self::returnErr(10007);
        }
    }
    /**
     * 错误返回
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    protected static function returnErr($code)
    {
        $error = array(
            10001 => '查询的业务数据不存在',
            10002 => '操作的业务动作失败',
            10003 => '分润失败，需做日志处理或重新发起请求',
            10004 => '订单支付失败',
            10005 => '接口不存在',
            10006 => '接口下的方法不存在',
            10007 => '服务端未登陆',
            10008 => '商品不存在',
            10009 => '主播不存在',
            10010 => '竞拍商品不存在',
            10011 => '竞拍人不存在',
            10012 => '下单单失败',
            10013 => '提交保证金失败',
            10014 => '竞拍失败',
            10015 => '添加收货地址失败',
            10016 => '删除收货地址失败',
            10017 => '姓名为空',
            10018 => '手机号码为空',
            10019 => '手机号码格式错误',
            10020 => '编辑收货地址失败',
            10021 => '消息类型为空',
            10022 => '消息推送失败',
            10023 => '消息删除失败',
            10024 => '设置默认收货地址失败',
            10025 => '创建竞拍失败',
            10026 => '编辑竞拍失败',
            10027 => '关闭竞拍失败',
            10028 => '确认完成虚拟竞拍失败',
            10029 => '确认竞拍退款失败',
            10030 => '申诉竞拍失败',
            10031 => '确认约会失败',
            10032 => '撤销失败',
            10033 => '推送会员为空',
            10034 => '区域数据错误',
            10035 => '收货地址为空',
            10036 => '竞拍已结束',
            10037 => '订单号错误',
            10038 => '名称不能为空',
            10039 => '描述不能为空',
            10040 => '时间不能为空',
            10041 => '地点不能为空',
            10042 => '联系人不能为空',
            10043 => '请输入正确的联系电话',
            10044 => '竞拍价格不能为0',
            10045 => '每次加价幅度不能为',
            10046 => '竞拍时长不能为0',
            10047 => '每次竞拍延时不能为0',
            10048 => '最大延时不能为0',
            10049 => '存在未完成的竞拍，创建竞拍失败',
            10050 => '已提交过保证金',
            10051 => '禁止发起竞拍，创建竞拍失败',
            10052 => '未提交保证金',
            10053 => '出价非最高价',
            10054 => '订单已付款',
            10055 => '直播间已关闭，无法创建竞拍',
            10056 => '约会时间早于竞拍完成时间，请重新选择约会时间',
            10057 => '添加商品失败',
            10058 => '删除商品失败',
            10059 => '商品图片不能为空',
            10060 => '商品详情不能为空',
            10061 => '商品价格不能为0',
        );
        api_ajax_return(array('status' => $code, 'error' => isset($error[$code]) ? $error[$code] : '未知错误'));
    }

}