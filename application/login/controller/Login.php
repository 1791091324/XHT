<?php
namespace app\login\controller;
use app\admin\model\User;
use app\admin\service\Token;
use think\Controller;
use think\Request;

class Login extends Controller{
    public function login () {
        $token = Token::getToken();
        $flag = "false";
        if(array_key_exists("username",$token)){
            $flag = "true";
        }
        $token["flag"] = $flag;
        $this -> assign("token",$token);
        return $this->fetch();
    }
    public function getLogin(){
        $request = Request::instance();
        $data = $request -> post();
        $user = User::getLogin($data["username"]);
        if($user == null){
            return [
                "errMsg" => "该账号不存在",
            ];
        }
        if(array_key_exists('password', $user)){
            return [
                "errMsg" => "该账号不存在",
            ];
        }
        if ($user["password"] != md5($data["password"])) {
            return [
                "errMsg" => "密码输入有误"
            ];
        }
        if($user["status"] <= 0){
            return [
                "errMsg" => "该账号被禁用了",
            ];
        }
        User::getLastTime($user->id);
        Token::setToken($user);
        return [
            "errMsg" => ""
        ];
     }
}
