<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\Ftpupload as FtpModel;
use app\admin\model\Image as ImageModel;
class Down extends Model
{
    
	protected static function init()
    {
      	Down::event('before_insert',function($down){
          if(isset($_FILES['softpic'])){
              //文字水印
              if(request()->post('waterword')){
                  //gif图片不进行水印
                  if(self::getExt(basename(request()->post('thumbImg')))!=='gif'){
                      Image::LetterWater(request()->post('waterword'),request()->post('thumbImg'));
                  }
              }
              if(self::getExt(basename(request()->post('thumbImg')))!=='gif'){
                  Image::ImgWater(request()->post('thumbImg'),'./watermark.png');
              }
              //ftp上传
              $savePath = FtpModel::uploadFile(request()->post('thumbImg'),'img');
                if($savePath){
                    unlink(request()->post('thumbImg'));
                    $down['softpic'] = $savePath;
                }
          }else{
//              外链图片
              $down['softpic'] = request()->post('thumbImg');
          }
          if(request()->post('softSrc')){
                $savePath = request()->post('softSrc');
                //文件上传
                if($savePath){
                    $down['downpath'] = $savePath;
                    $down['filesize'] = request()->post('size');
                }
          }else{
              //外链软件
              $down['downpath'] = request()->post('softSrc');
          }
      });
      	Down::event('before_update',function($down){
//      	    修改缩略图片
          if(request()->post('updatePicTips')){
                //文字水印
                if(request()->post('waterword')){
                    if(self::getExt(basename(request()->post('thumbImg')))!=='gif'){
                        Image::LetterWater(request()->post('waterword'),request()->post('thumbImg'));
                    }
                }
              if(self::getExt(basename(request()->post('thumbImg')))!=='gif'){
                  Image::ImgWater(request()->post('thumbImg'),'./watermark.png');
              }
                //ftp上传
                $savePath = FtpModel::uploadFile(request()->post('thumbImg'),'img');
                if($savePath){
                    unlink(request()->post('thumbImg'));
                    $down['softpic'] = $savePath;
                }
            }else{
//              外链图片
              $down['softpic'] = request()->post('thumbImg');
          }
            if(request()->post('updateSoftTips')){
                $savePath = request()->post('softSrc');
                //文件上传
                if($savePath){
                    $down['downpath'] = $savePath;
                }
            }else{
                $down['downpath'] = request()->post('softSrc');
            }

      });

      	Down::event('before_delete',function($down){
          
          		$arts=Down::find($down->softid);
          		$thumbpath=$_SERVER['DOCUMENT_ROOT'].$arts['softpic'];
                if(file_exists($thumbpath)){
                	@unlink($thumbpath);
                }
        });


    }
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
    //获取文件扩展名
    public static function getExt($filename)
    {
        $arr = explode('.',$filename);
        return array_pop($arr);
    }




}
