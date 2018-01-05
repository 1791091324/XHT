<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/19 0019
 * Time: 下午 2:35
 */

namespace app\admin\model;


use app\admin\service\Token;
use think\Model;

//文章列表
class Article extends Model
{
    //文章封面关联
    public function pictureImg(){
        return $this -> belongsTo("Upload","picture","id");
    }

    //文章类型关联
    public function ArticleType(){
        return $this -> belongsTo("Type","type","id");
    }

    //根据用户登录的id用户查询用户添加的文章详细信息
    public static function findArticleByUid($recycle = 1,$listRow = 13){
        $uid = Token::getUid();
        return self::with("pictureImg,ArticleType")
            -> where(array("uid" => $uid,"recycle"=>$recycle))
            -> order("top,top_time desc")
            -> paginate($listRow);
    }

    //根据文章的id编号查询该文章的详细信息
    public static function findArticleById($id = 0){
        return self::with("pictureImg,ArticleType")
            -> find($id);
    }

    //根据用户登录的id用户查询用户添加的文章
    public static function findArticleByIdAndName(){
        $uid = Token::getUid();
        return self::where(array("uid" => $uid))
            -> field("id,title")
            -> select();
    }

    //根据用户登录ID查询文章的所有id
    public static function findArticleByUidSelectId(){
        $list = self::where(array("uid" => Token::getUid()))
            -> field("id") -> select();
        $id = [];
        $y = 0;
        foreach ($list as $value){
            $id[$y] = $value["id"];
            $y ++;
        }
        return $id;
    }

}