webpackJsonp([11],{"+EKR":function(e,t,i){"use strict";var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"user-fans-content"},[i("mt-loadmore",{ref:"loadmore",attrs:{"top-method":e.loadTop,"bottom-method":e.loadBottom,"bottom-all-loaded":e.allLoaded,autoFill:e.autoFill},on:{"top-status-change":e.handleTopChange,"bottom-status-change":e.handleBottomChange}},[i("mint-loadmore-top",{attrs:{slot:"top",topStatus:e.topStatus},slot:"top"}),e.isLoaded?[i("user-list",{attrs:{list:e.list,allLoaded:e.allLoaded}})]:e._e(),i("mint-loadmore-bottom",{directives:[{name:"show",rawName:"v-show",value:!e.allLoaded,expression:"!allLoaded"}],attrs:{slot:"bottom",bottomStatus:e.bottomStatus},slot:"bottom"})],2)],1)},s=[],a={render:n,staticRenderFns:s};t.a=a},"9K2G":function(e,t,i){"use strict";var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"user-list"},[e.list&&e.list.length>0?i("ul",[e._l(e.list,function(t,n){return i("li",{staticClass:"list"},[i("div",{staticClass:"head-img"},[i("router-link",{attrs:{to:{path:"/user/others",query:{to_user_id:t.user_id}}}},[i("img",{directives:[{name:"lazy",rawName:"v-lazy",value:t.head_image,expression:"item.head_image"}],attrs:{width:"20px"}})])],1),i("div",{staticClass:"user-information"},[i("div",{staticClass:"user-title"},[i("div",{staticClass:"user-nick-name"},[i("span",{domProps:{innerHTML:e._s(t.nick_name)}}),i("div",{staticClass:"praise"},[1==t.sex?i("i",{staticClass:"icon iconfont sex-man icon-sex"},[e._v("")]):i("i",{staticClass:"icon iconfont sex-woman icon-sex"},[e._v("")]),i("img",{staticClass:"icon-level",attrs:{src:e.iconLevel[n]}})])]),i("div",{staticClass:"signature",domProps:{innerHTML:e._s(t.signature)}})]),i("div",{staticClass:"after"},[t.follow_id>0||t.has_focus>0?i("div",{staticClass:"btn-follow followed",on:{click:function(i){e.follow(t)}}},[i("i",{staticClass:"icon iconfont"},[e._v("")])]):i("div",{staticClass:"btn-follow",on:{click:function(i){e.follow(t)}}},[i("i",{staticClass:"icon iconfont"},[e._v("")])])])])])}),e.allLoaded?i("div",{staticClass:"no-more-data"},[e._v("无更多数据")]):e._e()],2):i("null-data")],1)},s=[],a={render:n,staticRenderFns:s};t.a=a},DH2y:function(e,t,i){"use strict";function n(e){i("YHB4")}var s=i("a5Ca"),a=i("9K2G"),o=i("J0+h"),l=n,r=o(s.a,a.a,l,"data-v-0e4de6d0",null);t.a=r.exports},EfCn:function(e,t,i){t=e.exports=i("xCYs")(!0),t.push([e.i,".user-list ul[data-v-0e4de6d0]{background-color:#fff;padding-left:.5rem}.user-list ul .list[data-v-0e4de6d0]{height:3.25rem;width:100%;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;position:relative}.user-list ul .list .head-img[data-v-0e4de6d0]{height:2rem;width:2rem;border-radius:2.5rem;overflow:hidden;-ms-flex-negative:0;flex-shrink:0}.user-list ul .list .head-img img[data-v-0e4de6d0]{width:100%;height:100%}.user-list ul .list .user-information[data-v-0e4de6d0]{border-bottom:1px solid #e6e8f1;width:100%;height:3.25rem;padding:0 .5rem;font-size:.65rem;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.user-list ul .list .user-information .user-title[data-v-0e4de6d0]{padding-top:.75rem;width:65%}.user-list ul .list .user-information .user-title .user-nick-name[data-v-0e4de6d0]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.user-list ul .list .user-information .user-title .user-nick-name span[data-v-0e4de6d0]{font-size:.75rem;color:#333;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.user-list ul .list .user-information .user-title .user-nick-name .praise .iconfont[data-v-0e4de6d0]{font-size:.75rem;margin:0 .25rem;line-height:1}.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-woman[data-v-0e4de6d0]{color:#ff71bb}.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-man[data-v-0e4de6d0]{color:#3fa2ff}.user-list ul .list .user-information .user-title .user-nick-name .praise img[data-v-0e4de6d0]{width:1.3rem;height:.65rem;vertical-align:text-top}.user-list ul .list .user-information .user-title .signature[data-v-0e4de6d0]{font-size:.7rem;color:#999;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.user-list ul .list .user-information .after[data-v-0e4de6d0]{position:absolute;right:.5rem;top:50%;margin-top:-.5rem;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.user-list ul .list .user-information .after .btn-follow[data-v-0e4de6d0]{height:1rem;line-height:1rem;width:1.75rem;background:#333;border-radius:.5rem;text-align:center}.user-list ul .list .user-information .after .btn-follow .iconfont[data-v-0e4de6d0]{color:#fff;vertical-align:middle}.user-list ul .list .user-information .after .btn-follow.followed[data-v-0e4de6d0]{background:#ccc}","",{version:3,sources:["D:/phpStudy/WWW/yingke/frontEnd/weixin/src/components/userList.vue"],names:[],mappings:"AAsBA,+BACE,sBAAuB,AACvB,kBAAoB,CACrB,AACD,qCACE,eAAgB,AAChB,WAAY,AACZ,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,mBAAoB,AAC5B,iBAAmB,CACpB,AACD,+CACE,YAAa,AACb,WAAY,AACZ,qBAAsB,AACtB,gBAAiB,AACjB,oBAAqB,AACjB,aAAe,CACpB,AACD,mDACE,WAAY,AACZ,WAAa,CACd,AACD,uDACE,gCAAiC,AACjC,WAAY,AACZ,eAAgB,AAChB,gBAAiB,AACjB,iBAAkB,AAClB,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,6BAA+B,CACxC,AACD,mEACE,mBAAoB,AACpB,SAAW,CACZ,AACD,mFACE,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,kBAAoB,CAC7B,AACD,wFACE,iBAAkB,AAClB,WAAY,AACZ,cAAe,AACf,gBAAiB,AACjB,uBAAwB,AACxB,kBAAoB,CACrB,AACD,qGACE,iBAAkB,AAClB,gBAAiB,AACjB,aAAe,CAChB,AACD,sGACE,aAAe,CAChB,AACD,oGACE,aAAe,CAChB,AACD,+FACE,aAAc,AACd,cAAe,AACf,uBAAyB,CAC1B,AACD,8EACE,gBAAiB,AACjB,WAAY,AACZ,gBAAiB,AACjB,uBAAwB,AACxB,kBAAoB,CACrB,AACD,8DACE,kBAAmB,AACnB,YAAa,AACb,QAAS,AACT,kBAAoB,AACpB,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,kBAAoB,CAC7B,AACD,0EACE,YAAa,AACb,iBAAkB,AAClB,cAAe,AACf,gBAAiB,AACjB,oBAAqB,AACrB,iBAAmB,CACpB,AACD,oFACE,WAAY,AACZ,qBAAuB,CACxB,AACD,mFACE,eAAiB,CAClB",file:"userList.vue",sourcesContent:["/*界面主题色*/\n/*背景色*/\n/*背景色（深）*/\n/*模块背景色*/\n/*主题风格色*/\n/*主题辅助风格色*/\n/*字体颜色*/\n/*主要字色*/\n/*浅色*/\n/*浅色*/\n/*更浅色（适用：二级标题、简介）*/\n/*更更浅色（适用：icon图标）*/\n/*标题*/\n/*字体大小*/\n/*线条颜色*/\n/*主要线条颜色*/\n/*更深线条颜色*/\n/*更浅线条颜色*/\n/*文本输入框边框颜色*/\n/*各类间距，高度*/\n/*横向间距*/\n/*纵向间距*/\n.user-list ul[data-v-0e4de6d0] {\n  background-color: #fff;\n  padding-left: .5rem;\n}\n.user-list ul .list[data-v-0e4de6d0] {\n  height: 3.25rem;\n  width: 100%;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  position: relative;\n}\n.user-list ul .list .head-img[data-v-0e4de6d0] {\n  height: 2rem;\n  width: 2rem;\n  border-radius: 2.5rem;\n  overflow: hidden;\n  -ms-flex-negative: 0;\n      flex-shrink: 0;\n}\n.user-list ul .list .head-img img[data-v-0e4de6d0] {\n  width: 100%;\n  height: 100%;\n}\n.user-list ul .list .user-information[data-v-0e4de6d0] {\n  border-bottom: 1px solid #e6e8f1;\n  width: 100%;\n  height: 3.25rem;\n  padding: 0 .5rem;\n  font-size: .65rem;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: justify;\n      -ms-flex-pack: justify;\n          justify-content: space-between;\n}\n.user-list ul .list .user-information .user-title[data-v-0e4de6d0] {\n  padding-top: .75rem;\n  width: 65%;\n}\n.user-list ul .list .user-information .user-title .user-nick-name[data-v-0e4de6d0] {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n}\n.user-list ul .list .user-information .user-title .user-nick-name span[data-v-0e4de6d0] {\n  font-size: .75rem;\n  color: #333;\n  display: block;\n  overflow: hidden;\n  text-overflow: ellipsis;\n  white-space: nowrap;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise .iconfont[data-v-0e4de6d0] {\n  font-size: .75rem;\n  margin: 0 .25rem;\n  line-height: 1;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-woman[data-v-0e4de6d0] {\n  color: #ff71bb;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-man[data-v-0e4de6d0] {\n  color: #3fa2ff;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise img[data-v-0e4de6d0] {\n  width: 1.3rem;\n  height: .65rem;\n  vertical-align: text-top;\n}\n.user-list ul .list .user-information .user-title .signature[data-v-0e4de6d0] {\n  font-size: .7rem;\n  color: #999;\n  overflow: hidden;\n  text-overflow: ellipsis;\n  white-space: nowrap;\n}\n.user-list ul .list .user-information .after[data-v-0e4de6d0] {\n  position: absolute;\n  right: .5rem;\n  top: 50%;\n  margin-top: -0.5rem;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n}\n.user-list ul .list .user-information .after .btn-follow[data-v-0e4de6d0] {\n  height: 1rem;\n  line-height: 1rem;\n  width: 1.75rem;\n  background: #333;\n  border-radius: .5rem;\n  text-align: center;\n}\n.user-list ul .list .user-information .after .btn-follow .iconfont[data-v-0e4de6d0] {\n  color: #fff;\n  vertical-align: middle;\n}\n.user-list ul .list .user-information .after .btn-follow.followed[data-v-0e4de6d0] {\n  background: #ccc;\n}\n"],sourceRoot:""}])},YHB4:function(e,t,i){var n=i("EfCn");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);i("XkoO")("09915a22",n,!0)},a5Ca:function(e,t,i){"use strict";var n=i("BO1k"),s=i.n(n),a=i("34+y"),o=(i.n(a),i("X+yh")),l=i.n(o),r=i("bIIf");t.a={props:["list","allLoaded"],components:{nullData:r.a},methods:{follow:function(e){this.axios.get(this.api.userFollow(),{params:{to_user_id:e.user_id}}).then(function(t){1==t.data.status?(void 0!=e.follow_id&&(e.follow_id=t.data.has_focus),void 0!=e.has_focus&&(e.has_focus=t.data.has_focus)):l()(t.data.error||"操作失败")}).catch(function(e){l()(e||"请求失败")})}},computed:{iconLevel:function(){var e=[],t=!0,n=!1,a=void 0;try{for(var o,l=s()(this.list);!(t=(o=l.next()).done);t=!0){var r=o.value;e.push(i("3T/a")("./rank_"+r.user_level+".png"))}}catch(e){n=!0,a=e}finally{try{!t&&l.return&&l.return()}finally{if(n)throw a}}return e}}}},sV07:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=i("w6G8"),s=i("+EKR"),a=i("J0+h"),o=a(n.a,s.a,null,null,null);t.default=o.exports},w6G8:function(e,t,i){"use strict";var n=i("34+y"),s=(i.n(n),i("X+yh")),a=i.n(s),o=i("DH2y"),l=i("DG/j"),r=i("Xy6p");t.a={name:"toolFollow",components:{userList:o.a,mintLoadmoreTop:l.a,mintLoadmoreBottom:r.a},data:function(){return{list:"",has_next:"",page:2,autoFill:!1,allLoaded:!1,isLoaded:!1,topStatus:"",bottomStatus:""}},created:function(){this.getData()},methods:{getData:function(){var e=this,t=arguments.length>0&&void 0!==arguments[0]&&arguments[0],i=this.api.userUser_follow();this.axios.get(i,{params:{to_user_id:this.$route.query.to_user_id}}).then(function(i){e.isLoaded=!0,1==i.data.status?(e.list=i.data.list,e.has_next=i.data.has_next,e.page=2):a()(i.data.error||"操作失败"),e.$nextTick(function(){t&&this.$refs.loadmore.onTopLoaded()})}).catch(function(i){a()(i||"网络异常，请求失败"),t&&e.$refs.loadmore.onTopLoaded()})},loadTop:function(e){this.getData(!0)},loadBottom:function(){var e=this,t=e.api.userUser_follow();if(!e.has_next)return!1;e.axios.get(t,{params:{p:e.page,to_user_id:e.$route.query.to_user_id}}).then(function(t){setTimeout(function(){if(1==t.data.status){if(e.has_next=t.data.has_next,e.page=e.page+1,t.data.list&&t.data.list.length&&t.data.list.length>0)for(var i=0;i<t.data.list.length;i++)e.list.push(t.data.list[i])}else a()(t.data.error||"操作失败");e.$nextTick(function(){e.$refs.loadmore.onBottomLoaded()})},500)}).catch(function(t){a()(t||"网络异常，请求失败"),e.$refs.loadmore.onBottomLoaded()})},handleTopChange:function(e){this.topStatus=e},handleBottomChange:function(e){this.bottomStatus=e}},watch:{list:function(){this.allLoaded=!Boolean(this.has_next)}}}}});
//# sourceMappingURL=11.cf04b939e61976e56f9a.js.map