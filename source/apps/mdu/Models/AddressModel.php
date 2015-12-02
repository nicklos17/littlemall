<?php

namespace Mall\Mdu\Models;

class AddressModel extends ModelBase
{

    const ADDRESS_NOT_DEF = 1;//[收货地址状态:非默认]
    const ADDRESS_DEF = 3;//[收货地址状态:默认]
    const NON_ADDRESS_DEF = 1;//[收货地址状态:取消默认]
    /**
     * 获取指定用户的收货地址
     *
     * @param $uid
     * @return Array
     */
    public function getUserAddress($uid)
    {
        $sql = 'SELECT p.pro_name, c.city_name,d.dis_name,s.street_name,a.u_addr_info,a.u_addr_zipcode,a.u_addr_id,a.pro_id,'
            .'a.u_addr_mobile, a.u_addr_default, a.u_addr_consignee, a.u_addr_tel FROM `cloud_user_address` a LEFT JOIN `cloud_provinces`'
            .' p ON a.pro_id=p.pro_id LEFT JOIN `cloud_citys` c ON a.city_id = c.city_id LEFT JOIN `cloud_districts` d ON'
            .' a.dis_id=d.dis_id LEFT JOIN `cloud_streets` s ON a.street_id=s.street_id WHERE a.u_id=:uid ORDER BY a.u_addr_default DESC';
        $res = $this->db->query(
            $sql,
            array(
                'uid' => $uid
            )
        );

        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * 添加地址
     *
     * @param Array $data
     * @return boolean
     */
    public function addAddress($data)
    {
        return $this->db->execute(
            'INSERT INTO `cloud_user_address` (`u_id`, `pro_id`, `city_id`, `dis_id`, `u_addr_info`, `u_addr_zipcode`, `u_addr_consignee`,'
            .' `u_addr_mobile`, `u_addr_tel`, `u_addr_default`) VALUES (?,?,?,?,?,?,?,?,?,?)',
            array(
                $data['uid'],
                $data['pro'],
                $data['city'],
                $data['dis'],
                $data['detailAddr'],
                $data['zipCode'],
                $data['consignee'],
                $data['mobile'],
                $data['tel'],
                $data['default']
            )
        );
    }

    /**
     * 更新地址
     *
     * @param $uid
     * @param Array $data
     * @return boolean
     */
    public function updateAddr($uid, $data)
    {
        return $this->db->execute(
            'UPDATE `cloud_user_address` SET `pro_id`=?, `city_id`=?, `dis_id`=?, `u_addr_info`=?, `u_addr_zipcode`=?, `u_addr_consignee`=?,'
            .' `u_addr_mobile`=?, `u_addr_tel`=?, `u_addr_default`=? WHERE `u_id`=? AND `u_addr_id`=?',
            array(
                $data['pro'],
                $data['city'],
                $data['dis'],
                $data['detailAddr'],
                $data['zipCode'],
                $data['consignee'],
                $data['mobile'],
                $data['tel'],
                $data['default'],
                $uid,
                $data['addrId']
            )
        );
    }

    /**
     * 手机更新地址
     *
     * @param $uid
     * @param Array $data
     * @return boolean
     */
    public function mobiUpdateAddr($uid, $data)
    {
        return $this->db->execute(
            'UPDATE `cloud_user_address` SET `pro_id`=?, `city_id`=?, `dis_id`=?, `u_addr_info`=?, `u_addr_zipcode`=?, `u_addr_consignee`=?,'
            .' `u_addr_mobile`=?, `u_addr_tel`=? WHERE `u_id`=? AND `u_addr_id`=?',
            array(
                $data['pro'],
                $data['city'],
                $data['dis'],
                $data['detailAddr'],
                $data['zipCode'],
                $data['consignee'],
                $data['mobile'],
                $data['tel'],
                $uid,
                $data['addrId']
            )
        );
    }


    /**
     * 将用户的收货地址设为非默认
     *
     * @param $uid
     * @return boolean
     */
    public function rmDefAddr($uid)
    {
        return $this->db->execute(
            'UPDATE `cloud_user_address` SET `u_addr_default`=? WHERE `u_id`=? AND `u_addr_default`=?',
            array(
                self::ADDRESS_NOT_DEF,
                $uid,
                self::ADDRESS_DEF
            )
        );
    }

    /**
     * 根据ID获取指定收货地址信息
     *
     * @param $uid
     * @param $aid
     * @return Array
     */
    public function getAddrById($uid, $aid)
    {
        $res = $this->db->query(
            'SELECT * FROM `cloud_user_address` WHERE `u_id`=? AND `u_addr_id`=? LIMIT 1',
            array(
                $uid,
                $aid
            )
        );

        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * 根据ID删除指定收货地址
     *
     * @param $uid
     * @param $aid
     * @return boolean
     */
    public function delAddrById($uid, $aid)
    {
        return $this->db->execute(
            'DELETE FROM `cloud_user_address` WHERE `u_id`= ? AND `u_addr_id` = ?',
            array(
                $uid,
                $aid
            )
        );
    }

    /**
     * 设置默认收货地址
     *
     * @param $uid
     * @param $aid
     * @return boolean
     */
    public function setDefAddr($uid, $aid)
    {
        return $this->db->execute(
            'UPDATE `cloud_user_address` SET `u_addr_default`= ? WHERE `u_id` = ? AND `u_addr_id` = ?',
            array(
                self::ADDRESS_DEF,
                $uid,
                $aid
            )
        );
    }

    //取消默认
    public function cancelDefAddr($uid, $aid)
    {
        return $this->db->execute(
            'UPDATE `cloud_user_address` SET `u_addr_default`= ? WHERE `u_id` = ? AND `u_addr_id` = ?',
            array(
                self::NON_ADDRESS_DEF,
                $uid,
                $aid
            )
        );
    }

}
