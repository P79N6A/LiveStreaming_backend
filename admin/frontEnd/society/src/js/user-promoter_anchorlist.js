
var vm = avalon.define({
    $id: "vm-search",
    search_data: {
	  	nick_name: '',
		login_state: '',
        promoter_name: '',
        type: $objAction.getQueryString("type"),
        mobile: '',
        bm_special: $objAction.getQueryString("bm_special") || 0
    },
    search() {
        // 搜素
        layer.load();
        let cls = ".ajax-block";
        $handleAjax.handle({
            url: TMPL_REAL + "/index.php?ctl=user&act=promoter_anchorlist",
            isTip: false,
            dataType: 'html',
            data: this.search_data
        }).done(function(result){
            layer.closeAll();
            var tplElement = $('<div id="tmpHTML"></div>').html(result),
            htmlObject = tplElement.find(cls),
            html = $(htmlObject).html();
            $(document).find(cls).html(html);

        }).fail(function(err){
            console.log(err);
        });
    }
});