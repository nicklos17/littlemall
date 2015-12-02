<?php

namespace Mall\Utils\Pay;

class MobiAlipayClass implements PayInterface
{
    private $di;
    private $mobiAlipayConfig;

    public function __construct($di)
    {
        $this->di = $di;
        //配置
        $this->mobiAlipayConfig = $this->di->get('sysconfig')['payment']['mobiAlipay'];
    }

    public function buildRequest($req)
    {
        include(__DIR__ . '/mobiAlipay/lib/alipay_submit.class.php');
        //构造要请求的参数数组
        $parameter = array(
                "service" => "alipay.wap.create.direct.pay.by.user",
                "partner" => trim($this->mobiAlipayConfig['partner']),
                "seller_id" => trim($this->mobiAlipayConfig['seller_id']),
                "payment_type" => '1',
                "notify_url" => trim($this->mobiAlipayConfig['notify_url']),
                "return_url" => trim($this->mobiAlipayConfig['return_url']),
                "out_trade_no" => $req['order_sn'],//订单号
                "subject" => $req['body'],//订单名称
                "total_fee" => $req['order_paied'],
                "body" => $req['desc'],//订单描述
                //"show_url" => "http://m.mall.yunduo.com/#/goods/商品ID",//商品展示地址
                "_input_charset" => trim(strtolower($this->mobiAlipayConfig['input_charset'])),
                //"it_b_pay"//超时时间
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($this->mobiAlipayConfig);
        echo $alipaySubmit->buildRequestForm($parameter, 'get', '');
    }

    public function notify($req)
    {
        require_once(__DIR__ . '/mobiAlipay/lib/alipay_notify.class.php');
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->mobiAlipayConfig);
        $verifyResult = $alipayNotify->verifyNotify();
        if($verifyResult)
        {
            //商户订单号
            $out_trade_no = $req['out_trade_no'];
            //支付宝交易号
            $trade_no = $req['trade_no'];
            //交易状态
            $trade_status = $req['trade_status'];

            if($req['trade_status'] == 'TRADE_FINISHED' || $req['trade_status'] == 'TRADE_SUCCESS')
            {
                //处理订单状态
                $uid = (new \Mall\Mdu\OrderModule())->setOrderStatus($out_trade_no, $_SERVER['REQUEST_TIME']);
                if($uid != 0)
                {
                   (new \Mall\Mdu\OrderLogsModule())->addOrderLogBySn($out_trade_no, $this->di['sysconfig']['orderActType']['successPayment'], $uid, '用户', '支付成功');

                    echo 'success';
                }
                else
                    echo 'fail';
            }
        }
        else
        {
            echo "fail";
        }
    }
}
