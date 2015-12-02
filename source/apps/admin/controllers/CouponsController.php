<?php

namespace Mall\Admin\Controllers;

use  Mall\Mdu\CouponsModule as Coupons;


class CouponsController extends ControllerBase
{
    private $coupons;
    
    public function initialize()
    {
        $this->coupons = new Coupons();
    }

    public function listCouponsAction()
    {
        if ($this->validFlag)
        {
            $where = $this->_sanReq;
        }
        else
        {
            $where = '';
        }
        $total = $this->coupons->getTotalCoupons($where);
        $page = new \Mall\Utils\Pagination($total[0], 20, (int)$this->request->get('page'));
        $couponslist = $this->coupons->showCouponsList($page->firstRow,$page->listRows,$where);
        $this->view->setVars(array(
            'couponslist' => $couponslist,
            'page' =>$page->createLink(),
        ));
    }

    public function listCouponsRuleAction()
    {
        if ($this->validFlag)
        {
            $where = $this->_sanReq;
        }
        else
        {
            $where = '';
        }
        $total = $this->coupons->getTotalCouponsRules($where);
        $page = new \Mall\Utils\Pagination($total[0], 20, (int)$this->request->get('page'));
        $couponsRuleslist = $this->coupons->showCouponsRulesList($page->firstRow,$page->listRows,$where);;
        $this->view->setVars(array(
            'couponsRuleslist' => $couponsRuleslist,
            'page' =>$page->createLink(),
        ));
    }

    public function addCouponsRuleAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                $error = json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            else
            {
                if($this->_sanReq['rangeType'] == 3)
                    $rang = $this->_sanReq['range-gid'];
                else if($this->_sanReq['rangeType'] == 5)
                    $rang = $this->_sanReq['range-cid'];
                else
                    $rang = '';
                $name = $this->_sanReq['name'];
                $type = $this->_sanReq['rangeType'];
                $info =  json_encode(array(
                    'minAmount' => $this->_sanReq['minAmount'], 
                    'maxAmount' => $this->_sanReq['maxAmount'],
                    'amount' => $this->_sanReq['amount'],
                 ));
                $status = $this->_sanReq['status'];
                $this->coupons->addCouponsRule($name, $type, $rang, $info, $status);
                $this->log('添加规则成功');
                $this->response->redirect("coupons/listCouponsRule");
            }
        }

        $this->view->setVars(array(
            'error' => isset($error) ? $error: '',
        ));
    }

    public function addCouponsAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                $error = json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            else
            {
                for ($i=0; $i < $this->_sanReq['num']; $i++) 
               {
                    $cpsn = \Mall\Utils\Inputs::createCode('6');
                    $crid = $this->_sanReq['crid'];
                    $starTime = strtotime($this->_sanReq['starTime']);
                    $endTime = strtotime($this->_sanReq['endTime']);
                    $status = $this->_sanReq['status'];
                    $value .= "('".$crid."', '".$cpsn."', '".$starTime."', '".$endTime."', '".$status."', '".$_SERVER['REQUEST_TIME']."'),";
                    $values = "VALUES ".$value;
               } 
               
                $this->coupons->batchAddCoupons(trim($values,','));
                $this->log('添加优惠期成功');
                $this->response->redirect("coupons/listCoupons");
            }
        }
        $rule = $this->coupons->showCouponsRule();
        $this->view->setVars(array(
            'rule' => $rule,
            'error' => isset($error) ? $error: '',
        ));
    }

    public function getCategoryAction()
    {
        $res = $this->coupons->showCategory();
        $arr = array();
        foreach ($res as $key => $v) 
        {
            $arr[$key]['title'] = $v['gcat_name'];
            $arr[$key]['result']['id'] = $v['gcat_id'];
        }
        
        echo json_encode(array('data' => $arr));
        $this->view->disable();
    }

    public function getGoodsNameAction()
    {
        $res = $this->coupons->showGoodsName();
        $arr = array();
        foreach ($res as $key => $v) 
        {
            $arr[$key]['title'] = $v['goods_name'];
            $arr[$key]['result']['id'] = $v['goods_id'];
        }

        echo json_encode(array('data' => $arr));
        $this->view->disable();
    }

    public function editeCouponsStatusAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->coupons->setCouponsStatus($this->_sanReq['status'], trim($this->_sanReq['id'], ','));
            if($res)
            {
                $this->log('id为'. $this->_sanReq['id'] . '状态设置' .$this->_sanReq['status']);
                echo json_encode(array('ret' =>1));
            }
        }
        $this->view->disable();
    }

    public function editeCouponsRulesStatusAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->coupons->setCouponsRulesStatus($this->_sanReq['status'], trim($this->_sanReq['id'], ','));
            if($res)
            {
                $this->log('id为'. $this->_sanReq['id'] . '状态设置' .$this->_sanReq['status']);
                echo json_encode(array('ret' =>1));
            }
        }
        $this->view->disable();
    }

}