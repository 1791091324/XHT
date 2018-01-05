<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/28 0028
 * Time: 下午 5:59
 */

namespace app\admin\model;


use think\Model;

//检测安装和下载
class Detection extends Model
{
    public static function findDetection($status = 0,$listRow = 13){
        return self::where(array("status" => $status))
            -> order("create_time desc")
            -> paginate($listRow);
    }
}