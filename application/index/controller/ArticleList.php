<?php
namespace app\index\controller;
use think\Controller;
use think\Config;
use think\Request;
use app\index\model\article;
use app\index\model\upload;
use app\index\model\notice;
use app\index\model\comment;
class ArticleList extends Admin {
    public function index (Request $request) {
        $token = $this -> Admin();
        if($token["options"] == "shut"){
            return $this -> fetch("home/index");
        }
        $limit = 3;
        $id = $request->param('id');
        $page=article::where(['type' => $id,'recycle' => 1])
            ->order('top_time desc')
            ->paginate($limit);
        $notice = notice::where(["status" => 1]) -> order("id desc") ->paginate(40);
        if ($page) {
            $this->assign('articleList',$page);
            $this->assign('notice',$notice);
            return $this->fetch();
        } else {
            $this->redirect('ArticleList/index');
        }
    }
    public function detail (Request $request) {
        $token = $this -> Admin();
        if($token["options"] == "shut"){
            return $this -> fetch("home/index");
        }
        $id = $request->param('id');
        if (!$id) {
            $this->reject('ArticleList/index');
        }
        $limit = 10;
        $article = article::get($id);
        $article -> hit = $article["hit"]+1;
        $article -> save();
        $notice = notice::where(["status" => 1]) -> order("id desc") ->paginate(40);
        $res = article::where([
            'id' => $id
        ])->find();
        $page = comment::where([
            'article_id' => $id,
            'comment_id' => ['eq','']
        ]) ->order('create_time desc')
            ->paginate($limit);
        foreach ($page as $v) {
            $v['reply'] = comment::where([
                'comment_id' => $v['id']
            ])->select();
        }
        if ($res) {
            $this->assign('articleDetail',$res);
            $this->assign('notice',$notice);
            $this->assign('comment',$page);
            return $this->fetch();
        } else {
            $this->reject('ArticleList/index');
        }
    }
    public function saveCommend ($format='json',Request $request) {
        $data = $request->post('commend');
        $id = $request->post('id');
        if (!in_array($format, ['json','xml'])) {
            $format = 'json';
        }
        Config::set('default_return_type',$format);
        if (!isset($data) || !$data) {
            return result(100, '评论不能为空！','');
        }
        if (!isset($id) || !$id) {
            $this->reject('ArticleList/index');
        }
        $article = article::where([
            'id' => $id
        ])->find();
        $address = GetIpLookup(ip());
        if(strlen($address) == 0){
            $address = "汕头";
        }
        if($article) {
            $comment = comment::create([
               'ip'=> ip(),
               'article_id' => $id,
                'content' => $data,
                'create_time' => time(),
                "address" => $address
            ]);
            if ($comment) {
                return result(200,'评论成功','');
            } else {
                return result(100,'评论失败','');
            }
        } else {
            $this->reject('ArticleList/index');
        }

    }
    public function reply ($format='json',Request $request) {
        if ($request->isPost()) {
            if (!in_array($format, ['json','xml'])) {
                $format = 'json';
            }
            Config::set('default_return_type',$format);
            $articleId = $request->post('articleId');
            $commentId = $request->post('id');
            $content =$request->post('content');
            if(!$articleId || !isset($articleId)) {
                return result(100,'文章不存在!', $articleId);
            }
            if(!$commentId || !isset($commentId)) {
                return result(100,'回复对象不存在！','');
            }
            if(!$content || !isset($content)) {
                return result(100,'回复内容不能为空！','');
            }
            $article = article::where([
                'id' => $articleId
            ])->find();
            if (!$article) {
                return result(100,'文章不存在!','');
            }
            $comment = comment::where([
                'id' => $commentId
            ])->find();
            if (!$comment) {
               return result(100,'回复对象不存在！','');
            }
            $res= comment::create([
               'ip' => ip(),
               'content' => $content,
               'article_id' => $articleId,
               'comment_id' => $commentId,
               'create_time' => time()
            ]);
            if ($res) {
                return result(200,'回复成功！','');
            } else {
                return result(100,'回复失败！','');
            }
        }
    }
    public function findImg () {
        $res = upload::select();
        return $res;
    }
    public function getReply ($id) {
        $reply = comment::where([
            'comment_id' => $id
        ])->select();
        return $reply;
    }
}