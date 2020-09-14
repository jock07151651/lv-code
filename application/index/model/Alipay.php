<?php
namespace app\index\Model;
use think\Model;
use think\Session;
class Alipay extends Model{
    /**
     * 手机端请求支付
     * @return [type] [description]
     */
    public function wapalipay($type='',$params=[]){
        // if(!$type){
        //     throw new \think\exception\HttpException(403, '非法请求');
        // }
        $alipay_config = config('alipay.alipay_options');
        // var_dump($type);exit;
        $type = 'cz';
        switch ($type){
            case 'cz':
                Session::set('dawn_alipay_pay_action','cz');
                break;
            case 'hk':
                Session::set('dawn_alipay_pay_action','hk');
                break;
        }
 
        /************************************************************/
 
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "notify_url" => $alipay_config['notify_url'],
            "return_url" => $alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            //商户订单号，商户网站订单系统中唯一订单号，必填
            "out_trade_no" => $params['out_trade_no'],
            //订单名称，必填
            "subject" => $params['subject'],
            //付款金额，必填
            "total_fee" => $params['total_fee'],
            //收银台页面上，商品展示的超链接，必填
            "show_url" => $params['show_url'],
            //"app_pay" => "Y",//启用此参数能唤起钱包APP支付宝
            //商品描述，可空
            "body" => $params['body'],
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
            //如"参数名"    => "参数值"   注：上一个参数末尾需要“,”逗号。
 
        );
        // var_dump($parameter);exit;
        //建立请求
        import('walipay.AlipaySubmit',EXTEND_PATH,'.php');
        $alipaySubmit = new \Alipay_wap\AlipaySubmit($alipay_config);
        // var_dump($alipaySubmit);exit;
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        return $html_text;
    }
}