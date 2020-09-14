<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\Ftpupload as ftpModel;
class Article extends Model
{
    
	protected static function init()
    {
      	Article::event('before_insert',function($article){
          if($_FILES['thumb']['tmp_name']){

              //ftp上传
              $savePath = FtpModel::uploadFile($_FILES['thumb']['tmp_name'],'img',$_FILES['thumb']['name']);
              if($savePath){
                  $article['thumb']=$savePath;
              }





//                $file = request()->file('thumb');
//                $info = $file->move('./uploads/article/');
//                if($info){
//                  $thumb='/uploads/article/'.$info->getSaveName();
//
//                    $article['thumb']=$thumb;
//                }
            }
      });


      	Article::event('before_update',function($article){
          if($_FILES['thumb']['tmp_name']){
          		$arts=Article::find($article->id);
          		$thumbpath = $arts['thumb'];
              //ftp上传
              $savePath = FtpModel::uploadFile($_FILES['thumb']['tmp_name'],'img',$_FILES['thumb']['name']);
              if($savePath){
                  //删除旧的图片
                  ftpModel::FtpDelete($thumbpath);
                  $article['thumb']=$savePath;
              }


//                if(file_exists($thumbpath)){
//                	@unlink($thumbpath);
//                }
//                $file = request()->file('thumb');
//                $info = $file->move('./uploads/article/');
//                if($info){
//                    // $thumb=ROOT_PATH . 'public' . DS . 'uploads'.'/'.$info->getExtension();
//                    $thumb='/uploads/article/'.$info->getSaveName();
//                    $article['thumb']=$thumb;
//                }

            }
      });

      	Article::event('before_delete',function($article){

          		$arts=Article::find($article->id);
                preg_match_all('/src="(.*)"/iUs', $arts['content'], $out);
                foreach ($out[1] as $key=>$val){
                    ftpModel::FtpDelete($val);
                }
          		$thumbpath=$arts['thumb'];
          		//ftp删除图片
          		ftpModel::FtpDelete($thumbpath);
        });


    }
    






}
