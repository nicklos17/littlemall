<?php

namespace Mall\Mdu\Models;

class CartModel extends ModelBase
{
    /**
     * [getTopCategory 获取购物车列表]
     * @return [type] [description]
     */
    public function getDataByUid($uid)
    {
        $query = $this->db->query('SELECT c.`goods_id`, c.`attrs_ids`, c.`car_good_num`, g.`goods_name`, g.`goods_status`,' .
        'c.`car_addtime`, g.`goods_price`, g.`goods_img`, g.`goods_sn` FROM `cloud_shopping_car` c LEFT JOIN (SELECT ' .
        '`goods_name`, `goods_price`, `goods_img`, `goods_id`, `goods_sn`, `goods_status` FROM `cloud_goods`) g ON c.`goods_id` = ' .
        'g.`goods_id` WHERE c.`u_id` = ?',// ORDER BY c.car_addtime DESC
            array(
                $uid
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [getTopCategory 获取购物车列表并获取组合商品的条形码]
     * @return [type] [description]
     */
    public function getCdataByUid($uid)
    {
        $query = $this->db->query('SELECT c.`car_addtime`, c.`goods_id`, c.`attrs_ids`, c.`car_good_num`, g.`goods_name`, g.`goods_status`, a.`g_attr_barcode` barcode, ' .
        'c.`car_addtime`, g.`goods_price`, g.`goods_img`, g.`goods_sn` FROM `cloud_shopping_car` c LEFT JOIN (SELECT ' .
        '`goods_name`, `goods_price`, `goods_img`, `goods_id`, `goods_sn`, `goods_status` FROM `cloud_goods`) g ON c.`goods_id` = ' .
        'g.`goods_id` LEFT JOIN (SELECT `attrs_ids`, `goods_id`, `g_attr_barcode` FROM `cloud_goods_attrs`) a ON ' .
        'a.`attrs_ids` = c.`attrs_ids` AND c.`goods_id` = a.`goods_id` WHERE c.`u_id` = ? ORDER BY c.`car_addtime` DESC',
            array(
                $uid
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [getTopCategory 获取用户选择的购物车商品及对应商品的信息以及组合属性商品的条形码]
     * @return [type] [description]
     */
    public function getOrderDatas($uid, $goodsIds, $attrsIds)
    {
        $cons = '';
        foreach($attrsIds as $k => $aid)
            $cons .= '(c.`u_id` = ' . $uid . ' AND c.`goods_id` = ' . $goodsIds[$k] . ' AND c.`attrs_ids` = \'' . $aid . '\') OR ';

        $query = $this->db->query('SELECT c.`goods_id`, c.`attrs_ids`, c.`car_good_num`, ga.`g_attr_barcode`, ga.`g_attr_nums`, g.`goods_name`, g.`goods_price`, ' .
        'g.`goods_img`, g.`goods_sn`, g.`goods_market`, g.`goods_type`, g.`gcat_id` FROM `cloud_shopping_car` c LEFT JOIN (SELECT ' .
        '`goods_name`, `goods_price`, `goods_img`, `goods_id`, `goods_sn`, `goods_market`, `goods_type`, `gcat_id` ' .
        ' FROM `cloud_goods`) g ON c.`goods_id` = g.`goods_id` LEFT JOIN (SELECT `g_attr_barcode`, `g_attr_nums`, `attrs_ids`, `goods_id` FROM `cloud_goods_attrs`) ' .
        'ga ON c.`attrs_ids` = ga.`attrs_ids` AND c.`goods_id` = ga.`goods_id` WHERE ' . rtrim($cons, ' OR '));

        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function addCart($uid, $goodsId, $attrsIds, $num)
    {
        return $this->db->execute('INSERT INTO `cloud_shopping_car` (`u_id`, `goods_id`, `attrs_ids`, '.
        '`car_good_num`, `car_addtime`) VALUES (?, ?, ?, ?, ?)',
            array(
                $uid,
                $goodsId,
                $attrsIds,
                $num,
                $_SERVER['REQUEST_TIME']
            )
        );
    }

    public function setCartNum($uid, $goodsId, $attrsIds, $num)
    {
        return $this->db->execute('UPDATE `cloud_shopping_car` SET `car_good_num` = `car_good_num` ' .
        '+ ? WHERE `u_id` = ? AND `goods_id` = ? AND `attrs_ids` = ?',
            array(
                $num,
                $uid,
                $goodsId,
                $attrsIds
            )
        );
    }

    public function getCart($uid, $goodsId, $attrsIds)
    {
        $query = $this->db->query('SELECT `u_id` FROM `cloud_shopping_car` WHERE `u_id` = ? ' .
        ' AND `goods_id` = ? AND `attrs_ids` = ? LIMIT 1',
            array(
                $uid,
                $goodsId,
                $attrsIds
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getGoodsStock($goodsId, $attrsIds)
    {
        $query = $this->db->query('SELECT `g_attr_nums`, `g_attr_barcode` FROM `cloud_goods_attrs` ' .
        'WHERE `goods_id` = ? AND `attrs_ids` = ? LIMIT 1',
            array(
                $goodsId,
                $attrsIds
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getCartNum($uid, $goodsId, $attrsIds)
    {
        $query = $this->db->query('SELECT `car_good_num` FROM `cloud_shopping_car` WHERE `u_id` = ? ' .
        ' AND `goods_id` = ? AND `attrs_ids` = ? LIMIT 1',
            array(
                $uid,
                $goodsId,
                $attrsIds
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getNumByUid($uid)
    {
        $query = $this->db->query('SELECT count(`u_id`) num FROM `cloud_shopping_car` WHERE `u_id` = ?',
            array(
                $uid
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function delCart($uid, $goodsId, $attrsIds)
    {
        return $this->db->execute('DELETE FROM `cloud_shopping_car` WHERE `u_id` = ? AND `goods_id` ' .
        '= ? AND `attrs_ids` = ?',
            array(
                $uid,
                $goodsId,
                $attrsIds
            )
        );
    }

    public function mulDelCart($uid, $goodsIds, $attrsIds)
    {
        $cons = '';
        foreach($attrsIds as $k => $aid)
            $cons .= '(`u_id` = ' . $uid . ' AND `goods_id` = ' . $goodsIds[$k] . ' AND `attrs_ids` = \'' . $aid . '\') OR ';

        return $this->db->execute('DELETE FROM `cloud_shopping_car` WHERE ' . rtrim($cons, ' OR '));
    }

    public function addNumCart($uid, $goodsId, $attrsIds)
    {
        return $this->db->execute('UPDATE `cloud_shopping_car` SET `car_good_num` = `car_good_num` ' .
        '+ 1 WHERE `u_id` = ? AND `goods_id` = ? AND `attrs_ids` = ?',
            array(
                $uid,
                $goodsId,
                $attrsIds
            )
        );
    }

    public function reduceNumCart($uid, $goodsId, $attrsIds)
    {
        return $this->db->execute('UPDATE `cloud_shopping_car` SET `car_good_num` = `car_good_num` ' .
        '- 1 WHERE `u_id` = ? AND `goods_id` = ? AND `attrs_ids` = ?',
            array(
                $uid,
                $goodsId,
                $attrsIds
            )
        );
    }

    /**
     * 清空数据库过期商品
     */
    public function expiredDel($uid)
    {
        if($this->db->execute('DELETE c FROM `cloud_shopping_car` c LEFT JOIN (SELECT ' .
        '`goods_id`, `goods_status` FROM `cloud_goods`) g ON c.`goods_id` = g.`goods_id` WHERE ' .
        'c.`u_id` = ? AND g.`goods_status` <> 1',
            array(
                $uid
            )
        ))
            return $this->db->affectedRows();
        else
            return false;
    }
}