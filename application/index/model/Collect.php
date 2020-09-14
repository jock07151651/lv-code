<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7 0007
 * Time: 上午 11:48
 */

namespace app\index\model;


use think\Model;

class Collect extends Model
{
    protected $table = 'e_downfava';
    public function soft()
    {
        return $this->hasOne('Soft','softid','softid');
    }
    public static function CollectList($userid='',$listLow=9)
    {
        return self::with(['soft'])->where(['e_downfava.userid'=>$userid])->order('favaid desc')->paginate($listLow);
    }
    //统计收藏条数
    public static function CollectCount($userid)
    {
        return self::where(['userid'=>$userid])->count();
    }
}