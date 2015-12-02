<?php

namespace Mall\Mdu;

class OrderLogsModule extends ModuleBase
{

    const LOG_TYPE_ORDER = 1; //订单
    const LOG_TYPE_BACK = 3; //售后

    protected $logs;

    public function __construct()
    {
        $this->logs = $this->initModel('\Mall\Mdu\Models\OrderLogsModel');
    }

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
        $this->logs->addOrderLog($orderId, $actType, $uid, $uname, $info);
    }

    /**
     * 通过orderSn添加订单日志
     *
     * @param int $orderId
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     * @return boolean
     */
    public function addOrderLogBySn($orderSn, $actType, $uid, $uname, $info)
    {
        //通过$orderSn获取OrderId
        $orderId = $this->logs->getOrderIdBySn($orderSn)['order_id'];
        $this->logs->addOrderLog($orderId, $actType, $uid, $uname, $info);
    }

    /**
     * 添加售后日志
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
        $this->logs->addBackLog($bordId, $actType, $uid, $uname, $info);
    }

    /**
     * 获得指定订单的日志
     *
     * @param $orderId
     * @return Array
     */
    public function getLogsByOrderId($orderId)
    {
        return $this->logs->getLogsByOrderId($orderId);
    }

    /**
     * 批量添加订单日志
     *
     * @param Array $idArr
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     */
    public function batchAddOrderLogs($idArr, $actType, $uid, $uname, $info)
    {
        return $this->logs->batchAddOrderLogs($idArr, $actType, $uid, $uname, $info);
    }

    /**
     * 批量添加售后日志
     *
     * @param Array $idArr
     * @param int $actType
     * @param int $uid
     * @param string $uname
     * @param string $info
     */
    public function batchAddBackLogs($idArr, $actType, $uid, $uname, $info)
    {
        return $this->logs->batchAddBackLogs($idArr, $actType, $uid, $uname, $info);
    }

    /**
     * 根据ID获取售后订单日志
     *
     * @param $bordId
     * @return Array
     */
    public function getBackLogsById($bordId)
    {
        return $this->logs->getBackLogsById($bordId);
    }

    /**
     * 添加售后日志(前台用户)
     *
     * @param $bordId
     * @param $actType
     * @param $uid
     * @param $uname
     * @param $info
     * @param $time
     * @return boolean
     */
    public function addUserBackLog($bordId, $actType, $uid, $uname, $info, $time)
    {
        return $this->logs->addUserBackLog($bordId, $actType, $uid, $uname, $info, $time);
    }

    /**
     * 获取订单日志操作时间
     *
     * @param $orderId
     * @param $actType
     * @return Array
     */
    public function getOrderLogTime($orderId, $actType)
    {
        return $this->logs->getLogTime($orderId, self::LOG_TYPE_ORDER, $actType);
    }

}