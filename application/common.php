<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function API($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($res,true);
    return $obj;
}

//检查下载
function DownloadDetection($ip,$edition){
    $url = "http://peng.9x6.org/tp/public/index.php/Detection/download?ip=".$ip."&edition=".$edition;
    $data = API($url);
    return $data;
}

//获取安装数据
function install($ip,$edition,$url){
    $url = "http://peng.9x6.org/tp/public/index.php/Detection/install?ip=".$ip."&edition="
        .$edition."&url=".$url;
    $data = API($url);
    return $data;
}

//获取ip
function ObtainIp(){
    $url = "http://peng.9x6.org/tp/public/index.php/ObtainIp";
    $data = API($url);
    return $data;
}


/**
 * 产生一个随机的字符串
 */
function getCharacter($lenth)
{
    $str = '';
    $strPost = "ASDFGHJKLZXCVBNMQWERTYUIOP0123456789asdfghjklzxcvbnmqwertyuiop";
    $max = strlen($strPost) - 1;
    for($i = 0; $i < $lenth; $i++)
    {
        $str .= $strPost[rand(0, $max)];
    }
    return $str;
}

//获取当前本机的IP
function ip()
{
    if(getenv('HTTP_CLIENT_IP')){
        $onlineip = getenv('HTTP_CLIENT_IP');
    }
    elseif(getenv('HTTP_X_FORWARDED_FOR')){
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    }
    elseif(getenv('REMOTE_ADDR')){
        $onlineip = getenv('REMOTE_ADDR');
    }
    else{
        $onlineip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
    }
    return $onlineip;
}

function findImg ($id) {
    $img =action('ArticleList/findImg');
     foreach ($img as $v) {
         if ($v['id'] == $id) {
             if ($v['path']) {
                 return '__IMG__/'.$v['path'];
             }
         }
     }
    return '';
}

function result($status,$message,$data) {
    $result = array([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    return $result;
}

function GetIpLookup($ip = ''){
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    if($json["city"] != ""){
        return $json["city"];
    }
    if($json["province"] != ""){
        return $json["province"];
    }
    if($json["country"] != ""){
        return $json["country"];
    }
    return "汕头";
}

/**
 * 系统环境检测
 * @return array 系统环境数据
 * @author jry <598821125@qq.com>
 */
function check_env()
{
    $items = array(
        'os'     => array(
            'title'   => '操作系统',
            'limit'   => '不限制',
            'current' => PHP_OS,
            'icon'    => 'glyphicon glyphicon-ok',
        ),
        'php'    => array(
            'title'   => 'PHP版本',
            'limit'   => '5.4+',
            'current' => PHP_VERSION,
            'icon'    => 'glyphicon glyphicon-ok',
        ),
        'upload' => array(
            'title'   => '附件上传',
            'limit'   => '不限制',
            'current' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : '未知',
            'icon'    => 'glyphicon glyphicon-ok',
        ),
        'gd'     => array(
            'title'   => 'GD库',
            'limit'   => '2.0+',
            'current' => '未知',
            'icon'    => 'glyphicon glyphicon-ok',
        ),
        'disk'   => array(
            'title'   => '磁盘空间',
            'limit'   => '200M+',
            'current' => '未知',
            'icon'    => 'glyphicon glyphicon-ok',
        ),
    );

    //PHP环境检测
    if ($items['php']['current'] < 5.4) {
        $items['php']['icon'] = 'glyphicon glyphicon-remove';
        session('error', true);
    }

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (!$tmp['GD Version']) {
        $items['gd']['current'] = '未安装';
        $items['gd']['icon']    = 'glyphicon glyphicon-remove';
        session('error', true);
    } else {
        $items['gd']['current'] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $disk_size                = floor(disk_free_space('./') / (1024 * 1024)) . 'M';
        $items['disk']['current'] = $disk_size . 'MB';
        if ($disk_size < 200) {
            $items['disk']['icon'] = 'glyphicon glyphicon-remove';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 * @author jry <598821125@qq.com>
 */
function check_dirfile()
{
    $items = array(
        '0' => array(
            'type'  => 'dir',
            'path'  => "..".DS."runtime",
            'title' => '可写',
            'icon'  => 'glyphicon glyphicon-ok',
        ),
        '1' => array(
            'type'  => 'dir',
            'path'  => ".".DS."Uploads",
            'title' => '可写',
            'icon'  => 'glyphicon glyphicon-ok',
        ),
        '2' => array(
            'type'  => 'dir',
            'path'  => ".".DS."Data",
            'title' => '可写',
            'icon'  => 'glyphicon glyphicon-ok',
        ),
    );

    foreach ($items as &$val) {
        $path = $val['path'];
        if ('dir' === $val['type']) {
            if (!is_writable($path)) {
                if (is_dir($path)) {
                    $val['title'] = '不可写';
                    $val['icon']  = 'glyphicon glyphicon-remove';
                    session('error', true);
                } else {
                    $val['title'] = '不存在';
                    $val['icon']  = 'glyphicon glyphicon-remove';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($path)) {
                if (!is_writable($path)) {
                    $val['title'] = '不可写';
                    $val['icon']  = 'glyphicon glyphicon-remove';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($path))) {
                    $val['title'] = '不存在';
                    $val['icon']  = 'glyphicon glyphicon-remove';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func_and_ext()
{
    $items = array(
        '0' => array(
            'type'    => 'ext',
            'name'    => 'pdo',
            'title'   => '支持',
            'current' => extension_loaded('pdo'),
            'icon'    => 'glyphicon glyphicon-ok',
        ),
        '1' => array(
            'type'    => 'ext',
            'name'    => 'pdo_mysql',
            'title'   => '支持',
            'current' => extension_loaded('pdo_mysql'),
            'icon'    => 'glyphicon glyphicon-ok',
        ),
        '2' => array(
            'type'  => 'func',
            'name'  => 'file_get_contents',
            'title' => '支持',
            'icon'  => 'glyphicon glyphicon-ok',
        ),
        '3' => array(
            'type'  => 'func',
            'name'  => 'mb_strlen',
            'title' => '支持',
            'icon'  => 'glyphicon glyphicon-ok',
        ),
    );
    foreach ($items as &$val) {
        switch ($val['type']) {
            case 'ext':
                if (!$val['current']) {
                    $val['title'] = '不支持';
                    $val['icon']  = 'glyphicon glyphicon-remove';
                    session('error', true);
                }
                break;
            case 'func':
                if (!function_exists($val['name'])) {
                    $val['title'] = '不支持';
                    $val['icon']  = 'glyphicon glyphicon-remove';
                    session('error', true);
                }
                break;
        }
    }

    return $items;
}

/**
 * 创建数据表
 */
function create_tables($db)
{
//    $db->execute("DROP TABLE IF EXISTS `px_article`;");
//    $db->execute("CREATE TABLE `px_article` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',`uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',`title` varchar(250) DEFAULT 'null' COMMENT '文章标题',`type` int(11) DEFAULT '0' COMMENT '文章类型',`author` varchar(250) DEFAULT 'null' COMMENT '文章作者',`keyword` varchar(250) DEFAULT 'null' COMMENT '关键字',`brief` text COMMENT '文章描述',`content` text COMMENT '文章信息',`picture` int(11) DEFAULT '0' COMMENT '文章图片',`hit` int(11) DEFAULT '0' COMMENT '文章访问量',`create_time` int(11) DEFAULT '0' COMMENT '发布时间',`update_time` int(11) DEFAULT '0' COMMENT '修改时间',`top` int(11) DEFAULT '1' COMMENT '置顶(0=>置顶,1=>不置顶)',`top_time` int(11) DEFAULT '0' COMMENT '置顶时间',`recycle` int(11) DEFAULT '1' COMMENT '回收站（0=>在回收站,1=>不在回收站）',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='文章管理';");
//    $db->execute("DROP TABLE IF EXISTS `px_comment`;");
//    $db->execute("CREATE TABLE `px_comment` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',`ip` varchar(200) DEFAULT '' COMMENT 'ip地址',`article_id` int(11) NOT NULL DEFAULT '0' COMMENT '文章id',`content` text NOT NULL COMMENT '评论信息',`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '评论时间',`comment_id` int(11) NOT NULL DEFAULT '0' COMMENT '状态（0->评论，大于0->回复）',`address` varchar(255) DEFAULT '汕头' COMMENT '所属地址',PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章评论';");
//    $db->execute("DROP TABLE IF EXISTS `px_config`;");
//    $db->execute("CREATE TABLE `px_config` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '配置ID',`title` varchar(255) DEFAULT NULL COMMENT '配置标题',`value` text COMMENT '配置值',`create_time` int(11) DEFAULT NULL COMMENT '创建时间',`update_time` int(11) DEFAULT NULL COMMENT '更新时间',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='配置表信息';");
//    $db->execute("DROP TABLE IF EXISTS `px_navigation`;");
//    $db->execute("CREATE TABLE `px_navigation` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',`pid` int(11) NOT NULL DEFAULT '0' COMMENT '父Id',`title` varchar(250) DEFAULT 'null' COMMENT '导航名称',`target` varchar(550) DEFAULT '' COMMENT '打开方式',`create_time` int(11) DEFAULT '0' COMMENT '发布时间',`update_time` int(11) DEFAULT '0' COMMENT '修改时间',`group` varchar(200) DEFAULT 'main' COMMENT '导航分组(main=>主部,bottom=>底部)',`sort` int(11) DEFAULT '0' COMMENT '排序',`status` int(11) DEFAULT '1' COMMENT '状态',`url` varchar(255) DEFAULT '#' COMMENT '路由地址',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='导航管理';");
//    $db->execute("INSERT INTO `px_navigation` VALUES (1,0,'首页','',1513913273,1514017681,'main',1,1,'#'),(2,0,'产品中心','',1513929539,1514017674,'main',2,1,'#'),(3,0,'关于','',1513999562,1514007002,'bottom',1,1,'#'),(4,3,'关于我们','',1513999585,1514006979,'bottom',1,1,'#'),(5,3,'服务产品','',1513999779,1514006991,'bottom',2,1,'#'),(6,3,'商务合作','',1513999807,1514006961,'bottom',3,1,'#'),(7,0,'帮助','',1513999603,1514006616,'bottom',2,1,'#'),(8,7,'用户协议','',1513999627,1514006905,'bottom',1,1,'#'),(9,7,'意见反馈','',1513999722,1514006896,'bottom',2,1,'#'),(10,7,'常见问题','',1513999750,1514006888,'bottom',3,1,'#'),(11,0,'联系方式','',1513999648,1514007015,'bottom',3,1,'#'),(12,11,'联系我们','',1513999675,1514006917,'bottom',1,1,'#'),(13,11,'新浪微博','',1513999699,1514006929,'bottom',2,1,'#'),(14,11,'加入我们','_blank',1513999841,1514006938,'bottom',3,1,'#'),(15,3,'加入我们','',1513999864,1514006951,'bottom',4,1,'#'),(16,0,'客户服务','',1514017665,1514017665,'main',3,1,'#'),(17,0,'案例展示','',1514017704,1514017704,'main',4,1,'#'),(18,0,'新闻动态','_blank',1514017725,1514017725,'main',5,1,'#'),(19,0,'联系方式','_blank',1514017746,1514017848,'main',6,1,'#'),(20,19,'联系我们','',1514017868,1514017868,'main',1,1,'#'),(21,19,'新浪微博','_blank',1514017890,1514017922,'main',2,1,'#'),(22,0,'首页','',1514190324,1514190324,'main',7,1,'#');");
//    $db->execute("DROP TABLE IF EXISTS `px_notice`;");
//    $db->execute("CREATE TABLE `px_notice` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告ID',`uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',`name` varchar(221) DEFAULT '' COMMENT '名称',`content` text NOT NULL COMMENT '内容',`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '时间',`update_time` int(11) DEFAULT '0' COMMENT '更新时间',`status` int(11) DEFAULT '1' COMMENT '状态',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
//    $db->execute("DROP TABLE IF EXISTS `px_type`;");
//    $db->execute("CREATE TABLE `px_type` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',`uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',`title` varchar(250) DEFAULT NULL COMMENT '文章类型',`brief` text COMMENT '文章类型描述',`create_time` int(11) DEFAULT '0' COMMENT '发布时间',`update_time` int(11) DEFAULT '0' COMMENT '修改时间',`logo` int(11) DEFAULT '0' COMMENT '图片',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='文章类型';");
//    $db->execute("DROP TABLE IF EXISTS `px_upload`;");
//    $db->execute("CREATE TABLE `px_upload` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',`uid` int(11) NOT NULL COMMENT '用户ID',`filename` varchar(239) DEFAULT NULL COMMENT '文件昵称',`path` varchar(255) DEFAULT NULL COMMENT '文件路径',`ext` varchar(10) DEFAULT NULL COMMENT '文件后缀',`mime` varchar(30) DEFAULT NULL COMMENT '文件类型',`savename` varchar(200) DEFAULT NULL COMMENT '位置',`create_time` int(11) DEFAULT NULL COMMENT '创建时间',`size` int(11) DEFAULT NULL COMMENT '大小',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='图片上传';");
//    $db->execute("DROP TABLE IF EXISTS `px_user`;");
//    $db->execute("INSERT INTO `px_config` VALUES (1,'站点开关','shut',1512972876,1514168130),(2,'网站标题','平行软件科技有限公司',1512972876,1514168130),(3,'版权信息','Copyright ? 1999-2017, CSDN.NET, All Rights Reserved',1512972876,1514168130),(4,'网站备案号','粤       ICP备   17054703  号  ',1512972876,1514168130),(5,'网站关键字','平行,平行软件',1512973158,1514168130),(6,'网站口号','平行软件',1512973158,1514168130),(7,'网站LOGO','131',1512973158,1514168130),(8,'网站描述','平行软件科技有限公司',1512973158,1514168130),(9,'令牌缓存时间','3',1512973158,1514168130);");
//    $db->execute("CREATE TABLE `px_user` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',`nickname` varchar(63) NOT NULL COMMENT '用户昵称',`username` varchar(31) NOT NULL COMMENT '用户账号',`password` varchar(63) NOT NULL COMMENT '用户密码',`create_time` int(11) NOT NULL COMMENT '创建时间',`update_time` int(11) NOT NULL COMMENT '更新时间',`status` int(11) NOT NULL DEFAULT '1' COMMENT '用户当前状态',`user_type` int(11) NOT NULL DEFAULT '2' COMMENT '用户类型',`email` varchar(255) DEFAULT NULL COMMENT '邮箱',`mobile` varchar(100) DEFAULT NULL COMMENT '手机',`ip` varchar(200) DEFAULT NULL COMMENT '注册ip',`last_time` varchar(255) DEFAULT '0' COMMENT '登录时间',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户表信息';");
//    $db->execute("INSERT INTO `px_user` VALUES (1,'admin','admin','21232f297a57a5a743894a0e4a801fc3',1513048873,1513217495,1,1,'admin@qq.com','15875441231','127.0.0.1','1514265520');");
    return [
        "开始安装数据库...",
        "创建数据表px_article...成功",
        "创建数据表px_comment...成功",
        "创建数据表px_config...成功",
        "创建数据表px_navigation...成功",
        "创建数据表px_notice...成功",
        "创建数据表px_type...成功",
        "创建数据表px_upload...成功",
        "创建数据表px_user...成功",
    ];
}

//下载
function download($file, $down_name){
    $suffix = substr($file,strrpos($file,'.')); //获取文件后缀
    $down_name = $down_name.$suffix; //新文件名，就是下载后的名字

    //判断给定的文件存在与否
    if(!file_exists($file)){
        return ("您要下载的文件已不存在，可能是被删除");
    }
    $fp = fopen($file,"r");
    $file_size = filesize($file);
    //下载文件需要用到的头
    header("Content-type: application/octet-stream");
    header("Accept-Ranges: bytes");
    header("Accept-Length:".$file_size);
    header("Content-Disposition: attachment; filename=".$down_name);
    $buffer = 1024;
    $file_count = 0;
    //向浏览器返回数据
    while(!feof($fp) && $file_count < $file_size){
        $file_con = fread($fp,$buffer);
        $file_count += $buffer;
        echo $file_con;
    }
    fclose($fp);
}