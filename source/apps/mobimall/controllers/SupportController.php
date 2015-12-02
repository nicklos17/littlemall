<?php

namespace Mall\MobiMall\Controllers;

use  Mall\Mdu\BackOrdersModule as Support,
     Mall\Mdu\OrderModule as Order,
     Mall\Utils\Pagination as Page,
     Mall\Utils\GoodsImgUpload as ImgUpload;

class SupportController extends ControllerBase
{

    const NUM_PER_PAGE = 3;//每页显示数
    const ORDER_STATUS_FINISHED = 11;//[订单状态:交易成功]
    const BORD_STATUS_PASS_AUDIT = 3; //[售后单状态:审核通过]
    const BORD_ACT_ROLE_FRONTEND = 1;//[操作售后的角色：前台]

    const MSG_ORDER_NONE = '10049'; //[错误：订单不存在或订单状态错误]
    const MSG_SHIP_RE_SUBMIT = '10055'; //[错误，该售后单已经提交了售后单号]
    const MSG_UPLOAD_INVALID = '10056'; //[错误：图片过大或格式错误]

    private $support;
    private $order;

    public function initialize()
    {
        $this->support = new Support();
        $this->order = new Order();
    }

    public function indexAction()
    {

        if(!$this->validFlag)
        {
            echo json_encode(
                array(
                    'ret' => 0
                )
            );
            $this->view->disable();
            return;
        }

        $total = $this->support->getTotal(['u_id'=>$this->uid]);
        $page = !empty($this->request->get('page')) ? intval($this->request->get('page')) : 1;
        if(($page - 1)  * self::NUM_PER_PAGE >= $total){
            echo json_encode(array());
            $this->view->disable();
            return;
        }
        //判断是否倒数第二页
        $c = $total%self::NUM_PER_PAGE == 0 ? $total/self::NUM_PER_PAGE : intval($total/self::NUM_PER_PAGE) + 1;
        if($page  == $c)
            $last = 1;
        else
            $last = 0;
        $page = new Page($total, self::NUM_PER_PAGE, $page);

        echo json_encode(
            array(
                'last' => $last,
                'ret' => 1,
                'supOrders' => $this->support->getBackOrdersByUser($this->uid, $page->firstRow >= 0 ? $page->firstRow : 0, $page->listRows),
            )
        );
        $this->view->disable();
        return;
    }

    public function applyAction()
    {
        if(!$this->validFlag)
            echo json_encode(array('ret' => 0));
        else
        {
            if($orderInfo = $this->order->getSpecifyOrder($this->uid, $this->_sanReq['orderSn'], self::ORDER_STATUS_FINISHED))
            {
                $orderGoods = $this->order->getOrderGoods($orderInfo['order_id']);
                foreach($orderGoods as $key=>$goods)
                {
                    $orderGoods[$key]['attrArr'] = $this->support->getGoodsAttr($goods['attrs_info']);
                }
                if($orderInfo['order_tel']){
                    $telInfo = explode('-', $orderInfo['order_tel']);
                    $orderInfo['areaCode'] = empty($telInfo[0]) ?  '' : $telInfo[0];
                    $orderInfo['telNum'] = empty($telInfo[1]) ? '' : $telInfo[1];
                    $orderInfo['ext'] = empty($telInfo[2]) ?  '' : $telInfo[2];
                }
                $orderLogMdu = new \Mall\Mdu\OrderLogsModule();
                echo json_encode(array(
                    'ret' => 1,
                    'orderSn' => $this->_sanReq['orderSn'],
                    'orderInfo' => $orderInfo,
                    'orderGoods' => $orderGoods,
                    //确认收货时间
                    'confirmTime' => $orderLogMdu->getOrderLogTime($orderInfo['order_id'], $this->sysconfig['orderActType']['received'])['ord_act_addtime'],
                    'nowTime' => $_SERVER['REQUEST_TIME']
                ));
            }
            else
                echo json_encode(array('ret' => 0));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax 添加售后单]
     */
    public function addSupportAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->support->addBackOrder($this->uid, $this->_sanReq, $this->mobi, self::BORD_ACT_ROLE_FRONTEND);

            if($res['ret'] == 1)
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => $this->sysconfig['flagMsg'][$res['code']]));
            }
        }

        $this->view->disable();
        return;
    }

    public function expressAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => '错误的售后单号'));
        }
        else
        {
            if($bordInfo = $this->support->getSpecifyBord($this->uid, $this->_sanReq['supportSn'], self::BORD_STATUS_PASS_AUDIT))
            {
                //判断该售后单是否已提交过快递单号
                if($bordInfo['bord_shipping_sn'] != '')
                {
                    echo json_encode(array('ret' => 0, 'msg' => '该售后单已填写过物流单号'));
                }
                else
                {
                    $this->session->set('supportSn', $this->_sanReq['supportSn']);
                    echo json_encode(array('ret' => 1));
                }
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => '网络异常,请重试'));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax: 提交快递单号]
     */
    public function submitExpressAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->support->addExpInfo($this->uid, $this->mobi, $this->session->get('supportSn'), $this->_sanReq['ship_comp'], $this->_sanReq['ship_sn']);
            if($res['ret'] == 1)
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => $this->sysconfig['flagMsg'][$res['code']]));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * 售后进度
     */
    public function progressAction()
    {
        if(!$this->validFlag)
        {
            $this->showMsg('/support', '该售后单不存在', '售后列表');
        }
        else
        {
            if($support = $this->support->getUserOrderBySn($this->uid, $this->_sanReq['supportSn']))
            {
                $this->view->setVars(
                    array(
                        'supportInfo' => $support,
                        //获取售后操作日志
                        'supportLogs' => (new \Mall\Mdu\OrderLogsModule())->getBackLogsById($support['bord_id'])
                    )
                );
            }
            else
            {
                $this->showMsg('/support', '该售后单不存在', '售后列表');
            }
        }
    }
}
