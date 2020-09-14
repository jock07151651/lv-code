<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7 0007
 * Time: 下午 2:55
 */

namespace app\index\controller;


use app\index\model\SoftCate;
use think\Controller;
use think\Db;

class AddSoft extends Controller
{
    public function AddSoft()
    {
        $class = self::getSoftClass();
        //top导航栏
        $nav = SoftCate::NavCate();
        return $this->fetch('addsoft/add',[
            'nav'=>$nav,
            'class'=>$class
        ]);
    }
    //递归获取软件分类信息
    private static function getSoftClass($pid=0,$space=1)
    {
        static $arr = [];
        $data = Db::table('e_downclass')->where(['bclassid'=>$pid])->select();

        foreach ($data as $key=>$val){
            if($val['bclassid']==$pid){
                $arr[] = str_repeat('|---',$space).$val['classname'];
                self::getSoftClass($val['classid'],($space+1));
            }
        }
        return $arr;
    }
}