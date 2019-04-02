function auto_layout_l_w(){
  var w_width = $(window).width();
  var w_mainbody = w_width-94;
  // 推荐视频

  var ratio = 285/160;
  var w_tjImg=245;  // 宽度最小阀值
  var num_tjImg=Math.floor((w_mainbody)/(w_tjImg+40));  // 一行显示图片个数
  var new_tjImg_w=((w_mainbody)/num_tjImg)-20;  // 新宽度值
  var new_Img_w=new_tjImg_w-20;
  var new_Img_h=new_Img_w/ratio;
  var img_ratio = 893/100;
  var img_w=(w_mainbody-20)/2;
  var img_h = img_w/img_ratio;
  $(".block-live").css({width:new_tjImg_w}).show().find("span.block-live-img").css({height:new_Img_h});
  $(".banner-t").css({height:img_h,weight:img_w});
  /*新秀直播限制为最多两行*/
  var elems = $(".block-live");
  for(var i = 0, l = elems.length; i < l; i++){
      var item_height = $(elems.get(i)).height();
      // if(item_height > 0){
      //   $(".new-live .m-live-list").css('height', item_height+40);
      //   break;
      // }
  }
};
$(function() {
  // 点击换一换
  $(".j-change-btn").on('click',function(){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=video&act=new_list&tmpl_pre_dir=inc","", "html").done(function(result){
        $(".j-change-con").html(result);
        auto_layout_l_w();
    }).fail(function(err){
        $.showToast('加载失败');
    });
  });
});
