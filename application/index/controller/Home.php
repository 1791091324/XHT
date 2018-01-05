<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/23 0023
 * Time: 下午 5:47
 */

namespace app\index\controller;

class Home extends Admin
{
    public function index(){
        $token = $this -> Admin();
        if($token["options"] == "shut"){
            return $this -> fetch("home/index");
        }
        return $this -> fetch();
    }
}