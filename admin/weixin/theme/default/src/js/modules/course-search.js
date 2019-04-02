$(document).on("pageInit","#page-course-index, #page-course-qi_list", function(e, pageId, $page) {
    // 搜索
    $("#form-search").submit(function(){
        // $.router.load(APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val(), true);
        location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=search&search="+$(this).find("input[name='search']").val();
    });
    $(".searchbar-cancel").on('click', function(){
        $("input[name='search']").val('');
    });
});

$(document).on("pageInit","#page-course-yu_list, #page-course-qi_list", function(e, pageId, $page) {
    // 搜索
    $("#form-search").submit(function(){
        // $.router.load(APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val(), true);
        location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=search&search="+$(this).find("input[name='search']").val();
    });
    $(".searchbar-cancel").on('click', function(){
        $("input[name='search']").val('');
    });
});


$(document).on("pageInit","#page-course-search", function(e, pageId, $page) {
	if(!empty($("input[name='search']").val())){
		$(".searchbar").addClass("searchbar-active");
	}

	var searching = false;

     init_paramet();

    // 评论列表
    var vm_main = new Vue({
        el: "#vm-vscope",
        data: data,
        beforeCreate: function(){
            $.showIndicator();
            var self = this;
            handleAjax.handle(ajax_url, '', '', 1).done(function(result){

                self.list = result.list;
                self.search_type = result.search_type;

                self.$nextTick(function(){
                    $.hideIndicator();
                });

            }).fail(function(err){
                $.toast(err);
            });
        },
        methods: {
            get_data: function(search){
                $.showIndicator();
                var self = this;
                handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=course&act=search&search="+search, '', '', 1).done(function(result){

                    self.list = result.list;
                    self.page = 2;
                    self.total_page = result.total_page;
                    self.search_type = 0;
                    self.search = search;

                    self.$nextTick(function(){
                        $.hideIndicator();
                    });

                }).fail(function(err){
                    $.toast(err);
                });

                // $('.lazyload').picLazyLoad();
            },
            choose_type: function(list, type){
                $.showIndicator();

                var self = this;
                self.search_type = type;

                var data_json = {
                    search: self.search,
                    search_type: self.search_type
                };

                console.log(data_json);

                handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=course&act=search", data_json, '', 1).done(function(result){

                    self.list = result.list;
                    self.page = 2;
                    self.total_page = result.total_page;

                    self.$nextTick(function(){
                        $.hideIndicator();
                    });

                }).fail(function(err){
                    $.toast(err);
                });
            },
            format: function(m){
                return m<10?'0'+m:m
            }
        },
        filters: {
            time: function(value){
                // 格式化时间
                // value是整数，否则要parseInt转换
                var time = new Date(parseInt(value)*1000);
                var y = time.getFullYear();
                var m = time.getMonth()+1;
                var d = time.getDate();

                return y-2000+'-'+vm_main.format(m)+'-'+vm_main.format(d);

            }
        }
    });

    // 无限滚动
    $($page).on('infinite', function(e) {

        if (vm_main.loading || vm_main.page > vm_main.total_page){
            $(".content-inner").css({paddingBottom:"0"});
            return;
        }
        vm_main.loading = true;

        handleAjax.handle(ajax_url, {page: vm_main.page}, '', 1).done(function(result){
            setTimeout(function(){
                for (var i=0; i < result.list.length; i++) {
                    vm_main.list.push( result.list[i] );
                }

                vm_main.page++;
                vm_main.loading = false;

                // $('.lazyload').picLazyLoad();
                $.refreshScroller(); 
            }, 1000);
           
        }).fail(function(err){
            $.toast(err);
        });


    });

 	$("#form-search").submit(function(){
 		var _this = $(this), ele_input = _this.find("input[name='search']"), search =ele_input.val();
        // $.router.load(APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val(), true);
        // location.href = APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val();

        	if(!empty(search)){
                ele_input.blur();
                vm_main.get_data(search);
            }
    });

 	$("input[name='search']").blur(function(){
    	if(empty($(this).val())) return;
 		$(".searchbar").addClass("searchbar-active");
  	});

  	$(".searchbar-cancel").on('click', function(){
  		$("input[name='search']").val('');
  	});



    // 初始化参数
    function init_paramet(){

        new_paramet = paramet.search ? '&search='+paramet.search : '';

        ajax_url = APP_ROOT+"/mapi/index.php?ctl=course&act=search"+new_paramet;
    }

    // 输入过滤emoji表情
    $("textarea[name='content']").on('input propertychange', function(){
        var val = $(this).val();
        $(this).val($.emoji2Str(val));
    });

});