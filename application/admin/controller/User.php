<?php
namespace app\admin\controller;

use app\admin\service\Token;
use app\admin\model\User as UserModel;
use think\exception\ErrorException;
use think\Request;

class User extends Admin{

    //设置前置
    protected $beforeActionList = [
        "GetToken" => ["only" => 'user,edit']
    ];

    private  $token = null;

    public function GetToken(){
        $this -> token = Token::getToken();
        $this -> assign("token",$this -> token);
    }


    public function user(){
        $user = new UserModel();
        $user = $user -> paginate(10);
        foreach ($user as &$value){
            if($value["user_type"] == 1){
                $value["user_type"] = "超级管理员";
            }
            if($value["user_type"] == 2){
                $value["user_type"] = "普通管理员";
            }
        }
        $user_type = "";
        if($this -> token["user_type"] != 1){
            $user_type = "普通管理员";
        }
        $this -> assign("error",$user_type);
        $this -> assign("user",$user);
        return $this -> fetch();
    }

    //编号
    public function edit(){
        $request = Request::instance();
        $data = $request -> param();
        if($request->isPost()){
            $data["password"] = md5($data["password"]);
            $user = UserModel::get($data["id"]);
            $user -> password = $data["password"];
            $user -> update_time = time();
            $user -> email = $data["email"];
            $user -> mobile = $data["mobile"];
            $user->save();
            return "ok";
        } else {
            $id = 0;
            try {
                $id = $data["id"];
            } catch (ErrorException $errorException){}
            if($id == 0){
                $this->assign("error","参数不合法");
            } elseif($this -> token["user_type"] != 1){
                $this->assign("error","您无权限操作");
            } else{
                $user = UserModel::findUserBySelectId($id);
                $this -> assign("error","");
                if($user == null){
                    $this -> assign("error","您编辑的用户已不存在");
                }
                $this -> assign("user",$user);
            }

            if($this -> token["user_type"] != 1){
                $user_type = "普通管理员";
                $this -> assign("error",$user_type);
            }
            return $this->fetch();
        }
    }

    //对用户进行启用或禁用
    public function disableByEnable(){
        $user_type = 0;
        try {
            $user_type = Token::getToken()["user_type"];
        } catch (ErrorException $errorException){}
        if($user_type != 1){
            return ["errMsg" =>"您无权限操作"];
        }
        $request = Request::instance();
        $id = $request -> param();
        $id = intval($id["id"]);
        $user = new UserModel();
        $user = $user -> find($id);
        if($user["user_type"] == 1){
            return ["errMsg" =>"该用户为超级管理员不能禁用"];
        } else {
            if($user["status"] == 1){
                $user["status"] = 0;
                $data = "用户成功禁用";
            } else {
                $user["status"] = 1;
                $data = "用户成功启用";
            }
        }
        $user->save();
        return [
            "errMsg" => "ok",
            "data" => $data
        ];
    }

    //退出登录
    public function Logout(){
        session("token",null);
    }

    //用户修改密码
    public function Password(){
        $token = Token::getToken();
        $this -> assign("token",$token);
        $request = Request::instance();
        if($request -> isPost()){
            $id = 0;
            try{ $id = $token["id"]; } catch (ErrorException $errorException) {}
            if($id == 0){
                session("token",null);
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $user = UserModel::findUserBySelectId($id);
            if($user == null){
                session("token",null);
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $data = $request -> param();
            if($user["password"] != md5($data["password"])){
                return [
                    "errMsg" => "密码错误"
                ];
            }
            $user = UserModel::get($id);
            $user -> password = md5($data["passwords"]);
            $user -> save();
            session("token",null);
            return [
                "errMsg" => "ok"
            ];
        }
        return $this -> fetch();
    }

    //修改用户信息
    public function EditInfo(){
        $token = Token::getToken();
        $this -> assign("token",$token);
        $id = 0;
        try{ $id = $token["id"]; } catch (ErrorException $errorException) {}
        $request = Request::instance();
        if($request->isPost()){
            if($id == 0){
                session("token",null);
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $request = Request::instance();
            $data = $request->param();
            $user = UserModel::get($id);
            $user -> nickname = $data["nickname"];
            $user -> email = $data["email"];
            $user -> mobile = $data["mobile"];
            $user -> save();
            return [
                "errMsg" => "ok"
            ];
        }
        $this->assign("user",UserModel::findUserBySelectId($id));
        return $this->fetch();
    }

    //禁用和启用项
    public function getEnable(){
        $request = Request::instance();
        $data = $request -> param();
        $status = $data["status"];
        $data = explode(",",$data["ids"]);
        $list = [];
        $ids = [];
        $y = 0;
        for($i = 0; $i < count($data) - 1; $i ++){
            $id = intval($data[$i]);
            if($id > 0){
                $u = UserModel::findUserBySelectId($id);
                if($u["user_type"] > 1){
                    $ids[$y] = $id;
                    $list[$y] = [
                        "id" => $id,
                        "status" => $status
                    ];
                    $y ++;
                }
            }
        }
        $user = new UserModel();
        $user -> isUpdate() -> saveAll($list);
        return $ids;
    }

    //添加用户
    public function add(){
        $request = Request::instance();
        if($request->isPost()){
            $data = $request -> param();
            $flag = UserModel::findUserByUsername($data["username"]);
            if(!$flag){
                return [
                    "errMsg" => "账号已存在",
                ];
            }
            $user = new UserModel();
            $user -> email = $data["email"];
            $user -> mobile = $data["mobile"];
            $user -> nickname = $data["nickname"];
            $user -> password = md5($data["password"]);
            $user -> ip = ip();
            $user -> user_type = $data["user_type"];
            $user -> username = $data["username"];
            $user -> status = 1;
            $user -> create_time = time();
            $user -> update_time = time();
            $user -> save();
            return [
                "errMsg" => "ok",
            ];
        } else {
            $token = Token::getToken();
            $this -> assign("token",$token);
            return $this -> fetch();
        }
    }

}