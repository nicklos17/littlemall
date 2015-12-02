<?php

namespace Mall\Admin\Controllers;

use JsonSchema\Constraints\String;
use Mall\Mdu\OrderModule as Order;
use Mall\Mdu\OrderLogsModule as Logs;
use Mall\Utils\Pagination as Page;

class OrderController extends ControllerBase
{

    const NUM_PER_PAGE = 30;//每页显示数
    const ACT_TYPE_ADD = 1; //[操作类型]增加
    const ACT_TYPE_DEL = 3; //[操作类型]删除
    const ACT_TYPE_UPDATE = 5; //[操作类型]修改

    private $order;
    private $orderLogs;

    public function initialize()
    {
        $this->order = new Order();
        $this->orderLogs = new Logs();
    }

    /**
     * [订单列表展示]
     * @return [type] [description]
     */
    public function indexAction()
    {
        if(!$this->validFlag)
        {
            $page = new Page($this->order->getTotal(), self::NUM_PER_PAGE, 1);
        }
        else
        {
            $page = new Page($this->order->getTotal($this->_sanReq), self::NUM_PER_PAGE, isset($this->_sanReq['page']) ? $this->_sanReq['page'] : 1);
        }
        $this->view->setVars(
            array(
                'list' => $this->order->orderList($page->firstRow >= 0 ? $page->firstRow : 0, $page->listRows, $this->_sanReq),
                'page' => $page->createLink()
            )
        );
    }

    /**
     * [订单详情]
     * @return [type] [description]
     */
    public function detailAction()
    {
        if(!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
             $this->view->disable();
             return;
        }

        $this->view->setVars(
            array(
                'orderInfo' => $this->order->getOrderInfo($this->_sanReq['orderId']),
                'orderGoods' => $this->order->getOrderGoods($this->_sanReq['orderId']),
                'orderLogs' => $this->orderLogs->getLogsByOrderId($this->_sanReq['orderId'])
            )
        );
    }

    /**
     * [修改订单的产品信息:诸如售价，尺码颜色等等]
     * @return [type] [description]
     */
    public function editGoodsAction()
    {
        if(!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->order->setOrderGoods(
                $this->_sanReq['orderGoodsId'],
                $this->_sanReq['orderId'],
                $this->_sanReq['goodsId'],
                $this->_sanReq['colorId'],
                $this->_sanReq['sizeId'],
                (string)number_format($this->_sanReq['price'], '2', '.', ''),
                $this->_sanReq['goodsNum']
            );

            if($res == 1)
            {
                $this->log('订单(order_id: '.$this->_sanReq['orderId'].') 商品品信息('.
                    'orderGoodsId:'.$this->_sanReq['orderGoodsId'].
                    ', goodsId:' . $this->_sanReq['goodsId'].
                    ', colorId:' . $this->_sanReq['colorId'].
                    ', sizeId:' . $this->_sanReq['sizeId'].
                    ', price:' . (string)number_format($this->_sanReq['price'], '2', '.', '').
                    ', goodsNum:' . $this->_sanReq['goodsNum'] . ') 编辑成功'
                );
                $this->addOrderLog($this->_sanReq['orderId'], self::ACT_TYPE_UPDATE, '编辑商品信息');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [修改订单信息]
     * @return [type] [description]
     */
    public function editOrderAction()
    {
        if(!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        elseif($this->order->getOrderStatus($this->_sanReq['orderId'], 'order_delivery_status', 1))
        {
            //只有尚未发货的订单才可修改订单信息
            $res = $this->order->setOrderInfo(
                $this->_sanReq['mobi'],
                $this->_sanReq['shippingType'],
                $this->_sanReq['province'],
                $this->_sanReq['city'],
                $this->_sanReq['district'],
                $this->_sanReq['street'],
                $this->_sanReq['addr'],
                $this->_sanReq['consignee'],
                $this->_sanReq['orderId']
            );

            if($res == 1)
            {
                $this->log('订单(order_id: '.$this->_sanReq['orderId'].')订单信息编辑成功');
                $this->addOrderLog($this->_sanReq['orderId'], self::ACT_TYPE_UPDATE, '编辑订单信息');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }
        else
        {
            echo json_encode(array(
                'ret' => 0,
                'msg' => array(
                    'msg' => array(
                        'msg' => $this->di['sysconfig']['flagMsg']['10031']
                    )
                )
            ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [获取商品所有属性值，比如所有颜色]
     * @return [type] [description]
     */
    public function goodsAttrAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            echo json_encode($this->order->getGoodsInfo($this->_sanReq['goodsId']));
        }
        $this->view->disable();
        return;
    }

    /**
     * [更改订单状态，包括订单状态，配送状态以及付款状态]
     * @return [type] [description]
     */
    public function editStatusAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->order->editStatus($this->_sanReq['orderId'], $this->_sanReq['operate']);
            if($res == 1)
            {
                $logTxt = $this->getOperateLogTxt($this->_sanReq['operate']);

                $this->log('对订单(order_id:'.$this->_sanReq['orderId'].')执行('.$logTxt['txt'] . ')操作 设置成功');
                $this->addOrderLog($this->_sanReq['orderId'], $logTxt['act'], '执行(' . $logTxt['txt'] . ')操作');
                echo json_encode(array('ret' => 1));
            }

            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }
        $this->view->disable();
        return;
    }

    /**
     * [批量编辑订单列表]
     * @return [type] [description]
     */
    public function batchOperateAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->order->batchEditStatus(
                $this->_sanReq['orderIds'],
                $this->_sanReq['operate']
            );

            if($res[0] == 1)
            {
                $logTxt = $this->getOperateLogTxt($this->_sanReq['operate']);

                $this->log('对订单{order_id:(' . rtrim($this->_sanReq['orderIds'], ',') . ')}执行(' . $logTxt['txt'] . ')操作 设置成功');
                $this->orderLogs->batchAddOrderLogs(explode(',', rtrim($this->_sanReq['orderIds'], ',')),
                    $logTxt['act'], $this->casData['uid'], $this->casData['uname'], '执行('.$logTxt['txt'] . ')操作');
                echo json_encode(array('ret' => 1, 'data'=>$res[1]));
            }

            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res[0]]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [显示订单提交页面]
     * @return [type] [description]
     */
    public function addorderAction()
    {

    }

    /**
     * [根据商品名称得到商品信息]
     * @return [type] [description]
     */
    public function getGoodsAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->order->showGoodsByName($this->_sanReq['goodsName']);
            if($res[0] == 1)
                echo json_encode(array('ret' => 1, 'data'=>$res[1]));
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res[0]]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [检查指定商品的某款是否有库存]
     * @return [type] [description]
     */
    public function checkGoodsAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->order->checkGoods(
                $this->_sanReq['goodsId'],
                $this->_sanReq['colorId'],
                $this->_sanReq['sizeId']
            );
            if($res == 1)
                echo json_encode(array('ret' => 1, 'data'=>$res));
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [创建新订单]
     * @return [type] [description]
     */
    public function createAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->order->createOrder(
                $this->_sanReq['goodsData'],
                $this->_sanReq['consignee'],
                $this->_sanReq['mobi'],
                $this->_sanReq['province'],
                $this->_sanReq['city'],
                $this->_sanReq['district'],
                $this->_sanReq['street'],
                $this->_sanReq['addr'],
                (string)number_format($this->_sanReq['shippingFee'], '2', '.', ''),
                (string)number_format($this->_sanReq['orderFee'], '2', '.', '')
            );
            if($res[0] == 1)
            {
                $this->log('订单(order_id:' . $res[1] . ') 添加成功');
                $this->addOrderLog($res[1], $this->sysconfig['orderActType']['add'], '订单添加');
                echo json_encode(array('ret' => 1, 'data'=>$res[1]));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res[0]]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * 根据type返回对应操作名
     *
     * @param int $type
     * @return String
     */
    protected function getOperateLogTxt($type)
    {
        if($type == 'invalid' || $type == 'batchInvalid') return array('txt' => '失效', 'act' => $this->sysconfig['orderActType']['invalid']);
        if($type == 'applyBack' || $type == 'batchApplyBack') return array('txt' => '申请退款', 'act' => $this->sysconfig['orderActType']['back']);
        if($type == 'deliver' || $type == 'batchDeliver') return array('txt' => '发货', 'act' => $this->sysconfig['orderActType']['delivery']);
        if($type == 'orderSuccess' || $type == 'batchOrderSuccess') return array('txt' => '交易成功', 'act' => $this->sysconfig['orderActType']['success']);
        if($type == 'orderClose' || $type == 'batchOrderClose') return array('txt' => '交易关闭', 'act' => $this->sysconfig['orderActType']['cancel']);
    }

    /**
     * 添加订单操作日志
     *
     * @param int $orderId
     * @param int $actType
     * @param string $info
     */
    protected function addOrderLog($orderId, $actType, $info)
    {
        $this->orderLogs->addOrderLog($orderId, $actType, $this->casData['uid'], $this->casData['uname'], $info);
    }

}
