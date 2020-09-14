<?php
namespace app\admin\model;
use think\Model;
class Downclass extends Model
{
    protected static function init()
    {
      // Cate::event('before_insert',function($cate){
      //     dump($cate->pid); die;
      // });

      Cate::event('before_delete',function(){
          dump(111); die;
          return false;
      });
    }

    public function catetree(){
        $cateres=$this->order('myorder asc')->select();
        return $this->sort($cateres);
    }
    public function language()
    {
        return db('language')->select();
    }

    public function sort($data,$bclassid=0,$level=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['bclassid']==$bclassid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sort($data,$v['classid'],$level+1);
            }
        }
        return $arr;
    }

    public function getchilrenid($classid){
        $cateres=$this->select();
        return $this->_getchilrenid($cateres,$classid);
    }

    public function _getchilrenid($downclass,$classid){
        static $arr=array();
        foreach ($cateres as $k => $v) {
            if($v['bclassid'] == $classid){
                $arr[]=$v['classid'];
                $this->_getchilrenid($cateres,$v['classid']);
            }
        }

        return $arr;
    }

    






}
