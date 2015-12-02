<?php

namespace Mall\Mdu;

class BackOrdersModule extends ModuleBase
{

    const SUCCESS = 1;
    const ERROR = 10000;

    const BORD_TYPE_BACK = 1; //[售后类型:退货]
    const BORD_TYPE_EXCHANGE = 3; //[售后类型:换货]
    const BORD_TYPE_EXCHANGE_MODULE = 5; //[售后类型:更换智能模块]
    const BORD_STATUS_PASS_AUDIT = 3;//[状态: 通过审核]
    const ORDER_STATUS_FINISHED = 11;
    const BORD_ACT_ROLE_FRONTEND = 1;//[操作售后的角色：前台]
    const BORD_ACT_ROLE_BACKEND = 3;//[操作售后的角色：后台]

    const MSG_ORDER_NONE = '10049'; //[错误：订单不存在或订单状态错误]
    const MSG_BACK_EXPIRED = '10050'; //[错误：该订单已超过7天退货期]
    const MSG_CHANGE_EXPIRED = '10051'; //[错误：该产品已超过30天的换货期]
    const MSG_MODULE_CHANGE_EXPIRED = '10052'; //[错误：该产品已经超过售后服务期]
    const MSG_BORD_TYPE_INVALID = '10053'; //[错误: 售后申请类型错误]
    const MSG_BORD_NUM_OVERFLOW = '10054'; //[错误: 售后申请数量超过允许的最大数量]
    const MSG_SHIP_RE_SUBMIT = '10055'; //[错误: 该售后单已经提交了售后单号]
    const MSG_BORD_NONE = '10057'; //[错误: 售后单不存在或状态错误]

    const LOG_TYPE_ORDER = 1; //[订单日志类型：订单操作]

    private $back;

    public function __construct()
    {
        $this->back = $this->initModel('\Mall\Mdu\Models\BackOrdersModel');
    }

    /**
     * [backOrderList 获取所有退货/退款单列表]
     *
     * @param int $offset
     * @param int $num
     * @param Array $conditions
     * @return Array
     */
    public function backOrdersList($offset, $num, $conditions = [])
    {
        return $this->back->getAllBackOrders($offset, $num, $this->filterConditions($conditions));
    }

    /**
     * [getTotal 获取所有退货/退款单数]
     *
     * @param array $conditions
     * @return int
     */
    public function getTotal($conditions = [])
    {
        return $this->back->getTotal($this->filterConditions($conditions))['num'];
    }

    /**
     * [deleteBackOrders 批量删除退货/退款单]
     *
     * @param $idArr
     * @return boolean
     */
    public function deleteBackOrders($idArr)
    {
        return $this->retCode($this->back->deleteBackOrders($idArr));
    }

    /**
     * [getOrderById 根据ID获取退货/退款单信息 ]
     *
     * @param int $bordId
     * @return Array
     */
    public function getOrderById($bordId)
    {
        return $this->back->getOrderById($bordId);
    }

    /**
     * [setOrderStatus 设置售后单状态]
     *
     * @param int $bordId
     * @param int $status
     * @param int $uid
     * @param string $uname
     * @return Code
     */
    public function setOrderStatus($bordId, $status, $uid, $uname)
    {
        $this->di['db']->begin();
        if(!$this->back->setOrderStatus($bordId, $status))
        {
            $this->di['db']->rollback();
            return $this->retCode(false);
        }

        $orderLogModel = new \Mall\Mdu\Models\OrderLogsModel();
        if(!$orderLogModel->addBordLog($bordId, self::BORD_ACT_ROLE_BACKEND, $status, $uid, $uname, '更改状态'))
        {
            $this->di['db']->rollback();
            return $this->retCode(false);
        }

        $this->di['db']->commit();
        return $this->retCode(true);
    }

    /**
     * [setOrdersStatus 批量设置状态]
     *
     * @param $idArr
     * @param $status
     * @return Code
     */
    public function setOrdersStatus($idArr, $status)
    {
        return $this->retCode($this->back->setOrdersStatus($idArr, $status));
    }

    /**
     * [setShipInfo 修改快递信息]
     *
     * @param $bordId
     * @param $shippingSn
     * @param $shippingName
     * @return Code
     */
    public function setShipInfo($bordId, $shippingSn, $shippingName)
    {
        return $this->retCode($this->back->setShipInfo($bordId, $shippingSn, $shippingName));
    }

    /**
     * [setOrderStatus 设置售后单类型]
     *
     * @param int $bordId
     * @param int $bordType
     * @return Code
     */
    public function setOrderType($bordId, $bordType)
    {
        return $this->retCode($this->back->setOrderType($bordId, $bordType));
    }

    /**
     * [setActMoney 设置实际退款金额]
     *
     * @param int $bordId
     * @param float $actMoney
     * @param float $backMoney
     * @return Code
     */
    public function setActMoney($bordId, $actMoney, $backMoney)
    {
        return $this->retCode($this->back->setActMoney($bordId, $actMoney, $backMoney));
    }

    /**
     * [getGoodsAttr 获取商品属性值]
     *
     * @param string $attrArr
     * @return Array
     */
    public function getGoodsAttr($attrArr)
    {
        $attrArr = json_decode($attrArr, true);
        foreach($attrArr as $key=>$attr){
            $attrArr[$key]['type'] = (new \Mall\Mdu\Models\AttributesModel())->getParentName($attr['id'])['attrs_name'];
        }
        return $attrArr;
    }

    /**
     * [getBackOrdersByUser 获取指定用户的售后单]
     *
     * @param $uid
     * @param $offset
     * @param $num
     * @return Array
     */
    public function getBackOrdersByUser($uid, $offset, $num)
    {
        $backOrders = $this->back->getAllBackOrders($offset, $num, ['u_id' => intval($uid)]);
        foreach($backOrders as $key=>$order)
        {
            $backOrders[$key]['attrArr'] = $this->getGoodsAttr($order['attrs_info']);
        }
        return $backOrders;
    }

    /**
     * [filterConditions 过滤搜索字段]
     *
     * @param Array $conditions
     * @return Array
     */
    protected function filterConditions($conditions)
    {
        $fieldArr = [];
        if(!empty($conditions['order_sn']))
            $fieldArr['order_sn'] = "'" . $conditions['order_sn'] . "'";
        if(!empty($conditions['bord_sn']))
            $fieldArr['bord_sn'] = "'" . $conditions['bord_sn'] . "'";
        if(!empty($conditions['start_time']))
            $fieldArr['start_time'] = strtotime($conditions['start_time']);
        if(!empty($conditions['end_time']))
            $fieldArr['end_time'] = strtotime('+1 day', strtotime($conditions['end_time']));
        if(!empty($conditions['bord_type']))
            $fieldArr['bord_type'] = $conditions['bord_type'];
        if(!empty($conditions['bord_type']))
            $fieldArr['bord_type'] = intval($conditions['bord_type']);
        if(!empty($conditions['bord_status']))
            $fieldArr['bord_status'] = intval($conditions['bord_status']);
        if(!empty($conditions['bord_reason']))
            $fieldArr['bord_reason'] = intval($conditions['bord_reason']);
        if(!empty($conditions['u_id']))
            $fieldArr['u_id'] = intval($conditions['u_id']);

        return $fieldArr;
    }

    /**
     * [retCode 根据结果返回相应code]
     *
     * @param $flag
     * @return Code
     */
    protected function retCode($flag)
    {
        if($flag)
            return self::SUCCESS;
        else
            return self::ERROR;
    }

    /**
     * [addBackOrder 添加售后订单]
     *
     * @param $uid
     * @param $data
     * @param $uname
     * @param $actRole
     * @return boolean
     */
    public function addBackOrder($uid, $data, $uname, $actRole)
    {
        //判断订单是否存在,订单状态是否正确
        $order = new \Mall\Mdu\Models\OrderModel();

        if($actRole == self::BORD_ACT_ROLE_BACKEND)
        {
            if(!$orderInfo = $order->getSpecifyOrderBySn($data['order_sn'], self::ORDER_STATUS_FINISHED))
                return array('ret' => 0, 'code' => self::MSG_ORDER_NONE);
        }
        else
        {
            if(!$orderInfo = $order->getSpecifyOrder($uid, $data['order_sn'], self::ORDER_STATUS_FINISHED))
                return array('ret' => 0, 'code' => self::MSG_ORDER_NONE);
        }

        $orderLogModel = new \Mall\Mdu\Models\OrderLogsModel();

        //确认订单时间
        $confirmTime = $orderLogModel->getLogTime($orderInfo['order_id'], self::LOG_TYPE_ORDER, $this->di['sysconfig']['orderActType']['received'])['ord_act_addtime'];
        $currentTime = $_SERVER["REQUEST_TIME"];

        //判断是否超过该售后类型的售后时间
        switch($data['bord_type'])
        {
            case self::BORD_TYPE_BACK: //退货
                if($currentTime - $confirmTime > $this->di['sysconfig']['supportTime']['back'])
                    return array('ret' => 0, 'code' => self::MSG_BACK_EXPIRED);
                break;
            case self::BORD_TYPE_EXCHANGE: //换货
                if($currentTime - $confirmTime > $this->di['sysconfig']['supportTime']['exchange'])
                    return array('ret' => 0, 'code' => self::MSG_CHANGE_EXPIRED);
                break;
            case self::BORD_TYPE_EXCHANGE_MODULE: //更换智能模块
                if($currentTime - $confirmTime > $this->di['sysconfig']['supportTime']['exchangeModel'])
                    return array('ret' => 0, 'code' => self::MSG_MODULE_CHANGE_EXPIRED);
                break;
            default:
                return array('ret' => 0, 'code' => self::MSG_BORD_TYPE_INVALID);
        }

        //判断省市区是否关联
        if(!(new \Mall\Mdu\Models\RegionModel())->getSpecifyDis($data['pro'], $data['city'], $data['dis']))
        {
            return array('ret' => 0, 'code' => self::ERROR);
        }

        $og = new \Mall\Mdu\Models\OrdergoodsModel();

        if($orderGoods = $og->getOrderGoodsById($data['order_goods_id']))
        {
            $gnum = 0;//已售后数量
            $goodsNums = $this->back->getBordGoodsNum($data['order_sn'], $orderGoods['attrs_barcode']);

            foreach($goodsNums as $goodsNum)
            {
                $gnum += intval($goodsNum['bord_goods_num']);
            }

            if(intval($data['goods_num']) > intval($orderGoods['ord_goods_num']) - $gnum)//申请数量超过剩余申请数量
            {
                return array('ret' => 0, 'code' => self::MSG_BORD_NUM_OVERFLOW);
            }

            $data['bord_imgs'] = isset($data['pic']) ? $data['pic'] : '';
            $data['order_id'] = $orderGoods['order_id'];
            $data['goods_name'] = $orderGoods['goods_name'];
            $data['goods_sn'] = $orderGoods['goods_sn'];
            $data['attrs_info'] = $orderGoods['attrs_info'];
            $data['attrs_barcode'] = $orderGoods['attrs_barcode'];
            $data['attrs_img'] = $orderGoods['attrs_img'];
            $data['tel'] = '';
            if(!empty($data['area_code']))
            {
                $data['tel'] .= $data['area_code'] . '-' . $data['tel_num'];
                if($data['ext'])
                {
                    $data['tel'] .= '-' . $data['ext'];
                }
            }

            $data['bord_sn'] = \Mall\Utils\Inputs::makeOrderSn();//生成售后编号

            $this->di['db']->begin();
            if($actRole == self::BORD_ACT_ROLE_BACKEND)
            {
                $bordId = $this->back->addBackOrderByAdmin($data['order_uid'], $data);
            }
            else
            {
                $bordId = $this->back->addBackOrder($uid, $data);
            }

            if(!$bordId)
            {
                $this->di['db']->rollback();
                return array('ret' => 0, 'code' => self::ERROR);
            }

            //该订单商品允许的售后数量已全部用完
            if(intval($data['goods_num']) + $gnum >= intval($orderGoods['ord_goods_num']))
            {
                //将该订单商品设为已售后
                if(!$og->setOrderGoodsBacked($data['order_goods_id']))
                {
                    $this->di['db']->rollback();
                    return array('ret' => 0, 'code' => self::ERROR);
                }
            }

            //写入售后日志
            if(!$orderLogModel->addBordLog($bordId, $actRole, $this->di['sysconfig']['supportActType']['apply'], $uid, $uname, '申请售后'))
            {
                $this->di['db']->rollback();
                return array('ret' => 0, 'code' => self::ERROR);
            }

            $this->di['db']->commit();
            return array('ret' => 1, 'id' => $bordId);
        }
    }

    /**
     * 获取指定状态的售后单,用于检查售后单状态是否正确
     *
     * @param $uid
     * @param $bordSn
     * @param $status
     * @return Array
     */
    public function getSpecifyBord($uid, $bordSn, $status)
    {
        return $this->back->getSpecifyBord($uid, $bordSn, $status);
    }

    /**
     * 添加快递信息到售后单
     *
     * @param $uid
     * @param $bordSn
     * @param $shipComp
     * @param $shipSn
     * @return boolean
     */
    public function addExpInfo($uid, $uname, $bordSn, $shipComp, $shipSn)
    {
        //验证售后单状态是否正确
        if($bordInfo = $this->getSpecifyBord($uid, $bordSn, self::BORD_STATUS_PASS_AUDIT))
        {
            if(isset($bordInfo['bord_shipping_sn']) && $bordInfo['bord_shipping_sn'])
            {
                //已经提交过快递单号
                return array('ret' => 0, 'code' => self::MSG_SHIP_RE_SUBMIT);
            }
            $this->di['db']->begin();
            if(!$this->back->addExpInfo($uid, $bordSn, $shipComp, $shipSn))
            {
                $this->di['db']->rollback();
                return array('ret' => 0, 'code' => self::ERROR);
            }
            if(!(new \Mall\Mdu\Models\OrderLogsModel())->addBackLog($bordInfo['bord_id'], $this->di['sysconfig']['supportActType']['applyExpress'], $uid, $uname, '提交快递单号'))
            {
                $this->di['db']->rollback();
                return array('ret' => 0, 'code' => self::ERROR);
            }
            $this->di['db']->commit();

            return array('ret' => 1);
        }
        else
        {
            return array('ret' => 0, 'code' => self::MSG_BORD_NONE);
        }
    }

    /**
     * [getOrderBySn 根据售后编号获取售后单信息]
     *
     * @param $uid
     * @param $bordSn
     * @return Array
     */
    public function getUserOrderBySn($uid, $bordSn)
    {
        return $this->back->getUserOrderBySn($uid, $bordSn);
    }

    public function getSupCnt($uid)
    {
        $info = $this->back->getSupCnt($uid);
        $info = $info[0]['cnt'];
        return $info;
    }
}
