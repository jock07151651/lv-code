<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2019/3/4 0004

 * Time: 下午 4:57

 */



namespace app\article\controller;



use app\index\model\Downmember as UserModel;

use app\index\model\SoftCate;

use app\index\validate\Register;

use think\captcha\Captcha;

use think\Controller;

use think\Cookie;

use think\Db;

use think\Session;



class Login extends Controller

{

    //登录界面

    public function index()
    {
        if(request()->isAjax()){

            $data = request()->post();

            //检测用户是否存在

            $exist = UserModel::UserIsExist($data['username']);

            if(!$exist){

                return json(['code'=>0,'info'=>'您输入的账号不存在，请重新输入！']);

            }

            //检测密码是否正确

            $pwdIsTrue = UserModel::PasswordIsTrue($data['username'],$data['password']);

            if(!$pwdIsTrue){

                return json(['code'=>0,'info'=>'用户密码不正确，请重新输入！']);

            }

            //记录用户信息

            $user = ToArray($pwdIsTrue);

            Session::set('username',$user['username']);

            Session::set('user_id',$user['userid']);

            Session::set('userpic',$user['userpic']);

            $lastUrl = Session::get('lastUrl');

            if($lastUrl=='http://code.662p.com/logout'||$lastUrl=='http://code.662p.com/login'||$lastUrl=='http://code.662p.com/register/'){

                $lastUrl = '/';

            }

            return json(['code'=>1,'info'=>'登陆成功','lastUrl'=>$lastUrl]);

        }

        if(isset($_SERVER['HTTP_REFERER'])){

            $lastUrl = $_SERVER['HTTP_REFERER'];

            Session::set('lastUrl',$lastUrl);

        }

        //top导航栏

        $nav = SoftCate::NavCate();

        return $this->fetch('',[

            'nav'=>$nav

        ]);

    }

    //注销登录

    public function logout()

    {

        Session::clear();

        $this->success('注销成功','/login');

    }

    //注册界面

    public function register()

    {

        import('PHPMailer.Email',EXTEND_PATH,'.php');

        $email = new \Email();
        
//        $res = $email->send();

        //top导航栏

        $nav = SoftCate::NavCate();

        //表单提交

        if(request()->isPost()){
            
            $data = request()->post();
            
            $RegisterValidate = new Register();

            if(!$RegisterValidate->check($data)){

                $res = $RegisterValidate->getError();

                return json(['code'=>0,'info'=>$res.Cookie::get('code')]);

            }

            $exist = db('downmember')->where(['email'=>trim($data['email'])])->find();

            if($exist){

                return json(['code'=>0,'info'=>'该邮箱已注册！']);

            }
            $data['registertime'] =time();
            $user = new UserModel($data);

            $res = $user->allowField(true)->save();

            if($res){

                $msg = ['code'=>1,'info'=>'注册成功'];

            }else{

                $msg = ['code'=>0,'info'=>'注册失败，请与客服人员联系！'];

            }

            return json($msg);

        }

        return $this->fetch('',[

            'nav'=>$nav

        ]);

    }

    //密码重置

    public function pwdReset()

    {

        if(request()->isAjax()){

            $data = request()->post();

            $emailExist = UserModel::EmailIsExist($data['email']);

            if(!$emailExist){

                return json(['code'=>0,'info'=>'邮箱不存在，您可以先注册会员哦。']);

            }

            //数据验证

            $validate = new Register();

            $res = $validate->scene('edit')->check($data);

            if(!$res){

                return json(['code'=>0,'info'=>$validate->getError()]);

            }

            $user = new UserModel();

            $res = $user->allowField(true)->save($data,['email'=>$data['email']]);

            if(!$res){

                $msg = ['code'=>0,'info'=>'密码重置失败，请与客服人员联系！'];

            }else{

                $msg = ['code'=>1,'info'=>'密码重置成功'];

            }

            return json($msg);

        }

        //top导航栏

        $nav = SoftCate::NavCate();

        return $this->fetch('reset',[

            'nav'=>$nav

        ]);

    }

    //发送QQ邮箱验证码

    public function SendEmailCode()

    {

        if(request()->isAjax()){

            $email  = request()->post('email');

            $type   = request()->post('type');

            if($type!=='pwdReset'){

                $exist  = db('downmember')->where(['email'=>trim($email)])->find();

                if($exist){

                    return json(['code'=>0,'info'=>'该邮箱已注册！']);

                }

                $title  = "源码天堂会员注册";

                $head   = "【会员注册】";

            }else{

                $title  = "【源码天堂密码重置】";

                $head   = "【密码重置】";

            }

            if(!$email){

                return json(['code'=>0,'info'=>'发送邮箱验证码失败，请与客服人员联系！']);

            }

            import('PHPMailer.Email',EXTEND_PATH,'.php');

            $SendApi = new \Email();

            $code = mt_rand(100000,999999);

            $res = $SendApi->send($email,$title,"$head 您的验证码为：$code,验证码有效期为30分钟哦！");

            if($res){

                Cookie::set('code',$code,1800);

                $msg = ['code'=>1,'info'=>'验证码已下发至您的邮箱了，请您注意查看！'];

            }else{

                $msg = ['code'=>0,'info'=>'验证码发送失败，请与客服人员联系！'];

            }

            return json($msg);

        }

    }

    //验证码

    public function captcha()

    {

        $config =    [

            // 验证码字体大小

            'fontSize'    =>    72,

            // 验证码位数

            'length'      =>    4,

            // 关闭验证码杂点

            'useNoise'    =>    false,

        ];

        $captcha = new Captcha($config);

        return $captcha->entry();

    }

    //检测是否登录

    public function IsLogin()

    {

        if(request()->isAjax()){

            if(Session::get('user_id')){

                $msg = ['code'=>1,'info'=>'用户已登录'];

            }else{

                $msg = ['code'=>0,'info'=>'请您先登录！'];

            }

            return json($msg);

        }

    }

}