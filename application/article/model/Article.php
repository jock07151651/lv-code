<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/22 0022
 * Time: 上午 10:51
 */

namespace app\article\model;


use think\Model;

class Article extends Model
{

    //获取数据
    public static function getList($where=[],$order='id desc',$limit='10')
    {
        return self::with('Cate')->where($where)->order($order)->limit($limit)->select();
    }
    public function Cate()
    {
        return $this->hasOne('Cate','id','cateid');
    }
    //字数截取
    public static function SubstrField($data,$field)
    {
        if(is_object($data)){
            $data = ToArray($data);
        }
        foreach ($data as $key=>$val) {
        }
        return $data;
    }
}