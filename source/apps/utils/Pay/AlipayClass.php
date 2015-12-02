<?php

namespace Mall\Utils\Pay;

//use Mall\Utils\Pay\UserInterface;

class AlipayClass implements PayInterface
{
    private $di;
    private $alipay_config;

    public function __construct($di)
    {
        $this->di = $di;
        //配置
        $this->alipay_config = $this->di->get('sysconfig')['payment']['alipay'];
    }

    public function buildRequest($req)
    {
        include(__DIR__ . '/alipay/lib/alipay_submit.class.php');
        //构造要请求的参数数组
        $parameter = array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($this->alipay_config['partner']),
                "seller_email" => trim($this->alipay_config['seller_id']),
                "payment_type" => '1',
                "notify_url" => trim($this->alipay_config['notify_url']),
                "return_url" => trim($this->alipay_config['return_url']),
                "out_trade_no"  => $req['order_sn'],//订单号
                "subject" => $req['body'],//订单名称
                "total_fee" => $req['order_paied'],
                "body" => $req['desc'],//订单描述
                //"show_url" => "http://mall.yunduo.com/goods/index/",
                "_input_charset" => trim(strtolower($this->alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($this->alipay_config);
        $htmlText = $alipaySubmit->buildRequestForm($parameter,'get', '');
        echo $htmlText;
    }

    public function notify($req)
    {
        require_once(__DIR__ . '/alipay/lib/alipay_notify.class.php');
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipay_config);
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
