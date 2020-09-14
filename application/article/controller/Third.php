<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/2/28
 * Time: 16:47
 */

namespace app\article\controller;


use think\Controller;
use think\Db;

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
            $latest = Db::table('e_downmember')->order('userid desc')->find();
            $user = [
                'username'=>'q_u'.($latest['userid']+1),
                'sex'=>($user_data['gender']?1:0),
                'qq_openid'=>$openid,
                'registerip'=>$_SERVER['REMOTE_ADDR'],
                'registertime'=>time(),
                'todaydate'=>date('Y-m-d',time()),
                'userpic'=>$user_data['figureurl_2'],
            ];
            $userExist = Db::table('e_downmember')->where(['qq_openid'=>$openid])->find();
            if($userExist){
                setcookie("session_id",session_id(),time()+3600*24*365*10,"/",".662p.com");
                $_SESSION['test'] = "test1";
                setcookie("name", "yangbai", 1000, "/", "662p.com");
                cookie('user_id',$userExist['userid'],30);
                $_SESSION['username'] = $userExist['username'];
                $_SESSION['user_id'] = $userExist['userid'];
                $_SESSION['userpic'] = $userExist['userpic'];
            }else{
                $res = Db::table('e_downmember')->insert($user);
                $userid = Db::table('e_downmember')->getLastInsID();
//                cookie('username',$user['username'],30);
//                cookie('user_id',$userid,30);
                $_SESSION['user_id'] = $userid;
                $_SESSION['username'] = $user['username'];
            }
        }
        $lastUrl = cookie('lastUrl');
        if($lastUrl){
            $this->redirect($lastUrl);
        }else{
            $this->redirect('http://code.662p.com');
        }
    }
    //微博第三方登录回调
    public function WbReturn()
    {
        import('WeiBo.weibo',EXTEND_PATH,'.class.php');
        $key = config('weibo.AppKey');
        $secret = config('weibo.AppSecret');
        $oauth = new \SaeTOAuthV2($key,$secret);
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = config('weibo.returnUrl');
            $token = $oauth->getAccessToken( 'code', $keys );
            $info = file_get_contents("https://api.weibo.com/2/users/show.json?access_token=".$token['access_token']."&uid=".$token['uid']);
            $info = json_decode($info,true);
            if($info){
                $latest = Db::table('e_downmember')->order('userid desc')->find();
                $one = [
                    'username'=>'w_u'.($latest['userid']+1),
                    'real_name'=>$info['name'],
                    'userpic'=>$info['profile_image_url'],
                    'registertime'=>time(),
                    'registerip'=>$_SERVER['REMOTE_ADDR'],
                    'todaydate'=>date('Y-m-d',time()),
                    'checked'=>1,
                    'wb_uid'=>$info['id']
                ];
                $exist = Db::table('e_downmember')->where(['wb_uid'=>$info['id']])->find();
                if(!$exist){

                    $res = Db::table('e_downmember')->insertGetId($one);
                    if($res){
                        if(cookie('type')){
                            //账号绑定
                            $userid = session('user_id');
                            Db::table('e_downmember')->where(['userid'=>$res])->setField('pid',$userid);
                        }else{
                            //登录并且注册账号
                            setcookie("session_id",session_id(),time()+3600*24*365*10,"/",".662p.com");
                            $_SESSION['test'] = "test1";
                            setcookie("name", "yangbai", 1000, "/", "662p.com");
                            cookie('user_id',$res,30);
                            $_SESSION['username'] = $one['username'];
                            $_SESSION['user_id'] = $res;
                            $_SESSION['userpic'] = $one['userpic'];
                        }
                    }
                }else{
                    var_dump('888');die;
                    if(cookie('type')){
                        //账号绑定
                        $userid = session('user_id');
                        Db::table('e_downmember')->where(['userid'=>$exist['userid']])->setField('pid',$userid);
                    }else{
                        setcookie("session_id",session_id(),time()+3600*24*365*10,"/",".662p.com");
                        $_SESSION['test'] = "test1";
                        setcookie("name", "yangbai", 1000, "/", "662p.com");
                        cookie('user_id',$exist['userid'],30);
                        $_SESSION['username'] = $exist['username'];
                        $_SESSION['user_id'] = $exist['userid'];
                        $_SESSION['userpic'] = $one['userpic'];
                    }
                }
            }
        }
        $lastUrl = cookie('lastUrl');
        if($lastUrl){
            $this->redirect($lastUrl);
        }else{
            $this->redirect('http://code.662p.com');
        }
    }
    //微博第三方登录
    public function WbLogin($type='')
    {
        if($type){
            cookie('type','bangding',3600);
            cookie('lastUrl',$_SERVER['HTTP_REFERER']);
        }
        import('WeiBo.weibo',EXTEND_PATH,'.class.php');
        $key = config('weibo.AppKey');
        $secret = config('weibo.AppSecret');
        $oauth = new \SaeTOAuthV2($key,$secret);
        $url = $oauth->getAuthorizeURL(config('weibo.returnUrl'));
        $this->redirect($url);die;
    }
}