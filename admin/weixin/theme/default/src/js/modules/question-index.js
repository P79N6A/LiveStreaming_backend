$(document).on("pageInit", "#page-question-index, #page-course-yu_list", function(e, pageId, $page) {
    
    init_paramet();

    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });
    
    // 无限滚动
 	$($page).on('infinite', function(e) {
    	infinite_scroll($page,ajax_url,".question-list",vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
    	pull_refresh($page,ajax_url,".question-list",vm_paging);
    });


    // 初始化参数
 	function init_paramet(){

        new_paramet = paramet.type ? '&type='+paramet.type : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=question&act=index"+new_paramet;
        console.log(ajax_url);
    }

    (function(){
        // 针对微信浏览器返回上一页默认读取缓存解决方案
        handleAjax.handle(ajax_url,'','',1).done(function(result){

            var view_count, ele = $(".question-list").find(".view-counts"), question_list = result.question_list;
            $(ele).each(function(i){
                $(this).html(question_list[i].count);
            });

        }).fail(function(err){
            $.toast(err);
        });
    })();

});