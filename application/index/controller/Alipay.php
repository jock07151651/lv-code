<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6 0006
 * Time: 下午 2:23
 */

namespace app\index\controller;


use think\Controller;
use think\Db;
use think\Session;

class Alipay extends Controller
{
    public function Notifyurl()
    {
        import('walipay.AlipayNotify',EXTEND_PATH,'.php');
        $alipay_config = config('alipay.alipay_options');
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $result = $_POST;
            //未经过处理时需处理
            $save['is_pay'] = 1;
            $save['pay_time'] = time();
            #查询该订单
            $order = db('downpayrecord')->where(['orderid'=>$result['out_trade_no']])->find();
            if (!empty($order))
            {
                $res = db('downpayrecord')->where('orderid="'.$result['out_trade_no'].'"')->update($save);
                if($res){
                    //订单处理完成后，增加会员点数
                    $buygroup = db('downbuygroup')->where(['id'=>$order['gid']])->find();
                    if($buygroup){
                        //给会员增加点数
                        db('downmember')->where(['userid'=>$order['userid']])->setInc('downfen',$buygroup['gfen']);
                        $user = db('downmember')->where(['userid'=>$order['userid']])->find();
                        if($user['groupid']=='4'){
                            //升级会员升级为其他会员
                            db('downmember')->where(['userid'=>$order['userid']])->update(['groupid'=>$buygroup['ggroupid'],'update_time'=>time()]);
                        }else if($user['groupid']==$buygroup['ggroupid']){
                            //重置的会员等级和当前的会员等级一样不做修改
                        }else if(($user['groupid']<$buygroup['ggroupid'])&&($user['groupid']!=='4')){
                            db('downmember')->where(['userid'=>$order['userid']])->update(['groupid'=>$buygroup['ggroupid'],'update_time'=>time()]);
                        }else{
                            //高级会员充值低点金额不做修改
                        }
                    }
                }
            }
            // 验证成功 修改数据库的订单状态等 $result['out_trade_no']为订单id
            echo "success";        //请不要修改或删除
        } else {
            //验证失败
            echo "fail";
        }
    }
    //支付宝支付成功后跳转的页面
    public function ReturnUrl()
    {

        $page = Session::get('PayLastPage');
        if(!$page){
            $this->redirect('http://code.662p.com');
        }
        $this->redirect($page);
    }
}