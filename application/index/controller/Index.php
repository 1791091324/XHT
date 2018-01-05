<?php
namespace app\index\controller;

use think\Request;

class Index extends Admin
{
    //首页
    public function index()
    {
        $token = $this -> Admin();
        if($token["options"] == "shut"){
            return $this -> fetch("home/index");
        }
        return $this->fetch();
    }

}
