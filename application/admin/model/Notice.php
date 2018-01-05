<?php
/**
 * Created by 平行软件科技有限公司.
 * User: 平行软件
 * Date: 2017/12/25 0025
 * Time: 上午 10:56
 */

namespace app\admin\model;


use app\admin\service\Token;
use think\Model;

class Notice extends Model
{
    //查询公告信息
    public static function findNoticeByUid($listRow = 13){
        return self::where(array("uid" => Token::getUid()))
            -> order("create_time desc")
            -> paginate($listRow);
    }
}