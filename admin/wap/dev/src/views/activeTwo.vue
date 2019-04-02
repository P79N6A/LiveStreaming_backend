<template>
	<div class="active-container">
		<div class="main">
			<img src="../assets/images/img-activeTwo-1.jpg" />
			<active-text :avatar="head_image" :nickName="from_nick_name" :text="text"></active-text>
			<active-btn :btnImg="btnImg" :submit="download"></active-btn>
		</div>
	</div>
</template>
<style>
	img{ max-width:100% }
</style>
<script type="text/javascript">
	import api from '../config/api';
	import activeText from '../components/activeText.vue'
	import activeBtn from '../components/activeBtn.vue'
	export default{
        components: {
        	activeText,
        	activeBtn
	  	},
		data(){
			return {
				scrollWatch:true,
				head_image: '',
				nick_name: '',
				text: '让我们一起在鲜肉APP上轻松的挣红包、愉快的玩耍吧！！',
				app_down_url: '',
				btnImg: require('@/assets/images/btn_download.png')
			}
		},
		created(){
			this.get();
		},
		mounted: function() {
			$(window).scrollTop(0);
		    $(window).on('scroll', () => {
		      	if (this.scrollWatch) {
	           		//your code here
        		}
		    });
        },
     	methods: {
     		get(){
     			// 抓取数据
				var self = this;
				self.axios.get(api.distributionInitRegister(),{
			    	params: {
			      		"test": 1,
						"user_id": self.$route.query.user_id
			    	}
			  	})
	        	.then(res => {
		    		console.log(res.data);
		    		var result = res.data;
		    		if(result.status == 1){
	                	this.head_image = result.head_image;
	                	this.from_nick_name = result.from_nick_name;
	                	this.app_down_url = result.app_down_url;
	                }
	                else{
	                	MessageBox(result.error);
	                }

		        })
		        .catch(err => console.log(err))
     		},
     		download(){
     			location.href = this.app_down_url;
     		}
        },
        destroyed () {
		    this.scrollWatch = false;
	  	}
	}
</script>