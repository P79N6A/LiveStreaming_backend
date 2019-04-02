<template>
  <div id="app">
    <transition name="fade" mode="out-in">
      <router-view></router-view>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'app'
}
</script>

<style lang="less">
  @import './assets/css/reset.css';
  @import './assets/css/variable.less';
  @import './assets/css/public.less';
 .fade-enter-active, .fade-leave-active {
      transition: all .4s ease;
  }
  .fade-enter{
      opacity:0;
  }
  .fade-leave-active{
      opacity:0;
  }
</style>
