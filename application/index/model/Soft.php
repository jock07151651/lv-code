<?php

/**

 * Created by PhpStorm.

 * User: 华初

 * Date: 2019/3/1

 * Time: 15:05

 */



namespace app\index\model;





use think\Model;



class Soft extends Model

{

    protected $table = 'e_down';

    //首页编辑推荐

    public static function RecommendSoft()

    {

        return self::order('count_all desc,softid desc')->limit(10)->select();

    }

}