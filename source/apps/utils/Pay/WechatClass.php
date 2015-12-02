<?php

namespace Mall\Utils\Pay;

//use Mall\Utils\Pay\UserInterface;

include(__DIR__ . '/wechat/lib/WxPayCore.php');

class WechatClass implements PayInterface
{
    private $di;
    private $wxConfig;

    public function __construct($di)
    {
        $this->di = $di;
        //配置
        $this->wxConfig = $this->di->get('sysconfig')['payment']['wechat'];
    }

    public function buildRequest($req)
    {
        //使用统一支付接口
        $unifiedOrder = new UnifiedOrder_pub($this->wxConfig);
        $unifiedOrder->setParameter("body", $req['body']);//商品描述
        $unifiedOrder->setParameter("out_trade_no", $req['order_sn']);//商户订单号
        $unifiedOrder->setParameter("total_fee", $req['order_paied']*100);//总金额
        $unifiedOrder->setParameter("notify_url",$this->wxConfig['NOTIFY_URL']);//通知地址
        $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型

        //获取统一支付接口结果
        $unifiedOrderResult = $unifiedOrder->getResult();

        if ($unifiedOrderResult["return_code"] == "FAIL")
        {
            //echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
            return 0;
        }
        elseif($unifiedOrderResult["result_code"] == "FAIL")
        {
            //echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
            //echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
            return 0;
        }
        elseif($unifiedOrderResult["code_url"] != NULL)
        {
            //从统一支付接口获取到code_url
            $code_url = $unifiedOrderResult["code_url"];
            return $code_url;
        }

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
                $notify->setReturnParameter("return_code", "FAIL");
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

                $uid = (new \Mall\Mdu\OrderModule())->setOrderStatus($notify->data['out_trade_no'], $_SERVER['REQUEST_TIME']);
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
