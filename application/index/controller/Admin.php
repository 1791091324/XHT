<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/23 0023
 * Time: 下午 4:20
 */

namespace app\index\controller;

use think\Controller;
use app\admin\service\Token;
use app\index\model\Navigation;
use app\index\model\type;

class Admin extends Controller
{
    public function Admin(){
        $token = Token::ConfigFindSelect();
        $this -> assign("token",$token);
        $nav = Navigation::getNavigationByGroup("main");
        $this -> assign("nav",$nav);
        $bottom = Navigation::getNavigationByGroup("bottom");
        $this -> assign("bottom",$bottom);
        $this -> assign('articleType',$this->findArticleList());
        return $token;
    }
    public function findArticleList (){
        $res = type::select();
        if ($res) {
            return $res;
        }
    }
}