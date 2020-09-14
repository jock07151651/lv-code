<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/2/28
 * Time: 11:13
 */

namespace app\index\controller;


use think\Session;
use think\Url;

class Buygroup extends Common
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $userid = Session::get('user_id');
        if(!$userid){
            $this->error('请先登录！','/login');
        }
    }

    //充值页
    public function index()
    {
        if(isset($_SERVER['HTTP_REFERER'])) {
            Session::set('PayLastPage',$_SERVER['HTTP_REFERER']);
        }
        $data = \app\index\model\Buygroup::order('gmoney asc')->select();
        return $this->fetch('user/buygroup',[
            'data'=>$data,
            'lcurrent'=>'1.2',
            'identity'=>'normal'
        ]);
    }
    //支付宝支付
    public function alipayPay($id='')
    {
        if(!$id){
            $this->error('请选择充值金额！');die;
        }
        //根据充值类型id获取充值金额
        $one = db('downbuygroup')->where(['id'=>$id])->find();
        $money = $one['gmoney'];
        if(!$money){
            $this->error('充值失败，请联系管理人员',Url('Buygroup/index'));
        }
        //生成订单
        $order = [
            'userid'=>Session::get('user_id'),
            'username'=>Session::get('username'),
            'gid'=>$id,
            'orderid'=>date('YmdHis',time()).mt_rand(1000000,9999990),
            'paybz'=>'充值类型：'.$one['gname'],
            'posttime'=>date('Y-m-d H:i:s',time()),
            'money'=>$one['gmoney'],
            'type'=>'tenpay',
            'payip'=> $_SERVER['REMOTE_ADDR']
        ];
        $result = db('downpayrecord')->insert($order);
        if(!$result){
            $this->error('生成订单失败，请与客服人员联系');die;
        }
        $alipay = new \app\index\model\Alipay();
        $res = $alipay->wapalipay('cz',[
            'out_trade_no'=>$order['orderid'],
            'subject'=>'充值类型：'.$one['gname'],
            'total_fee'=>$one['gmoney'],
            'show_url'=>'https://coding.imooc.com/class/309.html',
            "body" => '商品',
        ]);
        return $res;
    }


}