$(function(){
	$.datetimepicker.setLocale('ch');
    $('#begin_time').datetimepicker({
    	timepicker:false,
    	format:"Y-m-d"
    });
    $('#end_time').datetimepicker({
    	timepicker:false,
    	format:"Y-m-d"
    });
});

var vm = avalon.define({
    $id: "vm-search",
    search_data: {
	  	begin_time: '',
	  	end_time: '',
		game_id: '',
        is_win: '',
        id: $objAction.getQueryString("id")
    },
    search() {
        // 搜素
        layer.load();
        let cls = ".ajax-block";
        $handleAjax.handle({
            url: TMPL_REAL + "/index.php?ctl=user&act=gift_from_coin_detail",
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