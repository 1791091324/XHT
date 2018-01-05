<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/28 0028
 * Time: 下午 1:40
 */

if (is_file('./public/Data/install.lock')){
    echo "<script type='text/javascript'>window.location.href ='./public/index.php/login';</script>";
} else {
    echo "<script type='text/javascript'>window.location.href ='./public/index.php/install';</script>";
}