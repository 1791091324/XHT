<?php
namespace app\login\controller;
use app\admin\model\User;
use app\admin\service\Token;
use think\Controller;
use think\Db;
use think\Request;

class Register extends  Controller {

    public function register () {
        $token = Token::getToken();
        $this -> assign("token",$token);
        return $this->fetch();
    }


    public function getRegister(){
        $request = Request::instance();
        $data = $request -> param();
        $user = new User();
        if($user -> where(array("username" => $data["username"]))->find() != null){
            return ["errMsg" => "该账号已存在..."];
        }
        $data["password"] = md5($data["password"]);
        $data["create_time"] = time();
        $data["update_time"] = time();
        $data["status"] = 1;
        $data["ip"]=ip();
        $data["user_type"] = 2;
        Db::table("px_user") -> insert($data);
    }
}