<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/19 0019
 * Time: 下午 2:36
 */

namespace app\admin\model;

use app\admin\service\Token;
use think\Model;

/**
 * Class Type 文章类型
 */
class Type extends Model
{
    public function Upload(){
        return $this -> belongsTo("Upload","logo","id");
    }

    //根据用户的登录id编号查询该用户添加的文章类型
    public static function findTypeByUid($rows = 10){
        $uid = Token::getUid();
        $type = self::with("Upload")->where(array("uid"=>$uid))
            -> order("id desc")
            -> paginate($rows);
        return $type;
    }

    //根据文章类型的id查询文章类型详细信息
    public static function findTypeById($id){
        return self::with("Upload") -> find($id);
    }

    //根据用户的登录id编号查询使用的文章类型
    public static function findTypeBySelect(){
        return self::order("id desc")->select();
    }
}