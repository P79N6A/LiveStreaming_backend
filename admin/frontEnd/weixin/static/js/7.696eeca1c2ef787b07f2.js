webpackJsonp([7],{"9K2G":function(e,t,n){"use strict";var i=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"user-list"},[e.list&&e.list.length>0?n("ul",[e._l(e.list,function(t,i){return n("li",{staticClass:"list"},[n("div",{staticClass:"head-img"},[n("router-link",{attrs:{to:{path:"/user/others",query:{to_user_id:t.user_id}}}},[n("img",{directives:[{name:"lazy",rawName:"v-lazy",value:t.head_image,expression:"item.head_image"}],attrs:{width:"20px"}})])],1),n("div",{staticClass:"user-information"},[n("div",{staticClass:"user-title"},[n("div",{staticClass:"user-nick-name"},[n("span",{domProps:{innerHTML:e._s(t.nick_name)}}),n("div",{staticClass:"praise"},[1==t.sex?n("i",{staticClass:"icon iconfont sex-man icon-sex"},[e._v("")]):n("i",{staticClass:"icon iconfont sex-woman icon-sex"},[e._v("")]),n("img",{staticClass:"icon-level",attrs:{src:e.iconLevel[i]}})])]),n("div",{staticClass:"signature",domProps:{innerHTML:e._s(t.signature)}})]),n("div",{staticClass:"after"},[t.follow_id>0||t.has_focus>0?n("div",{staticClass:"btn-follow followed",on:{click:function(n){e.follow(t)}}},[n("i",{staticClass:"icon iconfont"},[e._v("")])]):n("div",{staticClass:"btn-follow",on:{click:function(n){e.follow(t)}}},[n("i",{staticClass:"icon iconfont"},[e._v("")])])])])])}),e.allLoaded?n("div",{staticClass:"no-more-data"},[e._v("无更多数据")]):e._e()],2):n("null-data")],1)},a=[],s={render:i,staticRenderFns:a};t.a=s},DH2y:function(e,t,n){"use strict";function i(e){n("YHB4")}var a=n("a5Ca"),s=n("9K2G"),o=n("J0+h"),r=i,A=o(a.a,s.a,r,"data-v-0e4de6d0",null);t.a=A.exports},Dm0A:function(e,t,n){t=e.exports=n("xCYs")(!0),t.push([e.i,".search-content .search-info[data-v-2597da40]{height:2.25rem;width:100%;padding:.5rem .4rem 0 .2rem;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;background:#fff;position:fixed;top:0;left:0;z-index:1}.search-content .search-info .back-button[data-v-2597da40]{padding-bottom:5px}.search-content .search-info .back-button .iconfont[data-v-2597da40]{font-size:1rem;color:#333}.search-content .search-info .search-bar[data-v-2597da40]{height:1.25rem;width:100%;padding:0 1rem 0 1.5rem}.search-content .search-info .search-bar input[data-v-2597da40]{height:100%;width:100%;display:block;color:#cfcfcf;font-size:.6rem;text-align:center;background:#f8f8f8;border:none;outline:none;border-radius:30px}.search-content .search-info .search-bar input[data-v-2597da40]::-webkit-input-placeholder{color:#cfcfcf}.search-content .search-info .search-button .iconfont[data-v-2597da40]{font-size:1rem;color:#333}.search-content .search-result[data-v-2597da40]{margin-top:2.25rem}","",{version:3,sources:["D:/phpStudy/WWW/yingke/frontEnd/weixin/src/views/index/search.vue"],names:[],mappings:"AAsBA,8CACE,eAAgB,AAChB,WAAY,AACZ,4BAA6B,AAC7B,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,8BAA+B,AACvC,gBAAiB,AACjB,eAAgB,AAChB,MAAO,AACP,OAAQ,AACR,SAAW,CACZ,AACD,2DACE,kBAAoB,CACrB,AACD,qEACE,eAAgB,AAChB,UAAY,CACb,AACD,0DACE,eAAgB,AAChB,WAAY,AACZ,uBAAyB,CAC1B,AACD,gEACE,YAAa,AACb,WAAY,AACZ,cAAe,AACf,cAAe,AACf,gBAAiB,AACjB,kBAAmB,AACnB,mBAAoB,AACpB,YAAa,AACb,aAAc,AACd,kBAAoB,CACrB,AACD,2FACE,aAAe,CAChB,AACD,uEACE,eAAgB,AAChB,UAAY,CACb,AACD,gDACE,kBAAoB,CACrB",file:"search.vue",sourcesContent:["/*界面主题色*/\n/*背景色*/\n/*背景色（深）*/\n/*模块背景色*/\n/*主题风格色*/\n/*主题辅助风格色*/\n/*字体颜色*/\n/*主要字色*/\n/*浅色*/\n/*浅色*/\n/*更浅色（适用：二级标题、简介）*/\n/*更更浅色（适用：icon图标）*/\n/*标题*/\n/*字体大小*/\n/*线条颜色*/\n/*主要线条颜色*/\n/*更深线条颜色*/\n/*更浅线条颜色*/\n/*文本输入框边框颜色*/\n/*各类间距，高度*/\n/*横向间距*/\n/*纵向间距*/\n.search-content .search-info[data-v-2597da40] {\n  height: 2.25rem;\n  width: 100%;\n  padding: .5rem .4rem 0 .2rem;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: justify;\n      -ms-flex-pack: justify;\n          justify-content: space-between;\n  background: #fff;\n  position: fixed;\n  top: 0;\n  left: 0;\n  z-index: 1;\n}\n.search-content .search-info .back-button[data-v-2597da40] {\n  padding-bottom: 5px;\n}\n.search-content .search-info .back-button .iconfont[data-v-2597da40] {\n  font-size: 1rem;\n  color: #333;\n}\n.search-content .search-info .search-bar[data-v-2597da40] {\n  height: 1.25rem;\n  width: 100%;\n  padding: 0 1rem 0 1.5rem;\n}\n.search-content .search-info .search-bar input[data-v-2597da40] {\n  height: 100%;\n  width: 100%;\n  display: block;\n  color: #cfcfcf;\n  font-size: .6rem;\n  text-align: center;\n  background: #f8f8f8;\n  border: none;\n  outline: none;\n  border-radius: 30px;\n}\n.search-content .search-info .search-bar input[data-v-2597da40]::-webkit-input-placeholder {\n  color: #cfcfcf;\n}\n.search-content .search-info .search-button .iconfont[data-v-2597da40] {\n  font-size: 1rem;\n  color: #333;\n}\n.search-content .search-result[data-v-2597da40] {\n  margin-top: 2.25rem;\n}\n"],sourceRoot:""}])},EfCn:function(e,t,n){t=e.exports=n("xCYs")(!0),t.push([e.i,".user-list ul[data-v-0e4de6d0]{background-color:#fff;padding-left:.5rem}.user-list ul .list[data-v-0e4de6d0]{height:3.25rem;width:100%;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;position:relative}.user-list ul .list .head-img[data-v-0e4de6d0]{height:2rem;width:2rem;border-radius:2.5rem;overflow:hidden;-ms-flex-negative:0;flex-shrink:0}.user-list ul .list .head-img img[data-v-0e4de6d0]{width:100%;height:100%}.user-list ul .list .user-information[data-v-0e4de6d0]{border-bottom:1px solid #e6e8f1;width:100%;height:3.25rem;padding:0 .5rem;font-size:.65rem;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.user-list ul .list .user-information .user-title[data-v-0e4de6d0]{padding-top:.75rem;width:65%}.user-list ul .list .user-information .user-title .user-nick-name[data-v-0e4de6d0]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.user-list ul .list .user-information .user-title .user-nick-name span[data-v-0e4de6d0]{font-size:.75rem;color:#333;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.user-list ul .list .user-information .user-title .user-nick-name .praise .iconfont[data-v-0e4de6d0]{font-size:.75rem;margin:0 .25rem;line-height:1}.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-woman[data-v-0e4de6d0]{color:#ff71bb}.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-man[data-v-0e4de6d0]{color:#3fa2ff}.user-list ul .list .user-information .user-title .user-nick-name .praise img[data-v-0e4de6d0]{width:1.3rem;height:.65rem;vertical-align:text-top}.user-list ul .list .user-information .user-title .signature[data-v-0e4de6d0]{font-size:.7rem;color:#999;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.user-list ul .list .user-information .after[data-v-0e4de6d0]{position:absolute;right:.5rem;top:50%;margin-top:-.5rem;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.user-list ul .list .user-information .after .btn-follow[data-v-0e4de6d0]{height:1rem;line-height:1rem;width:1.75rem;background:#333;border-radius:.5rem;text-align:center}.user-list ul .list .user-information .after .btn-follow .iconfont[data-v-0e4de6d0]{color:#fff;vertical-align:middle}.user-list ul .list .user-information .after .btn-follow.followed[data-v-0e4de6d0]{background:#ccc}","",{version:3,sources:["D:/phpStudy/WWW/yingke/frontEnd/weixin/src/components/userList.vue"],names:[],mappings:"AAsBA,+BACE,sBAAuB,AACvB,kBAAoB,CACrB,AACD,qCACE,eAAgB,AAChB,WAAY,AACZ,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,mBAAoB,AAC5B,iBAAmB,CACpB,AACD,+CACE,YAAa,AACb,WAAY,AACZ,qBAAsB,AACtB,gBAAiB,AACjB,oBAAqB,AACjB,aAAe,CACpB,AACD,mDACE,WAAY,AACZ,WAAa,CACd,AACD,uDACE,gCAAiC,AACjC,WAAY,AACZ,eAAgB,AAChB,gBAAiB,AACjB,iBAAkB,AAClB,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,6BAA+B,CACxC,AACD,mEACE,mBAAoB,AACpB,SAAW,CACZ,AACD,mFACE,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,kBAAoB,CAC7B,AACD,wFACE,iBAAkB,AAClB,WAAY,AACZ,cAAe,AACf,gBAAiB,AACjB,uBAAwB,AACxB,kBAAoB,CACrB,AACD,qGACE,iBAAkB,AAClB,gBAAiB,AACjB,aAAe,CAChB,AACD,sGACE,aAAe,CAChB,AACD,oGACE,aAAe,CAChB,AACD,+FACE,aAAc,AACd,cAAe,AACf,uBAAyB,CAC1B,AACD,8EACE,gBAAiB,AACjB,WAAY,AACZ,gBAAiB,AACjB,uBAAwB,AACxB,kBAAoB,CACrB,AACD,8DACE,kBAAmB,AACnB,YAAa,AACb,QAAS,AACT,kBAAoB,AACpB,oBAAqB,AACrB,oBAAqB,AACrB,aAAc,AACd,yBAA0B,AACtB,sBAAuB,AACnB,kBAAoB,CAC7B,AACD,0EACE,YAAa,AACb,iBAAkB,AAClB,cAAe,AACf,gBAAiB,AACjB,oBAAqB,AACrB,iBAAmB,CACpB,AACD,oFACE,WAAY,AACZ,qBAAuB,CACxB,AACD,mFACE,eAAiB,CAClB",file:"userList.vue",sourcesContent:["/*界面主题色*/\n/*背景色*/\n/*背景色（深）*/\n/*模块背景色*/\n/*主题风格色*/\n/*主题辅助风格色*/\n/*字体颜色*/\n/*主要字色*/\n/*浅色*/\n/*浅色*/\n/*更浅色（适用：二级标题、简介）*/\n/*更更浅色（适用：icon图标）*/\n/*标题*/\n/*字体大小*/\n/*线条颜色*/\n/*主要线条颜色*/\n/*更深线条颜色*/\n/*更浅线条颜色*/\n/*文本输入框边框颜色*/\n/*各类间距，高度*/\n/*横向间距*/\n/*纵向间距*/\n.user-list ul[data-v-0e4de6d0] {\n  background-color: #fff;\n  padding-left: .5rem;\n}\n.user-list ul .list[data-v-0e4de6d0] {\n  height: 3.25rem;\n  width: 100%;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  position: relative;\n}\n.user-list ul .list .head-img[data-v-0e4de6d0] {\n  height: 2rem;\n  width: 2rem;\n  border-radius: 2.5rem;\n  overflow: hidden;\n  -ms-flex-negative: 0;\n      flex-shrink: 0;\n}\n.user-list ul .list .head-img img[data-v-0e4de6d0] {\n  width: 100%;\n  height: 100%;\n}\n.user-list ul .list .user-information[data-v-0e4de6d0] {\n  border-bottom: 1px solid #e6e8f1;\n  width: 100%;\n  height: 3.25rem;\n  padding: 0 .5rem;\n  font-size: .65rem;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: justify;\n      -ms-flex-pack: justify;\n          justify-content: space-between;\n}\n.user-list ul .list .user-information .user-title[data-v-0e4de6d0] {\n  padding-top: .75rem;\n  width: 65%;\n}\n.user-list ul .list .user-information .user-title .user-nick-name[data-v-0e4de6d0] {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n}\n.user-list ul .list .user-information .user-title .user-nick-name span[data-v-0e4de6d0] {\n  font-size: .75rem;\n  color: #333;\n  display: block;\n  overflow: hidden;\n  text-overflow: ellipsis;\n  white-space: nowrap;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise .iconfont[data-v-0e4de6d0] {\n  font-size: .75rem;\n  margin: 0 .25rem;\n  line-height: 1;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-woman[data-v-0e4de6d0] {\n  color: #ff71bb;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise .sex-man[data-v-0e4de6d0] {\n  color: #3fa2ff;\n}\n.user-list ul .list .user-information .user-title .user-nick-name .praise img[data-v-0e4de6d0] {\n  width: 1.3rem;\n  height: .65rem;\n  vertical-align: text-top;\n}\n.user-list ul .list .user-information .user-title .signature[data-v-0e4de6d0] {\n  font-size: .7rem;\n  color: #999;\n  overflow: hidden;\n  text-overflow: ellipsis;\n  white-space: nowrap;\n}\n.user-list ul .list .user-information .after[data-v-0e4de6d0] {\n  position: absolute;\n  right: .5rem;\n  top: 50%;\n  margin-top: -0.5rem;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n}\n.user-list ul .list .user-information .after .btn-follow[data-v-0e4de6d0] {\n  height: 1rem;\n  line-height: 1rem;\n  width: 1.75rem;\n  background: #333;\n  border-radius: .5rem;\n  text-align: center;\n}\n.user-list ul .list .user-information .after .btn-follow .iconfont[data-v-0e4de6d0] {\n  color: #fff;\n  vertical-align: middle;\n}\n.user-list ul .list .user-information .after .btn-follow.followed[data-v-0e4de6d0] {\n  background: #ccc;\n}\n"],sourceRoot:""}])},TRIl:function(e,t,n){"use strict";function i(e){n("WWbY")}Object.defineProperty(t,"__esModule",{value:!0});var a=n("xwsg"),s=n("k8d/"),o=n("J0+h"),r=i,A=o(a.a,s.a,r,"data-v-2597da40",null);t.default=A.exports},WWbY:function(e,t,n){var i=n("Dm0A");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);n("XkoO")("52fea25a",i,!0)},YHB4:function(e,t,n){var i=n("EfCn");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);n("XkoO")("09915a22",i,!0)},a5Ca:function(e,t,n){"use strict";var i=n("BO1k"),a=n.n(i),s=n("34+y"),o=(n.n(s),n("X+yh")),r=n.n(o),A=n("bIIf");t.a={props:["list","allLoaded"],components:{nullData:A.a},methods:{follow:function(e){this.axios.get(this.api.userFollow(),{params:{to_user_id:e.user_id}}).then(function(t){1==t.data.status?(void 0!=e.follow_id&&(e.follow_id=t.data.has_focus),void 0!=e.has_focus&&(e.has_focus=t.data.has_focus)):r()(t.data.error||"操作失败")}).catch(function(e){r()(e||"请求失败")})}},computed:{iconLevel:function(){var e=[],t=!0,i=!1,s=void 0;try{for(var o,r=a()(this.list);!(t=(o=r.next()).done);t=!0){var A=o.value;e.push(n("3T/a")("./rank_"+A.user_level+".png"))}}catch(e){i=!0,s=e}finally{try{!t&&r.return&&r.return()}finally{if(i)throw s}}return e}}}},"k8d/":function(e,t,n){"use strict";var i=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"search-content"},[n("div",{staticClass:"search-info"},[n("div",{staticClass:"search-bar"},[n("input",{directives:[{name:"model",rawName:"v-model",value:e.keyword,expression:"keyword"}],attrs:{type:"text",placeholder:"请搜索用户名或用户ID"},domProps:{value:e.keyword},on:{input:function(t){t.target.composing||(e.keyword=t.target.value)}}})]),n("div",{staticClass:"search-button"},[n("i",{staticClass:"icon iconfont",on:{click:e.search}},[e._v("")])])]),n("div",{staticClass:"search-result"},[n("mt-loadmore",{ref:"loadmore",attrs:{"top-method":e.loadTop,"bottom-method":e.loadBottom,"bottom-all-loaded":e.allLoaded,autoFill:e.autoFill},on:{"top-status-change":e.handleTopChange,"bottom-status-change":e.handleBottomChange}},[n("mint-loadmore-top",{attrs:{slot:"top",topStatus:e.topStatus},slot:"top"}),[n("user-list",{attrs:{list:e.list,allLoaded:e.allLoaded}})],n("mint-loadmore-bottom",{directives:[{name:"show",rawName:"v-show",value:!e.allLoaded,expression:"!allLoaded"}],attrs:{slot:"bottom",bottomStatus:e.bottomStatus},slot:"bottom"})],2)],1)])},a=[],s={render:i,staticRenderFns:a};t.a=s},xwsg:function(e,t,n){"use strict";var i=n("qONS"),a=(n.n(i),n("UQTY")),s=n.n(a),o=n("34+y"),r=(n.n(o),n("X+yh")),A=n.n(r),l=n("Dd8w"),d=n.n(l),c=n("DH2y"),u=n("Umb+"),f=(n.n(u),n("NYxO")),m=n("DG/j"),h=n("Xy6p");t.a={name:"liveSearch",beforeRouteEnter:function(e,t,n){n(function(e){e.setFooterBarActiveName("indexSearch"),$(".page-content").scrollTop(0)})},data:function(){return{keyword:"",list:"",has_next:"",page:2,autoFill:!1,allLoaded:!0,topStatus:"",bottomStatus:"",isSearching:!1}},components:{userList:c.a,mintLoadmoreTop:m.a,mintLoadmoreBottom:h.a},methods:d()({},n.i(f.c)(["setFooterBarActiveName"]),{check:function(){return!!$.trim(this.keyword)||(A()("请输入用户名或用户ID"),this.list="",!1)},search:function(){var e=this,t=arguments.length>0&&void 0!==arguments[0]&&arguments[0];if(this.isSearching)return!1;if(this.check()){this.isSearching=!0,s.a.open();var n=this.api.userSearch();this.axios.get(""+n,{params:{keyword:this.keyword}}).then(function(n){s.a.close(),e.isSearching=!1,1==n.data.status?(e.list=n.data&&n.data.list,e.has_next=n.data.has_next,e.page=2):A()(n.data.error||"请求出错"),e.$nextTick(function(){t&&this.$refs.loadmore.onTopLoaded()})}).catch(function(n){e.isSearching=!1,s.a.close(),A()(n||"网络异常，请求失败"),t&&e.$refs.loadmore.onTopLoaded()})}},loadTop:function(e){this.check()?this.search(!0):this.$refs.loadmore.onTopLoaded()},loadBottom:function(){var e=this,t=e.api.userSearch();if(!e.has_next)return setTimeout(function(){A()({message:"无更多数据",duration:1e3}),e.$refs.loadmore.onBottomLoaded()},500),!1;e.axios.get(t,{params:{p:e.page,keyword:e.keyword}}).then(function(t){setTimeout(function(){if(1==t.data.status){if(e.has_next=t.data.has_next,e.page=e.page+1,t.data.list&&t.data.list.length&&t.data.list.length>0)for(var n=0;n<t.data.list.length;n++)e.list.push(t.data.list[n])}else A()(t.data.error||"请求出错");e.$nextTick(function(){e.$refs.loadmore.onBottomLoaded()})},500)}).catch(function(t){A()(t||"网络异常，请求失败"),e.$refs.loadmore.onBottomLoaded()})},handleTopChange:function(e){this.topStatus=e},handleBottomChange:function(e){this.bottomStatus=e}}),watch:{keyword:function(){this.search()},list:function(){this.allLoaded=!Boolean(this.has_next)}}}}});
//# sourceMappingURL=7.696eeca1c2ef787b07f2.js.map