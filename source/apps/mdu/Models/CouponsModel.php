<?php

namespace Mall\Mdu\Models;

class CouponsModel extends ModelBase
{   
    /**
     * [addCouponsRule 添加优惠券规则]
     * @param [type] $name   [description]
     * @param [type] $type   [description]
     * @param [type] $rang   [description]
     * @param [type] $info   [description]
     * @param [type] $status [description]
     */
    public function addCouponsRule($name, $type, $rang, $info, $status)
    {
         return $this->db->execute('INSERT INTO  cloud_coupon_rules(cr_name, cr_range_type, cr_range,cr_info, cr_status,cr_addtime) VALUES (?, ?, ?, ?, ?,?)', array(
            $name,
            $type,
            $rang,
            $info,
            $status,
            $_SERVER['REQUEST_TIME'],
        ));
    }
    /**
     * [addCoupons 添加优惠券]
     * @param [type] $crid     [description]
     * @param [type] $cpsn     [description]
     * @param [type] $starTime [description]
     * @param [type] $endTime  [description]
     * @param [type] $uid      [description]
     * @param [type] $status   [description]
     */
    public function addCoupons($values)
    {
         return $this->db->execute('INSERT INTO  cloud_coupons(cr_id,cp_sn, cp_start_time,cp_end_time, cp_status,cp_addtime) '.$values);

    }  
    /**
     * [getCouponsRule 获取优惠券]
     * @return [type] [description]
     */
    public function getCouponsRule()
    {
        return $this->db->query('SELECT * FROM cloud_coupon_rules ')->fetchAll();
    }

    /**
     * [getCodes 获取优惠券]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $arr    [description]
     * @return [type]         [description]
     */
    public function getCoupons($num, $offset, $condition)
    {
        return $this->db->query("SELECT * FROM cloud_coupons ".$condition." limit ?, ?",
            array(
                $num,
                $offset
            )
        )->fetchAll();
    }
    
    /**
     * [getTotalCode 获取优惠券总数]
     * @return [type] [description]
     */
    public function getTotalCoupons($condition)
    {
        return $this->db->query("SELECT count(*) as total FROM cloud_coupons ".$condition." ")->fetch();
    }

    /**
     * [getCodes 获取优惠券]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $arr    [description]
     * @return [type]         [description]
     */
    public function getAllCouponsRules($num, $offset, $condition)
    {
        return $this->db->query("SELECT * FROM cloud_coupon_rules ".$condition." limit ?, ?",
            array(
                $num,
                $offset
            )
        )->fetchAll();
    }
    
    /**
     * [getTotalCode 获取优惠券总数]
     * @return [type] [description]
     */
    public function getTotalCouponsRules($condition)
    {
        return $this->db->query("SELECT count(*) as total FROM cloud_coupon_rules ".$condition." ")->fetch();
    }

    public function getGoodsName()
    {
        return $this->db->query("SELECT goods_id,gcat_id,goods_name FROM cloud_goods LIMIT 10")->fetchAll();
    }

    public function getCategory()
    {
        return $this->db->query("SELECT gcat_id,gcat_name FROM cloud_good_cats LIMIT 10")->fetchAll();
    }

    public function setCouponsRulesStatus($status, $id)
    {
        return $this->db->execute('UPDATE cloud_coupon_rules SET cr_status = ? WHERE cr_id IN ('.$id.')', 
            array(
                $status,
            )
        );
    }

    public function setCouponsStatus($status, $id)
    {
        return $this->db->execute('UPDATE cloud_coupons SET cp_status = ? WHERE cp_id IN ('.$id.')', 
            array(
                $status,
            )
        );
    }

    public function getTotalCouponsByUid($condition)
    {
        return $this->db->query("SELECT count(*) as total FROM cloud_coupons ".$condition." ")->fetch();
    }

    public function getCouponsByUid($num, $offset, $condition)
    {
        return $this->db->query('SELECT * FROM cloud_coupons AS a INNER JOIN cloud_coupon_rules AS b ON a.cr_id = b.cr_id ' . $condition .' ORDER BY a.cp_addtime DESC LIMIT ?, ?',
            array(
                $num,
                $offset
            )
        )->fetchAll();
    }

    public function setCouponsUsedTime($cpsn)
    {
        return $this->db->execute('UPDATE cloud_coupon SET cp_used_time = ?  WHERE cp_sn = ?',
            array(
                $_SERVER['REQUEST_TIME'],
                $cpsn,
            )
        );
    }

    public function getAllCouponsByUid($condition)
    {
        return $this->db->query("SELECT * FROM cloud_coupons AS a INNER JOIN cloud_coupon_rules AS b ON a.cr_id = b.cr_id ".$condition." ORDER BY a.cp_addtime DESC ")->fetchAll();
    }

/**
 * [getCouponsId 通过优惠券兑换码获取优惠]
 * @param  [type] $condition [description]
 * @return [type]            [description]
 */
    public function getCouponsBySn($sn, $status, $time)
    {
        return $this->db->query('SELECT b.`cr_info`, a.`cp_id`, b.`cr_range_type`, b.`cr_range` FROM `cloud_coupons` AS a LEFT JOIN `cloud_coupon_rules` ' .
        ' AS b ON a.`cr_id` = b.`cr_id` WHERE a.`cp_sn` = ? AND a.`cp_status` = ? AND a.`cp_start_time` ' .
        ' < ? AND a.`cp_end_time` >= ? AND b.`cr_status` = 1 AND a.`u_id` = 0',
            array(
                $sn,
                $status,
                $time,
                $time
            )
        )->fetch();
    }

/**
 * [getCpsTipsById 通过优惠券ID获取优惠]
 * @param  [type] $cpId [description]
 * @param  [type] $time [description]
 * @return [type]       [description]
 */
    public function getCpsTipsById($cpId, $status, $time, $uid)
    {
        return $this->db->query('SELECT b.`cr_info`, a.`cp_id`, a.`cr_range_type`, a.`cr_range` FROM `cloud_coupons` AS a LEFT JOIN `cloud_coupon_rules` ' .
        ' AS b ON a.`cr_id` = b.`cr_id` WHERE a.`cp_id` = ? AND a.`u_id` = ? AND a.`cp_status` = ? AND a.`cp_start_time` ' .
        ' < ? AND a.`cp_end_time` > ? AND b.`cr_status` = 1',
            array(
                $cpId,
                $uid,
                $status,
                $time,
                $time
            )
        )->fetch();
    }

    /**
     * [setCpsStatusById 更改优惠券的状态,通过ID]
     * @param [type] $cpsVal [description]
     * @param [type] $cpId   [description]
     * @param [type] $uid    [description]
     */
    public function setCpsStatusById($cpId, $uid, $usedTime)
    {
        return $this->db->execute('UPDATE `cloud_coupons` SET `cp_used_time` = ? ' .
        'WHERE `cp_id` = ?  AND `u_id` = ? LIMIT 1',
            array(
                $usedTime,
                $cpId,
                $uid
            )
        );
    }

    /**
     * [setCpsStatusBySn 更改优惠券的状态,通过SN码无需Uid]
     * @param [type] $cpsVal [description]
     * @param [type] $cpId [description]
     * @param [type] $uid    [description]
     */
    public function setCpsStatusBySn($cpId, $usedTime)
    {
        return $this->db->execute('UPDATE `cloud_coupons` SET `cp_used_time` = ? WHERE `cp_id` = ? LIMIT 1',
            array(
                $usedTime,
                $cpId
            )
        );
    }

    /**
     * [getParcialGinfo 获取商品的分类id 商品价格]
     * @param  [type] $goodsId [description]
     * @return [type]          [description]
     */
    public function getParcialGinfo($goodsId)
    {
        $query = $this->db->query('SELECT `goods_id`, `gcat_id`, `goods_price` FROM `cloud_goods` WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }


    /**
     * [getCartGoods 获取用户选择的购物车商品及对应商品的信息]
     * @return [type] [description]
     */
    public function getCartGoods($uid, $goodsIds, $attrsIds)
    {
        $cons = '';
        foreach($attrsIds as $k => $aid)
            $cons .= '(c.`u_id` = ' . $uid . ' AND c.`goods_id` = ' . $goodsIds[$k] . ' AND c.`attrs_ids` = \'' . $aid . '\') OR ';

        $query = $this->db->query('SELECT c.`goods_id`,  c.`car_good_num`,  g.`goods_price`, g.`gcat_id` ' .
        'FROM `cloud_shopping_car` c LEFT JOIN (SELECT `goods_price`, `goods_id`, `gcat_id` FROM `cloud_goods`) g ' .
        'ON c.`goods_id` = g.`goods_id` WHERE ' . rtrim($cons, ' OR '));

        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }
}