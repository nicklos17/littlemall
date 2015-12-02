<?php

namespace Mall\Mdu\Models;

class OrderLogsModel extends ModelBase
{

    const LOG_TYPE_ORDER = 1; //订单
    const LOG_TYPE_BACK = 3; //售后
    const ACT_ROLE_FRONT = 1; //前台用户
    const ACT_ROLE_BACKEND = 3; //后台用户

    /**
     * 添加订单日志
     *
     * @param int $orderId
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     * @return boolean
     */
    public function addOrderLog($orderId, $actType, $uid, $uname, $info)
    {
        return $this->db->execute(
            'INSERT INTO `cloud_order_logs` (`order_id`, `ord_log_type`, `ord_act_role`, `ord_act_type`, `ord_act_uid`, `ord_act_uname`, `ord_act_info`, `ord_act_addtime`) ' .
            'VALUES (?,?,?,?,?,?,?,?)',
            array(
                $orderId,
                self::LOG_TYPE_ORDER,
                self::ACT_ROLE_BACKEND,
                $actType,
                $uid,
                $uname,
                $info,
                time()
            )
        );
    }

    /**
     * 批量添加订单日志
     *
     * @param Array $idArr
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     * @return boolean
     */
    public function batchAddOrderLogs($idArr, $actType, $uid, $uname, $info)
    {
        $time = time();
        $valueArr = '';
        foreach($idArr as $id)
        {
            $valueArr .= '(' . intval($id) . ', ' . self::LOG_TYPE_ORDER . ', ' . self::ACT_ROLE_BACKEND . ", $actType, $uid, '$uname', '$info', $time),";
        }
        return $this->db->execute(
            'INSERT INTO `cloud_order_logs` (`order_id`, `ord_log_type`, `ord_act_role`, `ord_act_type`, `ord_act_uid`, `ord_act_uname`, `ord_act_info`, `ord_act_addtime`) ' .
            'VALUES ' . rtrim($valueArr, ',')
        );
    }

    /**
     * 获得指定订单的日志
     *
     * @param int $orderId
     * @return Array
     */
    public function getLogsByOrderId($orderId)
    {
        $res = $this->db->query('SELECT `ord_act_uname`, `ord_act_info`, `ord_act_addtime` FROM `cloud_order_logs` WHERE `order_id` = ? AND `ord_log_type`=?',
            array(
                $orderId,
                self::LOG_TYPE_ORDER
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * 添加售后日志(后台)
     *
     * @param int $bordId
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     * @return boolean
     */
    public function addBackLog($bordId, $actType, $uid, $uname, $info)
    {
        return $this->db->execute(
            'INSERT INTO `cloud_order_logs` (`order_id`, `ord_log_type`, `ord_act_role`, `ord_act_type`, `ord_act_uid`, `ord_act_uname`, `ord_act_info`, `ord_act_addtime`) ' .
            'VALUES (?,?,?,?,?,?,?,?)',
            array(
                $bordId,
                self::LOG_TYPE_BACK,
                self::ACT_ROLE_BACKEND,
                $actType,
                $uid,
                $uname,
                $info,
                $_SERVER['REQUEST_TIME']
            )
        );
    }

    /**
     * 添加售后日志(前台用户)
     *
     * @param $bordId
     * @param $actType
     * @param $uid
     * @param $uname
     * @param $info
     * @return boolean
     */
    public function addUserBackLog($bordId, $actType, $uid, $uname, $info)
    {
        return $this->db->execute(
            'INSERT INTO `cloud_order_logs` (`order_id`, `ord_log_type`, `ord_act_role`, `ord_act_type`, `ord_act_uid`, `ord_act_uname`, `ord_act_info`, `ord_act_addtime`) ' .
            'VALUES (?,?,?,?,?,?,?,?)',
            array(
                $bordId,
                self::LOG_TYPE_BACK,
                self::ACT_ROLE_FRONT,
                $actType,
                $uid,
                $uname,
                $info,
                $_SERVER['REQUEST_TIME']
            )
        );
    }

    /**
     * 添加售后日志(前台用户)
     *
     * @param $bordId
     * @param $actRole
     * @param $actType
     * @param $uid
     * @param $uname
     * @param $info
     * @return boolean
     */
    public function addBordLog($bordId, $actRole, $actType, $uid, $uname, $info)
    {
        return $this->db->execute(
            'INSERT INTO `cloud_order_logs` (`order_id`, `ord_log_type`, `ord_act_role`, `ord_act_type`, `ord_act_uid`, `ord_act_uname`, `ord_act_info`, `ord_act_addtime`) ' .
            'VALUES (?,?,?,?,?,?,?,?)',
            array(
                $bordId,
                self::LOG_TYPE_BACK,
                $actRole,
                $actType,
                $uid,
                $uname,
                $info,
                $_SERVER['REQUEST_TIME']
            )
        );
    }

    /**
     * 批量添加售后日志
     *
     * @param Array $idArr
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     * @return boolean
     */
    public function batchAddBackLogs($idArr, $actType, $uid, $uname, $info)
    {
        $time = time();
        $valueArr = '';
        foreach($idArr as $id)
        {
            $valueArr .= '(' . intval($id) . ', ' . self::LOG_TYPE_BACK . ', ' . self::ACT_ROLE_BACKEND . ", $actType, $uid, '$uname', '$info', $time),";
        }
        return $this->db->execute(
            'INSERT INTO `cloud_order_logs` (`order_id`, `ord_log_type`, `ord_act_role`, `ord_act_type`, `ord_act_uid`, `ord_act_uname`, `ord_act_info`, `ord_act_addtime`) ' .
            'VALUES ' . rtrim($valueArr, ',')
        );
    }

    /**
     * 根据ID获取售后订单日志
     *
     * @param $bordId
     * @return Array
     */
    public function getBackLogsById($bordId)
    {
        $res = $this->db->query('SELECT `ord_act_addtime`, `ord_act_type` FROM `cloud_order_logs` WHERE `order_id` = ? AND `ord_log_type`=?',
            array(
                $bordId,
                self::LOG_TYPE_BACK
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * 获取日志操作时间
     *
     * @param $orderId
     * @param $logType
     * @param $actType
     * @return Array
     */
    public function getLogTime($orderId, $logType, $actType)
    {
        $res = $this->db->query('SELECT `ord_act_addtime` FROM `cloud_order_logs` WHERE `order_id` = ? AND `ord_log_type`=? AND `ord_act_type`=? LIMIT 1',
            array(
                $orderId,
                $logType,
                $actType

            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * 根据订单sn获取订单ID
     *
     * @param $orderSn
     * @return Array
     */
    public function getOrderIdBySn($orderSn)
    {
        $query = $this->db->query('SELECT `order_id` FROM `cloud_orders` WHERE `order_sn` = ? LIMIT 1',
            array(
                $orderSn,
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }
}