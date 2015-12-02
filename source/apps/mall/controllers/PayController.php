<?php

namespace Mall\Mall\Controllers;

use  Mall\Mdu\OrderModule as Order,
        Mall\Utils\Pay;

class PayController extends ControllerBase
{
    private $order;

    public function initialize()
    {
        $this->order = new Order;
    }

    public function wxpayAction()
    {
        $order = $this->order->getFeeByUidOid($this->uid, $this->di['session']->get('order_id'));
        $order['body'] = '云朵智能定位童鞋';
        $wx = Pay::getPayType($this->di, 'wechat');
        if(!$codeUrl = $wx->buildRequest($order))
            echo json_encode(['ret' => 0, 'error' => '通信出错,请重试下']);
        //unset($_SESSION['order_id']);
        $this->view->setVars(
            array(
                'url' => $codeUrl,
                'order' => $order
            )
        );
    }

    public function wxnotifyAction()
    {
        Pay::getPayType($this->di, 'wechat') -> notify($this->_sanReq);

        $this->view->disable();
        return;
    }

    public function alipayAction()
    {
        $order = $this->order->getFeeByUidOid($this->uid, $this->di['session']->get('order_id'));
        $order['body'] = '云朵网络智能童鞋';
        $order['desc'] = '云朵智能定位童鞋';
        $ali = Pay::getPayType($this->di, 'alipay');
        $ali -> buildRequest($order);
        //unset($_SESSION['order_id']);
    }

    public function alinotifyAction()
    {
        $this->_sanReq['uid'] = $this->uid;
        Pay::getPayType($this->di, 'alipay') -> notify($this->_sanReq);

        $this->view->disable();
        return;
    }
}
