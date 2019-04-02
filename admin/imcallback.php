<?php

header("Content-Type:text/html; charset=utf-8");

$json = file_get_contents('php://input');
$post = json_decode($json, true);
define("CTL", 'ctl');
define("ACT", 'act');
if (!defined('APP_ROOT_PATH')) {
    define('APP_ROOT_PATH', str_replace('imcallback.php', '', str_replace('\\', '/', __FILE__)));
}

require APP_ROOT_PATH . 'public/directory_init.php';
require APP_ROOT_PATH . 'system/define.php';
require APP_ROOT_PATH . "system/cache/Rediscache/Rediscache.php";

switch ($post['CallbackCommand']) {
    case 'Group.CallbackAfterNewMemberJoin':
        define("FANWE_REQUIRE", true);
        //require './system/system_init.php';
        require APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
        require APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php';
        $video_viewer_obj = new VideoViewerRedisService();

        $video_viewer_obj->member_join($post);
        break;
    case 'Group.CallbackAfterMemberExit':
        define("FANWE_REQUIRE", true);
        //require './system/system_init.php';
        require APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
        require APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php';
        $video_viewer_obj = new VideoViewerRedisService();

        $video_viewer_obj->member_exit($post);
        break;
    case 'State.StateChange':
        define("FANWE_REQUIRE", true);
        //require './system/system_init.php';
        require APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
        require APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php';
        $video_viewer_obj = new UserRedisService();
        switch ($post['Info']['Action']) {
            case 'Login':
                $video_viewer_obj->online_set_add($post['Info']['To_Account']);
                break;
            case 'Logout':
            default:
                $video_viewer_obj->online_set_rm($post['Info']['To_Account']);

                //查询用户最后观看的房间信息
                require APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php';
                $video_viewer_obj = new VideoViewerRedisService();
                $video_viewer_obj->member_drop_line_exit($post['Info']['To_Account']);
                break;
        }
        break;
}

echo json_encode(array('ActionStatus' => 'OK', 'ErrorCode' => 0, 'ErrorInfo' => ''));

exit;
//log_result2(print_r($post,1));

// 打印log
function log_result2($word)
{
    $file = "./imcallback_log/notify_url.log"; //log文件路径
    $fp = fopen($file, "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "执行日期：" . strftime("%Y-%m-%d-%H：%M：%S", time()) . "\n" . $word . "\n\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
