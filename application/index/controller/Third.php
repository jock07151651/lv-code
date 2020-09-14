<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/2/28
 * Time: 16:47
 */

namespace app\index\controller;


use think\Controller;

class Third extends Controller
{
    public function qqLogin()
    {
        import('QQLogin.API.qqConnectAPI',EXTEND_PATH,'.php');
        $qc = new \QC();
        $qc->qq_login();die;
    }
    //QQ登录成功后回调页面
    public function ReturnUrl()
    {
        import('QQLogin.API.qqConnectAPI',EXTEND_PATH,'.php');
        $qc = new \QC();
        $access_token = $qc->qq_callback();//access_token
        $openid = $qc->get_openid();//openid
        $qc = new \QC($access_token,$openid);
        $user_data = $qc->get_user_info();  //get_user_info()为获得该用户的信息，
        if(!empty($user_data)){
            $save = [
                'openid'=>$openid,
            ];
        }
        $this->redirect('http://code.662p.com');
//        var_dump($user_data);die;
    }
}