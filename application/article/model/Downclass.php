<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/30 0030
 * Time: 下午 4:30
 */

namespace app\article\model;


use think\Model;

class Downclass extends Model
{
    public static function getChildIDs($id)
    {
        $id = '|'.$id.'|';
        $ids = self::where(['featherclass'=>['like',"%$id%"]])->column('classid');
        foreach ($ids as $key=>$val){
            $search = '|'.$val.'|';
            $idss = self::where(['featherclass'=>['like',"%$search%"]])->column('classid');
            if(!empty($idss)){
                $ids = array_merge($ids,$idss);
            }
        }
        $id = intval(trim($id,'|'));
        $ids[] = $id;
        return $ids;
    }
}