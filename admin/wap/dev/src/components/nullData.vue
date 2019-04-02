<template>
	<div class="null-data">
		<img :src="img_notData" />
		<span v-text="text"></span>
	</div>
</template>
<style lang="less">
	@import (once) "../assets/css/variable.less";
	.null-data {
	    background-color: #fff;
	    padding:20px;
	    text-align:center;
	    color: @fc-light;
	    img{
    	 	width: 160px;
	        height: 160px;
	        display: block;
	        margin: 0 auto;
	    }
	  }
</style>
<script>
	export default{
		props: ['text'],
		data(){
			return {
				img_notData: require('@/assets/images/not-data.png')
			}
		}
	}
</script>