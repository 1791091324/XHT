<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/11 0011
 * Time: 下午 2:25
 */
namespace app\admin\service;


//令牌
use app\admin\controller\Admin;
use app\admin\model\Config;
use think\Cache;
use think\exception\ErrorException;
use think\Session;

class Token
{
    // 生成令牌
    public static function generateToken()
    {
        $randChar = getCharacter(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = "YUlBFB8busKBXff1ypRz";
        return md5($randChar . $timestamp . $tokenSalt);
    }

    /**
     * 写入缓存
     * @param $data 要以json的格式写入
     */
    public static function setToken($data){
        $token = self::generateToken();
        $time = 7200;
        $config = self::ConfigFindSelect();
        if($config["cache"] > 0){
            $time = intval($config["cache"] * 60 * 60);
        }
        Session::set("token",$token);
        Cache::set($token,$data,$time);
    }

    //读取缓存
    public static function getToken(){
        $token = Session::get("token");
        if($token == "" || $token == null){
            return [
                "errMsg" => "没有令牌不能操作",
                "nickname" => "用户管理",
                "config" => self::ConfigFindSelect(),
                "user_type" => 2,
                "last_time" => date("Y-m-d H:i:s")
            ];
        }
        $token = Cache::get($token);
        if($token == "" || $token == null){
            return [
                "errMsg" => "您当前使用的令牌已过期",
                "nickname" => "用户管理",
                "config" => self::ConfigFindSelect(),
                "user_type" => 2,
                "last_time" => date("Y-m-d H:i:s")
            ];
        }
        if(!is_array($token)){
            $token = json_decode($token,true);
        }
        if(!array_key_exists("nickname",$token)){
            return [
                "errMsg" => "没有令牌不能操作",
                "nickname" => "用户管理",
                "config" => self::ConfigFindSelect(),
                "user_type" => 2,
                "last_time" => date("Y-m-d H:i:s")
            ];
        }
        $token["config"] = self::ConfigFindSelect();
        $token["errMsg"] = "";
        return $token;
    }

    //获取用户id
    public static function getUid(){
        $uid = 0;
        $token = self::getToken();
        try{
            $uid = $token["id"];
        } catch (ErrorException $errorException){}
        return $uid;
    }

    public static function ConfigFindSelect(){
        $Config = Config::findSelect();
        $data = array();
        foreach ($Config as $value){
            if($value["id"] == 1){
                $data["options"] = $value["value"];
            }
            if($value["id"] == 2){
                $data["title"] = $value["value"];
            }
            if($value["id"] == 3){
                $data["copyright"] = $value["value"];
            }
            if($value["id"] == 5){
                $data["Keyword"] = $value["value"];
            }
            if($value["id"] == 4){
                $data["record"] = $value["value"];
            }
            if($value["id"] == 6){
                $data["slogan"] = $value["value"];
            }
            if($value["id"] == 7){
                $data["logo"] = $value["value"];
                if($data["logo"] > 0){
                    $data["image"] = Admin::get_cover($data["logo"]);
                }
            }
            if($value["id"] == 8){
                $data["describe"] = $value["value"];
            }
            if($value["id"] == 9){
                $data["cache"] = $value["value"];
            }
        }
        return $data;
    }
}