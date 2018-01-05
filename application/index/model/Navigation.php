<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/23 0023
 * Time: 下午 3:09
 */
namespace app\index\model;

use think\Model;

class Navigation extends Model
{
    protected $hidden = "create_time,update_time";

    //父导航与子导航的关系
    public function NavPid(){
        return $this -> hasMany("Navigation","pid","id")
            -> where("status = 1")
            -> order("sort asc");
    }

    //根据导航分组查询
    public static function getNavigationByGroup($group = "main"){
        return self::with("NavPid")
            -> where(array("status" => 1, "group" => $group,"pid" => 0))
            -> order("sort asc")
            -> select();
    }
}