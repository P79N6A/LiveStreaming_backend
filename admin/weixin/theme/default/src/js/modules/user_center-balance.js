$(document).on("pageInit", "#page-user_center-balance", function(e, pageId, $page) {

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
    	infinite_scroll($page,ajax_url,".trade-list",vm_paging);
    });

    // 初始化参数
 	function init_paramet(){

        new_paramet = paramet.type ? '&type='+paramet.type : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=user_center&act=balance"+new_paramet;

    }

});