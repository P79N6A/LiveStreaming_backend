<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class IndexPropAction extends CommonAction{

  public function index()
  {
  	parent::index();
  }
	
	public function add()
	{
		$this->display();
	}
	
	public function insert()
	{
    B('FilterString');
    $data = M("IndexProp")->create();
    //开始验证有效性
    $this->assign("jumpUrl",u(MODULE_NAME."/add"));
    if($data['send_content']=='')
    {
    	$this->error(l("广告内容不能为空"));
    }
//		if($data['url']=='')
//		{
//			$this->error(l("广告链接不能为空"));
//		}
    $data['send_time'] = NOW_TIME;
    $data['send_status'] = 0;
		$content = $data['send_content'];
		$url = $data['send_url'];
    $message_id = M("IndexProp")->add($data);
    if ($message_id) {
        $sql = "SELECT id,group_id,live_in FROM ".DB_PREFIX."video where live_in in(1,3)";
        $group_id_all = $GLOBALS['db']->getAll($sql,true,true);
        $ext = array();
        $ext['type'] = 51;
        $ext['text'] = $content;
        $ext['url'] = $url;
        $ext['desc'] = $content;
        //执行消息推送 所有直播间（直播、回播）
				$ret = $this->push_im($ext,$group_id_all);
        if ($ret['ActionStatus'] == 'FAIL'){
            $GLOBALS['db']->query("update ".DB_PREFIX."index_prop set send_status = 1,ret_data='".$ret['ErrorCode']."' where id=".$message_id);
        }else{
            $GLOBALS['db']->query("update ".DB_PREFIX."index_prop set send_status = 2 where id=".$message_id);
        }
        //成功提示
        $this->success(L("INSERT_SUCCESS"));
    } else {
        //错误提示
        $this->error(L("INSERT_FAILED"));
    }
	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				//$MODULE_NAME='PromoteMsg';
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();
				foreach($rel_data as $data)
				{
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
			
				if ($list!==false) {
					save_log(l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log(l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	/**
	 * IM 推送消息实体
	 * @param $broadMsg
	 * @param $group_id_all
	 * @return mixed
	 */
	function push_im($broadMsg,$group_id_all)	//IM消息推送
	{
		$all_success_flag = 1; //所有群组IM发送都成功的标志位,默认置1
		#构造rest API请求包
		$msg_content = array();
		//创建$msg_content 所需元素
		$msg_content_elem = array(
			'MsgType' => 'TIMCustomElem',       //定义类型为普通文本型
			'MsgContent' => array(
				'Data' => json_encode($broadMsg)	//转为JSON字符串
			)
		);
		log_result($broadMsg);
		//将创建的元素$msg_content_elem, 加入array $msg_content
		array_push($msg_content, $msg_content_elem);

		//引入IM API文件
		fanwe_require(APP_ROOT_PATH.'system/tim/TimApi.php');
		$tim_api = createTimAPI();
		$broadMsg['user_id']=168159;
		//向所有群组发送消息
		$ret = array(); //存放发送返回信息
		for ($i=0;$i<count($group_id_all);$i++)
		{
			$ret[] = $tim_api->group_send_group_msg2($broadMsg['user_id'],$group_id_all[$i]['group_id'],$msg_content);
			$idx = 'group'.$i;
			$root[$idx] = $group_id_all[$i]['group_id'];
		}
		//遍历群组发送情况，对其中发送失败的群组且错误码为10002的，自动重发一次
		for ($i=0;$i<count($ret);$i++)
		{
			if ($ret[$i]['ActionStatus'] == 'FAIL' && $ret[$i]['ErrorCode'] == 10002){
				//10002 系统错误，请再次尝试或联系技术客服。
				log_err_file(array(__FILE__,__LINE__,__METHOD__,$ret[$i]));
				/*if ($i==1) $group_id_all[$i]['group_id'] = 66666;//错误测试*/
				$ret[$i] = $tim_api->group_send_group_msg2($broadMsg['user_id'], $group_id_all[$i]['group_id'], $msg_content);
				$root['repeat_test'] = 1;

			}
		}

		//查看是否全部发送成功,对于没发送成功的情况进行回馈
		for ($i=0;$i<count($ret);$i++)
		{
			//定义对应信息的存放键值
			$err_info = 'error_notify'.$i;
			$status_info = 'status_notify'.$i;
			//出错的写入对应位置
			if ($ret[$i]['ActionStatus'] == 'FAIL'){
				$root[$err_info] = $ret[$i]['ErrorInfo'].":".$ret[$i]['ErrorCode'];
				$root[$status_info] = 0;
				$all_success_flag = 0;
			}
		}
		if ($all_success_flag)
		{
			$root['status_notify_all'] = 1;
			$root['ret'] = $ret;
		}
		else
		{
			$root['status_notify_all'] = 0;
		}
		return $root;
	}
}
?>