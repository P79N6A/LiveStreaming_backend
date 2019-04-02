$(function(){
init_ajax_page();
init_ajax_page_click();
//console.log("qqq");

});
//ajax 加载分页
function  init_page(){

}
function  init_ajax_page(){
  $(".j-ajax-page .m-page a").each(function(){
      var url=$(this).attr("href");
      $(this).addClass("q");
      $(this).attr("href","javascript:void(0)");
      $(this).attr("data-scr",url);
      //console.log("url:"+url);
  });
}

function init_ajax_page_click(func){
    $(".j-ajax-page").on('click',".m-page a",function(){
      var scr=$(this).attr("data-scr");

      $.ajax({
      url:scr,
      type:"POST",
      success:function(html)
      {
        //console.log("成功");
        $(".j-ajax-page").html($(html).find(".j-ajax-page").html());
        if(func!=null){
            auto_layout_l_w();
        }
        init_ajax_page();
       	
      },
      error:function()
      {
        //console.log("加载失败");
      }
    });
  });
}
