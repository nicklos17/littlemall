<?php

namespace Mall\Utils\Pay;

include(__DIR__ . '/mobiWechat/WxPayPubHelper.php');

class MobiWechatClass implements PayInterface
{
    private $di;
    private $wxConfig;
    private $jsApi;

    public function __construct($di)
    {
        $this->di = $di;
        //配置
        $this->wxConfig = $this->di->get('sysconfig')['payment']['mobiWechat'];
        $this->jsApi = new JsApi_pub($this->wxConfig);
    }

    public function wxpay()
    {
        $url = $this->jsApi->createOauthUrlForCode($this->wxConfig['js_api_call_url']);
         Header("Location: $url");
    }

    public function checkCode()
    {
        if(!isset($_GET['code']))
        {
            $url = $this->jsApi->createOauthUrlForCode($this->wxConfig['js_api_call_url']);
            Header("Location: $url");
            return null;
        }
        else
        {
          //以获取openid
          $code = $_GET['code'];
          $this->jsApi->setCode($code);
          $openid = $this->jsApi->getOpenId();
          return $openid;
        }
    }
    public function buildRequest($req)
    {
        //获取prepay_id============
        $unifiedOrder = new UnifiedOrder_pub($this->wxConfig);
        //设置统一支付接口参数
        $unifiedOrder->setParameter("openid", "$req[openId]");//商品描述
        $unifiedOrder->setParameter("body", "$req[body]");//商品描述
        $unifiedOrder->setParameter("out_trade_no", "$req[order_sn]");//商户订单号
        $amount = $req['order_paied']*100;
        $unifiedOrder->setParameter("total_fee", "$amount");//总金额
        $unifiedOrder->setParameter("notify_url", $this->wxConfig['notify_url']);//通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
        $prepay_id = $unifiedOrder->getPrepayId();
        //使用jsapi调起支付============
        $this->jsApi->setPrepayId($prepay_id);
        $this->jsApi = $this->jsApi->getParameters();
        return "<script>
        function jsApiCall(){
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                {$this->jsApi},
                function(res){
                    //支付成功
                    if(res.err_msg == 'get_brand_wcpay_request:ok')
                        window.location = '/ng/#/order';
                    //用户取消
                    else if(res.err_msg == 'get_brand_wcpay_request:cancel'){
                        window.location = '/ng/#/order';
                    }
                    //支付失败
                    else{
                        alert('支付失败,请重试下');
                        window.location = '/ng/#/order';
                    }
                }
            );
        }
        ;(function(){
            if (typeof WeixinJSBridge == 'undefined'){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
            //监控关闭窗口
            WeixinJSBridge.invoke('closeWindow',{},function(res){
                window.location.href = '/ng/#/order';
            });
        })()</script>";
    }

    public function notify($req)
    {
        //使用通用通知接口
        $notify = new Notify_pub($this->wxConfig);

        //存储微信的回调
        $xml = urldecode(file_get_contents('php://input'));
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE)
        {
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }

        else
        {
            //$returnXml = $notify->returnXml();
            //echo $returnXml;
            if ($notify->data["return_code"] == "FAIL")
            {
                $notify->setReturnParameter("return_code","FAIL");
                //此处应该更新一下订单状态，商户自行增删操作
                //$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL")
            {
                $notify->setReturnParameter("return_code", "FAIL");
                //此处应该更新一下订单状态，商户自行增删操作
                //$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
            }
            else
            {
                //处理订单状态
                $uid =  (new \Mall\Mdu\OrderModule())->setOrderStatus($notify->data['out_trade_no'], $_SERVER['REQUEST_TIME']);
                if($uid != 0)
                {
                    (new \Mall\Mdu\OrderLogsModule())->addOrderLogBySn($notify->data['out_trade_no'], $this->di['sysconfig']['orderActType']['successPayment'], $uid, '用户', '支付成功');

                    $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
                }
                else
                    $notify->setReturnParameter("return_code", "FAIL");
                //此处应该更新一下订单状态，商户自行增删操作
                //$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");
            }
            $returnXml = $notify->returnXml();
            echo $returnXml;
        }
    }
}
