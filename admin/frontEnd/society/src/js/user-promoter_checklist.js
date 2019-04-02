
var vm = avalon.define({
    $id: "vm-search",
    search_data: {
	  	promoter_name: '',
		check_state: ''
    },
    search() {
        // 搜素
        layer.load();
        let cls = ".ajax-block";
        $handleAjax.handle({
            url: TMPL_REAL + "/index.php?ctl=user&act=promoter_checklist",
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