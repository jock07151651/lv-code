<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/22 0022
 * Time: 上午 11:39
 */

namespace app\article\model;


use think\Model;

class Cate extends Model
{
    //获取分类下的数据
    public function articleList()
    {
        return $this->hasMany('Article','cateid','id')->order('id desc');
    }
    //获取分类数据
    public static function getList($where=[],$order='id desc',$limit=10)
    {
        return self::with('articleList')->where($where)->order($order)->paginate($limit);
    }
}