<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6 0006
 * Time: 上午 10:21
 */

namespace app\index\model;


use think\Model;

class Downurl extends Model
{
    //获取所有下载路径
    public static function getDownPath($where=[])
    {
        return self::where($where)->order(['urlid asc'])->select();
    }
}