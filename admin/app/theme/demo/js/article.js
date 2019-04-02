$(function(){

});
// 点击左部菜单
/*$(".j-article-menu li").on('click',function(){
  var url=$(this).attr("data-src");
  $(".j-article-menu li").removeClass("active");
  $(this).addClass("active");
    $.ajax({
      url:url,
      type:"POST",
      success:function(html)
      {
        console.log("成功");
        $(".m-uc-content").html($(html).find(".m-uc-content").html());
      },
      error:function()
      {
        console.log("加载失败");
      }
    });
});*/
