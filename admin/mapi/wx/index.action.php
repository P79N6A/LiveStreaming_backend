<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class indexCModule extends baseModule
{
    public function index()
    {
        //$user_id = self::getUserId();

        api_ajax_return(array(data => 1,));
    }
}
