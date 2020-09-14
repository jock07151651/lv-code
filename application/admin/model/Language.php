<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/2/27
 * Time: 16:19
 */

namespace app\admin\model;


use think\Model;

class Language extends Model
{
    public static function language()
    {
        return self::select();
    }
}