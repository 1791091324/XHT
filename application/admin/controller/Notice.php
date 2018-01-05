<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/25 0025
 * Time: 上午 10:56
 */

namespace app\admin\controller;


use app\admin\service\Token;
use think\Request;
use app\admin\model\Notice as NoticeModel;

//公告管理
class Notice extends Admin
{
    protected $beforeActionList = [
        "getToken" => ["only"=>'index,add,edit']
    ];

    private $token = null;

    public function getToken(){
        $this -> token = Token::getToken();
        $this -> assign("token",$this -> token);
    }

    //公告列表
    public function index(){
        $notice = NoticeModel::findNoticeByUid();
        $this -> assign("notice",$notice);
        return $this -> fetch();
    }

    //添加公告
    public function add(){
        $request = Request::instance();
        if($request -> isPost()){
            $data = $request -> param();
            $uid = Token::getUid();
            if($uid == 0){
                return [
                    "errMsg" => "令牌失效"
                ];
            }
            $notice = new NoticeModel();
            $notice -> uid = $uid;
            $notice -> name = $data["name"];
            $notice -> content = $data["content"];
            $notice -> status = 1;
            $notice -> create_time = time();
            $notice -> update_time = time();
            return $notice -> save();
        }
        return $this -> fetch();
    }

    //编辑公告
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
            $notice = NoticeModel::get($data["id"]);
            $notice -> name = $data["name"];
            $notice -> content = $data["content"];
            $notice -> update_time = time();
            $notice -> save();
            return;
        }
        $this -> assign("notice",NoticeModel::get($data["id"]));
        return $this -> fetch();
    }

    //禁用或启用
    public function DisableAndEnabled(){
        $request = Request::instance();
        $data =  $request -> param();
        $notice = NoticeModel::get($data["id"]);
        if($notice["status"] == 0){
            $notice["status"] = 1;
        } else {
            $notice["status"] = 0;
        }
        $notice -> save();
        return $notice -> id;
    }

    //批量禁用或启用
    public function getDisableAndEnabled(){
        $request = Request::instance();
        $data = $request -> param();
        $uid = Token::getUid();
        if ($uid == 0){
            return [
                "errMsg" => "令牌失效"
            ];
        }
        $status = $data["status"];
        $id = $data["ids"];
        $data = explode(",",$id);
        $y  = 0;
        $list = [];
        for ($i = 0; $i < count($data) - 1; $i ++){
            if(intval($data[$i]) > 0){
                $list[$y] = [
                    "status" => $status,
                    "id" => $data[$i]
                ];
                $y ++;
            }
        }
        $nav = new NoticeModel();
        $nav -> isUpdate() -> saveAll($list);
        return;
    }

    //删除
    public function Delete(){
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
        return NoticeModel::destroy($ids);
    }
}