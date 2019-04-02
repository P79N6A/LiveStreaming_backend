// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Mint from 'mint-ui'
import 'mint-ui/lib/style.css'

//添加mockjs拦截请求，模拟返回服务器数据
import mock from './config/mock'

Vue.use(VueAxios, axios)
Vue.use(Mint)

Vue.config.productionTip = false

/* eslint-disable no-new */
// new Vue({
//   el: '#app',
//   router,
//   template: '<App/>',
//   components: { App }
// })

const routerApp = new Vue({
  	router,
  	render: h => h(App)
}).$mount('#app');

export default routerApp;
