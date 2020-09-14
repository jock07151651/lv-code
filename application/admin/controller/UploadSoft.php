<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11 0011
 * Time: 上午 11:06
 */

namespace app\admin\controller;
use app\admin\model\Ftpupload;
use app\admin\model\Image as ImgModel;
use think\Db;

class UploadSoft extends Common
{
    //文件上传
    public function index()
    {
        if(request()->isAjax()){
            switch (request()->post('type'))
            {
                //上传软件softpic缩略图
                case 1:
                    $res = ImgModel::uploadOne('softpic');
                    if($_FILES){
                        $size = self::getFilesize($_FILES['softpic']['size']);
                    }else{
                        return json(['code'=>0,'info'=>'已取消上传']);
                    }
                    if($res){
                        $msg = ['code'=>1,'info'=>'图片上传成功','url'=>'.'.DS.'temporary'.DS.$res,'size'=>$size];
                        //图片缩略
                        ImgModel::ImgThumb($msg['url']);
                    }else{
                        $msg = ['code'=>0,'info'=>'图片上传失败'];
                    }
                    return json($msg);
                    break;
                //上传水印图
                case 2:

                    $res = ImgModel::uploadOne('water');
                    if($_FILES){
                        $size = self::getFilesize($_FILES['water']['size']);
                    }else{
                        return json(['code'=>0,'info'=>'已取消上传']);
                    }

                    if ($res){
                        $imgSrc = request()->post('imgSrc');
                        if(!$imgSrc){
                            return json(['code'=>0,'info'=>'请先上传缩略图！']);
                        }
                        //图片水印
                        ImgModel::ImgWater($imgSrc,'.'.DS.'temporary'.DS.$res);
                        unlink('.'.DS.'temporary'.DS.$res);
                        $msg = ['code'=>1,'info'=>'图片水印成功','url'=>'.'.DS.'temporary'.DS.$res,'size'=>$size];
                    }else{
                        $msg = ['code'=>0,'info'=>'图片上传失败'];
                    }
                    return json($msg);
                    break;
                //上传软件压缩包
                case 3:
                    if(!$_FILES){
                        return json(['code'=>0,'info'=>'已取消上传']);
                    }
                    $res = Ftpupload::uploadFile($_FILES['downpath']['tmp_name'],'soft',$_FILES['downpath']['name']);
                    if($_FILES){
                        $size = self::getFilesize($_FILES['downpath']['size']);
                    }else{
                        return json(['code'=>0,'info'=>'已取消上传']);
                    }
                    if($res){
                        $msg = ['code'=>1,'info'=>'软件上传成功','url'=>$res,'fileSize'=>$size];
                    }else {
                        $msg = ['code'=>0,'info'=>'软件上传失败'];
                    }
                    return json($msg);
                    break;
            }
        }
    }
    //文件删除
    public function delete()
    {
        if(request()->isAjax()){
            $type = request()->post('type');
            switch ($type)
            {
                case 1:

                        $res = unlink(request()->post('src'));
                        if($res){
                            $msg = ['code'=>1,'info'=>'删除文件成功'];
                        }else{
                            $msg = ['code'=>0,'info'=>'删除文件失败'];
                        }
                        return json($msg);
                    break;
                case 2:
                        $res = unlink(request()->post('src'));
                        if($res){
                            $msg = ['code'=>1,'info'=>'删除文件成功'];
                        }else{
                            $msg = ['code'=>0,'info'=>'删除文件失败'];
                        }
                        return json($msg);
                    break;
                case 3:
                        $res = Ftpupload::FtpDelete(request()->post('src'));
                        if($res){
                            $msg = ['code'=>1,'info'=>'删除文件成功'];
                        }else{
                            $msg = ['code'=>0,'info'=>'删除文件失败'];
                        }
                        return json($msg);
                    break;
            }
        }
    }
    //删除远程ftp文件
    public function EditDelete()
    {
        $data = request()->post();
        if(strstr($data['urls'],'http')){
            $res = Ftpupload::FtpDelete($data['urls']);
            if($data['type']=='demo'){
                Db::table('e_down')->where(['softid'=>$data['softid']])->update(['downpath'=>'','counttime'=>time()]);
            }else{
                Db::table('e_down')->where(['softid'=>$data['softid']])->update(['softpic'=>'','counttime'=>time()]);
            }
        }else{
            $res = unlink('.'.$data['urls']);
        }

        if($res){
            $msg = ['code'=>1,'info'=>'删除成功','type'=>$data['type']];
        }else{
            $msg = ['code'=>0,'info'=>'删除失败'];
        }
        return json($msg);
    }
    //获取文件大小
    private static function getFilesize($num){
        $p = 0;
        $format='bytes';
        if($num>0 && $num<1024){
            $p = 0;
            return number_format($num).' '.$format;
        }
        if($num>=1024 && $num<pow(1024, 2)){
            $p = 1;
            $format = 'KB';
        }
        if ($num>=pow(1024, 2) && $num<pow(1024, 3)) {
            $p = 2;
            $format = 'MB';
        }
        if ($num>=pow(1024, 3) && $num<pow(1024, 4)) {
            $p = 3;
            $format = 'GB';
        }
        if ($num>=pow(1024, 4) && $num<pow(1024, 5)) {
            $p = 3;
            $format = 'TB';
        }
        $num /= pow(1024, $p);
        return number_format($num, 3).' '.$format;
    }
}