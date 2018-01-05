<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/19 0019
 * Time: 下午 2:34
 */

namespace app\admin\controller;

use app\admin\model\Article as ArticleModel;
use app\admin\model\Type;
use app\admin\service\Token;
use think\Request;
use app\admin\model\Comment;

/**
 * Class Article  文章类型或文章管理
 * @package app\admin\controller
 */
class Article extends Admin
{
    //文章类型列表
    public function type(){
        $token = Token::getToken();
        $this -> assign("token",$token);
        $request = Request::instance();
        $type = Type::findTypeByUid();
        if($request -> isPost()) {
            return $type;
        }
        $this->assign("type",$type);
        return $this -> fetch();
    }

    //添加文章类型
    public function typeAdd(){
        $request = Request::instance();
        if($request -> isPost()){
            $data = $request -> param();
            $type = new Type();
            $uid = Token::getUid();
            if($uid == 0){
                return ["errMsg" => "令牌失效"];
            }
            $type -> uid = $uid;
            $type -> title = $data["title"];
            $type -> brief = $data["brief"];
            $type -> logo = $data["logo"];
            $type -> create_time = time();
            $type -> update_time = time();
            $type -> save();
            return;
        }
        $token = Token::getToken();
        $this -> assign("token",$token);
        return $this -> fetch();
    }

    //编辑文章类型
    public function typeEdit(){
        $this -> assign("token",Token::getToken());
        $request = Request::instance();
        $data = $request -> param();
        if($request -> isPost() ){
            $uid = Token::getUid();
            if($uid == 0){
                return ["errMsg" => "令牌失效"];
            }
            $type =  Type::get($data["id"]);
            $type -> title = $data["title"];
            $type -> brief = $data["brief"];
            $type -> logo = $data["logo"];
            $type -> update_time = time();
            $type -> save();
            return;
        }
        $type = Type::findTypeById($data["id"]);
        $this -> assign("type",$type);
        return $this -> fetch();
    }

    //删除文章类型
    public function typeDelete(){
        $request = Request::instance();
        $data = $request->param()["ids"];
        $data = explode(",",$data);
        $ids = [];
        $y = 0;
        for ($i = 0; $i < count($data); $i ++){
            $id = intval($data[$i]);
            if($id > 0){
                $ids[$y] = $id;
                $y ++;
            }
        }
        return Type::destroy($ids);
    }

    //添加文章
    public function add(){
        $this -> assign("token",Token::getToken());
        $request = Request::instance();
        if($request -> isPost()){
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $data = $request -> param();
            $Article = new ArticleModel();
            $Article -> uid = $uid;
            $Article -> title = $data["title"];
            $Article -> type = $data["type"];
            $Article -> author = $data["author"];
            $Article -> keyword = $data["keyword"];
            $Article -> brief = $data["brief"];
            $Article -> content = $data["content"];
            $Article -> picture = $data["picture"];
            $Article -> hit = $data["hit"];
            $Article -> create_time = time();
            $Article -> top_time = time();
            $Article -> update_time = time();
            $Article -> save();
            return $Article;
        }
        $this -> assign("type",Type::findTypeBySelect());
        return $this -> fetch();
    }

    //查询文章
    public function index(){
        $this -> assign("token",Token::getToken());
        $Article = ArticleModel::findArticleByUid();
        $this -> assign("article",$Article);
        return $this -> fetch();
    }

    //编辑文章
    public function edit(){
        $this -> assign("token",Token::getToken());
        $request = Request::instance();
        $data  = $request -> param();
        if($request -> isPost()){
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $Article = ArticleModel::get($data["id"]);
            $Article -> uid = $uid;
            $Article -> title = $data["title"];
            $Article -> type = $data["type"];
            $Article -> author = $data["author"];
            $Article -> keyword = $data["keyword"];
            $Article -> brief = $data["brief"];
            $Article -> content = $data["content"];
            $Article -> picture = $data["picture"];
            $Article -> hit = $data["hit"];
            $Article -> update_time = time();
            $Article -> save();
            return;
        }
        $article = ArticleModel::findArticleById($data["id"]);
        $this -> assign("article",$article);
        $this -> assign("type",Type::findTypeBySelect());
        return $this -> fetch();
    }

    //是否置顶
    public function Recycle(){
        $request = Request::instance();
        $data = $request -> param();
        $article =  ArticleModel::get($data["id"]);
        if($article["top"] == 1){
            $article["top"] = 0;
            $article["top_time"] = time();
        } else {
            $article["top"] = 1;
            $article["top_time"] = strtotime($article["create_time"]);
        }
        $article -> save();
        return;
    }

    //加入或取出回收站
    public function RecycleOne(){
        $request = Request::instance();
        $shu = $request -> param();
        $data = explode(",",$shu["ids"]);
        $list = [];
        $y = 0;
        for ($i = 0; $i < count($data); $i ++){
            $id = intval($data[$i]);
            if($id > 0){
                $list[$y] = [
                    "id" => $id,
                    "recycle" => $shu["recycle"]
                ];
                $y ++;
            }
        }
        $article = new ArticleModel();
        $article -> isUpdate() -> saveAll($list);
        return $list;
    }

    //删除文章
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
        return ArticleModel::destroy($list);
    }

    //回收站
    public function recovery(){
        $token = Token::getToken();
        $this -> assign("token",$token);
        $this -> assign("article",ArticleModel::findArticleByUid(0));
        return $this->fetch();
    }
}