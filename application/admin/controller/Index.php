<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/9 0009
 * Time: 上午 11:15
 */

namespace app\admin\controller;

use app\admin\service\Token;
use app\admin\model\User;
use app\admin\model\Upload;
use app\admin\model\Article;
use app\admin\model\Notice;
use app\admin\model\Detection;

class Index extends Admin
{
    public function index(){
        $token = Token::getToken();
        $this -> assign("token",$token);
        //计算用户表当前的用户人数
        $count = User::findUserByCount();
        //计算今日新增的会员
        $newCount = User::getNewMembers();
        //计算你上传多少图片
        $picture = Upload::where(array("uid" => Token::getUid())) -> count();
        //计算你发布有多少文章
        $article = Article::where(array("uid" => Token::getUid())) -> count();
        //及时你有多少个公告管理
        $notice = Notice::where(array("uid" => Token::getUid())) -> count();
        $data = [
            "count" => $count,
            "newCount" => $newCount,
            "picture" => $picture,
            "article" => $article,
            "notice" => $notice
        ];
        $this -> assign("data",$data);
        return $this -> fetch();
    }
}