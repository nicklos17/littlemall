<?php

namespace Mall\MobiMall\Controllers;

use  Mall\Mdu\OrderModule as Order,
      Mall\Utils\Pay;

class PayController extends ControllerBase
{
    private $order;
    private $wx;

    public function initialize()
    {
        $this->order = new Order;
        $this->wx = Pay::getPayType($this->di, 'mobiWechat');
    }

    public function wxpayAction()
    {
        $this->wx->wxpay();

        $this->view->disable();
        return;
    }

     //微信授权跳转页面
    function redirectPayAction()
    {
        if(!$openId = $this->wx->checkCode())
        {
            echo json_encode(['ret' => 0, 'error' => '通信出错,请重试下']);
            $this->view->disable();
            return;
        }

        $order = $this->order->getFeeByUidOid($this->uid, $this->di['session']->get('order_id'));
        $order['body'] = '云朵智能定位童鞋';
        $order['openId'] = $openId;
        echo $this->wx->buildRequest($order);
        //unset($_SESSION['order_id']);
        $this->view->disable();
        return;
     }

    public function wxnotifyAction()
    {
        Pay::getPayType($this->di, 'mobiWechat') -> notify($this->_sanReq);

        $this->view->disable();
        return;
    }

    public function alipayAction()
    {
        $order = $this->order->getFeeByUidOid($this->uid, $this->di['session']->get('order_id'));
        $order['body'] = '云朵网络智能童鞋';
        $order['desc'] = '云朵智能定位童鞋';
        $ali = Pay::getPayType($this->di, 'mobiAlipay');
        $ali -> buildRequest($order);
        //unset($_SESSION['order_id']);
    }

    public function alinotifyAction()
    {
        Pay::getPayType($this->di, 'mobiAlipay') -> notify($this->_sanReq);

        $this->view->disable();
        return;
    }
}
