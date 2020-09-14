<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/27 0027
 * Time: 下午 4:17
 */

namespace app\article\model;


use think\Model;

class Down extends Model
{
    /**
     * @return array
     */
    public static function getList($where=[],$order='id desc',$limit=10)
    {
        $where['checked'] = 1;
        return self::where($where)->order($order)->limit($limit)->select();
    }
    //根据分类id获取软件信息
    public static function getSoftByClassID($id)
    {
        if(is_array($id)){
            $arr = [];
            foreach ($id as $key=>$val){
                $ids = Downclass::getChildIDs($val);
                foreach ($ids as $k=>$v){
                    $arr[] = $v;
                }
            }
            $data = self::where(['classid'=>['in',$arr],'checked'=>1])->order('softid desc')->limit(10)->select();
        }else{
            $ids = Downclass::getChildIDs($id);
            $data = self::where(['classid'=>['in',$ids],'checked'=>1])->order('softid desc')->limit(10)->select();
        }
        return $data;
    }

}