<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//全局变量规则
// Route::pattern(['name' => '\w+', 'type'=>'\w+', 'keys'=>'\w+', 'token' => '\w+','order'=>'[A-Za-z]+', 'id' => '\d+', 'uid' => '\d+',  'page' => '\d+', 'year' => '\d{4}', 'month' => '\d{2}']);

//后台

Route::rule('/article/examine/:id','admin/article/examine','get|post');
/*
 * index模块
 * */
Route::get('index.html','index/errorpage/index');
Route::rule('/','index/Index/index','get|post');
//注册页
Route::rule('register','index/Login/register','get|post');
//登录页
Route::rule('login','index/Login/index','get|post');
//测试页
Route::rule('test','index/Download/test','get|post');
//检测用户是否登录
Route::rule('isLogin','index/Login/IsLogin','get|post');
//注销登录
Route::rule('logout','index/Login/logout','get');
//密码重置页
Route::rule('reset/:id','index/Login/pwdReset','get|post');
//首页
Route::get('','index/index/index');
//搜索页
Route::rule('search','index/index/search','get|post');
//源码下载详情页
Route::get('view/:id','index/download/detail');
//充值页面
Route::rule('buygroup','index/buygroup/index');
//充值
Route::get('alipay','index/buygroup/alipayPay');
//支付宝支付成功回调地址
Route::rule('notify','index/Alipay/Notifyurl','get|post');
//支付宝支付成功跳转地址
Route::get('return','index/Alipay/ReturnUrl');
//QQ登录
Route::get('qqLogin','index/Third/qqLogin');
//QQ登录回调url
Route::any('qq_return','index/Third/ReturnUrl');
//发送QQ邮箱验证码的地址
Route::rule('sendCode','index/Login/SendEmailCode','get|post');
//发送手机验证码
Route::rule('/rcode','index/Login/resetCode','get|post');
//软件列表
//Route::get('lists/:id/[:page]','index/Softlist/index');
Route::get('list/:id','index/Softlist/index');

//软件分类
Route::get('/lists/class','index/Softlist/classification');
//软件下载
Route::rule('DownSoft','index/Download/DownSoft','get|post');
//发表评论
Route::rule('/comment/publish','index/Comment/index','get|post');
//回复评论
Route::rule('/comment/reply','index/Comment/reply','get|post');
//举报评论
Route::rule('/comment/report/:id','index/Comment/report','get|post');
//评论点赞
Route::rule('/fabulous/:id','index/Comment/fabulous','get|post');
//收藏夹
Route::get('fava','index/Collect/index');
//软件收藏
Route::post('collect','index/Softlist/collection');

//软件删除
Route::post('favaDelete','index/Collect/delete');
//软件投稿
Route::rule('AddSoft/index.php','index/AddSoft/AddSoft','get|post');
//根据字母搜索
Route::rule('zm/:letter','index/SearchByLetter/index','get|post');
//会员中心
Route::rule('cp','index/User/index','get|post');
//消费记录
Route::rule('buybak','index/User/payrecord','get|post');
//用户点击演示地址，检测用户是否登录
Route::rule('clogin','index/Login/chekIsLogin','get|post');
//注册时候检测用户名称是否重复
Route::rule('/checkName','index/Login/checkName','get|post');
//发送信息
Route::rule('/e_check','index/Login/SendEmailCode','get|post');
//文件找不到页面
Route::get('missing','index/NotFound/index');
//上传源码
Route::rule('/code/upload','index/Upload/index','get|post');
/*
 * admin 模块
 * */

Route::post('/getDownClass','admin/Down/getDownClass');
Route::post('/img/getDownClass','admin/Img/getDownClass');
//文件上传
Route::rule('/SoftUpload','admin/UploadSoft/index','get|post');
//删除本地的文件
Route::rule('/DeleteOne','admin/UploadSoft/delete','get|post');

//删除ftp的文件
Route::rule('/EditDelete','admin/UploadSoft/EditDelete','get|post');

//blog模块
Route::get('blog','blog/Index/index');



//多模块二级域名

Route::domain('www', function () {
//    var_dump('888');die;
    // 动态注册域名的路由规则
    Route::rule('/','article/index/index');
    Route::get('404','article/errorpage/index');
    Route::rule('list/:id', 'article/Lists/index');
    Route::rule('article/:id','article/Article/detail','get|post');
    Route::rule('/login','article/Login/index','get|post');
    Route::rule('/register','article/Login/register','get|post');
    Route::rule('qqLogin','article/Third/qqLogin');
    Route::rule('qq_return','article/Third/ReturnUrl');
    Route::rule('wb_return','article/Third/WbReturn','get|post');
    Route::rule('wbLogin','article/Third/WbLogin');
    Route::rule('d_weibo','article/Third/d_weibo','get|post');
    //注销登录
    Route::rule('logout','article/Login/logout','get');
});

//图片素材
Route::domain('sucai',function (){
    Route::rule('/list/[:id]','img/Index/index','get|post');
    Route::rule('/list','img/Index/index','get|post');
    Route::rule('/view/:id','img/Download/detail','get|post');
    //图片下载
    Route::rule('/DownSoft','img/Download/DownSoft','get|post');
});

//会员中心
Route::domain('my',function(){
    //个人首页
    // Route::rule('/test/index/:id','index/Member/index','get|post');

    Route::rule('/','index/buygroup/index','get|post');
    //访问个人首页
    // Route::rule('/[:id]','index/Member/index','get|post');
    // Route::rule('/:nickname','index/Member/index','get|post');
    // Route::rule('/:id/blog','index/Member/blog','get|post');
    // Route::rule('/:id/code','index/Member/code','get|post');
    // Route::rule('/:id/sucai','index/Member/sucai','get|post');
    Route::rule('/[:id]','my/index/index','get|post');
    Route::rule('/:nickname','my/index/index','get|post');
    Route::rule('/:id/blog','my/index/blog','get|post');
    Route::rule('/:id/code','my/index/code','get|post');
    Route::rule('/:id/sucai','my/index/sucai','get|post');

    //编辑器上传图片
    Route::rule('/upload/markdown','index/Markdown/upload','get|post');
    //编写文章
    Route::rule('/mdeditor','index/Markdown/index','get|post');
    Route::rule('/blog','index/Markdown/blog','get|post');   //会员文章管理
    //文章发布
    Route::rule('/mdeditor/publish','index/Markdown/publish','get|post');
    //充值页面
    Route::rule('buygroup','index/buygroup/index');
    //个人资料
    Route::rule('/set','index/User/set','get|post');
//    //文章管理
//    Route::rule('/set','index/article/userUpload','get|post');
    //通知和信息
    Route::rule('/notice','index/User/notice','get|post');
    //我的设置
    Route::rule('/myset','index/User/myset','get|post');
    //申请认证
    Route::rule('/apply','index/User/apply',"get|post");
    //重置密码
    Route::rule('/setpwd','index/User/setpwd',"get|post");
    //设置email
    Route::rule('/setemail','index/User/setEmail',"get|post");
    //修改手机
    Route::rule('/setmobile','index/User/setMobile',"get|post");
    //设置头像
    Route::rule('/setavator','index/User/setAvator',"get|post");
    //注销登录
    Route::rule('logout','index/Login/logout','get');
   // Route::rule('login','index/Login/index','get');

    //发送手机验证码（重置密码）
    Route::rule('/pwdCode','index/User/sendCode','get|post');
    //充值页面
    Route::rule('buygroup','index/buygroup/index');
    //充值
    Route::get('alipay','index/buygroup/alipayPay');
    //消费记录
    Route::rule('buybak','index/User/payrecord','get|post');
    //下载记录
    Route::rule('/downrecord','index/User/downRecord','get|post');
    //收藏夹
    Route::get('fava','index/Collect/index');
});



