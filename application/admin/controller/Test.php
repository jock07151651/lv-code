<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/2/26
 * Time: 19:31
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;

class Test extends Controller
{
    public function index()
    {
        var_dump('888');die;
        $arr = [
            'name'=>'广州必游信息科技有限公司',
            'mam'=>'强哥牛逼',
            'code'=>666
        ];
        var_dump(json_encode($arr,JSON_UNESCAPED_UNICODE));die;
//        $data = Db::table('e_down')->field('softid,softname,softtype,classid')->where(['softtype'=>0])->select();
//        $classid = 11;
//        $softtype = 15;
//        $ids = Db::table('e_downclass')->where(['classid'=>$classid])->find();
//        $ids = explode('|',$ids['sonclass']);
//        $ids[] = $classid;
//        $ids = array_filter($ids);
//        var_dump($data);die;
//        foreach ($data as $key=>$val)
//        {
//            if(in_array($val['classid'],$ids)){
//                $res = Db::table('e_down')->where(['softid'=>$val['softid']])->update(['softtype'=>$softtype]);
//            }
//        }
//        var_dump($res);die;
        return $this->fetch();
    }
    public function ii()
    {
        return $this->fetch();
    }
}