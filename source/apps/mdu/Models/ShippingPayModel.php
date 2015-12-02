<?php

namespace Mall\Mdu\Models;

class ShippingPayModel extends ModelBase
{

    /**
     * 获取所有快递费用规则
     * 联合省市区表获取对应地名
     *
     * @param int $offset
     * @param int $num
     * @param Array $conditions
     * @return Array
     */
    public function getAllRules($offset, $num, $conditions = [])
    {
        $where = '';
        foreach($conditions as $key=>$condition)
        {
            $where .= "and a.$key=$condition ";
        }
        return $this->db->query(
            'SELECT a.`shipping_pay`, a.`pro_id`, a.`city_id`, a.`dis_id`, b.`pro_name`, c.`city_name`, d.`dis_name` '.
            'FROM `cloud_shipping_pay` a left join `cloud_provinces` b on a.`pro_id`=b.`pro_id`'.
            ' left join `cloud_citys` c on a.`city_id`=c.`city_id` left join `cloud_districts` d on a.`dis_id`=d.`dis_id` WHERE 1=1 ' . $where .
            'LIMIT ?,?',
            array(
                $offset,
                $num
            )
        )->fetchAll();
    }

    /**
     * [getRule 获取指定快递费用规则]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @return Array
     */
    public function getRule($proId, $cityId, $disId)
    {
        $res = $this->db->query(
            'SELECT * FROM `cloud_shipping_pay` '.
            'WHERE `pro_id`=:proId AND `city_id`=:cityId AND `dis_id`=:disId LIMIT 1',
            array(
                'proId' => $proId,
                'cityId' => $cityId,
                'disId' => $disId
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [addRule 添加快递费用规则]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param int $fee
     * @return boolean
     */
    public function addRule($proId, $cityId, $disId, $fee)
    {
        return $this->db->execute(
            'INSERT INTO `cloud_shipping_pay` (`pro_id`, `city_id`, `dis_id`, `shipping_pay`) '.
            'VALUES (:proId, :cityId, :disId, :shippingPay)',
            array(
                'proId' => $proId,
                'cityId' => $cityId,
                'disId' => $disId,
                'shippingPay' => $fee
            )
        );
    }

    /**
     * [updateRule 更新快递费用规则]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param int $fee
     * @return boolean
     */
    public function updateRule($proId, $cityId, $disId, $fee)
    {
        return $this->db->execute(
            'UPDATE `cloud_shipping_pay` set `shipping_pay` = :shippingPay '.
            'WHERE `pro_id` = :proId and `city_id` = :cityId and `dis_id` = :disId',
            array(
                'shippingPay' => $fee,
                'proId' => $proId,
                'cityId' => $cityId,
                'disId' => $disId
            )
        );
    }

    /**
     * [deleteRule 删除快递费用规则]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @return int boolean
     */
    public function deleteRule($proId, $cityId, $disId)
    {
        return $this->db->execute(
            'DELETE FROM `cloud_shipping_pay` '.
            'WHERE `pro_id` = :proId and `city_id` = :cityId and `dis_id` = :disId',
            array(
                'proId' => $proId,
                'cityId' => $cityId,
                'disId' => $disId
            )
        );
    }

    /**
     * [deleteRules 批量删除快递费用规则]
     *
     * @param Array $rulesArr
     * @return boolean
     */
    public function deleteRules($rulesArr)
    {
        $rulesArrStr = '';
        foreach($rulesArr as $rule)
        {
            $rulesArrStr .= '(' . intval($rule[0]) .',' . intval($rule[1]) . ',' . intval($rule[2]) . '),';
        }
        $rulesArrStr = rtrim($rulesArrStr, ',');
        return $this->db->execute(
            "DELETE FROM `cloud_shipping_pay` ".
            "WHERE (`pro_id`, `city_id`, `dis_id`) IN ($rulesArrStr)"
        );
    }

    /**
     * [getTotalOfRules 获取所有快递费用规则数]
     *
     * @param Array $conditions
     */
    public function getTotalOfRules($conditions = [])
    {
        $where = 'WHERE 1=1';
        foreach($conditions as $key=>$condition)
        {
            $where .= " and $key=$condition";
        }
        return $this->db->query("SELECT COUNT(*) num FROM `cloud_shipping_pay` $where")->fetch();
    }

    /**
     * [getRuleByPro 根据省份获取快递费用规则]
     *
     * @param $proId
     * @return Array
     */
    public function getRuleByPro($proId)
    {
        $res = $this->db->query(
            'SELECT `shipping_pay` FROM `cloud_shipping_pay` '.
            'WHERE `pro_id`=:proId LIMIT 1',
            array(
                'proId' => $proId,
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

}