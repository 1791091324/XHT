<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/9 0009
 * Time: 下午 3:54
 */
namespace app\admin\model;

use think\Model;
//用户信息列表
class User extends Model
{
    //登录
    public static function getLogin($username){
        $user = self::where(array("username"=>$username))
            ->field("id,nickname,username,password,status,user_type,last_time")
            -> find();
        if($user == null){
            return $user;
        }
        $user["last_time"] = date("Y-m-d H:i:s");
        return $user;
    }

    //查询用户列表详细信息
    public static function findUserBySelectId($id){
        $user = self::field("id,nickname,username,password,email,mobile,user_type")->find($id);
        return $user;
    }

    //计算用户表当前的用户人数
    public static function findUserByCount(){
        return self::count();
    }

    //计算今日新增的会员
    public static function getNewMembers(){
        $start = strtotime(date("Y-m-d"));
        $end = strtotime(date('Y-m-d',strtotime('+1 day')));
        return self::where("create_time >= $start and create_time <= $end") ->count();
    }

    //修改用户最后登录时间
    public static function getLastTime($id){
        $user = self::get($id);
        $user["last_time"] = time();
        $user -> save();
    }

    //判断账号是否存在
    public static function findUserByUsername($username){
        $user = self::where(array("username"=>$username))->find();
        if($user == null){
            return true;
        }
        return false;
    }
}