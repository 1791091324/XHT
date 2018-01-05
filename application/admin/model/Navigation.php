<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/22 0022
 * Time: 上午 10:54
 */

namespace app\admin\model;

use think\Model;

class Navigation extends Model
{
    //子导航或父导航关系
    public function NavPid(){
        return $this -> hasMany("Navigation","pid","id")
            -> order("sort asc");
    }

    //查询导航管理
    public static function findNavigation($group = "main",$listRow = 10){
        return self::with("NavPid")
            -> where(array("group" => $group,"pid" => 0))
            -> order("sort asc")
            -> paginate($listRow);
    }
}