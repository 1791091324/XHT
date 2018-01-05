<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/12 0012
 * Time: 上午 9:39
 */

namespace app\admin\controller;


use app\admin\service\Token;
use think\Request;
use app\admin\model\Config as ConfigModel;

//配置管理
class Config extends Admin
{
    public function index(){
        $token = Token::getToken();
        $this -> assign("token",$token);
        $request = Request::instance();
        $this -> assign("error","");
        if($request->isPost()){
            $data = $request -> post();
            ConfigModel::findUpdate($data["logo"],"logo");
            ConfigModel::findUpdate($data["options"],"options");
            ConfigModel::findUpdate($data["copyright"],"copyright");
            ConfigModel::findUpdate($data["title"],"title");
            ConfigModel::findUpdate($data["record"],"record");
            ConfigModel::findUpdate($data["Keyword"],"Keyword");
            ConfigModel::findUpdate($data["slogan"],"slogan");
            ConfigModel::findUpdate($data["describe"],"describe");
            ConfigModel::findUpdate($data["cache"],"cache");
            $Config = Token::ConfigFindSelect();
            $this -> assign("config",$Config);
            $token = Token::getToken();
            $this -> assign("token",$token);
            return $this -> fetch();
        } else{
            if (array_key_exists('user_type', $token)) {
                if($token["user_type"] != 1) {
                    $user_type = "普通管理员";
                    $this -> assign("error",$user_type);
                }
            }
            $Config = Token::ConfigFindSelect();
            $this -> assign("config",$Config);
            return $this -> fetch();
        }
    }
}