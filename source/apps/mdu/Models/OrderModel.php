<?php

namespace Mall\Mdu\Models;

class OrderModel extends ModelBase
{

    public function getOrderList()
    {
        $query = $this->db->query('SELECT * FROM `cloud_orders` ORDER BY `order_addtime` DESC');
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [获取符合条件订单总数]
     *
     * @param [array] $conditions [条件数组]
     * @return int
     */
    public function getTotal($conditions = [])
    {
        $where = '';
        if(!empty($conditions)){
            $where = 'WHERE';
            foreach($conditions as $key=>$condition)
            {
                if($key == 'start_time')
                {
                    $where .= ' order_addtime >=' . $condition . ' AND';
                }
                else if($key == 'end_time')
                {
                    $where .= ' order_addtime <=' . $condition . ' AND';
                }
                else
                {
                    $where .= " $key=$condition AND";
                }
            }
            $where = rtrim($where, 'AND');
        }
        return $this->db->query("SELECT COUNT(*) num FROM `cloud_orders` $where")->fetch();
    }

    /**
     * [getAllBackOrders 获取所有订单数据]
     *
     * @param int $offset
     * @param int $num
     * @param Array $conditions
     * @return Array
     */
    public function getAllOrders($offset, $num, $conditions = [])
    {
        $where = '';
        if(!empty($conditions)){
            $where = 'WHERE';
            foreach($conditions as $key=>$condition)
            {
                if($key == 'start_time')
                {
                    $where .= ' order_addtime >=' . $condition . ' AND';
                }
                else if($key == 'end_time')
                {
                    $where .= ' order_addtime <=' . $condition . ' AND';
                }
                else
                {
                    $where .= " $key = $condition AND";
                }
            }
            $where = rtrim($where, 'AND');
        }
        $query = $this->db->query(
            'SELECT * FROM cloud_orders '. $where .
            ' ORDER BY `order_addtime` DESC LIMIT ?,?',
            array(
                $offset,
                $num
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [getOrderListByType 通过u_id,u_mobi等方式获取订单列表]
     * @param  [type] $val  [查询参数]
     * @param  [type] $type [方式]
     * @return [type]       [description]
     */
    public function getOrderListByType($val,$type)
    {
        $query = $this->db->query("SELECT * FROM cloud_orders WHERE $type= ? ORDER BY `order_addtime` DESC",
            array(
                $val
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [getOrderListByType 通过订单ID获取单条订单]
     * @param  [type] $val  [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function getOrderByOrderid($orderId)
    {
        $query = $this->db->query('SELECT * FROM cloud_orders WHERE order_id = ? LIMIT 1',
            array(
                $orderId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
     }

    /**
     * 根据订单sn获取订单信息
     *
     * @param $orderSn
     * @param $status
     * @return Array
     */
    public function getSpecifyOrderBySn($orderSn, $status)
    {
        $query = $this->db->query('SELECT * FROM `cloud_orders` WHERE `order_sn` = ? AND `order_status`=? LIMIT 1',
            array(
                $orderSn,
                $status
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * 根据订单sn获取订单信息
     *
     * @param $orderSn
     * @return Array
     */
    public function getOrderBySn($orderSn)
    {
        $query = $this->db->query('SELECT * FROM `cloud_orders` WHERE `order_sn` = ? LIMIT 1',
            array(
                $orderSn,
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * 根据订单sn获取uid
     *
     * @param $orderSn
     * @return Array
     */
    public function getUidBySn($orderSn)
    {
        $query = $this->db->query('SELECT `u_id` FROM `cloud_orders` WHERE `order_sn` = ? LIMIT 1',
            array(
                $orderSn,
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

     /**
      * [获取订单状态]
      * @param  [type] $orderId [订单id]
      * @return [type]          [description]
      */
    public function getOrderStatus($orderId)
    {
        $query = $this->db->query('SELECT order_status, order_delivery_status,'.
            'order_pay_status FROM cloud_orders WHERE order_id = ? LIMIT 1',
            array(
                $orderId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
     }

     /**
      * [修改订单信息]
      * @param  [type] $data    [description]
      * @param  [type] $orderId [description]
      * @return [type]          [description]
      */
    public function updateOrder($data, $orderId)
    {
        return $this->db->execute('UPDATE `cloud_orders` SET `order_mobile` = ? , `order_shipping_type` = ?, `order_province` = ?, `order_city` = ?, `order_district` = ?, `order_street` = ?,
            `order_addr` = ?, `order_consignee` =? WHERE order_id = ?',
            array(
                $data['mobi'],
                $data['shippingType'],
                $data['province'],
                $data['city'],
                $data['district'],
                $data['street'],
                $data['addr'],
                $data['consignee'],
                $orderId
            )
        );
    }

    /**
     * [更改订单状态]
     * @param [string] $orderId [订单id]
     * @param [string] $field   [要更新的数据库字段名]
     * @param [string] $value   [更新值]
     */
    public function setOrderStatus($orderId, $field, $value)
    {
        return $this->db->execute("UPDATE `cloud_orders` SET $field = ?".
            "WHERE order_id = ?",
            array($value, $orderId)
        );
    }

     /**
      * [获取订单状态]
      * @param  [type] $orderIds [订单ids]
      * @return [type]          [description]
      */
    public function getMultiOrderStatus($orderIds)
    {
        $query = $this->db->query("SELECT order_id, order_status,".
            "order_delivery_status, order_pay_status FROM cloud_orders WHERE order_id in($orderIds)");
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [批量修改订单状态]
     * @param [type] $orderIds [description]
     * @param [type] $field    [description]
     * @param [type] $value    [description]
     */
    public function setMultiOrderStatus($orderIds, $field, $value)
    {
        $ids = '';
        foreach($orderIds as $id)
        {
            $ids .= intval($id) . ',';
        }
        $ids = rtrim($ids, ',');
        return $this->db->execute("UPDATE `cloud_orders` SET $field = ? ".
            "WHERE order_id in($ids)",
            array($value)
        );
    }

    /**
     * [创建新订单]
     * @return [bool|string] [添加失败返回false，成功返回id]
     */
    public function createOrder($orderSn, $consignee, $mobi, $province,
        $city, $district, $street, $addr, $shippingFee, $totalFee, $addTime)
    {
        if($this->db->execute('INSERT INTO `cloud_orders` (`order_sn`, `order_consignee`, '.
            '`order_mobile`, `order_province`, `order_city`, `order_district`, `order_street`,'.
            '`order_addr`, `order_shipping_fee`, `order_total`, `order_addtime`) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
            array(
                $orderSn,
                $consignee,
                $mobi,
                $province,
                $city,
                $district,
                $street,
                $addr,
                $shippingFee,
                $totalFee,
                $addTime
                )
            )
        )
            return $this->db->lastInsertId();
        else
            return FALSE;
    }
    /**----------------------------------------------------------------------前端---------------------------------------------------------------------**/

    public function addOrder($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo, $shipFee, $isinv,$invType,
        $invTitle, $addTime, $proId, $cityId, $disId, $addrInfo, $zipcode, $consignee, $addrMobi, $tel)
    {
        if($this->db->execute('INSERT INTO `cloud_orders` (`order_sn`, `u_id`, `u_mobi`, `order_consignee`, `order_zipcode`, '.
            '`order_mobile`, `order_tel`, `order_province`, `order_city`, `order_district`, `order_addr`, '.
            '`order_shipping_fee`, `order_total`, `order_paied`, `order_pay_off`, `order_addtime`, `order_is_inv`, `order_inv_type` ' .
            ', `order_inv_title`, `order_buyer_msg` ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $orderSn,
                $uid,
                $mobi,
                $consignee,
                $zipcode,
                $addrMobi,
                $tel,
                $proId,
                $cityId,
                $disId,
                $addrInfo,
                $shipFee,
                $totalFee,
                $paiedFee,
                $payoffFee,
                $addTime,
                $isinv,
                $invType,
                $invTitle,
                $memo
              )
        ))
            return $this->db->lastInsertId();
        else
            return FALSE;
    }

    public function addOrderByCode($uid, $mobi, $orderSn, $totalFee, $paiedFee, $payoffFee, $memo, $shipFee, $isinv,$invType,
        $invTitle, $addTime, $proId, $cityId, $disId, $addrInfo, $zipcode, $consignee, $addrMobi, $tel)
    {
        if($this->db->execute('INSERT INTO `cloud_orders` (`order_sn`, `u_id`, `u_mobi`, `order_consignee`, `order_zipcode`, '.
            '`order_mobile`, `order_tel`, `order_province`, `order_city`, `order_district`, `order_addr`, '.
            '`order_shipping_fee`, `order_total`, `order_paied`, `order_pay_off`, `order_addtime`, `order_is_inv`, `order_inv_type` ' .
            ', `order_inv_title`, `order_buyer_msg`, `order_pay_status`, `order_pay_time`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $orderSn,
                $uid,
                $mobi,
                $consignee,
                $zipcode,
                $addrMobi,
                $tel,
                $proId,
                $cityId,
                $disId,
                $addrInfo,
                $shipFee,
                $totalFee,
                $paiedFee,
                $payoffFee,
                $addTime,
                $isinv,
                $invType,
                $invTitle,
                $memo,
                3,
                $addTime
              )
        ))
            return $this->db->lastInsertId();
        else
            return FALSE;
    }

    public function addOrderGoods($orderId, $goodsId, $goodsName, $goodsSn, $market, $price,
        $barcode, $img, $type, $buyNum, $attrsInfo)
    {
        return $this->db->execute('INSERT INTO `cloud_order_goods` (`order_id`, `goods_id`, '.
            '`goods_name`, `goods_sn`, `goods_market`, `ord_goods_price`, `attrs_info`, `attrs_barcode`, '.
            '`attrs_img`, `ord_goods_num`, `ord_goods_type`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $orderId,
                $goodsId,
                $goodsName,
                $goodsSn,
                $market,
                $price,
                $attrsInfo,
                $barcode,
                $img,
                $buyNum,
                $type
            )
        );
    }


    public function addMulOrderGoods($orderId, $goodsDates)
    {
        $conditions = '';
        foreach($goodsDates as $g)
        {
            //商品属性
            $attrsIds = explode(',', $g['attrs_ids']);
            $attrsInfo = str_replace("\\u", "\\\u", json_encode([['id' => $attrsIds[0], 'name' => $g['col']], ['id' => $attrsIds[1], 'name' => $g['size']]]));
            $conditions .= ",($orderId, $g[goods_id], '$g[goods_name]', '$g[goods_sn]', $g[goods_market],
                $g[goods_price], '$attrsInfo' , '$g[g_attr_barcode]', '$g[goods_img]', $g[car_good_num],
                $g[goods_type])";
        }

        return $this->db->execute('INSERT INTO `cloud_order_goods` (`order_id`, `goods_id`, '.
            '`goods_name`, `goods_sn`, `goods_market`, `ord_goods_price`, `attrs_info`, `attrs_barcode`, '.
            '`attrs_img`, `ord_goods_num`, `ord_goods_type`) VALUES' . ltrim($conditions, ',')
        );
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

        $query = $this->db->query('SELECT `order_id`, `order_province`, `order_city`, ' .
        '`order_district`, `order_addr`, `order_consignee`, `order_mobile`, `order_tel` ' .
        'FROM `cloud_orders` WHERE `u_id`=? AND `order_sn`=? AND `order_status`=? LIMIT 1',
            array(
                $uid,
                $orderSn,
                $status
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [getOrderSec 获取订单支付详情]
     * @param  [type] $uid     [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function getOrderSec($uid, $orderId)
    {
        $query = $this->db->query('SELECT o.`order_status`, o.`order_sn`, o.`order_paied`, o.`order_mobile`, '.
        'o.`order_tel`, o.`order_consignee`, o.`order_inv_type`, o.`order_addr`, ' .
        'p.`pro_name`, c.`city_name`, d.`dis_name` FROM `cloud_orders` o LEFT JOIN (SELECT `pro_id`, `pro_name` ' .
        'FROM `cloud_provinces`) p ON o.order_province = p.pro_id LEFT JOIN (SELECT `city_id`, `city_name` ' .
        'FROM `cloud_citys`) c ON o.order_city = c.city_id LEFT JOIN (SELECT `dis_id`, `dis_name` ' .
        'FROM `cloud_districts`) d ON o.order_district = d.dis_id WHERE `u_id` = ? AND `order_id`=? LIMIT 1',
            array(
                $uid,
                $orderId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getFeeByUidOid($uid, $orderId)
    {
        $query = $this->db->query('SELECT `order_sn`, `order_paied` FROM `cloud_orders` ' .
        'WHERE `u_id` = ? AND `order_id`=? LIMIT 1',
            array(
                $uid,
                $orderId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getMyOrders($offset, $num, $conditions = [])
    {
        $where = '';
        if(!empty($conditions))
        {
            $where = 'WHERE';
            foreach($conditions as $key=>$condition)
            {
                if($key == 'start_time')
                {
                    $where .= ' order_addtime >=' . $condition . ' AND';
                }
                else if($key == 'end_time')
                {
                    $where .= ' order_addtime <=' . $condition . ' AND';
                }
                else
                {
                    $where .= " $key=$condition AND";
                }
            }
            $where = rtrim($where, 'AND');
        }
        $query = $this->db->query(
            'select `order_id`, `u_id`, `order_sn`, `order_status`, `order_delivery_status`, ' .
            '`order_pay_status`, `order_addtime`, `order_paied` from `cloud_orders`
            '. $where .
            ' ORDER BY `order_addtime` DESC LIMIT ?, ?',
            array(
                $offset,
                $num
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getOrderInfoBySn($sn,$uid)
    {
        $query = $this->db->query('SELECT o.`order_sn`, o.`order_pay_status`, o.`order_delivery_status`, ' .
        'o.`order_addtime`, o.`order_pay_time`, o.`order_total`, o.`order_pay_off`, o.`order_addr`, o.`order_id`, ' .
        'o.`order_shipping_fee`, o.`order_paied` , o.`order_shipping_id`, o.`order_shipping_sn`, o.`order_status`, ' .
        'p.`pro_name`, c.`city_name`, d.`dis_name`, o.`order_consignee`, o.`order_mobile`, o.`order_tel`, o.`order_buyer_msg`, ' .
        'o.`order_is_inv`, o.`order_inv_type`, o.`order_inv_title` FROM `cloud_orders` o LEFT JOIN ' .
        '(SELECT `pro_id`, `pro_name` FROM `cloud_provinces`) p ON o.`order_province` = p.`pro_id` LEFT JOIN ' .
        '(SELECT `city_id`, `city_name` FROM `cloud_citys`) c ON o.`order_city` = c.`city_id` LEFT JOIN ' .
        '(SELECT `dis_id`, `dis_name` FROM `cloud_districts`) d ON o.`order_district` = d.`dis_id` ' .
        ' WHERE o.`order_sn` = ? AND o.`u_id` = ? LIMIT 1',
            array(
                $sn,
                $uid
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function setOrderStatusByUidSn($sn,$uid, $field, $value)
    {
        return $this->db->execute("UPDATE `cloud_orders` SET $field = ? ".
            "WHERE order_sn = ? AND u_id = ?",
            array($value, $sn, $uid));
    }

    public function setStatusDeliBySn($sn, $uid)
    {
        return $this->db->execute('UPDATE `cloud_orders` SET `order_status` = 3, `order_delivery_status` = 5 '.
        'WHERE order_sn = ? AND u_id = ?',
            array(
                $sn,
                $uid
            )
        );
    }

    public function uptOrderStatus($orderSn, $payTime)
    {
        return $this->db->execute('UPDATE `cloud_orders` SET `order_pay_status` = 3, `order_pay_time` = ? '.
        'WHERE `order_sn` = ?',
            array(
                $payTime,
                $orderSn
            )
        );
    }

    /**
     * [incrGoodsNum 立即购买扣除商品库存]
     * note:扣除数量通过组合商品的唯一标识二维码
     */
    public function decrGoodsNum($goodsId, $attrsIds, $num)
    {
        return $this->db->execute('UPDATE `cloud_goods_attrs` SET `g_attr_nums` = `g_attr_nums` - ? '.
        'WHERE `goods_id` = ? AND attrs_ids = ?',
            array(
                $num,
                $goodsId,
                $attrsIds
            )
        );
    }

    /**
     * [incrGoodsNums 批量更改购买商品的库存//由于属性表为组合唯一标识]
     * @param  [type] $goodsDates [description]
     * @return [type]             [description]
     */
    public function decrGoodsNums($goodsIds, $attrsIds, $goodsDates)
    {
        $conditions = '';
        $num = count($goodsIds);
        foreach($goodsDates as $g)
        {
            for($i = 0; $i < $num; $i++)
            {
                if($g['goods_id'] == $goodsIds[$i] && $g['attrs_ids'] == $attrsIds[$i])
                    $conditions .= ",('$g[g_attr_barcode]', $g[car_good_num])";
            }
        }
        return $this->db->execute('INSERT INTO `cloud_goods_attrs` (`g_attr_barcode`, `g_attr_nums`) '.
        'VALUES ' . ltrim($conditions, ',') .'ON DUPLICATE KEY UPDATE `g_attr_nums` = `g_attr_nums` - VALUES(`g_attr_nums`)'
        );
    }

    public function getExOids($exTime)
    {
        $time = $_SERVER['REQUEST_TIME'] - $exTime;
        $query = $this->db->query('SELECT GROUP_CONCAT(`order_id`) `oids` FROM `cloud_orders` ' .
        'WHERE `order_status` = 1 AND `order_pay_status` = 1 AND `order_addtime` < ?',
            array(
                $time
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getGidsAids($oids)
    {
        $query = $this->db->query('SELECT `goods_id`, `attrs_info`, `ord_goods_num` num FROM `cloud_order_goods` ' .
        'WHERE `order_id` IN (' . $oids . ')'
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [uptGoodsNums description]
     * @param  [type] $gids [商品id[]]
     * @param  [type] $aids [组合属性id[]]
     * @param  [type] $nums [下单对应的总数量]
     * @return [type]       [description]
     */
    public function uptGoodsNums($gids, $aids, $nums)
    {
        $cons = '';
        $num = count($gids);
        for($i = 0; $i < $num; $i++)
           $cons .= ",($gids[$i], '$aids[$i]', $nums[$i])";

        return $this->db->execute('INSERT INTO `cloud_goods_attrs` (`attrs_ids`, `goods_id`, `g_attr_nums`) '.
        'VALUES ' . ltrim($cons, ',') .'ON DUPLICATE KEY UPDATE `g_attr_nums` = `g_attr_nums` + VALUES(`g_attr_nums`)'
        );
    }

    public function batchSetOrderStatus($oids)
    {
        return $this->db->execute('UPDATE `cloud_orders` SET `order_status` = 5 WHERE order_id IN (' . $oids . ')');
    }

    public function getPendPayCnt($uid)
    {
        $query = $this->db->query('select count(*) as cnt from cloud_orders where u_id = ? and order_status = 1', array($uid));
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }
}
