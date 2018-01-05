<?php
/**
 * Created by PhpStorm.
 * User: 平行软件科技有限公司
 * Date: 2017/12/11 0011
 * Time: 下午 4:44
 */

namespace app\admin\controller;

use app\admin\model\Upload as UploadModel;
use app\admin\service\Token;
use app\install\service\Write;
use think\Cache;
use think\Request;

class Upload extends Admin
{
    //图片放大
    public function picture(){
        $request = Request::instance();
        $data = $request -> param();
        $upload = UploadModel::get($data["id"]);
        return $upload["path"];
    }

    //图片列表
    public function index(){
        $this -> image();
        $token = Token::getToken();
        $this -> assign("token",$token);
        $upload = UploadModel::findUploadByUid();
        foreach ($upload as &$value){
            $value["size"] = number_format($value["size"]/1024,2)."KB";
        }
        $this -> assign("upload",$upload);
        return $this -> fetch();
    }

    //删除图片
    public function delete(){
        $upload = UploadModel::findUploadById();
        if(file_exists(substr($upload["path"],1, strlen($upload["path"])-1))){
            unlink(substr($upload["path"],1, strlen($upload["path"])-1));
        }
        UploadModel::DelUploadById();
        return [
            "errMsg" => "ok",
            "data" => "该图片已删除",
        ];
    }

    //批量删除图片
    public function getDelete(){
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
        $upload = UploadModel::all($ids);
        foreach ($upload as $key => $list){
            if(file_exists(substr($list["path"],1, strlen($list["path"])-1))){
                unlink(substr($list["path"],1, strlen($list["path"])-1));
            }
        }
        return UploadModel::destroy($ids);
    }

    //单个删除图片
    public function getDel(){
        $upload = UploadModel::findUploadById();
        if(file_exists(substr($upload["path"],1, strlen($upload["path"])-1))){
            unlink(substr($upload["path"],1, strlen($upload["path"])-1));
        }
        UploadModel::DelUploadById();
        $upload = new UploadModel();
        $uid = Token::getUid();
        $upload = $upload -> where(array("uid" => $uid)) -> select();
        return $upload;
    }

    //查询已上传图片
    public function getUpload(){
        $upload = new UploadModel();
        $uid = Token::getUid();
        $upload = $upload -> where(array("uid" => $uid)) ->order("id desc") -> select();
        return $upload;
    }

    //上传图片
    public static function upload($uid){
        $file = request()->file('file');
        if(!is_dir("Uploads")){
            mkdir("Uploads");
        }
        if($file){
            $info = $file->move("Uploads". DS );
            if($info){
                $upload = new UploadModel([
                    "uid" => $uid,
                    "filename" => $info->getFilename(),
                    "path" => DS ."Uploads".DS .$info->getSaveName(),
                    "mime" => $info->getMime(),
                    "create_time" => time(),
                    "savename" => $info->getSaveName(),
                    "size" => $info->getSize(),
                    "ext" => $info->getExtension(),
                ]);
                $upload -> save();
                return $upload -> id;
            }else{
                return 0;
            }
            return 0;
        }
    }

    public function image(){
        $uid = Token::getUid();
        if($uid > 0){
            $this -> delDirFile("upload",$uid);
        }
        return;
    }

    //循环目录下的所有文件
    function delDirFile( $dirName,$uid )
    {
        if ( $handle = opendir( "$dirName" ) ) {
            while ( false !== ( $item = readdir( $handle ) ) ) {
                if ( $item != "." && $item != ".." ) {
                    if ( is_dir( "$dirName". DS ."$item" ) ) {
                        $this->delDirFile("$dirName". DS ."$item",$uid);
                    } else {
                        $list = "$dirName". DS ."$item";
                        $flag = UploadModel::findUploadByPath(DS ."$list");
                        if($flag){
                            $upload = new UploadModel([
                                "uid" => $uid,
                                "path" => DS ."$list",
                                "ext" => substr(strrchr($list, '.'), 1),
                                "size" =>  rand(1000,99999),
                                "mime" => "image/".substr(strrchr($list, '.'), 1),
                                "savename" => $list,
                                "create_time" => time(),
                                "filename" => getCharacter(24).".".substr(strrchr($list, '.'), 1)
                            ]);
                            $upload -> save();
                        }
                    }
                }
            }
        }
    }

    //循环目录下的所有文件
    function delDirAndFile( $dirName )
    {
        if ( $handle = opendir( "$dirName" ) ) {
            while ( false !== ( $item = readdir( $handle ) ) ) {
                if ( $item != "." && $item != ".." ) {
                    if ( is_dir( "$dirName". DS ."$item" ) ) {
                        $this->delDirAndFile("$dirName". DS ."$item");
                    } else {
                        unlink( "$dirName". DS ."$item" );
                    }
                }
            }
            closedir( $handle );
            rmdir( $dirName );
        }
    }

    public function lay_img_upload(){
        $file = Request::instance()->file('file');
        if(empty($file)){
            $result["code"] = "1";
            $result["msg"] = "请选择图片";
            $result['data']["src"] = '';
        }else{
            $info = $file->move("Uploads".DS);
            if($info){
                $uid = Token::getUid();
                if($uid == 0){
                    $result["code"] = "2";
                    $result["msg"] = "上传出错";
                    $result['data']["src"] ='';
                    $result["id"] = 0;
                    return json_encode($result);
                }
                $upload = new UploadModel([
                    "uid" => $uid,
                    "filename" => $info->getFilename(),
                    "path" => DS ."Uploads".DS .$info->getSaveName(),
                    "mime" => "image/".$info->getExtension(),
                    "create_time" => time(),
                    "savename" => $info->getSaveName(),
                    "size" => $info->getSize(),
                    "ext" => $info->getExtension(),
                ]);
                $upload -> save();
                //成功上传后 获取上传信息
                $result["code"] = '0';
                $result["msg"] = "上传成功";
                $result['data']["src"] = "/tp/public/Uploads/".$info->getSaveName();
                $result["id"] = $upload -> id;
                return json_encode($result);
            }else{
                // 上传失败获取错误信息
                $result["code"] = "2";
                $result["msg"] = "上传出错";
                $result['data']["src"] ='';
                $result["id"] = 0;
                return json_encode($result);
            }
        }
        return json_encode($result);
    }

    //清除缓存
    public function getCaching(){
        $dirName = RUNTIME_PATH. DS ."temp";
        if(file_exists($dirName)){
            $this->delDirAndFile($dirName);
        }
        $dirName = RUNTIME_PATH. DS ."log";
        if(file_exists($dirName)){
            $this->delDirAndFile($dirName);
        }
        return "ok";
    }

    //清除令牌缓存
    public function getToken(){
        $url = "..". DS ."runtime". DS ."temp";
        if(file_exists($url)){
            $this -> delDirAndFile($url);
        }
        $url = "..". DS ."runtime". DS ."log";
        if(file_exists($url)){
            $this -> delDirAndFile($url);
        }
        Cache::clear();
        return "ok";
    }

    //重新配置文件
    public function fileByConfigure(){
        Write::util();
        Write::config();
        Write::configJson();
        return;
    }
}