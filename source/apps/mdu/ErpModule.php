<?php

namespace Mall\Mdu;

class ErpModule extends ModuleBase
{
    const PAY_STATUS = 3;//已支付
    const DELIVERY_STATUS = 1;//未发货
    const ALREADY_DELIVERY_STATUS = 3;//已发货

    protected $erp;

    public function __construct()
    {
        $this->erp = $this->initModel('\Mall\Mdu\Models\ErpModel');
    }

    public function getOrdersList($pn, $pageNum, $startTime, $endTime)
    {
        return $this->erp->getOrderList($pn, $pageNum, self::PAY_STATUS, self::DELIVERY_STATUS, $startTime, $endTime);
    }

    public function getOrdersNum($startTime, $endTime)
    {
        return $this->erp->getOrderNum(self::PAY_STATUS, self::DELIVERY_STATUS, $startTime, $endTime)['num'];
    }

    public function uptOrderStatus($orderSn, $shippingId, $shippingSn)
    {
        if($this->erp->uptOrderStatus($orderSn, $shippingId, $shippingSn, self::ALREADY_DELIVERY_STATUS))
            return 1;
        else
            return 0;
    }
}