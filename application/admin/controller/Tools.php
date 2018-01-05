<?php
namespace app\admin\controller;
use think\Controller;
class Tools extends Controller {
    public function ueditor()
    {
        if(request()->isPost()){

            $content = input('post.content');
            dump($content);
            die;
        }

        return $this->fetch();
    }
}