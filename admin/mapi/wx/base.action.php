<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class baseCModule  extends baseModule
{
    public static $lib='';
	public function __construct()
	{
        parent::__construct();
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = dirname(__FILE__);
    }

}