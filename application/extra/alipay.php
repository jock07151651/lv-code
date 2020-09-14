<?php
/**
 * Created by PhpStorm.
 * Power By Mikkle
 * Email：776329498@qq.com
 * Date: 2017/8/30
 * Time: 9:59
 */
return [
    "default_options_name"=>"alipay_options",
    /*支付宝wap支付参数*/
    "alipay_options" =>[
        'partner'           =>'2088121358030124',
        'seller_id'         =>'2088121358030124',//和上面一样
        'key'               =>'uy2vimhqei1gef848calo5uql9iidqp7',
        'notify_url'        =>'http://code.662p.com/notify',  //回调地址
        'return_url'        =>'http://code.662p.com/return',  //同步回调
        'sign_type'         =>strtoupper('MD5'),
        'input_charset'     =>strtolower('utf-8'),
        'cacert'            =>'',//需要放在根目录
        'transport'         =>'http',
        'payment_type'      =>'1',
        'service'           =>'create_direct_pay_by_user'
    ],

];