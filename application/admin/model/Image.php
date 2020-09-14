<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/3/10
 * Time: 13:42
 */

namespace app\admin\model;


use think\Model;

class Image extends Model
{
    //上传单图
    public static function uploadOne($field='')
    {
        // 获取表单上传文件 例如上传了001.jpg
        if(!$field){
            return false;
        }
        $file = request()->file($field);
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'temporary');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                return $info->getSaveName();
            }else{
                // 上传失败获取错误信息
                cookie('uploadError',$file->getError(),10);
                return false;
            }
        }
        return false;
    }
    //多图片上传
    public static function uploadMore($name){
        // 获取表单上传文件
        $files = request()->file($name);
        $data = [];
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'temporary');
            if($info){
                // 成功上传后 获取上传信息
                $data[] = '.'.DS.'temporary'.DS.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                return ['code'=>0,'info'=>$file->getError()];
            }
        }
        return $data;
    }
    //图片缩略
    public static function ImgThumb($imgSrc)
    {
        $ext = self::getExt($imgSrc);
        if($ext=='gif'){
            return true;
        }
        $image = \think\Image::open($imgSrc);
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
        $width = $image->width();
        $height = $image->height();
        $res = $image->thumb($width*0.80, $height*0.80)->save($imgSrc);
    }
    //图片水印
    public static function ImgWater($imgSrc,$waterImg)
    {
        $image = \think\Image::open($imgSrc);
        // 给原图左上角添加水印并保存water_image.png
        return $image->water($waterImg,\think\Image::WATER_CENTER)->save($imgSrc);
    }
    //文字水印
    public static function LetterWater($word,$imgSrc)
    {
        $image = \think\Image::open($imgSrc);
        // 给原图左上角添加水印并保存water_image.png
        $image->text($word,'./ttf/zhengkai.TTF',16,'#ffffff')->save($imgSrc);
    }
    //获取文件扩展名
    public static function getExt($filename)
    {
        $filename = basename($filename);
        $arr = explode('.',$filename);
        return array_pop($arr);
    }
}