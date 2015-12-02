<?php

namespace Mall\Mdu;

class OrderModule extends ModuleBase
{

    const ERROR = 10000; //操作失败
    const ERROR_PAY = 0; //操作失败
    const SUCCESS = 1; //操作成功
    const CONFIRM_ORDER =3 ;//确认收货
    const ORDER_EXPIRED = 1800;//订单超时/秒
    //const GOODS_NONE = 2; //商品可卖数小于1
    //const ATTR_NONE = 3; //没找到相应的属性
    //const ORDER_NONE = 4; //订单号不存在

    const ORDER_NONE = 10027;
    const ORDER_BATCH_ERROR = 10028;//请检查所有订单是否符合更改条件
    const GOODS_NONE = 10029;//此款商品已经售罄,请更换属性
    const ATTR_NONE = 10030;//没找到相应的属性
    const GOODS_NOT_EXISTS = 10032;//找不到对应商品
    const COUPONS_TIPS = -1;
    const COUPONS_STATUS = 1; //优惠规则状态

    private $order;
    private $orderGoods;
    private $goods;
    private $orderLogs;
    public function __construct()
    {
        $this->order = $this->initModel('\Mall\Mdu\Models\OrderModel');
        $this->orderGoods = $this->initModel('\Mall\Mdu\Models\OrdergoodsModel');
        $this->goods = $this->initModel('\Mall\Mdu\Models\GoodsModel');
        $this->orderLogs = $this->initModel('\Mall\Mdu\Models\OrderLogsModel');
    }

    /**
     * [获取订单总数]
     * @param  [type] $conditions [description]
     * @return [type]             [description]
     */
    public function getTotal($conditions = [])
    {
        return $this->order->getTotal($this->filterConditions($conditions))['num'];
    }

    /**
     * [orderList 获取订单列表]
     * @param  [type] $val  [获取方式的值]
     * @param  [type] $field [获取方式的索引,数据库字段]
     * @return [type]       [返回多维数组]
     */
    public function orderList($offset, $num, $conditions = [])
    {
        return $this->order->getAllOrders($offset, $num, $this->filterConditions($conditions));
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
            $fieldArr ['order_sn'] = "'" . $conditions['order_sn'] . "'";
        if(!empty($conditions['goods_name']))
            $fieldArr ['goods_name'] = "'" . $conditions['goods_name'] . "'";
        if(!empty($conditions['u_mobi']))
            $fieldArr ['u_mobi'] = "'" .$conditions['u_mobi'] . "'";
        if(!empty($conditions['order_consignee']))
            $fieldArr ['order_consignee'] = "'" . $conditions['order_consignee'] . "'";
        if(!empty($conditions['order_shipping_sn']))
            $fieldArr ['order_shipping_sn'] = "'" .$conditions['order_shipping_sn'] . "'";
        if(!empty($conditions['start_time']))
            $fieldArr['start_time'] = strtotime($conditions['start_time']);
        if(!empty($conditions['end_time']))
            $fieldArr['end_time'] = strtotime('+1 day', strtotime($conditions['end_time'])) - 1;
        if(isset($conditions['u_id']))
            $fieldArr['u_id'] = "'" . $conditions['u_id'] . "'";
        if(isset($conditions['act']) && $conditions['act'] == 'pendingPayment')
        {
            $fieldArr['order_pay_status'] = 1;
            $fieldArr['order_status'] = 1;
        }
        if(isset($conditions['act']) && $conditions['act'] == 'afterReceipt')
            $fieldArr['order_delivery_status'] = 3;
        if(!empty($conditions['orderStatus']))
        {
            switch ($conditions['orderStatus'])
            {
                case 1:
                    $fieldArr['order_status'] = 1;
                    break;
                case 3:
                    $fieldArr['order_status'] = 3;
                    break;
                case 5:
                    $fieldArr['order_pay_status'] = 1;
                    break;
                case 7:
                    $fieldArr['order_pay_status'] = 3;
                    $fieldArr['order_delivery_status'] = 1;
                    break;
                case 9:
                    $fieldArr['order_pay_status'] = 3;
                    break;
                case 11:
                    $fieldArr['order_delivery_status'] = 3;
                    break;
                case 13:
                    $fieldArr['order_pay_status'] = 5;
                    break;
                case 15:
                    $fieldArr['order_pay_status'] = 7;
                    break;
            }
        }
        return $fieldArr;
    }

    /**
     * [根据订单id获取订单信息]
     * @param  [string] $orderId [订单id]
     * @return [type]          [description]
     */
    public function getOrderInfo($orderId)
    {
        return $this->order->getOrderByOrderid($orderId);
    }

    /**
     * [根据订单id获取订单详情]
     * @param  [string] $orderId [订单id]
     * @return [array]          [description]
     */
    public function getOrderGoods($orderId)
    {
        return $this->orderGoods->getOrderGoodsByOrderid($orderId);
    }

    /**
     * [获取商品的某个大属性的所有值]
     * @param  [商品id] $goodsId [商品id]
     * @return [array]          [返回该商品所有的颜色]
     */
    public function getGoodsInfo($goodsId)
    {
        $goodsInfo = $this->goods->getGoodsInfo($goodsId);
        if(!empty($goodsInfo))
        {
            $attrs = explode(',', $goodsInfo['goods_attrs']);
            $colorID = $attrs[0];
            $sizeID = $attrs[1];
            $goodsInfo['allColor'] = $this->goods->getGoodsAttrs($goodsId, $colorID);
            $goodsInfo['allSize'] = $this->goods->getGoodsAttrs($goodsId, $sizeID);
            unset($goodsInfo['goods_attrs']);
        }
        return $goodsInfo;
    }

    /**
     * [获取订单状态]
     * @param  [type] $orderId [订单id]
     * @return [type]          [description]
     */
    public function getOrderStatus($orderId, $status, $val)
    {
        $orderStatus = $this->order->getOrderStatus($orderId);
        return $orderStatus[$status] == $val ? TRUE: FALSE;
    }

    /**
     * [获取订单状态]
     * @param  [type] $orderId [订单id]
     * @return [type]          [description]
     */
    public function getOrderStatusById($orderId)
    {
        return $this->order->getOrderStatus($orderId);
    }

    /**
     * [修改订单信息]
     * @param [type] $mobi      [description]
     * @param [type] $provice   [description]
     * @param [type] $city      [description]
     * @param [type] $district  [description]
     * @param [type] $street    [description]
     * @param [type] $addr      [description]
     * @param [type] $consignee [description]
     * @param [type] $orderId   [description]
     * @return [string]          [0,1]
     */
    public function setOrderInfo($mobi, $shippingType, $provice, $city, $district, $street, $addr, $consignee, $orderId)
    {
        $data = array(
                'mobi' => $mobi,
                'shippingType' => $shippingType,
                'province' => $provice,
                'city' => $city,
                'district' => $district,
                'street' => $street,
                'addr' => $addr,
                'consignee' => $consignee
            );
        if($this->order->updateOrder($data, $orderId))
            return self::SUCCESS;
        else
            return self::ERROR;
    }

    /**
     * [根据商品id获取商品属性]
     * @param  [string] $goodsId [商品id]
     * @return [array]          [主要返回商品的大属性id，比如颜色，尺码..]
     */
    public function getGoodsAttr($goodsId)
    {
        return $this->goods->getGoodsInfo($goodsId);
    }

    /**
     * [修改订单详情资料,比如修改尺码和价格]
    * @param [string] $orderGoodsId [订单商品id]
     * @param [string] $goodsId [商品id]
     * @param [string] $colorId [颜色id]
     * @param [string] $sizeId [尺码id]
     * @param [string] $price   [价格]
     * @return [string]          [返回该商品所有的颜色]
     */
    public function setOrderGoods($orderGoodsId, $orderId, $goodsId, $colorId,$sizeId, $price, $goodsNum)
    {
        //必须颜色id在前,尺码id在后进行查询
        $goodsNums = $this->goods->getGoodsNum($goodsId, $colorId, $sizeId);
        if(!empty($goodsNums) && $goodsNums['g_attr_nums'] > 0)
        {
            $colorAttr = $this->goods->getAttrName($goodsId, $colorId);
            $sizeAttr = $this->goods->getAttrName($goodsId, $sizeId);
            if(!empty($colorAttr) && !empty($sizeAttr))
            {
                $attrCode = array(
                    array('id' => $sizeAttr['attrs_id'], 'name' => $sizeAttr['attrs_name']),
                    array('id' => $colorAttr['attrs_id'], 'name' => $colorAttr['attrs_name'])
                );
                if($this->orderGoods->updateOrderGoods($orderGoodsId, $orderId, $goodsId, $price, json_encode($attrCode), $goodsNum))
                    return self::SUCCESS;
                else
                    return self::ERROR;
            }
            else
            {
                return self::ATTR_NONE;
            }
        }
        else
        {
            return self::GOODS_NONE;
        }
    }

    /**
     * [修改订单状态]
     * @param  [string] $orderId        [订单id]
     * @param  [string] $operate [要执行的操作代码, 在视图中进行规定]
     * @return [string]                 [返回状态码]
     */
    public function editStatus($orderId, $operate)
    {
        $status = $this->order->getOrderStatus($orderId);
        if(empty($status))
        {
            return self::ORDER_NONE;
        }
        if($operate == 'invalid' && $status['order_status'] == 0 &&
            $status['order_pay_status'] == 1 && $status['order_delivery_status'] == 1)
        {
            //失效操作：未付款未发货的订单
            return $this->retCode($this->order->setOrderStatus($orderId, 'order_status', 3));
        }
        elseif($operate == 'applyBack' && $status['order_status'] == 0 &&
            $status['order_pay_status'] == 3 &&
            ($status['order_delivery_status'] == 1 || $status['order_delivery_status'] == 3))
        {
            //申请退款：已付款的订单
            return $this->retCode($this->order->setOrderStatus($orderId, 'order_pay_status', 5));
        }
        elseif($operate == 'deliver' && $status['order_status'] == 0 &&
            $status['order_pay_status'] == 3 && $status['order_delivery_status'] == 1)
        {
            //发货：已付款但未发货的订单
            return $this->retCode($this->order->setOrderStatus($orderId, 'order_delivery_status', 3));
        }
        elseif($operate == 'orderSuccess' && $status['order_status'] == 0 &&
            $status['order_pay_status'] == 3 && $status['order_delivery_status'] == 3)
        {
            //交易成功：已付款已发货的订单
            return $this->retCode($this->order->setOrderStatus($orderId, 'order_status', 1));
        }
        elseif($operate == 'orderClose' && $status['order_pay_status'] == 7 &&
            ($status['order_delivery_status'] == 1 || $status['order_delivery_status'] == 3))
        {
            //交易关闭：退款成功的订单
            return $this->retCode($this->order->setOrderStatus($orderId, 'order_status', 3));
        }
    }

    /**
     * [批量修改订单状态]
     * @param  [string] $orderIds [一组用','相连的订单id字符串]
     * @param  [string] $operate [要执行的操作代码, 在视图中进行规定]
     * @return [type]           [description]
     */
    public function batchEditStatus($orderIds, $operate)
    {
        $orderIds = explode(',', rtrim($orderIds, ','));
        foreach ($orderIds as $val) {
            $ids[] = intval($val);
        }
        $totalNum = count($ids);
        $allStatus = $this->order->getMultiOrderStatus(implode(',', $ids));
        if(empty($allStatus))
        {
            return array(self::ORDER_NONE);
        }

        $enableIds = array();
        switch ($operate)
        {
            case 'batchInvalid':
                foreach($allStatus as $vals)
                {
                    if($vals['order_pay_status'] == 1 && $vals['order_status'] == 1)
                    {
                        $enableIds[] = $vals['order_id'];
                    }
                }
                $toField = 'order_status';
                $toStatus = 5;
                break;
            case 'batchDeliver':
                foreach($allStatus as $vals)
                {
                    if($vals['order_pay_status'] == 3 && $vals['order_delivery_status'] == 1)
                    {
                        $enableIds[] = $vals['order_id'];
                    }
                }
                $toField = 'order_delivery_status';
                $toStatus = 3;
                break;
            case 'batchApplyBack':
                foreach($allStatus as $vals)
                {
                    if($vals['order_pay_status'] == 3 &&
                        $vals['order_status'] == 0 &&
                        ($vals['order_delivery_status'] == 1 || $vals['order_delivery_status'] == 3))
                    {
                        $enableIds[] = $vals['order_id'];
                    }
                }
                $toField = 'order_pay_status';
                $toStatus = 5;
                break;
            case 'batchOrderSuccess':
                foreach($allStatus as $vals)
                {
                    if($vals['order_status'] == 3 && $vals['order_pay_status'] == 3 && $vals['order_delivery_status'] == 5)
                    {
                        $enableIds[] = $vals['order_id'];
                    }
                }
                $toField = 'order_status';
                $toStatus = 11;
                break;
            case 'batchOrderClose':
                foreach($allStatus as $vals)
                {
                    if($vals['order_pay_status'] == 7 &&
                        ($vals['order_delivery_status'] == 1 || $vals['order_delivery_status'] == 3))
                    {
                        $enableIds[] = $vals['order_id'];
                    }
                }
                $toField = 'order_status';
                $toStatus = 3;
                break;
        }

        if(empty($enableIds))
        {
            return array(self::ORDER_BATCH_ERROR);
        }
        else
        {
            $enableNum = sizeof($enableIds);
            if($enableNum != $totalNum)
            {
                return array(self::ORDER_BATCH_ERROR);
            }
            if($this->order->setMultiOrderStatus($enableIds, $toField, $toStatus))
                return array(self::SUCCESS, $enableNum);
            else
               return array(self::ERROR);
        }
    }

    /**
     * [根据商品名称返回商品信息]
     * @param  [string] $goodsName [商品名称]
     * @return [array]            [返回商品的相关数据]
     */
    public function showGoodsByName($goodsName)
    {
        $goodsInfo = $this->goods->getGoodsByName($goodsName);
        if(!empty($goodsInfo) && $goodsInfo['goods_attrs'])
        {
            $attrs = explode(',', $goodsInfo['goods_attrs']);
            $colorID = $attrs[0];
            $sizeID = $attrs[1];
            $goodsInfo['allColor'] = $this->goods->getGoodsAttrs($goodsInfo['goods_id'], $colorID);
            $goodsInfo['allSize'] = $this->goods->getGoodsAttrs($goodsInfo['goods_id'], $sizeID);
            unset($goodsInfo['goods_attrs']);
            return array(1, $goodsInfo);
        }
        else
        {
            return array(self::GOODS_NOT_EXISTS);
        }
    }

    /**
     * [查找某款商品是否有库存]
     * @param  [string] $goodsId [商品id]
     * @param  [string] $colorId [颜色id]
     * @param  [string] $sizeId  [尺码id]
     * @return [type]          [description]
     */
    public function checkGoods($goodsId, $colorId, $sizeId)
    {
        $goodsNum = $this->goods->checkGoods($goodsId, $colorId, $sizeId);
        if(!empty($goodsNum) && $goodsNum['g_attr_nums'] > 0)
        {
            return self::SUCCESS;
        }
        else
        {
            return self::GOODS_NONE;
        }
    }

    /**
     * [创建新订单]
     * @param  [array] $goodsData    [订单商品详情]
     * @param  [string] $consignee    [收货人姓名]
     * @param  [string] $mobi         [收货人手机]
     * @param  [string] $province     [省id]
     * @param  [string] $city         [市id]
     * @param  [string] $district     [县id]
     * @param  [string] $street       [街道id]
     * @param  [string] $addr       [具体地址]
     * @param  [string] $shippingFee  [快递金额]
     * @param  [string] $totalFee     [总金额]
     * @return [string]               [返回结果标志]
     */
    public function createOrder($goodsData, $consignee, $mobi, $province, $city, $district, $street, $addr, $shippingFee, $totalFee)
    {
        if(!is_array($goodsData) && empty($goodsData))
        {
            return array(self::GOODS_NONE);
        }
        $num = count($goodsData);
        foreach ($goodsData as $k => $val)
        {
            $goodsIds[$k] = $goodsAttrs[$k]['goodsId'] = $val['goodsId'];
            $goodsAttrs[$k]['goodsAttr'] = intval($val['colorId']) . ',' . intval($val['sizeId']);
            $colors[] = array('goodsId' => $val['goodsId'], 'attrId' => $val['colorId']);
            $sizes[] = array('goodsId' => $val['goodsId'], 'attrId' => $val['sizeId']);
        }

        $colorAttrs = $this->goods->getAttrNames($colors);
        $sizeAttrs = $this->goods->getAttrNames($sizes);
        $colorNum = count($colorAttrs);
        $sizeNum = count($sizeAttrs);

        //获取每条数据的颜色和尺码
        for($k = 0; $k < $num; $k++)
        {
            for($m = 0; $m < $colorNum; $m++)
            {
                if($goodsData[$k]['goodsId'] == $colorAttrs[$m]['goods_id'] && $goodsData[$k]['colorId'] == $colorAttrs[$m]['attrs_id'])
                {
                    $goodsData[$k]['colorName'] = $colorAttrs[$m]['attrs_name'];
                    break;
                }
            }

            for($n = 0; $n < $sizeNum; $n++)
            {
                if($goodsData[$k]['goodsId'] == $sizeAttrs[$n]['goods_id'] && $goodsData[$k]['sizeId'] == $sizeAttrs[$n]['attrs_id'])
                {
                    $goodsData[$k]['sizeName'] = $sizeAttrs[$n]['attrs_name'];
                    break;
                }
            }

            $goodsData[$k]['attrs_info'] = json_encode(
                array(
                    array('id' => $goodsData[$k]['colorId'], 'name' => $goodsData[$k]['colorName']),
                    array('id' => $goodsData[$k]['sizeId'], 'name' => $goodsData[$k]['sizeName'])
                )
            );
        }

        //获取商品详情，比如尺码颜色属性，售价库存等等
        $allAttrs = $this->goods->batchCheckNums($goodsAttrs);
        //取属性图片
        $attrsImgs = $this->goods->getAttrsImg($goodsAttrs);

        $cou = count($allAttrs);
        if($num != $cou)
        {
            return array(self::ATTR_NONE);
        }
        //获取商品的信息，比如货号商品名等等
        $allInfos = $this->goods->batchGoodsInfo($goodsIds);

        if(empty($allInfos))
        {
            return array(self::GOODS_NONE);
        }
        $infoNum = count($allInfos);

        for($i = 0; $i < $cou; $i++)
        {
            $value = $allAttrs[$i];
            $col = explode(',', $value['attrs_ids'])[0];
            $allAttrs[$i]['attrs_img'] = $attrsImgs[$i][$col];

            for($j = 0; $j < $infoNum; $j++)
            {
                if($allAttrs[$i]['goods_id'] == $allInfos[$j]['goods_id'])
                {
                    $allAttrs[$i]['goods_sn'] = $allInfos[$j]['goods_sn'];
                    $allAttrs[$i]['goods_name'] = $allInfos[$j]['goods_name'];
                    $allAttrs[$i]['goods_market'] = $allInfos[$j]['goods_market'];
                    $allAttrs[$i]['goods_price'] = $allInfos[$j]['goods_price'];
                    break;
                }
            }
            for($k = 0; $k < $num; $k++)
            {
                if($allAttrs[$i]['goods_id'] == $goodsData[$k]['goodsId'] &&
                $allAttrs[$i]['attrs_ids'] == ($goodsData[$k]['colorId'] . ',' . $goodsData[$k]['sizeId']))
                {
                    $allAttrs[$i]['goods_num'] = $goodsData[$k]['goodsNum'];
                    $allAttrs[$i]['attrs_info'] = $goodsData[$k]['attrs_info'];
                    break;
                }
            }
        }

        $this->di['db']->begin();
        if($orderId = $this->order->createOrder(
                \Mall\Utils\Inputs::makeOrderSn(),
                $consignee,
                $mobi,
                $province,
                $city,
                $district,
                $street,
                $addr,
                $shippingFee,
                $totalFee,
                $_SERVER['REQUEST_TIME']
            )
        )
        {
            if($this->orderGoods->createOrderGoods($orderId, $allAttrs))
            {
                $this->di['db']->commit();
                return array(self::SUCCESS, $orderId);
            }
            else
            {
                $this->di['db']->rollback();
                return array(self::ERROR);
            }
        }
        else
        {
            $this->di['db']->rollback();
            return array(self::ERROR);
        }
    }

    /**
     * 根据订单sn获取订单信息
     *
     * @param $orderSn
     * @return Array
     */
    public function getOrderBySn($orderSn)
    {
        return $this->order->getOrderBySn($orderSn);
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

    /**----------------------------------------------------------------------前端---------------------------------------------------------------------**/

    /**
     * 创建订单(立即购买)
     */
    public function addBuyNowOrder($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo,
        $shipFee, $isinv, $invType, $invTitle, $addTime, $addr, $goodsDates, $buyNum, $codeFlag)
    {
        $this->di['db']->begin();

        //判断是否使用云码
        if($codeFlag == 1)
        {
            //使用云码创建订单
            if(!$orderId = $this->order->addOrderByCode($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo, $shipFee, $isinv,$invType,
                $invTitle, $addTime, $addr['pro_id'], $addr['city_id'], $addr['dis_id'], $addr['u_addr_info'],
                $addr['u_addr_zipcode'], $addr['u_addr_consignee'], $addr['u_addr_mobile'], $addr['u_addr_tel']))
            {
                $this->di['db']->rollback();
                return self::ERROR;
            }
            //更改云码状态
            $codeInfo = $this->di['session']->get('codeInfo');
            if(!(new \Mall\Mdu\Models\CodesModel)->setCodeUsed($uid, $orderId, $codeInfo['yc_id'], $_SERVER['REQUEST_TIME']))
            {
                $this->di['db']->rollback();
                return self::ERROR;
            }
            //生成支付日志
            (new \Mall\Mdu\OrderLogsModule())->addOrderLogBySn($orderSn, 17, $uid, '用户', '支付成功');
        }
        else
        {
            //创建订单
            if(!$orderId = $this->order->addOrder($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo, $shipFee, $isinv,$invType,
                $invTitle, $addTime, $addr['pro_id'], $addr['city_id'], $addr['dis_id'], $addr['u_addr_info'],
                $addr['u_addr_zipcode'], $addr['u_addr_consignee'], $addr['u_addr_mobile'], $addr['u_addr_tel']))
            {
                $this->di['db']->rollback();
                return self::ERROR;
            }
        }
        //商品属性
        $attrsIds = explode(',', $goodsDates['attrs_id']);
        $attrsInfo = json_encode([['id' => $attrsIds[0], 'name' => $goodsDates['col']], ['id' => $attrsIds[1], 'name' => $goodsDates['size']]]);

        if(!$this->order->addOrderGoods($orderId, $goodsDates['goods_id'], $goodsDates['goods_name'],
            $goodsDates['goods_sn'], $goodsDates['goods_market'], $goodsDates['goods_price'],
            $goodsDates['g_attr_barcode'], $goodsDates['goods_img'], $goodsDates['goods_type'],
            $buyNum, $attrsInfo
        ))
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }

        //暂时采用下单即扣库存
        if(!$this->order->decrGoodsNum($goodsDates['goods_id'], $goodsDates['attrs_id'], $buyNum))
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }

        $this->di['db']->commit();        

        unset($_SESSION['codeInfo']);
        $this->di['session']->set('order_id', $orderId);
        \Mall\Utils\RedisLib::getRedis($this->di)->del('item:' . $goodsDates['goods_id'] . ':' . $goodsDates['attrs_id']);
        return self::SUCCESS;
    }

    /**
     * 创建订单(购物车购买)
     */
    public function addBuyCartOrder($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo,
    $shipFee, $isinv, $invType, $invTitle, $addTime,$addr, $goodsDates, $attrsIds, $goodsIds)
    {
        $this->di['db']->begin();
        //创建订单
        if(!$orderId = $this->order->addOrder($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo, $shipFee, $isinv,$invType,
            $invTitle, $addTime, $addr['pro_id'], $addr['city_id'], $addr['dis_id'], $addr['u_addr_info'],
            $addr['u_addr_zipcode'], $addr['u_addr_consignee'], $addr['u_addr_mobile'], $addr['u_addr_tel']))
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }
        //添加订单商品
        if(!$this->order->addMulOrderGoods($orderId, $goodsDates))
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }

        //删除已下单的购物车商品
        if(!(new \Mall\Mdu\Models\CartModel())->mulDelCart($uid, $goodsIds, $attrsIds))
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }

        //暂时采用下单即扣库存
        if(!$this->order->decrGoodsNums($goodsIds, $attrsIds, $goodsDates))
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }

        $this->di['db']->commit();
        //更新购物车商品总数量
        $this->di['session']->set('n', intval($this->di['session']->get('n')) - count($goodsIds));
        $this->di['session']->set('order_id', $orderId);
        foreach($goodsIds as $k => $gid)
            \Mall\Utils\RedisLib::getRedis($this->di)->del('item:' . $goodsIds[$k] . ':' . $attrsIds[$k]);
        return self::SUCCESS;
   }

    /**
     * 根据订单Id获取指定用户的该订单信息
     *
     * @param $uid
     * @param $orderSn
     * @param $status
     */
    public function getSpecifyOrder($uid, $orderSn, $status)
    {
        return $this->order->getSpecifyOrder($uid, $orderSn, $status);
    }

    /**
     * 根据订单Id获取订单的详细信息
     *
     * @param $uid
     * @param $orderSn
     * @param $status
     */
    public function getOrderSec($uid, $orderId)
    {
        return $this->order->getOrderSec($uid, $orderId);
    }

    /**
     * [根据订单id获取订单信息]
     * @param  [string] $orderId [订单id]
     * @return [type]          [description]
     */
    public function getFeeByUidOid($uid, $orderId)
    {
        return $this->order->getFeeByUidOid($uid, $orderId);
    }

    /**
     * [orderList 获取我的订单列表]
     * @param  [type] $val  [获取方式的值]
     * @param  [type] $field [获取方式的索引,数据库字段]
     * @return [type]       [返回多维数组]
     */
    public function myOrderList($offset, $num, $conditions = [])
    {
        $arr = array();
        $orders = $this->order->getMyOrders($offset, $num, $this->filterConditions($conditions));

        foreach($orders as $k => $v)
        {
            $arr[$k] = $v;
            $arr[$k]['goods'] = $this->orderGoods->getOrderGoodsByOrderid($v['order_id']);
        }

        return $arr;
    }

    /**
     * [getOrderDetail 订单详情]
     * @param  [type] $sn  [description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getOrderDetail($sn,$uid)
    {
        return $this->order->getOrderInfoBySn($sn, $uid);
    }

    /**
     * [confirmReceipt 确认收货]
     * @param  [type] $sn  [description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function confirmReceipt($sn, $uid)
    {
        $res = $this->order->getOrderInfoBySn($sn,$uid);
        if($res)
        {
            if($res['order_delivery_status'] == self::CONFIRM_ORDER)
            {
                $this->orderLogs->addOrderLog($res['order_id'], $this->di['sysconfig']['orderActType']['received'], $uid, '', '确认收货');
                return $this->order->setStatusDeliBySn($sn , $uid);
            }
            else
            {
                return self::ERROR;
            }
        }
        else
            return self::ERROR;
    }

     /* //todo 未去商品库存 @L
     * [支付成功通过ordersn uid修改订单状态]
     * @param  [string] $orderSn [订单sn]
     * @return [type]          [description]
     */
    public function setOrderStatus($orderSn, $paytime)
    {
        if($this->order->uptOrderStatus($orderSn, $paytime))
        {
            //通过sn获取uid
            if($uid = $this->order->getUidBySn($orderSn)['u_id'])
                return $uid;
            else
                return self::ERROR_PAY;
        }
        else
            return self::ERROR_PAY;
    }

    /**
     * [getCpsTips 通过优惠券ID或者优惠券sn兑换码获取优惠金额]
     * @param  [type] $cpsType [description]
     * @param  [type] $cpsVal  [description]
     * @return [type]          [description]
     */
    public function getCpsTips($cpsType, $cpsVal, $uid)
    {
        $cps = new \Mall\Mdu\Models\CouponsModel();
        //选择优惠券 val 为 id
        if($cpsType == 1)
        {
            $res = $cps->getCpsTipsById($cpsVal, self::COUPONS_STATUS, $_SERVER['REQUEST_TIME'], $uid);
            if(!$res)
                return self::COUPONS_TIPS;
            else
                return $res;
        }
        //输入优惠券兑换码 val 为sn码
        if($cpsType == 3)
        {
            $res = $cps->getCouponsBySn($cpsVal, self::COUPONS_STATUS, $_SERVER['REQUEST_TIME']);
            if(!$res)
                return self::COUPONS_TIPS;
            else
                return $res;
        }
    }

    /**
     * [setCpsStatus 下单成功 更该优惠券状态]
     * @param [type] $cpsType [description]
     * @param [type] $cpId    [description]
     * @param [type] $uid     [description]
     */
    public function setCpsStatus($cpsType, $cpId, $uid, $useTime)
    {
        $cps = new \Mall\Mdu\Models\CouponsModel();

        if($cpsType == 1)
        {
            return $cps->setCpsStatusById($cpId, $uid);
        }
        if($cpsType == 3)
        {
            return $cps->setCpsStatusBySn($cpId, $useTime);
        }
    }

    /**
     * [getExOids 获取超时未支付的订单]
     * @return [type] [description]
     */
    public function getExOids($exTime)
    {
        return $this->order->getExOids($exTime);
    }

    /**
     * [getGidsAids 通过获取到的过期的订单IDS获取对应订单下的商品]
     * @return [type] [description]
     */
    public function getGidsAids($oids)
    {
        return $this->order->getGidsAids($oids);
    }

    /**
     * [uptGoodsNums 批量更新组合商品库存]
     * @return [type]              [description]
     */
    public function uptGoodsNums($exOids, $gids, $aids, $nums)
    {
        //订单状态置为取消
        $this->di['db']->begin();
        if(!$this->order->batchSetOrderStatus($exOids))
            $this->di['db']->rollback();

        //批量增加组合商品的数量
        if(!$this->order->uptGoodsNums($gids, $aids, $nums))
            $this->di['db']->rollback();

        $this->di['db']->commit();
    }

    public function getPendPayCnt($uid)
    {
        $info = $this->order->getPendPayCnt($uid);
        $info = $info[0]['cnt'];
        return $info;
    }
}
