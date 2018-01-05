<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/25 0025
 * Time: 下午 4:49
 */

namespace app\admin\controller;

use app\admin\model\Article;
use app\admin\model\Comment as CommentModel;
use app\admin\service\Token;
use think\Request;

//文章评论
class Comment extends Admin
{
    protected $beforeActionList = [
        "getPublic" => ["only" => 'index,add,edit']
    ];

    private $token = null;

    public function getPublic(){
        $this -> token = Token::getToken();
        $this -> assign("token",$this -> token);
    }

    //查询文章评论
    public function index(){
        $id = Article::findArticleByUidSelectId();
        $list = CommentModel::findComment($id);
        $this -> assign("list",$list);
        return $this -> fetch();
    }

    //添加评论
    public function add(){
        $request = Request::instance();
        $data = $request -> param();
        if($request -> isPost()){
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $data = $request -> param();
            if (array_key_exists("ip",$data)){
                if($data["ip"] == ""){
                    $data["ip"] = ip();
                }
            }
            $Comment = new CommentModel([
                "ip" => $data["ip"],
                "create_time" => time(),
                "article_id" => $data["article_id"],
                "comment_id" => $data["comment_id"],
                "address" => $data["address"],
                "content" => $data["content"]
            ]);
            $Comment -> save();
            return;
        }
        if(array_key_exists("id",$data)){
            $list = CommentModel::findCommentById($data["id"]);
            if($list["comment_id"] == 0){
                $this -> assign("article_id",$list['article_id']);
                $this -> assign("comment_id",$data["id"]);
            }
            else {
                $this -> assign("article_id",0);
                $this -> assign("comment_id",0);
            }
        } else {
            $this -> assign("article_id",0);
            $this -> assign("comment_id",0);
        }
        $article = Article::findArticleByIdAndName();
        $this -> assign("article",$article);
        return $this -> fetch();
    }

    //编辑评论
    public function edit(){
        $request = Request::instance();
        $data = $request -> param();
        if($request -> isPost()){
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            if (array_key_exists("ip",$data)){
                if($data["ip"] == ""){
                    $data["ip"] = ip();
                }
            }
            $list = CommentModel::get($data["id"]);
            $list -> ip = $data["ip"];
            $list -> address = $data["address"];
            $list -> content = $data["content"];
            $list -> save();
            return;
        }
        $this -> assign("list",CommentModel::get($data["id"]));
        return $this -> fetch();
    }

    //删除评论
    public function delete(){
        $request = Request::instance();
        $shu = $request -> param();
        $data = explode(",",$shu["ids"]);
        $list = [];
        $y = 0;
        for ($i = 0; $i < count($data); $i ++){
            $id = intval($data[$i]);
            if($id > 0){
                $list[$y] = $id;
                $y ++;
            }
        }
        return CommentModel::findDeleteById($list);
    }
}