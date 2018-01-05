<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/28 0028
 * Time: 上午 10:07
 */
namespace app\install\service;

use think\Request;

class Write
{
    //创建一个config.php文件
    public static function config(){
        $url = "../".DS."conf";
        if(!file_exists($url)){
            mkdir($url);
        }
        $request = Request::instance();
        $data = explode("/public",$request->baseFile());
        $top = $data[0];
        if(file_exists($url.DS."config.php")){
            unlink($url.DS."config.php");
        }
        if(!file_exists($url.DS."config.php")){
            $file = fopen($url.DS."config.php", "w") or die("无法打开文件!");
            $txt = "<?php
return [
    'template' => [
          'layout_on' => true,
          'layout_name'=>'layout'
    ],
    'view_replace_str'       => [
        '__COMPANY__' => '平行软件科技有限公司',
        '__NAME__' => '平行软件',
        '__HTTP__' => '". $request->domain() .$top."/public/index.php',
        '__CSS-JS__' => \"". $request->domain() .$top."/public/static/data/\",
        '__IMG__' => '". $request->domain() .$top."/public',
    ],
];";
            fwrite($file, $txt);
            fclose($file);
        }
    }

    //创建一个database.php
    public static function database($db){
        $url = "../".DS."conf";
        if(!file_exists($url)){
            mkdir($url);
        }
        if(file_exists($url.DS."database.php")){
            unlink($url.DS."database.php");
        }
        if(!file_exists($url.DS."database.php")) {
            $file = fopen($url . DS . "database.php", "w") or die("无法打开文件!");
            $txt = "<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => '".$db["type"]."',
    // 服务器地址
    'hostname'        => '".$db["host"]."',
    // 数据库名
    'database'        => '".$db["database"]."',
    // 用户名
    'username'        => '".$db["username"]."',
    // 密码
    'password'        => '".$db["password"]."',
    // 端口
    'hostport'        => '".$db["port"]."',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '".$db["prefix"]."',
    // 数据库调试模式
    'debug'           => false,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'collection',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
];";
            fwrite($file, $txt);
            fclose($file);
        }
    }

    //创建一个config.json文件
    public static function configJson(){
        $url = ".".  DS ."static". DS ."ueditor". DS ."php";
        if(!file_exists($url)){
            mkdir($url);
        }
        if(file_exists($url.DS."config.json")){
            unlink($url.DS."config.json");
        }
        $request = Request::instance();
        $data = explode("/public",$request->baseFile());
        $top = $data[0];
        if(!file_exists($url.DS."config.json")){
            $file = fopen($url.DS."config.json", "w") or die("无法打开文件!");
            $txt = "/* 前后端通信相关的配置,注释只允许使用多行方式 */
{
    /* 上传图片配置项 */
    \"imageActionName\": \"uploadimage\", /* 执行上传图片的action名称 */
    \"imageFieldName\": \"upfile\", /* 提交的图片表单名称 */
    \"imageMaxSize\": 2048000, /* 上传大小限制，单位B */
    \"imageAllowFiles\": [\".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\"], /* 上传图片格式显示 */
    \"imageCompressEnable\": true, /* 是否压缩图片,默认是true */
    \"imageCompressBorder\": 1600, /* 图片压缩最长边限制 */
    \"imageInsertAlign\": \"none\", /* 插入的图片浮动方式 */
    \"imageUrlPrefix\": \"\", /* 图片访问路径前缀 */
    \"imagePathFormat\": \"".$top."/public/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}\", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                                /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
                                /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
                                /* {time} 会替换成时间戳 */
                                /* {yyyy} 会替换成四位年份 */
                                /* {yy} 会替换成两位年份 */
                                /* {mm} 会替换成两位月份 */
                                /* {dd} 会替换成两位日期 */
                                /* {hh} 会替换成两位小时 */
                                /* {ii} 会替换成两位分钟 */
                                /* {ss} 会替换成两位秒 */
                                /* 非法字符 \ : * ? \" < > | */
                                /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */

    /* 涂鸦图片上传配置项 */
    \"scrawlActionName\": \"uploadscrawl\", /* 执行上传涂鸦的action名称 */
    \"scrawlFieldName\": \"upfile\", /* 提交的图片表单名称 */
    \"scrawlPathFormat\": \"".$top."/public/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}\", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    \"scrawlMaxSize\": 2048000, /* 上传大小限制，单位B */
    \"scrawlUrlPrefix\": \"\", /* 图片访问路径前缀 */
    \"scrawlInsertAlign\": \"none\",

    /* 截图工具上传 */
    \"snapscreenActionName\": \"uploadimage\", /* 执行上传截图的action名称 */
    \"snapscreenPathFormat\": \"".$top."/public/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}\", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    \"snapscreenUrlPrefix\": \"\", /* 图片访问路径前缀 */
    \"snapscreenInsertAlign\": \"none\", /* 插入的图片浮动方式 */

    /* 抓取远程图片配置 */
    \"catcherLocalDomain\": [\"127.0.0.1\", \"localhost\", \"img.baidu.com\"],
    \"catcherActionName\": \"catchimage\", /* 执行抓取远程图片的action名称 */
    \"catcherFieldName\": \"source\", /* 提交的图片列表表单名称 */
    \"catcherPathFormat\": \"".$top."/public/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}\", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    \"catcherUrlPrefix\": \"\", /* 图片访问路径前缀 */
    \"catcherMaxSize\": 2048000, /* 上传大小限制，单位B */
    \"catcherAllowFiles\": [\".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\"], /* 抓取图片格式显示 */

    /* 上传视频配置 */
    \"videoActionName\": \"uploadvideo\", /* 执行上传视频的action名称 */
    \"videoFieldName\": \"upfile\", /* 提交的视频表单名称 */
    \"videoPathFormat\": \"/ueditor/php/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}\", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    \"videoUrlPrefix\": \"\", /* 视频访问路径前缀 */
    \"videoMaxSize\": 102400000, /* 上传大小限制，单位B，默认100MB */
    \"videoAllowFiles\": [
        \".flv\", \".swf\", \".mkv\", \".avi\", \".rm\", \".rmvb\", \".mpeg\", \".mpg\",
        \".ogg\", \".ogv\", \".mov\", \".wmv\", \".mp4\", \".webm\", \".mp3\", \".wav\", \".mid\"], /* 上传视频格式显示 */

    /* 上传文件配置 */
    \"fileActionName\": \"uploadfile\", /* controller里,执行上传视频的action名称 */
    \"fileFieldName\": \"upfile\", /* 提交的文件表单名称 */
    \"filePathFormat\": \"/ueditor/php/upload/file/{yyyy}{mm}{dd}/{time}{rand:6}\", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    \"fileUrlPrefix\": \"\", /* 文件访问路径前缀 */
    \"fileMaxSize\": 51200000, /* 上传大小限制，单位B，默认50MB */
    \"fileAllowFiles\": [
        \".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\",
        \".flv\", \".swf\", \".mkv\", \".avi\", \".rm\", \".rmvb\", \".mpeg\", \".mpg\",
        \".ogg\", \".ogv\", \".mov\", \".wmv\", \".mp4\", \".webm\", \".mp3\", \".wav\", \".mid\",
        \".rar\", \".zip\", \".tar\", \".gz\", \".7z\", \".bz2\", \".cab\", \".iso\",
        \".doc\", \".docx\", \".xls\", \".xlsx\", \".ppt\", \".pptx\", \".pdf\", \".txt\", \".md\", \".xml\"
    ], /* 上传文件格式显示 */

    /* 列出指定目录下的图片 */
    \"imageManagerActionName\": \"listimage\", /* 执行图片管理的action名称 */
    \"imageManagerListPath\": \"/ueditor/php/upload/image/\", /* 指定要列出图片的目录 */
    \"imageManagerListSize\": 20, /* 每次列出文件数量 */
    \"imageManagerUrlPrefix\": \"\", /* 图片访问路径前缀 */
    \"imageManagerInsertAlign\": \"none\", /* 插入的图片浮动方式 */
    \"imageManagerAllowFiles\": [\".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\"], /* 列出的文件类型 */

    /* 列出指定目录下的文件 */
    \"fileManagerActionName\": \"listfile\", /* 执行文件管理的action名称 */
    \"fileManagerListPath\": \"/ueditor/php/upload/file/\", /* 指定要列出文件的目录 */
    \"fileManagerUrlPrefix\": \"\", /* 文件访问路径前缀 */
    \"fileManagerListSize\": 20, /* 每次列出文件数量 */
    \"fileManagerAllowFiles\": [
        \".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\",
        \".flv\", \".swf\", \".mkv\", \".avi\", \".rm\", \".rmvb\", \".mpeg\", \".mpg\",
        \".ogg\", \".ogv\", \".mov\", \".wmv\", \".mp4\", \".webm\", \".mp3\", \".wav\", \".mid\",
        \".rar\", \".zip\", \".tar\", \".gz\", \".7z\", \".bz2\", \".cab\", \".iso\",
        \".doc\", \".docx\", \".xls\", \".xlsx\", \".ppt\", \".pptx\", \".pdf\", \".txt\", \".md\", \".xml\"
    ] /* 列出的文件类型 */

}";
            fwrite($file, $txt);
            fclose($file);
        }
    }

    //创建一个install.lock文件
    public static function install(){
        $url = ".". DS ."Data";
        if(!file_exists($url)){
            mkdir($url);
        }
        if(!file_exists($url.DS."install.lock")){
            $file = fopen($url.DS."install.lock", "w") or die("无法打开文件!");
            $txt = "lock";
            fwrite($file, $txt);
            fclose($file);
        }
    }

    //创建一个util.js文件
    public static function util(){
        $url = ".".  DS ."static". DS ."js". DS ."index".DS."common";
        if (!file_exists($url)){
            mkdir($url);
        }
        if(file_exists($url.DS."util.js")){
            unlink($url.DS."util.js");
        }
        if(!file_exists($url.DS."util.js")){
            $file = fopen($url.DS."util.js", "w") or die("无法打开文件!");
            $request = Request::instance();
            $root = $request->root(true);
            $txt = "// 过滤sql语句
var util={
    confirm: function (value, length) {
        var value =$.trim(value);
        console.log(!value)
        var re = /select|update|delete|exec|count|’|\"|=|;|>|<|%/i;
        if (!value || value.length > length) {
            if ( re.test(value) ) {
                return false
            }
            return false
        }
        return true
    },
    _request: function (obj) {
        $.ajax({
            url: obj.url,
            data: obj.data,
            type: obj.type,
            dataType: 'JSON',
            success: function (res) {
                if (obj.fun) {
                    obj.fun(res)
                }
            }
        })
    },
    dialog: function (text) {
        $('.mask span').html(text)
        $('.mask').show();
        var time;
        clearTimeout(time);
        time = setTimeout(function () {
            $('.mask').hide();
            $('.mask span').html('');
        }, 1500)
    }
}
var http={
    commend :'".$root."/index/article_list/saveCommend',
    reply : '".$root."/index/article_list/reply'
}";
            fwrite($file, $txt);
            fclose($file);
        }
        return $url;
    }
}