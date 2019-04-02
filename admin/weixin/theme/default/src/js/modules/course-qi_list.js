$(document).on("pageInit", "#page-course-qi_list, #page-course-yu_list", function(e, pageId, $page) {
	var act;
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
    	infinite_scroll($page,APP_ROOT+"/weixin/index.php?ctl=course&act="+act+new_paramet,".course-list",vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
    	pull_refresh($page,ajax_url,".course-list",vm_paging);
    });


    // 初始化参数
 	function init_paramet(){
 		if(pageId == 'page-course-yu_list'){
			act = 'yu_list';
		}
		switch(pageId){
			case 'page-course-yu_list':
				act = 'yu_list';
				break;
			case 'page-course-qi_list':
				act = 'qi_list';
				break;
		}

        new_paramet = paramet.list_type ? '&list_type='+paramet.list_type : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=course&act="+act+new_paramet;
        console.log(ajax_url);
    }

});