<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/11 0011
 * Time: 下午 3:32
 */

namespace app\admin\model;


use think\Model;

class Config extends Model
{
    protected $hidden = ['tip', 'create_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public static function findUpdate($data,$kay)
    {
        $id = 0;
        if($kay == "options") { $id = 1; }
        if($kay == "title") { $id = 2; }
        if($kay == "copyright") { $id = 3; }
        if($kay == "record") { $id = 4; }
        if($kay == "Keyword") { $id = 5; }
        if($kay == "slogan") { $id = 6; }
        if($kay == "logo") { $id = 7; }
        if($kay == "describe") { $id = 8; }
        if($kay == "cache") { $id = 9; }
        self::where(["id"=>$id])->update(["value"=>$data,"update_time"=>time()]);
    }

    public static function findSelect(){
        $Config = self::select();
        return $Config;
    }

}