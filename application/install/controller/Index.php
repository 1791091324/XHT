<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/28 0028
 * Time: 上午 10:05
 */
namespace app\install\controller;

use app\install\service\Write;
use think\Controller;
use think\Db;
use think\Request;

class Index extends Controller
{
    //安装协议
    public function index(){
        if(!file_exists("../".DS."conf".DS."config.php")){
            Write::config();
            $this->redirect("install/index/index");
        }
        Write::config();
        $url = ".". DS ."Data";
        if(!file_exists($url)){
            mkdir($url);
        }
        if(file_exists($url.DS."install.lock")){
            $this->redirect("index/index/index");
        }
        session("step",2);
        return $this->fetch();
    }

    //环境检测
    public function index2(){
        $url = ".". DS ."Data";
        if(file_exists($url.DS."install.lock")){
            $this->redirect("index/index/index");
        }
        $step = session("step");
        if($step == null){
            return $this -> redirect("index");
        }
        //环境检测
        $this->assign('check_env', check_env());

        //目录文件读写检测
        $this->assign('check_dirfile', check_dirfile());

        //函数及扩展库检测
        $this->assign('check_func_and_ext', check_func_and_ext());

        session("step",3);

        return $this -> fetch();
    }

    public function step2(){
        $check_env = check_env();
        $check_dirfile = check_dirfile();
        $check_func_and_ext = check_func_and_ext();
        foreach ($check_func_and_ext as $value){
            if($value["icon"] == "glyphicon glyphicon-remove"){
                return "ok";
            }
        }
        foreach ($check_dirfile as $value){
            if($value["icon"] == "glyphicon glyphicon-remove"){
                return "ok";
            }
        }
        if($check_env["disk"]["icon"] == "glyphicon glyphicon-remove"){
            return "ok";
        }
        if($check_env["gd"]["icon"] == "glyphicon glyphicon-remove"){
            return "ok";
        }
        if($check_env["os"]["icon"] == "glyphicon glyphicon-remove"){
            return "ok";
        }
        if($check_env["php"]["icon"] == "glyphicon glyphicon-remove"){
            return "ok";
        }
        if($check_env["upload"]["icon"] == "glyphicon glyphicon-remove"){
            return "ok";
        }
        return "no";
    }

    //参数设置
    public function index3(){
        $url = ".". DS ."Data";
        if(file_exists($url.DS."install.lock")){
            $this->redirect("index/index/index");
        }
        $step = session("step");
        if($step == null){
            return $this -> redirect("index");
        }
        if($step == 2){
            return $this -> redirect("index2");
        }
        session("step",4);
        return $this -> fetch();
    }

    //安装数据
    public function step3($db = null){
        $request = Request::instance();
        if($request -> isPost()){
            if (!is_array($db)) {
                return [
                    "errMsg" => "请填写完整的数据库配置"
                ];
            }
            if(empty($db['type']) || empty($db['host'])
                || empty($db['port']) || empty($db['database'])
                || empty($db['username'])){
                return [
                    "errMsg" => "请填写完整的数据库配置"
                ];
            }
            $db["prefix"] = "px_";
            $db_name = $db['database'];
            unset($db['database']); // 防止不存在的数据库导致连接数据库失败
            try{
                $db_instance = Db::connect($db,true);
                $result1 = $db_instance->execute('select version()');
            } catch (ErrorException $errorException){
                return [
                    "errMsg" => "数据库连接失败，请检查数据库配置！",
                ];
            }
            if (!$result1) {
                return [
                    "errMsg" => "数据库连接失败，请检查数据库配置！",
                ];
            }
            //检测是否已存在数据库
            $result2 = $db_instance->execute('SELECT * FROM information_schema.schemata WHERE schema_name like "%db=' . $db_name . '&%" or schema_name="' . $db_name . '"');
            if (!$result2) {
                //创建数据库
                $sql = "CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8";
                $db_instance->execute($sql) || $this->error($db_instance->getError());
            }
            $db["database"] = $db_name;
            $db_instance1 = Db::connect($db,true);
            //创建数据表
            $ip = ObtainIp()["ip"];
            $edition = config('edition.edition');
            $table = install($ip,$edition,$request->root(true));
            foreach ($table as $value){
                $db_instance1 -> execute($value);
            }
            $db_tables = create_tables($db_instance1);
            //创建配置文件
            Write::database($db);
            session("tables",$db_tables);
            return;
        }
        return $this -> redirect("index3");
    }

    //开始安装
    public function index4(){
        $url = ".". DS ."Data";
        if(file_exists($url.DS."install.lock")){
            $this->redirect("index/index/index");
        }
        $step = session("step");
        if($step == null){
            return $this -> redirect("index");
        }
        if($step == 2){
            return $this -> redirect("index2");
        }
        if($step == 3){
            return $this -> redirect("index3");
        }
        $tables = session("tables");
        $this -> assign("tables",$tables);
        Write::install();
        Write::configJson();
        write::util();
        return $this -> fetch();
    }

    public function index5(){
        return $this -> fetch();
    }

}