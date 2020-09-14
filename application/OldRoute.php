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
/*
 * index模块
 * */
Route::get('index.html','index/errorpage/index');
Route::rule('/','index/index/index','get|post');
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
Route::rule('reset','index/Login/pwdReset','get|post');
//首页
Route::get('','index/index/index');
//搜索页
Route::get('search/index.php','index/index/search');
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
//软件列表
//Route::get('lists/:id/[:page]','index/Softlist/index');
Route::get('list/:id','index/Softlist/index');

//软件分类
Route::get('/lists/class','index/Softlist/classification');
//软件下载
Route::rule('DownSoft','index/Download/DownSoft','get|post');
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
//文件找不到页面
Route::get('missing','index/NotFound/index');
/*
 * admin 模块
 * */

Route::post('/getDownClass','admin/Down/getDownClass');
//文件上传
Route::rule('/SoftUpload','admin/UploadSoft/index','get|post');
//删除ftp的文件
Route::rule('/DeleteOne','admin/UploadSoft/delete','get|post');



//blog模块
Route::get('blog','blog/Index/index');



//多模块二级域名
Route::domain('www', function () {
    // 动态注册域名的路由规则
    Route::rule('/','article/index/index');
    Route::get('404','article/errorpage/index');
    Route::rule('list/:id', 'article/Lists/index');
    Route::rule('article/:id','article/Article/detail','get|post');
    Route::rule('/login','article/Login/index','get|post');
    Route::rule('/register','article/Login/register','get|post');
    Route::rule('qqLogin','article/Third/qqLogin');
    Route::rule('qq_return','article/Third/ReturnUrl');
});




