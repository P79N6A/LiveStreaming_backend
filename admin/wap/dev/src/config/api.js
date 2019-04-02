
const _baseUrl = process.env.API_ROOT
export default {
    distributionInitRegister() {
        return _baseUrl + '?ctl=distribution&act=init_register&itype=xr'
    },
 	distributionRegister() {
        return _baseUrl + '?ctl=distribution&act=register&itype=xr'
    },
    get_verifycode() {
        return _baseUrl + '?ctl=login&act=send_mobile_verify&itype=app'
    },
    get_weiboGoodsInfo() {
        return _baseUrl + '?ctl=weibo&act=goods_info&itype=xr'
    }
}
