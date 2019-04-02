<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class indexCModule  extends baseModule
{

 	//首页
	public function index()
	{
		$root = array();
		$root['page_title'] = "首页";
		$m_config =  load_auto_cache("m_config");//初始化手机端配置
		$root['app_logo']=get_spec_image(".".substr($m_config['app_logo'],strpos($m_config['app_logo'],'/public')),170,170);//logo 170*170
		$root['app_name']=$m_config['app_name'];//应用名称
        $root['android_filename']=$m_config['android_filename'];//android下载链接
		api_ajax_return($root);
	}
    //联系我们
    public function contact(){
		$root = array();
		$root['page_title'] = "联系我们";
		$sql = "select a.id,a.cate_id,a.title,a.content from ".DB_PREFIX."article a  left join ".DB_PREFIX."article_cate b on b.id = a.cate_id  where a.is_delete = 0 and a.is_effect = 1 and b.title = '联系我们'";
		$article = $GLOBALS['db']->getRow($sql,true,true);
		$root['content'] = $article['content'];
		api_ajax_return($root);
    }
	//隐私政策
    public function privacy(){
		$root = array();
		$root['page_title'] = "隐私政策";
		$sql = "select a.id,a.cate_id,a.title,a.content from ".DB_PREFIX."article a  left join ".DB_PREFIX."article_cate b on b.id = a.cate_id  where a.is_delete = 0 and a.is_effect = 1 and b.title = '隐私政策'";
		$article = $GLOBALS['db']->getRow($sql,true,true);
		$root['content'] = $article['content'];
		api_ajax_return($root);
    }
    //服务条款
    public function service(){
		$root = array();
		$root['page_title'] = "服务条款";
		$sql = "select a.id,a.title,a.cate_id,a.content from ".DB_PREFIX."article a left join ".DB_PREFIX."article_cate b on b.id = a.cate_id where a.is_delete = 0 and a.is_effect = 1 and b.title = '主播协议'";
		$article = $GLOBALS['db']->getRow($sql,true,true);
		$root['content'] = $article['content'];
		api_ajax_return($root);
    }
}

?>