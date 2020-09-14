<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5 0005
 * Time: 下午 3:46
 */

namespace app\article\model;


use think\Model;
use think\queue\job\Topthink;

class Common extends Model
{
    public static function substrField($data,$field,$num)
    {
        if(is_object($data)){
            $data = ToArray($data);
        }
        if (count($data) == count($data, 1)) {
            //一维数组
            $data[$field] = mb_substr($data[$field],1,$num,'UTF-8');
        } else {
            //二维数组
            foreach ($data as $key=>$val){
                $data[$key][$field] = mb_substr($val[$field],1,$num,'UTF-8');
            }
        }
        return $data;
    }
}