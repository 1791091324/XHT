<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/13 0013
 * Time: 上午 10:15
 */

namespace app\admin\model;


use app\admin\service\Token;
use think\Model;
use think\Request;

class Upload extends Model
{
    //根据用户的id编号查询文件上传的信息
    public static function findUploadByUid($listRows = 16){
        //获取用户的id编号
        $uid = Token::getUid();
        $upload = self::where(array("uid" => $uid)) ->order("id desc") -> paginate($listRows);
        return $upload;
    }

    //根据文件的id编号查询详细信息
    public static function findUploadById(){
        $request = Request::instance();
        $id = $request->post("id");
        $Upload = self::find($id);
        return $Upload;
    }

    //根据文件的id编号删除对应的信息
    public static function DelUploadById(){
        $request = Request::instance();
        $id = $request->post("id");
        self::get($id) ->delete();
    }

    //根据文件的id编号查询详细信息
    public static function findUploadByIdS($id){
        $Upload = self::find($id);
        return $Upload;
    }

    //根据文件的path查询该文件是否存在
    public static function findUploadByPath($path){
        $Upload = self::where(array("path" => $path)) -> find();
        if($Upload == null){
            return true;
        }
        return false;
    }
}