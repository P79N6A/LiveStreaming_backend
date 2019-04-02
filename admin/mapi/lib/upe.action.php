<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
// header("Content-Type:text/html;charset=utf-8");
class upeModule extends baseModule
{

    //接收Excel导入的数据
    public function excel_data()
    {

        if (UPDATE_EXCEL) {
            if (!empty($_FILES['file']['name'])) {

                $file = $_FILES['file'];
                $fileType = explode(".", $_FILES['file']['name']); //获取文件后缀
                if ($fileType[1] == "csv") {
//判断文件的后缀
                    $content = @file_get_contents($file['tmp_name']); //获取上传文件的内容
                    $content = explode("\n", $content); //将内容以回车拆分
                    unset($content[0]); //销毁第一个（第一个是标题没有用）
                    //查询配置表的信息
                    $res = $GLOBALS['db']->getAll("select code,val from " . DB_PREFIX . "m_config;");

                    $date = array();
                    if ($content) {
                        //$key_last = key(end($content));//获取数组最后一个key值
                        foreach ($content as $k => $v) {
                            if ($v != '') {
                                $imp_row = explode(",", $v);
                                $upe_title = iconv("GBK", "utf-8", trim($imp_row[0])); //内容进行编码
                                $upe_code = trim($imp_row[1]);
                                $upe_val = trim($imp_row[2]);

                                $date[$k]['code'] = $upe_code;
                                $date[$k]['val'] = $upe_val;
                                if ($upe_val != "") {
                                    $res = $GLOBALS['db']->autoExecute(DB_PREFIX . 'm_config', array('val' => $upe_val), 'UPDATE', "code='" . $upe_code . "';");
                                }
                            }
                            //echo current($content). "<br>";//输出当前数组的值

                        }
                        echo "更新完成";
                        //header("Location:http://site.88817235.cn/mapi/index.php?ctl=upe&act=upe_view");

                    }
                } else {
                    echo "请上传csv后缀的Excel文件";
                }
            } else {
                echo "请选择Excel文件上传";
            }
        } else {
            print_r("请开启update_Excel模式");exit;
        }

        exit;
        /**/
    }

    public function upe_view()
    {

        if (UPDATE_EXCEL) {
            $html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <title>Excel导入</title>
            </head>
            <body>
            <script type="text/javascript" src="' . SITE_DOMAIN . '/admin/Tpl/default/Common/js/jquery.js"></script>
            <div align="center" style="padding-top: 50px;">
                <form id="form_type" method="post" action="' . SITE_DOMAIN . '/mapi/index.php?ctl=upe&act=excel_data" enctype="multipart/form-data">
                    <h3>导入Excel表：</h3><input  type="file" name="file" id="file_up" />
                    <input type="submit"  value="导入" />
                </form>
            </div>
<script type="text/javascript">



        </script>
</body>
</html>';
            echo $html;
        } else {
            print_r("请开启update_Excel模式");exit;
        }
    }

}
