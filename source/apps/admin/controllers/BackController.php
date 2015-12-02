<?php

namespace Mall\Admin\Controllers;

use Mall\Mdu\BackOrdersModule as Back;
use Mall\Utils\Pagination as Page;
use Mall\Mdu\OrderLogsModule as Logs;

class BackController extends ControllerBase
{

    const NUM_PER_PAGE = 30;//每页显示数
    const ACT_TYPE_ADD = 1; //[操作类型]增加
    const ACT_TYPE_DEL = 9; //[操作类型]删除
    const ACT_TYPE_UPDATE = 3; //[操作类型]修改
    const BORD_ACT_ROLE_BACKEND = 3;//[操作售后的角色：后台]

    private $back;
    protected $backLogs;

    public function initialize()
    {
        $this->back = new Back();
        $this->backLogs = new Logs();
    }

    /**
     * [售后服务列表展示]
     */
    public function indexAction()
    {
        if(!$this->validFlag)
        {
            $page = new Page($this->back->getTotal(), self::NUM_PER_PAGE, 1);
        }
        else
        {
            $page = new Page($this->back->getTotal($this->_sanReq), self::NUM_PER_PAGE, isset($this->_sanReq['page']) ? $this->_sanReq['page'] : 1);
        }
        $this->view->setVars(
            array(
                'list' => $this->back->backOrdersList($page->firstRow >= 0 ? $page->firstRow : 0, $page->listRows, $this->_sanReq),
                'page' => $page->createLink()
            )
        );
    }

    /**
     * [添加售后单]
     */
    public function addAction()
    {

    }

    /**
     * [同步订单信息到前端]
     */
    public function syncOrderInfoAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $order = new \Mall\Mdu\OrderModule();
            if($orderInfo = $order->getOrderBySn($this->_sanReq['order_sn']))
            {
                $orderGoods = $order->getOrderGoods($orderInfo['order_id']);
                foreach($orderGoods as $key=>$goods)
                {
                    $orderGoods[$key]['attrArr'] = $this->back->getGoodsAttr($goods['attrs_info']);
                }
                echo json_encode(
                    array(
                        'ret' => 1,
                        'orderInfo' => $orderInfo,
                        'orderGoods' => $orderGoods
                    )
                );
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => $this->di['sysconfig']['flagMsg'][10027])); //10027: order do not exist
            }

            $this->view->disable();
            return;
        }
    }

    /**
     * [add support order]
     */
    public function addOrderAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res=$this->back->addBackOrder($this->casData['uid'], $this->_sanReq, $this->casData['uname'], self::BORD_ACT_ROLE_BACKEND);
                echo json_encode($res);
                //$this->addBackLog($bordId, 1, '申请售后');
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax 删除售后单]
     */
    public function deleteOrdersAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $idArrStr = $this->_sanReq['id_arr'];
            $idArrStr = rtrim($idArrStr, ',');
            $idArr = explode(',', $idArrStr);

            $res = $this->back->deleteBackOrders($idArr);
            if($res == 1)
            {
                $this->log('售后单id: ('.$this->_sanReq['id_arr'].') 删除成功');
                $this->backLogs->batchAddBackLogs($idArr, self::ACT_TYPE_DEL, $this->casData['uid'], $this->casData['uname'], '删除售后单');
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
     * [详细信息]
     */
    public function detailAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }
        $backInfo = $this->back->getOrderById($this->_sanReq['bord_id']);
        $region = new \Mall\Mdu\RegionModule();
        //$attrInfo = $this->back->getGoodsAttr($backInfo['attrs_info']);
        $this->view->setVars(
            array(
                'backInfo' => $backInfo,
                'attrInfo' => $this->back->getGoodsAttr($backInfo['attrs_info']),
                'province' => $region->getProvinceById($backInfo['bord_pro_id'])['pro_name'],
                'city' => $region->getCityNameById($backInfo['bord_city_id'])['city_name'],
                'district' => $region->getDisNameById($backInfo['bord_dis_id'])['dis_name']
            )
        );
    }

    /**
     * [ajax 设置售后单状态]
     */
    public function setStatusAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->back->setOrderStatus($this->_sanReq['bord_id'], $this->_sanReq['status'], $this->casData['uid'], $this->casData['uname']);
            if($res == 1)
            {
                $this->log('售后单(bord_id: '.$this->_sanReq['bord_id'].'), 状态(' . $this->getStatusLogTxt($this->_sanReq['status']) .') 设置成功');
                //$this->addBackLog($this->_sanReq['bord_id'], self::ACT_TYPE_UPDATE, '将状态更改为(' . $this->getStatusLogTxt($this->_sanReq['status']) . ')');
                //$this->addBackLog($this->_sanReq['bord_id'], $this->_sanReq['status'], '将状态更改为(' . $this->getStatusLogTxt($this->_sanReq['status']) . ')');
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
     * [ajax 修改快递信息]
     */
    public function setShipAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->back->setShipInfo($this->_sanReq['bord_id'], $this->_sanReq['shipping_sn'], $this->_sanReq['shipping_name']);
            if($res == 1)
            {
                $this->log('售后单(bord_id: ' . $this->_sanReq['bord_id'].'), 快递信息(shipping_sn: ' . $this->_sanReq['shipping_sn'] . ', shipping_name: ' . $this->_sanReq['shipping_name'] . ') 设置成功');
                //$this->addBackLog($this->_sanReq['bord_id'], self::ACT_TYPE_UPDATE, '更改快递信息');
                //$this->addBackLog($this->_sanReq['bord_id'], 0, '更改快递信息');
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
     * [ajax 设置售后类型]
     */
    public function setTypeAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->back->setOrderType($this->_sanReq['bord_id'], $this->_sanReq['bord_type']);
            if($res == 1)
            {
                $this->log('售后单(bord_id: ' . $this->_sanReq['bord_id'] . '), 售后类型(' .  $this->getTypeLogTxt($this->_sanReq['bord_type'])  . ') 设置成功');
                //$this->addBackLog($this->_sanReq['bord_id'], self::ACT_TYPE_UPDATE, '将类型更改为(' . $this->getTypeLogTxt($this->_sanReq['bord_type']) . ')');
                $this->addBackLog($this->_sanReq['bord_id'], 0, '将类型更改为(' . $this->getTypeLogTxt($this->_sanReq['bord_type']) . ')');
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
     * [ajax 修改退款(申请退款，实际退款)]
     */
    public function setActMoneyAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->back->setActMoney($this->_sanReq['bord_id'], $this->_sanReq['act_money'], $this->_sanReq['back_money']);
            if($res == 1)
            {
                $this->log('售后单(bord_id: ' . $this->_sanReq['bord_id'] . '), 退款金额(申请：' . $this->_sanReq['back_money'] . ',实际: ' . $this->_sanReq['act_money'] . ') 更改成功');
                //$this->addBackLog($this->_sanReq['bord_id'], self::ACT_TYPE_UPDATE, '更改实际退款金额');
                $this->addBackLog($this->_sanReq['bord_id'], 0, '更改退款金额');
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
     * [ajax 批量设置状态]
     */
    public function batchSetStatusAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $idArr = explode(',', rtrim($this->_sanReq['id_arr'], ','));
            $res = $this->back->setOrdersStatus($idArr, $this->_sanReq['status']);
            if($res == 1)
            {
                $this->log('售后单{bord_id: (' . rtrim($this->_sanReq['id_arr'], ',') . ')}, 状态(' . $this->getStatusLogTxt($this->_sanReq['status']) . ') 设置成功');
                //$this->backLogs->batchAddBackLogs($idArr, self::ACT_TYPE_UPDATE, $this->casData['uid'], $this->casData['uname'], '将状态更改为('.$this->getStatusLogTxt($this->_sanReq['status']) . ')');
                $this->backLogs->batchAddBackLogs($idArr, $this->_sanReq['status'], $this->casData['uid'], $this->casData['uname'], '将状态更改为('.$this->getStatusLogTxt($this->_sanReq['status']) . ')');
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
     * 添加售后操作日志
     *
     * @param int $bordId
     * @param int $actType
     * @param string $info
     */
    protected function addBackLog($bordId, $actType, $info)
    {
        $this->backLogs->addBackLog($bordId, $actType, $this->casData['uid'], $this->casData['uname'], $info);
    }

    /**
     * 获取相应状态名
     *
     * @param $status
     * @return string
     */
    protected function getStatusLogTxt($status)
    {
        if( $status == 1) return '未审核';
        if( $status == 3) return '审核通过';
        if( $status == 5) return '审核未通过';
        if( $status == 7) return '完成';
        if( $status == 9) return '关闭';
        if( $status == 11) return '物品已接收，符合条件';
        if( $status == 13) return '物品已接收，不符条件';
    }

    /**
     * 获取相应状态名
     *
     * @param $status
     * @return string
     */
    protected function getTypeLogTxt($status)
    {
        if( $status == 1) return '退货';
        if( $status == 3) return '换货';
        //if( $status == 5) return '退款';
        if( $status == 5) return '更换智能模块';
    }

}