<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12 0012
 * Time: 下午 6:03
 */

namespace app\index\model;


use think\Model;

class PayRecord extends Model
{
    //充值记录表
    protected $table = 'e_downpayrecord';
    public function getPaybzAttr($value)
    {
        return explode('：',$value);
    }
}