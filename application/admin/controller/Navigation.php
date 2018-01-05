<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/22 0022
 * Time: 上午 10:54
 */

namespace app\admin\controller;


use app\admin\service\Token;
use app\admin\model\Navigation as NavigationModel;
use think\Request;

class Navigation extends Admin
{
    //导航分组查询
    public function group(){
        $request = Request::instance();
        $data = $request -> param();
        $nav = NavigationModel::where(array("group" => $data["group"],"pid" => 0)) -> select();
        return $nav;
    }

    //添加导航
    public function add(){
        $token = Token::getToken();
        $this -> assign("error","");
        if (array_key_exists('user_type', $token)) {
            if($token["user_type"] != 1) {
                $user_type = "普通管理员";
                $this -> assign("error",$user_type);
            }
        }
        $this -> assign("token",$token);
        $request = Request::instance();
        if($request -> isPost()){
            $data = $request -> param();
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    'errMsg' => '令牌失效'
                ];
            }
            if($data["url"] == ""){
                $data["url"] = "#";
            }
            $nav = new NavigationModel([
                "group" => $data["group"],
                "pid" => $data["pid"],
                "sort" => $data["sort"],
                "target" => $data["target"],
                "url" => $data["url"],
                "update_time" => time(),
                "create_time" => time(),
                "status" => 1,
                "title" => $data["title"],
            ]);
            $nav -> save();
            return $nav -> id;
        }
        return $this -> fetch();
    }

    //主部
    public function main(){
        $token = Token::getToken();
        $this -> assign("error","");
        if(array_key_exists("user_type",$token)){
            if($token["user_type"] != 1){
                $user_type = "普通管理员";
                $this -> assign("error",$user_type);
            }
        }
        $this->assign("nav",NavigationModel::findNavigation());
        $this -> assign("token",$token);
        return $this -> fetch();
    }

    //底部
    public function bottom(){
        $token = Token::getToken();
        $this -> assign('error',"");
        if(array_key_exists("user_type",$token)){
            if($token["user_type"] != 1){
                $user_type = "普通管理员";
                $this -> assign("error",$user_type);
            }
        }
        $this->assign("nav",NavigationModel::findNavigation("bottom"));
        $this -> assign("token",$token);
        return $this -> fetch();
    }

    //编辑导航
    public function edit(){
        $request = Request::instance();
        $data = $request -> param();
        $token = Token::getToken();
        $this -> assign("error","");
        if(array_key_exists("error",$token)){
            if($token["user_type"] != 1){
                $user_type = "普通管理员";
                $this -> assign("error",$user_type);
            }
        }
        if($request -> isPost()){
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            if($data["url"] == ""){
                $data["url"] = "#";
            }
            $nav = new NavigationModel();
            $nav -> save([
                "group" => $data["group"],
                "pid" => $data["pid"],
                "sort" => $data["sort"],
                "target" => $data["target"],
                "url" => $data["url"],
                "update_time" => time(),
                "status" => 1,
                "title" => $data["title"],
            ],["id" => $data["id"]]);
            return $data;
        }
        $this -> assign('token',$token);
        $nav = NavigationModel::get($data["id"]);
        $this->assign("nav",$nav);
        return $this -> fetch();
    }

    //禁用或启用
    public function DisableAndEnabled(){
        $request = Request::instance();
        $data = $request -> param();
        $uid = Token::getUid();
        if($uid == 0){
            return [
                "errMsg" => '令牌失效'
            ];
        }
        $nav = NavigationModel::get($data["id"]);
        if($nav["status"] == 1){
            $nav["status"] = 0;
        } else {
            $nav["status"] = 1;
        }
        return $nav -> save();
    }

    //批量禁用或启用
    public function getDisableAndEnabled(){
        $request = Request::instance();
        $data = $request -> param();
        $uid = Token::getUid();
        if ($uid == 0){
            return [
                "errMsg" => "令牌失效"
            ];
        }
        $status = $data["status"];
        $id = $data["ids"];
        $data = explode(",",$id);
        $y  = 0;
        $list = [];
        for ($i = 0; $i < count($data) - 1; $i ++){
            if(intval($data[$i]) > 0){
                $list[$y] = [
                    "status" => $status,
                    "id" => $data[$i]
                ];
                $y ++;
            }
        }
        $nav = new NavigationModel();
        $nav -> isUpdate() -> saveAll($list);
        return;
    }

    //删除
    public function delete(){
        $request = Request::instance();
        $data = $request -> param();
        $uid = Token::getUid();
        if($uid == 0){
            return [
                "errMsg" => "令牌失效"
            ];
        }
        $id = $data["ids"];
        $data = explode(",",$id);
        for ($i = 0; $i < count($data) - 1; $i ++) {
            if (intval($data[$i]) > 0) {
                $ids = intval($data[$i]);
                NavigationModel::where("id = $ids or pid = $ids") -> delete();
            }
        }
        return;
    }

}