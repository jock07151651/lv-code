<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7 0007
 * Time: 下午 5:07
 */

namespace app\index\controller;


use app\index\model\Down;
use app\index\model\SoftCate;

class SearchByLetter extends Common
{
    //根据字母搜索
    public function index($letter='A')
    {
        //获取最新下载分类
        $LatestCate = SoftCate::LatestCate();
        //获取热门下载分类
        $HotCate = SoftCate::HotCate();
        //获取列表信息
        $list = Down::with(['CodeClass'])->where('zm','=',$letter)->paginate(10);
        //获取分类信息
        $data = SoftCate::where(['bclassid'=>0])->field('classkey,classintro,classid,classname')->find();
        //获取子级类目
        $childCate = SoftCate::NavCate();
        return $this->fetch('letter/index',[
            'data'=>$data,
            'soft'=>$list,
            'childCate'=>$childCate,
            'latest'=>$LatestCate,
            'hotCate'=>$HotCate,
            'letter'=>$letter
        ]);
    }
}