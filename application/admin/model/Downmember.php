<?php
namespace app\admin\model;
use think\Model;
class Downmember extends Model
{


protected static function init()
    {
        Downmember::event('before_insert',function($Downmember){
          if($_FILES['userpic']['tmp_name']){
                $file = request()->file('userpic');
                $info = $file->move('./uploads/user/');
                if($info){
                    // $userpic=ROOT_PATH . 'public' . DS . 'uploads'.'/'.$info->getExtension();
                   // $userpic='/public' . DS . 'uploads'.'/'.$info->getSaveName();
                  $userpic='/uploads/user/'.$info->getSaveName();
                  
                    $Downmember['userpic']=$userpic;
                }
            }
      });


        Downmember::event('before_update',function($Downmember){
          if($_FILES['userpic']['tmp_name']){
                $arts=Downmember::find($Downmember->userid);
                $userpicpath=$_SERVER['DOCUMENT_ROOT'].$arts['userpic'];
                if(file_exists($userpicpath)){
                    @unlink($userpicpath);
                }
                $file = request()->file('userpic');
                $info = $file->move('./uploads/user/');
                if($info){
                    // $userpic=ROOT_PATH . 'public' . DS . 'uploads'.'/'.$info->getExtension();
                    $userpic='/uploads/user/'.$info->getSaveName();
                    $Downmember['userpic']=$userpic;
                }

            }
      });

        Downmember::event('before_delete',function($Downmember){
          
                $arts=Downmember::find($Downmember->userid);
                $userpicpath=$_SERVER['DOCUMENT_ROOT'].$arts['userpic'];
                if(file_exists($userpicpath)){
                    @unlink($userpicpath);
                }
        });


    }





}
