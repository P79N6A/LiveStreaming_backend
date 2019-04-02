<?php

fanwe_require(APP_ROOT_PATH.'mapi/lib/society.action.php');
class societyCModule  extends societyModule
{
	// 申请工会
 	public function apply_sociaty()
    {
        api_ajax_return();
    }
}
?>