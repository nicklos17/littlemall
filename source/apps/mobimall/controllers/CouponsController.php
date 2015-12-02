<?php

namespace Mall\MobiMall\Controllers;

use  Mall\Mdu\CouponsModule as Coupons;
use  Mall\Utils\RedisLib;

class CouponsController extends ControllerBase
{
    const NUM_PER_PAGE = 4;//每页显示数
    const COUPONS_STATUS = 1;
    private $coupons;
    
    public function initialize()
    {
        $this->coupons= new Coupons();
    }

    public function indexAction()
    {
        $where = array('u_id' => $this->uid);

        $total = $this->coupons->getTotalCouponsByUid($where)[0];
        $p = !empty($this->request->get('page')) ? $this->request->get('page') : 1;
        if(($p - 1) * self::NUM_PER_PAGE > $total){
            echo json_encode(array());

            $this->view->disable();
            return;
        }

        $page = new \Mall\Utils\Pagination($total, self::NUM_PER_PAGE, $p);
        echo json_encode($this->coupons->getMyCoupons($page->firstRow, $page->listRows, $where));

        $this->view->disable();
        return;
    }

    protected  function filterAct($str)
    {
        if($str == 'usable')
        {
            $where['cp_used_time ='] = 0;
            $where['cp_end_time >'] = $_SERVER['REQUEST_TIME'];
        }
        elseif($str == 'used')
        {
            $where['cp_used_time >'] = 0;
        }
        elseif($str == 'expire')
        {
            $where['cp_end_time <'] = $_SERVER['REQUEST_TIME'];
        }
        else
        {
            return false;
        }

        return $where;
    }

    public function checkCouponsAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            else
            {
                if($res = $this->coupons->checkCoupons($this->_sanReq['sn'], self::COUPONS_STATUS, $_SERVER['REQUEST_TIME']))
                {
                    echo json_encode(
                        array(
                            'ret' =>1,
                            'amount' => $this->_couponsFilterAction($res)
                        )
                    );
                }
                else
                    echo json_encode(array('ret' =>0));
            }
        }

        $this->view->disable();
        return;
    }

    public function _couponsFilterAction($couponsInfo)
    {
        $amount = json_decode($couponsInfo['cr_info'],true)['amount'];
        if($couponsInfo['cr_range_type'] == 1)
        {
            return $amount;
        }
        else
        {
            //下单的商品(分立即购买与购物车购买)
            $cg = json_decode($this->cookies->get('cg')->getValue(), true);
            //获取商品信息
            $goodsInfos = [];
            if(isset($cg['n']) && $cg['n'] > 0)//立即购买
            {
                $goodsInfos[0] = $this->coupons->getParcialGinfo(intval($cg['g']));
                $goodsInfos[0]['car_good_num'] = intval($cg['n']);
                //array (size=1)  0 =>     array (size=4)      'goods_id' => string '206' (length=3)      'gcat_id' => string '3' (length=1)      'goods_price' => string '359.00' (length=6)      'car_good_num' => int 1
            }
            else
            {
                //购物车多商品 获取所有商品的信息
                //'goods_price' => string '359.00' (length=6)      'gcat_id' => string '3' (length=1)  1 =>     array (size=4)      'goods_id' => string '206' (length=3)      'car_good_num' => string '20' (length=2)      'goods_price' => string '359.00' (length=6)   
                $attrsIds = $goodsIds = [];
                foreach($cg as $val)
                {
                    array_push($attrsIds, $val['a']);
                    array_push($goodsIds, $val['g']);
                }
                $goodsInfos = $this->coupons->getCartGoods($this->uid, $goodsIds, $attrsIds);
            }

            $filterSum = 0;
            foreach ($goodsInfos as $val)
            {
                //分别统计购买商品的总价ORDER BY 商品ID 商品分类
                if($couponsInfo['cr_range_type'] == 3)
                {
                    if($val['goods_id'] == $couponsInfo['cr_range'])
                    {
                        $filterSum += $val['goods_price'] * $val['car_good_num'];
                    }
                    else
                        continue;
                }
                if($couponsInfo['cr_range_type'] == 5)
                {
                    if($val['gcat_id'] == $couponsInfo['cr_range'])
                    {
                        $filterSum += $val['goods_price'] * $val['car_good_num'];
                    }
                    else
                        continue;
                }
            }
            return sprintf('%.2f', $filterSum - $amount) > 0 ? $amount : $filterSum;;
        }
    }

}
