<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/25 0025
 * Time: 下午 4:49
 */

namespace app\admin\model;


use think\Model;

class Comment extends Model
{
    //与文章关联
    public function getArticle(){
        return $this -> belongsTo("Article","article_id","id");
    }

    //与评论关联
    public function CommentId(){
        return $this -> hasMany("Comment","comment_id","id")
            -> order("create_time desc");
    }

    //根据文章所有id查询文章评论信息
    public static function findComment($ids = [],$listRow = 10){
        return self::with("getArticle,CommentId")
            -> where("article_id","in",$ids)
            -> where(array("comment_id" => 0))
            -> order("create_time desc")
            -> paginate($listRow);
    }

    //删除文章评论
    public static function findDeleteById($ids){
        self::where("comment_id","in",$ids) -> delete();
        return self::where ("id","in",$ids)
            -> delete();
    }

    //根据ID查询详细信息
    public static function findCommentById($id){
        return self::find($id);
    }
}