webpackJsonp([17],{JA2h:function(t,o,a){o=t.exports=a("xCYs")(!0),o.push([t.i,".user-video-content .nullData[data-v-623f4065]{margin-top:10.7rem}","",{version:3,sources:["D:/phpStudy/WWW/yingke/frontEnd/weixin/src/views/user/tool/small_video.vue"],names:[],mappings:"AAsBA,+CACE,kBAAoB,CACrB",file:"small_video.vue",sourcesContent:["/*界面主题色*/\n/*背景色*/\n/*背景色（深）*/\n/*模块背景色*/\n/*主题风格色*/\n/*主题辅助风格色*/\n/*字体颜色*/\n/*主要字色*/\n/*浅色*/\n/*浅色*/\n/*更浅色（适用：二级标题、简介）*/\n/*更更浅色（适用：icon图标）*/\n/*标题*/\n/*字体大小*/\n/*线条颜色*/\n/*主要线条颜色*/\n/*更深线条颜色*/\n/*更浅线条颜色*/\n/*文本输入框边框颜色*/\n/*各类间距，高度*/\n/*横向间距*/\n/*纵向间距*/\n.user-video-content .nullData[data-v-623f4065] {\n  margin-top: 10.7rem;\n}\n"],sourceRoot:""}])},Kx0Y:function(t,o,a){"use strict";var e=a("bIIf"),n=a("77sl"),s=a("DG/j"),i=a("Xy6p");o.a={name:"toolSmallVideo",components:{nullData:e.a,svideoList:n.a,mintLoadmoreTop:s.a,mintLoadmoreBottom:i.a},data:function(){return{list:"",has_next:"",page:2,autoFill:!1,allLoaded:!1,isLoaded:!1,topStatus:"",bottomStatus:""}},created:function(){this.getData()},methods:{getData:function(){var t=this,o=(arguments.length>0&&void 0!==arguments[0]&&arguments[0],this.api.svideoVideo());this.axios.get(o,{params:{to_user_id:this.$route.query.to_user_id}}).then(function(o){t.isLoaded=!0,1==o.data.status?(t.list=o.data.list,t.has_next=o.data.has_next,t.page=2):t.Toast(o.data.error||"请求出错"),t.$nextTick(function(){t.$emit("init"),t.isRefresh&&t.$refs.loadmore.onTopLoaded()})}).catch(function(o){t.Toast(o||"网络异常，请求失败"),t.$emit("init")})},loadTop:function(t){this.getData(!0)},loadBottom:function(){var t=this,o=this,a=o.api.svideoVideo();if(!o.has_next)return!1;o.axios.get(a,{params:{page:o.page,to_user_id:o.$route.query.to_user_id}}).then(function(a){setTimeout(function(){if(1==a.data.status){if(o.has_next=a.data.has_next,o.page=o.page+1,a.data.list&&a.data.list.length&&a.data.list.length>0)for(var e=0;e<a.data.list.length;e++)o.list.push(a.data.list[e])}else t.Toast(a.data.error||"请求出错");o.$nextTick(function(){o.$refs.loadmore.onBottomLoaded(),o.$emit("init")})},500)}).catch(function(o){t.Toast(o||"网络异常，请求失败")})},handleTopChange:function(t){this.topStatus=t},handleBottomChange:function(t){this.bottomStatus=t}},watch:{list:function(){this.allLoaded=!Boolean(this.has_next)}}}},"iJ+J":function(t,o,a){var e=a("JA2h");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);a("XkoO")("b307877e",e,!0)},t7Og:function(t,o,a){"use strict";function e(t){a("iJ+J")}Object.defineProperty(o,"__esModule",{value:!0});var n=a("Kx0Y"),s=a("ym71"),i=a("J0+h"),l=e,d=i(n.a,s.a,l,"data-v-623f4065",null);o.default=d.exports},ym71:function(t,o,a){"use strict";var e=function(){var t=this,o=t.$createElement,a=t._self._c||o;return a("div",{staticClass:"user-video-content"},[a("mt-loadmore",{ref:"loadmore",attrs:{"top-method":t.loadTop,"bottom-method":t.loadBottom,"bottom-all-loaded":t.allLoaded,autoFill:t.autoFill},on:{"top-status-change":t.handleTopChange,"bottom-status-change":t.handleBottomChange}},[a("mint-loadmore-top",{attrs:{slot:"top",topStatus:t.topStatus},slot:"top"}),t.isLoaded?[a("svideo-list",{attrs:{list:t.list,allLoaded:t.allLoaded}})]:t._e(),a("mint-loadmore-bottom",{directives:[{name:"show",rawName:"v-show",value:!t.allLoaded,expression:"!allLoaded"}],attrs:{slot:"bottom",bottomStatus:t.bottomStatus},slot:"bottom"})],2)],1)},n=[],s={render:e,staticRenderFns:n};o.a=s}});
//# sourceMappingURL=17.d04df8ba640f566deb7e.js.map