import Vue from 'vue'
import Router from 'vue-router'
import activeIndex from '@/views/activeIndex'
import activeTwo from '@/views/activeTwo'
import weiboGoodsInfo from '@/views/weiboGoodsInfo'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'activeIndex',
      component: activeIndex
    },
    {
      path: '/activeIndex',
      name: 'activeIndex',
      component: activeIndex
    },
    {
      path: '/activeTwo',
      name: 'activeTwo',
      component: activeTwo
    },
    {
      path: '/weiboGoodsInfo',
      name: 'weiboGoodsInfo',
      component: weiboGoodsInfo
    }
  ]
})
