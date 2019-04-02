/**
 * Created by Administrator on 2016/11/17.
 */
// 搜索家族成员
init_ajax_page();
init_ajax_page_click();
function join_family(family_id,obj){
    handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=family_user&act=user_join",{family_id:family_id}).done(function(msg){
        $.showSuccess(msg);
        $(obj).removeClass("btn-primary").addClass("btn-disabled").html("申请中");
        obj.onclick = function (){return false;};
    }).fail(function(err){
        $.showErr(err);
    });
}
