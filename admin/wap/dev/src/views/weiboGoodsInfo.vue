<template>
	<div class="container">
		<div class="goods-info" v-if="goods">
			<div class="goods-info-title">
				<img :src="goods.photo_image" />
				<div class="title">
					<span class="price"><i>￥</i>{{goods.price}}</span>
					<span class="after">已售<i>{{goods.red_count}}</i>笔</span>
				</div>
			</div>
			<div class="goods-info-detail">
				<p class="detail-title">{{goods.content}}</p>
				<img :src="item.url" v-for="item in goods.images" />
			</div>
		</div>
		<null-data text="该商品不存在" v-else="!goods"></null-data>
	</div>
</template>
<style lang="less" scoped>
	@import (once) "../assets/css/variable.less";
	.container{
		background: @body-bg;
	}
	.goods-info-title{
		background:#fff;
		.title{
			color: @color-theme;
			display: flex;
			justify-content:space-between;
			height:45px;
			line-height:45px;
			padding:0 1.2rem;
			.price{
				font-size: 2.2rem;
				i{
					font-size: 1.4rem;
				}
			}
			.after{
				font-size:1.2rem;
				color: @fc-light;
				i{
					color: @color-theme;
				}
			}
		}
	}
	.goods-info-detail{
		margin-top:10px;
		padding: 10px;
		background:#fff;
		font-size:1.2rem;
		color:@fc-dark;
		.detail-title{
			padding: 6px 0;
		}
		img{
			margin:5px 0;
		}
	}
</style>
<script>
	import api from '../config/api';
	import nullData from '../components/nullData.vue';
	import { MessageBox } from 'mint-ui'
	export default{
		components:{
			nullData
		},
		data(){
			return{
				goods: ''
			}
		},
		beforeRouteEnter(to, from, next){
		 	next(vm => {
		     	
		    });
		},
		created(){
			this.get();
		},
		mounted(){
		 	$(window).scrollTop(0);
		    $(window).on('scroll', () => {
		      	if (this.scrollWatch) {
	           		//your code here
        		}
		    });
		},
		methods:{
			get(){
				var self = this;
				self.axios.get(api.get_weiboGoodsInfo(),{
	            	params:{
	            		test: 1,
	            		weibo_id: self.$route.query.weibo_id
	            	}
	            })
	        	.then(res => {
		    		console.log(res.data);
		    		var result = res.data;
		    		if(result.status == 1){
	                	this.goods = result.goods;
	                }
	                else{
	                	this.goods = '';
	                	MessageBox(result.error || '商品不存在！');
	                }

		        })
		        .catch(err => console.log(err))
			}
		},
		destroyed () {
		    this.scrollWatch = false;
	  	},
	  	watch: {
		    '$route' (to, from) {
	      		this.get();
		    }
	  	},
	}
</script>