<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/2/27
 * Time: 13:53
 */

namespace app\img\model;


use think\Model;

class Img extends Model
{
    public function CodeClass()
    {
        return $this->hasOne('Imgclass','classid','classid')->field('classname,classid,bclassid');
    }
    public static function CodeUpdate()
    {
        //获取最新13条数据
        $data = self::with('CodeClass')
            ->order('softid desc')
            ->field('softname,softtime,homepage')
            ->limit(13)
            ->select();
        return $data;
    }
    //获取多条数据
    public static function getList($where,$order,$limit)
    {
        return self::where($where)->order($order)->limit($limit)->select();
    }
    //获取数据
    public static function getOne($where=[],$order)
    {
        return self::where($where)->order($order)->find();
    }
}