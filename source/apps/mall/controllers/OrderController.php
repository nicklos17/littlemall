<?php

namespace Mall\Mall\Controllers;

use  Mall\Mdu\OrderModule as Order,
        Mall\Utils\Pagination as Page,
        Mall\Mdu\CartModule as Cart,
        Mall\Mdu\GoodsModule as Goods;
        //Mall\Mdu\CouponsModule as Coupons;

class OrderController extends ControllerBase
{
    const NUM_PER_PAGE = 5;//每页显示数
    const PAY_OVER = 1;//未支付状态
    const MAX_NUM = 20;//单个商品最大购买量

    private $order;
    private $cart;

    public function initialize()
    {
        $this->order = new Order;
        $this->cart = new Cart;
    }

    /**
     * [订单列表展示]分页
     * @return [type] [description]
     */
    public function indexAction()
    {
        header("Access-Control-Allow-Origin: http://my.yunduo.net");
        $where = array('u_id' => $this->uid, 'act' =>$this->request->get('act'));
        $page = new Page($this->order->getTotal($where), self::NUM_PER_PAGE, !empty($this->request->get('page')) ? $this->request->get('page') : 1);
        $list = $this->order->myOrderList($page->firstRow >= 0 ? $page->firstRow : 0, $page->listRows, $where);
        $this->view->setVars(
            array(
                'list' => $list,
                'page' => $page->createDotLinks(),
                'act' => $this->request->get('act'),
            )
        );
        $this->view->setVars(
            array(
                'head_title' => '订单中心 -- ',
            )
        );
    }

    /**
     * [detailAction description]
     * @return [type] [description]
     */
    public function detailAction()
    {
        $orderInfo = $this->order->getOrderDetail($this->request->get('sn'), $this->uid);
        if(empty($orderInfo))
        {
            $this->showMsg('/order', '对不起，您浏览的暂时无法显示。这可能是因为输入的网址不正确。', '我的订单');
        }
        $this->view->setVars(
            array(
                'orderInfo' => $orderInfo,
                'orderGoods' => $this->order->getOrderGoods($orderInfo['order_id']),
            )
        );
        $this->view->setVars(
            array(
                'head_title' => '订单中心 -- ',
            )
        );
    }

    /**
     * [pc微信支付扫码或取订单状态]
    **/
    public function poolOrderStatusAction()
    {
       $orderInfo = $this->order->getOrderStatusById($this->di['session']->get('order_id'));
       if($orderInfo['order_pay_status'] == 3) $this->di['session']->remove('order_id');
        echo json_encode(array('ret' => $orderInfo));
        $this->view->disable();
        return;
    }

    public function confirmAction()
    {
        if(isset($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], 'cart') ||
            strpos($_SERVER['HTTP_REFERER'], 'goods') || strpos($_SERVER['HTTP_REFERER'], 'confirm')))
        {
            $this->view->setVars(
                array(
                    'addressList' => (new \Mall\Mdu\AddressModule())->getUserAddress($this->uid),
                   // 'coupons'=>(new Coupons())->getAllCouponsByUid(
                   //     array(
                   //         'u_id' => $this->uid,
                   //         'cp_used_time'=> 0,
                   //         'cp_status' => 1,
                   //         'cp_end_time >' => $_SERVER['REQUEST_TIME']
                   //     )
                   // ),
                )
            );
        }
        else
            //$this->showMsg('/cart', 'error uri', '购物车');
            //return;
            $this->response->redirect('public/error404');
    }

/**
 * @L
 * [buyCartAction 立即购买]
 * @return [type] [description]
 */
    public function buyNowAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0));
            $this->view->disable();
            return;
        }

        if($this->request->isPost())
        {
            //判断是否使用云码
            $codeFlag = 0;
            if($codeInfo = $this->di['session']->get('codeInfo'))
            {
                //重新获取云码信息  排除多终端记录的session
                $codeInfo = (new  \Mall\Mdu\CodesModule())->getCodeInfo($codeInfo['yc_id']);
                if($this->_sanReq['n'] != 1)
                {
                    echo json_encode(array('ret' =>0));
                    $this->view->disable();
                    return;
                }

                //是否在云码商品之中
                $ids = explode(',', $codeInfo['yc_good_ids']);
                //是否全免
                if((in_array($this->_sanReq['g'], $ids) || $codeInfo['yc_good_ids'] == 0) && $codeInfo['yc_type'] == 3 && !$codeInfo['yc_used_time'])
                    $codeFlag = 1;
                else
                    $this->session->remove('codeInfo');
            }

            //获取商品属性 以及判断库存
            $gInfo = $this->cart->getGoodsInfo($this->uid, $this->_sanReq['g'], $this->_sanReq['a'], $this->_sanReq['n']);
            if($gInfo != 10000)
            {
                //单件商品是否超过最大购买量
                if(intval($this->_sanReq['n']) > self::MAX_NUM)
                {
                    echo json_encode(array('ret' =>0));
                    $this->view->disable();
                    return;
                }
                if($goodsDates = $this->cart->getBuyNowNames($gInfo, $this->_sanReq['g'], $this->_sanReq['a']))
                {
                    $goodsDates['codeFlag'] = $codeFlag;
                    echo json_encode(array('ret' => 1, 'g' => $goodsDates));
                }
                else
                echo json_encode(array('ret' =>0));
            }
            else
                echo json_encode(array('ret' => 3));
            $this->view->disable();
            return;
        }
    }

/**
 * @L
 * [buyCartAction 购物车下单]
 * @return [type] [description]
 */
    public function buyCartAction()
    {
        if($this->request->isPost())
        {
            $attrsIds = $goodsIds = [];
            foreach(json_decode($this->_sanReq['select'], true) as $val)
            {
                array_push($attrsIds, $val['a']);
                array_push($goodsIds, $val['g']);
            }
            $goodsDates = $this->cart->getOrderDatas($this->uid, $goodsIds, $attrsIds);
            $datas = $this->cart->getCartNames($goodsDates);
            if(empty($datas))
                echo json_encode(array('ret' => 0));
            else if($datas == 10000)
                echo json_encode(array('ret' => 3));
            else
                echo json_encode(array('ret' => 1, 'data' => $datas));
            die;
        }
    }

    /**
     * [ajax:获取快递费用]
     */
    public function getShipPayAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            echo json_encode(array(
                'ret' => 1,
                'data' => (new \Mall\Mdu\ShippingPayModule())->getShipPayByPro($this->_sanReq['proId']))
            );
        }

        $this->view->disable();
        return;
    }

    public function PaymentsAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }
        if(!empty($this->_sanReq['id']))
            $this->di['session']->set('order_id', $this->_sanReq['id']);

        if(!$this->di['session']->has('order_id'))
            $this->response->redirect('order');

        $orderInfo = $this->order->getOrderSec($this->uid, $this->di['session']->get('order_id'));

        if($orderInfo['order_status'] != self::PAY_OVER)
            $this->response->redirect('order');
        $this->view->setVars(
            array(
                'order' => $orderInfo
            )
        );
    }

    /**
     * 下单@L
     */
    public function goAction()
    {
        if(!$this->validFlag || !$this->_sanReq['select'])
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }
        else
        {
            $select = json_decode($this->_sanReq['select'], true);
            //收货地址
             $addr = (new \Mall\Mdu\AddressModule())->getAddrById($this->uid, $this->_sanReq['add_id']);

            //取快递费用
            $shipFee = sprintf("%.2f", (new \Mall\Mdu\ShippingPayModule())->getShipPayByPro($addr['pro_id']));

            //发票
            if($this->_sanReq['is_inv'] == 3)
            {
                $this->_sanReq['inv_type'] = 3;
                $this->_sanReq['inv_title'] = 3;
            }
            //订单编码
            $orderSn = \Mall\Utils\Inputs::makeOrderSn();

            //商品总价,订单金额
            $totalFee = $paiedFee = 0;
            //立即购买
            if(isset($select['n']) && $select['n'] > 0)
            {
                $goodsNum = intval($select['n']);
                //获取商品属性 以及判断库存
                $gInfo = $this->cart->getGoodsInfo($this->uid, $select['g'], $select['a'], $goodsNum);
                if($gInfo != 10000)
                {
                    //单件商品是否超过最大购买量
                    if(intval($goodsNum) > self::MAX_NUM)
                    {
                        echo json_encode(array('ret' =>0));
                        $this->view->disable();
                        return;
                    }

                    if(!$goodsDates = $this->cart->getBuyNowNames($gInfo, $select['g'], $select['a']))
                        echo json_encode(array('ret' =>0));
                    else
                    {
                        //@parm $payoffFee //优惠金额 备用
                        $payoffFee = 0;
                        //云码生效标识
                        $codeFlag = 0;

                        //判断是否使用源码
                        if($codeInfo = $this->di['session']->get('codeInfo'))
                        {
                            //重新获取云码信息  排除多终端记录的session
                            $codeInfo = (new  \Mall\Mdu\CodesModule())->getCodeInfo($codeInfo['yc_id']);
                            $ids = explode(',', $codeInfo['yc_good_ids']);
                            if((in_array($select['g'], $ids) || $codeInfo['yc_good_ids'] == 0) && $codeInfo['yc_type'] == 3 && !$codeInfo['yc_used_time'])
                            {
                                if($goodsNum != 1)
                                {
                                    echo json_encode(array('ret' => 0));
                                    $this->view->disable();
                                    return false;

                                }
                                //源码暂时不使用运费
                                $payoffFee = sprintf('%.2f', $goodsDates['goods_price']  + $shipFee);
                                $codeFlag = 1;
                            }
                            else
                                $this->session->remove('codeInfo');
                        }
                        //商品总价
                        $totalFee = sprintf('%.2f', $goodsDates['goods_price']*$goodsNum);
                        $paiedFee = sprintf('%.2f', $totalFee + $shipFee - $payoffFee);
                    }
                }
                else
                {
                    echo json_encode(array('ret' => 3));
                    $this->view->disable();
                    return false;
                }
                //下单
                if($this->order->addBuyNowOrder($this->uid, $this->mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $this->_sanReq['memo'],
                $shipFee, $this->_sanReq['is_inv'], $this->_sanReq['inv_type'], $this->_sanReq['inv_title'], $_SERVER['REQUEST_TIME'],
                $addr, $goodsDates, $goodsNum, $codeFlag) != 1)
                {
                    echo json_encode(array('ret' => 0));
                }
            }
            //购物车多商品购买
            else
            {
                $attrsIds = $goodsIds = [];
                foreach($select as $val)
                {
                    array_push($attrsIds, $val['a']);
                    array_push($goodsIds, $val['g']);
                }
                if(count($attrsIds) != count($goodsIds))
                    exit(json_encode(array('ret' =>0)));

                $data = $this->cart->getOrderDatas($this->uid, $goodsIds, $attrsIds);
                $goodsDates = $this->cart->getCartNames($data);
                if($goodsDates == 10000 || !$goodsDates)
                {
                    echo json_encode(array('ret' => 3));
                    $this->view->disable();
                    return;
                }
                else
                {
                    //优惠金额 备用
                    $payoffFee = 0;
                    //商品总价
                    $filterGoodsSum = 0;
                    foreach($goodsDates as $g)
                    {
                        $totalFee += sprintf('%.2f', $g['goods_price']*$g['car_good_num']);
                    }

                    $paiedFee = sprintf('%.2f', $totalFee - $payoffFee) > 0 ? sprintf('%.2f', $totalFee + $shipFee) : $shipFee;
                }

                //下单
                if($this->order->addBuyCartOrder($this->uid, $this->mobi, $orderSn, $totalFee, $paiedFee, $payoffFee = 0, $this->_sanReq['memo'],
                    $shipFee, $this->_sanReq['is_inv'], $this->_sanReq['inv_type'], $this->_sanReq['inv_title'],
                    $_SERVER['REQUEST_TIME'], $addr, $goodsDates, $attrsIds, $goodsIds) != 1)
                {
                    echo json_encode(array('ret' => 0));
                }
            }

            setcookie('cg', '', time()-3600);
            echo json_encode(array('ret' => 1));

            $this->view->disable();
            return;
        }
    }

   /**
   * 查看物流
   */
   public function getExpressAction()
   {
    if ($this->request->isPost())
    {
        if (!$this->validFlag)
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        else
        {
            $expressCode = $this->di->get( 'sysconfig' )['express'];
            $expressName = 1;
            if($code = array_search($expressName,$expressCode))
            {
                $express = new \Mall\Utils\Express();
                $result = $express->getorder($code,$this->_sanReq['code']);
                $str = '';
                if(!empty($result['data']))
                {
                    foreach (array_reverse($result['data']) as $v)
                        $str .='<p>'.$v['time'].' '.$v['context'].'</p>' ;
                    echo $str;
                }
                else
                    echo '';
            }
            else
                    echo '';
        }
    }
    $this->view->disable();
    return;
   }

   /**
   * 确认收货
   */
   public function confirmOrderAction()
   {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            else
            {
                $res = $this->order->confirmReceipt($this->_sanReq['sn'], $this->uid);
                if($res)
                    echo json_encode(array('ret' =>1));
                else
                    echo json_encode(array('ret' =>0, 'msg' => '确认收货失败'));
            }
        }
        $this->view->disable();
        return;
   }

    public function getPendPayCntAction()
    {
        header("Access-Control-Allow-Origin: http://my.yunduo.net");
        error_reporting(E_ALL || ~E_NOTICE);
        //$uid = $this->request->get('uid');
        $uid = isset($this->uid)? $this->uid: '';
        if ($uid) {
            echo  json_encode(array('ret' => 1, 'info' => $this->order->getPendPayCnt($uid)));
        } else {
            echo json_encode(array('ret' => 0, 'msg' => '获取待付款订单数失败'));
        }
        $this->view->disable();
    }
}
