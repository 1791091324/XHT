<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/12 0012
 * Time: 上午 9:41
 */

namespace app\admin\controller;

use app\admin\model\Upload as UploadModel;
use app\admin\service\Token;
use think\Controller;

class Admin extends Controller
{
    private $token = null;

    public function getPublic(){
        $this->token = Token::getToken();
        $this -> assign("token",$this -> token);
    }

    //图片
    public static function get_cover($id){
        $upload = new UploadModel();
        $upload = $upload -> find($id)["path"];
        return $upload;
    }
}