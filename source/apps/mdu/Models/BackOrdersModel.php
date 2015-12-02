<?php

namespace Mall\Mdu\Models;

class BackOrdersModel extends ModelBase
{

    const ORDER_FINISHED = 7;//[状态: 售后单完成]
    const ORDER_NOT_AUDIT = 1;//[状态: 未审核]
    const ORDER_PASS_AUDIT = 3;//[状态: 通过审核]
    const ORDER_CLOSED = 9; //[状态: 关闭]

    /**
     * [getAllBackOrders 获取所有售后单]
     *
     * @param int $offset
     * @param int $num
     * @param Array $conditions
     * @return Array
     */
    public function getAllBackOrders($offset, $num, $conditions = [])
    {
        $where = '';
        if(!empty($conditions)){
            $where = 'WHERE';
            foreach($conditions as $key=>$condition)
            {
                if($key == 'start_time')
                {
                    $where .= ' bord_addtime >=' . $condition . ' AND';
                }
                else if($key == 'end_time')
                {
                    $where .= ' bord_addtime <=' . $condition . ' AND';
                }
                else
                {
                    $where .= " $key=$condition AND";
                }
            }
            $where = rtrim($where, 'AND');
        }
        return $this->db->query(
            'SELECT * FROM `cloud_back_orders` ' . $where .
            ' ORDER BY `bord_addtime` DESC LIMIT ?,?',
            array(
                $offset,
                $num
            )
        )->fetchAll();
    }

    /**
     * [getTotal 获取所有售后单数量]
     *
     * @param Array $conditions
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
                    $where .= ' bord_addtime >=' . $condition . ' AND';
                }
                else if($key == 'end_time')
                {
                    $where .= ' bord_addtime <=' . $condition . ' AND';
                }
                else
                {
                    $where .= " $key=$condition AND";
                }
            }
            $where = rtrim($where, 'AND');
        }
        return $this->db->query("SELECT COUNT(*) num FROM `cloud_back_orders` $where")->fetch();
    }

    /**
     * [deleteOrders 批量售后单]
     *
     * @param Array $idArr
     * @return boolean
     */
    public function deleteBackOrders($idArr)
    {
        $filteredStr = '';
        foreach($idArr as $id)
        {
            $filteredStr .= intval($id) . ',';
        }
        $filteredStr = rtrim($filteredStr, ',');
        return $this->db->execute("DELETE FROM `cloud_back_orders` WHERE `bord_id` in($filteredStr)");
    }

    /**
     * [getOrderById 根据ID获取售后单信息]
     *
     * @param int $bordId
     * @return Array
     */
    public function getOrderById($bordId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_back_orders` WHERE `bord_id` = ? LIMIT 1',
            array(
                $bordId
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
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
        $res = $this->db->query('SELECT * FROM `cloud_back_orders` WHERE `bord_sn` = ? AND `u_id`=? LIMIT 1',
            array(
                $bordSn,
                $uid
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [setOrderStatus 设置售后单状态]
     *
     * @param int $bordId
     * @param int $status [1:未审核 3:审核通过 5:审核未通过 7:完成 9:关闭]
     * @return boolean
     */
    public function setOrderStatus($bordId, $status)
    {
        $condition = '';
        if($status == self::ORDER_FINISHED){
            $condition = ',bord_finish_time='.time();
        }
        return $this->db->execute("UPDATE `cloud_back_orders` SET `bord_status`=? $condition WHERE `bord_id`=?",
            array(
                $status,
                $bordId
            )
        );
    }

    /**
     * [setOrdersStatus 批量设置售后单状态]
     *
     * @param $idArr
     * @param $status
     * @return boolean
     */
    public function setOrdersStatus($idArr, $status)
    {
        $filteredStr = '';
        foreach($idArr as $id)
        {
            $filteredStr .= intval($id) . ',';
        }
        $filteredStr = rtrim($filteredStr, ',');

        $condition = '';
        if($status == self::ORDER_FINISHED){
            $condition = ',bord_finish_time='.time();
        }

        return $this->db->execute("UPDATE `cloud_back_orders` SET `bord_status`=? $condition WHERE `bord_id` in($filteredStr)",
            array(
                $status,
            )
        );
    }

    /**
     * [setShipInfo 修改快递信息]
     *
     * @param int $bordId
     * @param string $shippingSn
     * @param string $shippingName
     * @return boolean
     */
    public function setShipInfo($bordId, $shippingSn, $shippingName)
    {
        return $this->db->execute('UPDATE `cloud_back_orders` SET `bord_shipping_sn`=? , `bord_shipping_name`=? WHERE `bord_id`=?',
            array(
                $shippingSn,
                $shippingName,
                $bordId
            )
        );
    }

    /**
     * [setOrderStatus 设置售后单类型]
     *
     * @param int $bordId
     * @param int $bordType [1:退货 3:换货 5:退款]
     * @return boolean
     */
    public function setOrderType($bordId, $bordType)
    {
        return $this->db->execute('UPDATE `cloud_back_orders` SET `bord_type`=? WHERE `bord_id`=?',
            array(
                $bordType,
                $bordId
            )
        );
    }

    /**
     * [setOrderStatus 设置售后单类型]
     *
     * @param int $bordId
     * @param float $actMoney
     * @param float $backMoney
     * @return boolean
     */
    public function setActMoney($bordId, $actMoney, $backMoney)
    {
        return $this->db->execute('UPDATE `cloud_back_orders` SET `bord_act_money`=?, `bord_back_money`=? WHERE `bord_id`=?',
            array(
                $actMoney,
                $backMoney,
                $bordId
            )
        );
    }

    /**
     * [addBackOrder 添加售后订单]
     *
     * @param $uid
     * @param $data
     * @return mixed
     */
    public function addBackOrder($uid, $data)
    {
        $this->db->execute('INSERT INTO `cloud_back_orders` (`order_id`,`order_sn`,`bord_sn`,`u_id`,
        `bord_addtime`,`bord_status`,`goods_name`,`goods_sn`,`bord_goods_num`,`attrs_info`,`attrs_barcode`,`attrs_img`,`bord_imgs`,`bord_msg`, `bord_type`,`bord_reason`,
        `bord_pro_id`,`bord_city_id`,`bord_dis_id`,`bord_addr_info`,`bord_consignee`,`bord_mobile`,`bord_tel`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            array(
                $data['order_id'],
                $data['order_sn'],
                $data['bord_sn'],
                $uid,
                time(),
                self::ORDER_NOT_AUDIT,
                $data['goods_name'],
                $data['goods_sn'],
                $data['goods_num'],
                $data['attrs_info'],
                $data['attrs_barcode'],
                $data['attrs_img'],
                $data['bord_imgs'],
                $data['bord_msg'],
                $data['bord_type'],
                $data['bord_reason'],
                $data['pro'],
                $data['city'],
                $data['dis'],
                $data['addr_detail'],
                $data['consignee'],
                $data['mobile'],
                $data['tel']
            )
        );

        return $this->db->lastInsertId();
    }

    /**
     * [addBackOrder 添加售后订单]
     *
     * @param $uid
     * @param $data
     * @return mixed
     */
    public function addBackOrderByAdmin($uid, $data)
    {
        $this->db->execute('INSERT INTO `cloud_back_orders` (`order_id`,`order_sn`,`bord_sn`,`u_id`,
        `bord_addtime`,`bord_status`,`goods_name`,`goods_sn`,`bord_goods_num`,`attrs_info`,`attrs_barcode`,`attrs_img`,`bord_shipping_name`,`bord_shipping_sn`, `bord_type`,`bord_reason`,
        `bord_pro_id`,`bord_city_id`,`bord_dis_id`,`bord_addr_info`,`bord_consignee`,`bord_mobile`,`bord_tel`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            array(
                $data['order_id'],
                $data['order_sn'],
                $data['bord_sn'],
                $uid,
                time(),
                self::ORDER_NOT_AUDIT,
                $data['goods_name'],
                $data['goods_sn'],
                $data['goods_num'],
                $data['attrs_info'],
                $data['attrs_barcode'],
                $data['attrs_img'],
                $data['ship_comp'],
                $data['ship_sn'],
                $data['bord_type'],
                $data['bord_reason'],
                $data['pro'],
                $data['city'],
                $data['dis'],
                $data['addr_detail'],
                $data['consignee'],
                $data['mobile'],
                $data['tel']
            )
        );

        return $this->db->lastInsertId();
    }

    /**
     * 根据鞋款获取售后申请数量
     *
     * @param $uid (0 代表后台添加)
     * @param $orderSn
     * @param $goodsSn
     * @return Array
     */
/*    public function getGoodsNumOfBord($uid, $orderSn, $goodsSn)
    {
        return $this->db->query('SELECT `bord_goods_num` FROM `cloud_back_orders` WHERE (`u_id`=? OR `u_id`=0) AND `order_sn`=? AND `goods_sn`=?',
            array(
                $uid,
                $orderSn,
                $goodsSn
            )
        )->fetchAll();
    }*/

    /**
     * 根据鞋款获取售后申请数量
     *
     * @param $orderSn
     * @param $goodsSn
     * @return Array
     */
    public function getBordGoodsNum($orderSn, $barcode)
    {
        return $this->db->query('SELECT `bord_goods_num` FROM `cloud_back_orders` WHERE `order_sn`=? AND `attrs_barcode`=? AND `bord_status` <> ?',
            array(
                $orderSn,
                $barcode,
                self::ORDER_CLOSED
            )
        )->fetchAll();
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
        return $this->db->query('SELECT `user_shipping_sn`, `bord_id` FROM `cloud_back_orders` WHERE `u_id`=? AND `bord_sn`=? AND `bord_status`=? LIMIT 1',
            array(
                $uid,
                $bordSn,
                $status
            )
        )->fetch();
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
    public function addExpInfo($uid, $bordSn, $shipComp, $shipSn)
    {
        return $this->db->execute(
            'UPDATE `cloud_back_orders` SET `user_shipping_name`=?, `user_shipping_sn`=? WHERE `u_id` = ? AND `bord_sn`=? AND `bord_status`=?',
            array(
                $shipComp,
                $shipSn,
                $uid,
                $bordSn,
                self::ORDER_PASS_AUDIT
            )
        );
    }

    public function getSupCnt($uid)
    {
        $query = $this->db->query('select count(*) as cnt from cloud_back_orders where u_id = ?', array($uid));
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }
}
