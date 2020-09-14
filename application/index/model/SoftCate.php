<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/3/1
 * Time: 11:13
 */

namespace app\index\model;


use think\Model;

class SoftCate extends Model
{
    protected $table = 'e_downclass';
    //获取最新下载分类
    public static function LatestCate()
    {
        return db('down')->order('classid desc')->limit(10)->select();
    }
    //获取热门下载分类
    public static function HotCate()
    {
        return db('down')->order('count_all desc,classid desc')->limit(10)->select();
    }
    //获取一级分类信息
    public static function FirstClass()
    {
        return self::where(['bclassid'=>0])->select();
    }
    //获取子级分类（获取pid的第一级子类目）
    public static function childCate($pid)
    {
        $pid = self::FirstClassid($pid);
        //获取子类目
        $data = self::where(['bclassid'=>$pid])->order('classid asc')->select();
        return $data;
    }
    //获取第一级classid
    private static function FirstClassid($pid)
    {
        static $parent;
        $parent = self::where(['classid'=>$pid])->find();
        $parent = json_decode(json_encode($parent),true);
        if($parent['bclassid']!==0){
            self::FirstClassid($parent['bclassid']);
        }
        return $parent['classid'];
    }
    //获取一级类目
    public static function NavCate()
    {
        return self::where(['bclassid'=>0])->order('classid asc')->select();
    }
    //下载分类，获取子级类目（无限获取子级类目）
    public static function DownCate($pid='')
    {
        $data = self::getChild($pid);
        return $data;
    }
    //递归获取子级类目
    private static function getChild($pid)
    {
        static $cate = [];
        $data = self::where(['bclassid'=>$pid])->field('classid,classname')->select();
        if(!empty($data)){
            $data = json_decode(json_encode($data),true);
            foreach ($data as $key=>$val){
                $cate[] = $val;
                self::getChild($val['classid']);
            }
        }
        return $cate;
    }
    //根据bclassid获取自己分类
    public static function getClassByPid($bclassid)
    {
        return self::where(['bclassid'=>$bclassid])->select();
    }
}