<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class articleCModule  extends baseModule
{
    //PC端帮助和反馈 -- 分类
    public function index(){
        //输出文章
        $root = array('status'=>1,'error'=>'');
        $cate_id = intval($_REQUEST['id']);
        $cate_title = strim($_REQUEST['title']);
        $article_cates = load_auto_cache('article_cates');
        if (!$cate_id && $cate_title) {
            foreach ($article_cates as $cate) {
                if($cate_title == $cate['title']) {
                    $cate_id = $cate['id'];
                }
            }
        }

        $cate_id = $cate_id > 0 ? $cate_id : 1;
        $article = $GLOBALS['db']->getRow("select a.* from ".DB_PREFIX."article as a where is_effect = 1 and is_delete = 0 and cate_id=".$cate_id." order by sort desc",true,true);
        $root['page_title'] = $article['title'];
        $root['article'] = $article;
        $root['cate_id'] = $article['cate_id'];
        $root['article_cates'] = load_auto_cache('article_cates');
        api_ajax_return($root);
    }

    //PC端帮助和反馈 -- 列表
    public function faq(){
    	fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $root = array('status'=>1,'error'=>'');
        $page = intval($_REQUEST['p']);//取第几页数据
        if($page==0||$page==''){
            $page = 1;
        }
        $page_size=10;
        $limit = (($page-1)*$page_size).",".$page_size;
        $faq_group = strim($_REQUEST['faq_group']);
        $faq_group = $faq_group!=''?$faq_group:'充值问题';
        $faq_list = $GLOBALS['db']->getAll("select f.* from ".DB_PREFIX."faq as f where 1 = 1 and f.group = '".$faq_group."' and f.is_show=1  order by f.sort desc, f.click_count desc limit ".$limit);
        foreach($faq_list as $k=>$v){
            $faq_list[$k]['article_url'] = url('article#faq_show',array('id'=>$v['id']));
        }
        
        //全部问题分类列表
        $faq_cates = $GLOBALS['db']->getAll("select f.group from ".DB_PREFIX."faq as f where f.is_show=1 group by (f.group) limit 0,6") ;
        foreach ($faq_cates as $k=>$v) {
            $faq_cates[$k]['articlelist_url'] = url('article#faq',array('faq_group'=>$v['group']));
        }
        $root['faq_cates'] = $faq_cates;
        
        $rs_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."faq where `group` = '".$faq_group."' and is_show=1 ");
		$page = new Page($rs_count,$page_size);   //初始化分页对象
        $root['page'] = $page->show();
        $root['list'] = $faq_list;
        $root['page_title'] = $faq_group;
        $root['article_cates'] = load_auto_cache('article_cates');
        $root['cate_id'] = 'faq';
        api_ajax_return($root);
    }

    //PC端帮助与反馈 -- 内容
    public function faq_show(){
        $root = array('status'=>1,'error'=>'');
        $id = intval($_REQUEST['id']);
        $faq_info = $GLOBALS['db']->getRow("select f.* from ".DB_PREFIX."faq as f where f.is_show=1 and f.id = ".$id);
        if(!empty($faq_info)){
            $GLOBALS['db']->query("UPDATE ".DB_PREFIX."faq SET click_count=click_count+1 WHERE id = ".$faq_info['id']);
        }
        $root['faq_info'] = $faq_info;
        $root['page_title'] = $faq_info['question'];
        $root['cate_id'] = 'faq';
        $root['article_cates'] = load_auto_cache('article_cates');
        api_ajax_return($root);
    }
    
     //PC新闻列表
    public function news(){
    	fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $root = array('status'=>1,'error'=>'');
        $page=intval($_REQUEST['p']);//页码
        $page_size = 10;//分页数量
        if($page==0||$page==''){
            $page = 1;
        }
        $param=array('page'=>$page,'page_size'=>$page_size);
        $article_list = load_auto_cache("article",$param);
        $page = new Page($article_list['rs_count'],$page_size);   //初始化分页对象
        $root['page_title'] = '新闻公告';
        $root['news'] = $article_list['listmsg'];
        $root['page'] = $page->show();
        $root['cate_id'] = 'news';
        $root['article_cates'] = load_auto_cache('article_cates');
        api_ajax_return($root);
    }

    //PC新闻详细
    public function show(){
        //输出文章
        $root = array('status'=>1,'error'=>'');
        $article_id = intval($_REQUEST['id']);
        $article = $GLOBALS['db']->getRow("select a.id,a.title,a.content,a.create_time from ".DB_PREFIX."article as a where a.is_effect = 1 and a.is_delete = 0 and a.id=".$article_id." order by sort desc",true,true);
        if($article_id==0||$article==''){
        	$root['status'] = '文章不存在';
        	$root['error'] =0;
        }
        $GLOBALS['db']->query("UPDATE ".DB_PREFIX."article SET click_count=click_count+1 WHERE id = ".$article_id);
        $root['page_title'] = $article['title'];
        $root['article'] = $article;
        $root['cate_id'] = 'news';
        $root['article_cates'] = load_auto_cache('article_cates');
        api_ajax_return($root);
    }
}

?>