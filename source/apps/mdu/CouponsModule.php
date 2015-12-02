<?php

namespace Mall\Mdu;

class CouponsModule extends ModuleBase
{
    protected $coupons;

    public function __construct()
    {
        $this->coupons = $this->initModel('\Mall\Mdu\Models\CouponsModel');
    }

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
        return $this->coupons->addCouponsRule($name, $type, $rang, $info, $status);
    }

    /**
     * [addCoupons description]
     * @param [type] $crid     [description]
     * @param [type] $cpsn     [description]
     * @param [type] $starTime [description]
     * @param [type] $endTime  [description]
     * @param [type] $uid      [description]
     * @param [type] $status   [description]
     */
    public function batchAddCoupons($values)
    {
        return $this->coupons->addCoupons($values);
    }

    /**
     * [showCouponsRule description]
     * @return [type] [description]
     */
    public function showCouponsRule()
    {
        return $this->coupons->getCouponsRule();
    }

    /**
     * [showCouponsList 优惠券列表]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $where  [description]
     * @return [type]         [description]
     */
    public function showCouponsList($num, $offset, $where)
    {
        $condition = $this->couponsSearch($where);
        return $this->coupons->getCoupons($num, $offset, $condition);
    }

    /**
     * [getTotalCoupons 优惠券总数]
     * @return [type] [description]
     */
    public function getTotalCoupons($where)
    {
        $condition = $this->couponsSearch($where);
        return $this->coupons->getTotalCoupons($condition);
    }

    /**
     * [showCouponsList 规则列表]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $where  [description]
     * @return [type]         [description]
     */
    public function showCouponsRulesList($num, $offset, $where)
    {
        $condition = $this->couponsRuleSearch($where);
        return $this->coupons->getAllCouponsRules($num, $offset, $condition);
    }

    /**
     * [getTotalCoupons 规则总数]
     * @return [type] [description]
     */
    public function getTotalCouponsRules($where)
    {
        $condition = $this->couponsRuleSearch($where);
        return $this->coupons->getTotalCouponsRules($condition);
    }

    public function showGoodsName()
    {
        return $this->coupons->getGoodsName();
    }

    public function showCategory()
    {
        return $this->coupons->getCategory();
    }

    public function setCouponsRulesStatus($status, $id)
    {
        return $this->coupons->setCouponsRulesStatus($status, $id);
    }

    public function setCouponsStatus($status, $id)
    {
        return $this->coupons->setCouponsStatus($status, $id);
    }

    /**
     * [couponsSearch 条件搜索]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function couponsSearch($where)
    {
        if(!empty($where))
        {
            $condition = '';
            if($where['status'] == '0')
            {
                $condition .= ' where cp_status in(1,3)  ';
            }
            else
            {
                $condition .= ' where cp_status = '.$where['status'].'  ';
            }
            if(!empty($where['starTime']))
            {
                $condition .= " and cp_start_time >='".strtotime($where['starTime'])."' ";
            }
            if(!empty($where['endTime']))
            {
                $condition .= " and cp_end_time <='".strtotime($where['endTime'])."' ";
            }
            if(!empty($where['sn']))
            {
                $condition .= " and cp_sn ='".$where['sn']."'";
            }
        }
        else
        {
            $condition = '';
        }
        return $condition;
    }

    /**
     * [couponsRuleSearch 条件搜索]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function couponsRuleSearch($where)
    {
        if(!empty($where))
        {
            $condition = '';
            if($where['status'] == '0')
            {
                $condition .= ' where cr_status in(1,3)';
            }
            else
            {
                $condition .= ' where cr_status = '.$where['status'];
            }

            if(!empty($where['name']))
            {
                $condition .= " and cr_name ='".$where['name']."'";
            }
        }
        else
        {
            $condition = '';
        }

        return $condition;
    }

    public function getMyCoupons($num, $offset, $condition)
    {
        $condition = $this->myCouponsCondition($condition);
        return $this->coupons->getCouponsByUid($num, $offset, $condition);
    }

    public function getTotalCouponsByUid($condition)
    {
        $condition = $this->myCouponsCondition($condition);
        return $this->coupons->getTotalCouponsByUid($condition);
    }

    public function myCouponsCondition($where)
    {
        if(is_array($where))
        {
            $condition = ' where ';
            $field = '';
            foreach($where as $k => $v)
            {
                $arr = explode(' ', $k);
                if(count($arr)>1)
                {
                    $field .=$arr[0] . $arr[1] . "'" . $v . "'" . ' and ';
                }
                else
                {
                    $field .= "$k = '".$v."' and ";
                }
            }

            $condition .= trim($field, ' and');
        }
        else
        {
            $condition = '';
        }
        return $condition;
    }

    public function uesdCoupons($sn)
    {
        return $this->coupons->setCouponsUsedTime($sn);

    }

    public function getAllCouponsByUid($condition)
    {
        $condition = $this->myCouponsCondition($condition);
        return $this->coupons->getAllCouponsByUid($condition);
    }

    /**
     * [checkCoupons 通过优惠券兑换码获取优惠券信息]
     * @param  [type] $sn     [description]
     * @param  [type] $status [description]
     * @param  [type] $time   [description]
     * @return [type]         [description]
     */
    public function checkCoupons($sn, $status, $time)
    {
        return $this->coupons->getCouponsBySn($sn, $status, $time);
    }

    public function getParcialGinfo($goodsId)
    {
        return $this->coupons->getParcialGinfo($goodsId);
    }

    public function getCartGoods($uid, $goodsIds, $attrsIds)
    {
        return $this->coupons->getCartGoods($uid, $goodsIds, $attrsIds);
    }
}