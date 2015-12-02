<?php

namespace Mall\Mall\Controllers;

use  Mall\Mdu\ErpModule as Erp;

class ErpController extends ControllerBase
{
    private $erp;

    public function initialize()
    {
        $this->erp = new Erp;
        $this->region = new \Mall\Mdu\RegionModule();
    }

    public function getOrdersAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('flag' => 0, 'msg' => $this->warnMsg));

            $this->view->disable();
            return;
        }

        if($this->request->isPost())
        {
            $total = -1;
            if(empty($this->_sanReq['pn']))
            {
                //获取订单总数
                $total = $this->erp->getOrdersNum($this->_sanReq['startTime'], $this->_sanReq['endTime']);
                $this->_sanReq['pn'] = 1;
            }
            if($list_source = $this->erp->getOrdersList($this->_sanReq['pn'], $this->_sanReq['pageNum'], $this->_sanReq['startTime'], $this->_sanReq['endTime']))
            {
                $list = array_map(function($val){
                    $val['order_province'] = $this->region->getProvinceById($val['order_province'])['pro_name'] ?: '';
                    $val['order_city'] = $this->region->getCityNameById($val['order_city'])['city_name'] ?: '';
                    $val['order_district'] = $this->region->getDisNameById($val['order_district'])['dis_name'] ?: '';
                    $val['order_street'] = $val['order_street'] == 0 ? $this->region->getStreetNameById($val['order_street'])['street_name'] ?: '' : '';
                    return $val;
                }, $list_source);
                echo json_encode(array('flag' => 1, 'msg' => $list, 'total' => $total));
            }
            else
                echo json_encode(array('flag' =>0, 'msg' => 'empty', 'total' => $total));

            $this->view->disable();
            return;
        }
    }

    public function updateDeliveryAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('flag' => 0, 'msg' => $this->warnMsg));

            $this->view->disable();
            return;
        }

        if($this->request->isPost())
        {
            if($this->erp->uptOrderStatus($this->_sanReq['sn'], $this->_sanReq['shippingId'], $this->_sanReq['shippingSn']) == 1)
                echo json_encode(array('flag' => 1, 'msg' => 'update delivery success'));
            else
                echo json_encode(array('flag' =>0, 'msg' => 'update delivery failed'));

            $this->view->disable();
            return;
        }
    }
}
