<?php

namespace Mall\Mdu\Models;

class OrdergoodsModel extends ModelBase
{

    const ORDER_GOODS_BACKED = 3; //[订单商品已申请售后]

    /**
     * [根据订单id获取订单详情]
     * @param  [string] $orderId [订单id]
     * @return [array]          [description]
     */
    public function getOrderGoodsByOrderid($orderId)
    {
        $query = $this->db->query('SELECT * FROM `cloud_order_goods` WHERE `order_id` = ?',
            array($orderId));
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
     }

     /**
      * [修改订单商品状态]
      * @param  [string] $orderGoodsId [订单商品id]
      * @param  [string] $orderId      [订单id]
      * @param  [string] $goodsId      [商品id]
      * @param  [string] $price        [售价]
      * @param  [string] $attrCode     [属性信息]
      * @return [array]               [description]
      */
     public function updateOrderGoods($orderGoodsId, $orderId, $goodsId, $price, $attrCode, $goodsNum)
     {
        return $this->db->execute('UPDATE `cloud_order_goods` SET `ord_goods_price` = ?,'.
        '`attrs_info` = ?, `ord_goods_num` = ? WHERE `order_id` = ? AND `goods_id` = ? AND `ord_goods_id` = ?', 
            array(
                $price,
                $attrCode,
                $goodsNum,
                $orderId,
                $goodsId,
                $orderGoodsId
            )
        );
     }

     /**
      * [批量创建订单商品详情]
      * @param  [string] $orderId   [订单id]
      * @param  [array] $goodsData [订单商品信息]
      * @return [type]            [description]
      */
     public function createOrderGoods($orderId, $goodsData)
     {
        if(!empty($goodsData))
        {
            $vals = '';
            foreach($goodsData as $datas)
            {
                $vals .= "($orderId, $datas[goods_id], '$datas[goods_sn]', '$datas[attrs_img]', '$datas[goods_name]', $datas[goods_num], " .
                "$datas[goods_market], $datas[goods_price], '$datas[attrs_info]', '$datas[g_attr_barcode]'),";
            }
            $vals = rtrim($vals, ',');
            return $this->db->execute("INSERT INTO `cloud_order_goods` (`order_id`, `goods_id`, `goods_sn`, `attrs_img`, ".
                "`goods_name` , `ord_goods_num` , `goods_market`, `ord_goods_price`, `attrs_info`, ".
                "`attrs_barcode`) values $vals");
        }
        else
        {
            return false;
        }
     }

    /**
     * 获取订单的某个订单商品
     *
     * @param $orderGoodsId
     * @return Array
     */
    public function getOrderGoodsById($orderGoodsId)
    {
        $query = $this->db->query('SELECT `order_id`, `goods_sn`, `goods_name`, `attrs_barcode`, `attrs_img`, `attrs_info`, `ord_goods_num` FROM cloud_order_goods WHERE `ord_goods_id` = ? LIMIT 1',
            array(
                $orderGoodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * 将订单商品设为已经申请售后
     *
     * @param $orderGoodsId
     * @return boolean
     */
    public function setOrderGoodsBacked($orderGoodsId)
    {
        return $this->db->execute('UPDATE `cloud_order_goods` set `ord_goods_back`=? WHERE `ord_goods_id`=?',
            array(
                self::ORDER_GOODS_BACKED,
                $orderGoodsId
            )
        );
    }

}