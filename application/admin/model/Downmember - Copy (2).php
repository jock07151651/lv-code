<?php
namespace app\admin\model;
use think\Model;
class Downmember extends Model
{


    // protected static function init()
    // {
    //     Downmember::event('before_insert',function($userid){
    //       if($_FILES['userpic']['tmp_name']){
    //             $file = request()->file('userpic');
    //             $info = $file->move('./uploads/user/');
    //             if($info){
    //               $userpic='/uploads/user/'.$info->getSaveName();
    //                 $downmember['userpic']=$userpic;
    //             }
    //         }
    //   });
    //     Downmember::event('before_update',function($userid){
    //       if($_FILES['userpic']['tmp_name']){
    //             $arts=Downmember::find($member->userid);
    //             $thumbpath=$_SERVER['DOCUMENT_ROOT'].$arts['userpic'];
    //             if(file_exists($thumbpath)){
    //                 @unlink($thumbpath);
    //             }
    //             $file = request()->file('userpic');
    //             $info = $file->move('./uploads/user/');
    //             if($info){
    //                 $userpic='/uploads/user/'.$info->getSaveName();
    //                 $downmember['userpic']=$userpic;
    //             }

    //         }
    //   });

    //     Downmember::event('before_delete',function($userid){
          
    //             $arts=Downmember::find($downmember->softid);
    //             $thumbpath=$_SERVER['DOCUMENT_ROOT'].$arts['userpic'];
    //             if(file_exists($thumbpath)){
    //                 @unlink($thumbpath);
    //             }
    //     });


    // }


//---------------------------

   public function addmember($data){
    if(empty($data) || !is_array($data)){
        return false;
    }
    if($data['password']){
        $data['password']=md5($data['password']);
    }
    $adminData=array();
    $adminData['username']=$data['username'];
    $adminData['password']=$data['password'];
    $adminData['groupid']=$data['groupid'];
     return $this->save($adminData);



}

   public function getmember(){
    return $this::paginate(10,false,[
        'type'=>'boot',
        'var_page' => 'page',
        ]);
   }

   public function saveamember($data,$members){
        if(!$data['username']){
            return 2;//管理员用户名为空
        }
        if(!$data['password']){
            $data['password']=$members['password'];
        }else{
            $data['password']=md5($data['password']);
        }
        db('downmember')->where(array('userid'=>$data['userid']))->update(['groupid'=>$data['groupid']]);
        return $this::update(['username'=>$data['username'],'password'=>$data['password']],['userid'=>$data['userid']],['groupid'=>$data['groupid']]);
    
    }

    public function delmember($userid){
        if($this::destroy($userid)){
            return 1;
        }else{
            return 2;
        }
    }

    public function login($data){
        $admin=Admin::getByName($data['name']);
        if($admin){
            if($admin['password']==md5($data['password'])){
                session('id', $admin['id']);
                session('name', $admin['name']);
                return 2; //登录密码正确的情况
            }else{
                return 3; //登录密码错误
            }
        }else{
            return 1; //用户不存在的情况
        }

    }



}
