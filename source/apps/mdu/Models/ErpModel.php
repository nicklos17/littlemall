<?php

namespace Mall\Mdu\Models;

class ErpModel extends ModelBase
{
    public function getOrderList($pn, $pageNum, $paySta, $deliverySta, $startTime, $endTime)
    {
        $query = $this->db->query('SELECT o.*, yc.yc_ctx, og.* FROM `cloud_orders` o LEFT JOIN (SELECT GROUP_CONCAT(`attrs_barcode`) attrs_barcode, GROUP_CONCAT(`ord_goods_num`) ord_goods_num, ' .
        '`order_id` FROM `cloud_order_goods` GROUP BY `order_id`) og ON o.`order_id` = og.`order_id` LEFT JOIN (SELECT order_id, yc_ctx FROM `cloud_yun_codes`) yc ON o.order_id = yc.order_id WHERE o.`order_pay_status` = ?' .
        ' AND o.`order_delivery_status` = ? AND o.order_addtime >= ? AND o.order_addtime <= ? LIMIT ?, ?',
            array(
                $paySta,
                $deliverySta,
                $startTime,
                $endTime,
                ($pn - 1)*$pageNum,
                $pageNum
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getOrderNum($paySta, $deliverySta, $startTime, $endTime)
    {
        $query = $this->db->query('SELECT count(`order_id`) num FROM `cloud_orders` WHERE ' .
        '`order_pay_status` = ? AND `order_delivery_status` =? AND order_addtime >= ? AND order_addtime <= ?',
            array(
                $paySta,
                $deliverySta,
                $startTime,
                $endTime,
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function uptOrderStatus($orderSn, $shippingId, $shippingSn, $status)
    {
        return $this->db->execute('UPDATE `cloud_orders` SET `order_delivery_status` = ? , ' .
        '`order_shipping_sn` = ? , `order_shipping_id` = ? WHERE `order_sn` = ?',
            array(
                $status,
                $shippingSn,
                $shippingId,
                $orderSn
            )
        );
    }
}