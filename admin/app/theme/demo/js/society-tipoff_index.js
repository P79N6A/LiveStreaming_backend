/**
 * Created by Administrator on 2017/1/20.
 */
// 搜索违规主播

function user_id_search(user_id){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=society&act=tipoff_index",{user_id:user_id},"html").done(function(result){

        var htmlobject = $('<div id="tmpl">'+result+'</div>');
        var html = $(htmlobject).find("table.mdl").html();

        $("table.mdl").html(html);

    }).fail(function(err){
        $.showErr(err);
    });
}
$("#j-id-search").on('click',function(){
    var search_key = $("input[name='search_user_id']").val();
    user_id_search(search_key);
});