<?php

namespace Mall\Mdu;
use Mall\Utils\RedisLib;

class CartModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const NON_ENOUGH = 3;
    const MAX_NUM = 20;
    const MAX_NUM_ERR = 5;

    protected $cart;

    public function __construct()
    {
        $this->cart = $this->initModel('\Mall\Mdu\Models\CartModel');
    }

    public function getCartData($uid)
    {
        return $this->cart->getDataByUid($uid);
    }

    /**
     * [getCdata 获取购物车商品对应的组合商品二维码 商品信息 商品属性图片]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getCdata($uid)
    {
        return $this->cart->getCdataByUid($uid);
    }

    //获取商品信息,购物车信息以及组合商品的条形码
    public function getOrderDatas($uid, $goodsIds, $attrsIds)
    {
        return $this->cart->getOrderDatas($uid, $goodsIds, $attrsIds);
    }

    public function addCart($uid, $goodsId, $attrsIds, $num)
    {
        //判断单件商品是否超过
        if($num > self::MAX_NUM)
            return self::MAX_NUM_ERR;

        //取组合商品库存
        if(!$enableStock = $this->cart->getGoodsStock($goodsId, $attrsIds)['g_attr_nums'])
            return self::ERROR;
        else
        {
            if($num > $enableStock)
                return self::ERROR;
        }

        //判断是否已经添加过此商品
        if($cartNum = $this->cart->getCartNum($uid, $goodsId, $attrsIds)['car_good_num'])
        {
            //购物车的数量+新增的数量与可卖数判断 可卖数不足
            if($cartNum + $num > self::MAX_NUM)
                return self::MAX_NUM_ERR;

            //购物车的数量+新增的数量与可卖数判断 可卖数不足
            if($cartNum + $num > $enableStock)
                return self::ERROR;

            if($this->cart->setCartNum($uid, $goodsId, $attrsIds, $num))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
        else
        {
            //更新购物车商品总数量
            $this->di['session']->set('n', intval($this->di['session']->get('n')) + 1);

            if($this->cart->addCart($uid, $goodsId, $attrsIds, $num))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    public function delCart($uid, $goodsId, $attrsIds)
    {
        if($this->cart->delCart($uid, $goodsId, $attrsIds))
        {
            //更新购物车商品总数量
            $this->di['session']->set('n', intval($this->di['session']->get('n')) - 1);
            return self::SUCCESS;
        }
        else
            return self::ERROR;
    }

    public function batchDelCart($uid, $goodsIds, $attrsIds)
    {
        if(!$this->cart->mulDelCart($uid, $goodsIds, $attrsIds))
            return self::ERROR;
        else
        {
            //更新购物车商品总数量
            $this->di['session']->set('n', intval($this->di['session']->get('n')) - count($goodsIds));
            return self::SUCCESS;
        }
    }

    public function increase($uid, $goodsId, $attrsIds)
    {
        //取组合商品库存
        if(!$enableStock = $this->cart->getGoodsStock($goodsId, $attrsIds)['g_attr_nums'])
            return self::ERROR;
        else
        {
            //判断购物车商品数量是否大于可卖数
            if($cartNum = $this->cart->getCartNum($uid, $goodsId, $attrsIds)['car_good_num'])
            {
                //判断单件商品是否超过
                if($cartNum >= self::MAX_NUM)
                    return self::MAX_NUM_ERR;
                //可卖数不足
                if($enableStock <= $cartNum)
                    return self::NON_ENOUGH;
            }
            else
                return self::ERROR;
        }

        if($this->cart->addNumCart($uid, $goodsId, $attrsIds))
            return self::SUCCESS;
        else
            return self::ERROR;
    }

    public function decrease($uid, $goodsId, $attrsIds)
    {
        //判断购物车是否有此商品
        if(!$this->cart->getCart($uid, $goodsId, $attrsIds))
            return self::ERROR;

        if($this->cart->reduceNumCart($uid, $goodsId, $attrsIds))
            return self::SUCCESS;
        else
            return self::ERROR;
    }

    public function getGoodsInfo($uid, $goodsId, $attrsIds, $num)
    {
        //取组合商品库存
        $attrInfo = $this->cart->getGoodsStock($goodsId, $attrsIds);
        //库存不足
        if((!$enableStock = $attrInfo['g_attr_nums']) || $enableStock < $num)
            return self::ERROR;

        //获取组合商品属性
        $list = (new \Mall\Mdu\Models\GoodsModel())->getGoodsSec($goodsId);
        $list['g_attr_barcode'] = $attrInfo['g_attr_barcode'];
        return $list;
    }


    //获取组合商品属性ids对应的属性别名
    public function getAttrsNames($data)
    {
        //记录商品的属性获取
        $cacheAttr = [];
        foreach($data as $k => $d)
        {
            $tmpga = 'gAttrs' . $d['goods_id'];
            //获取该商品的组合属性（属性名）

            if(empty($cacheAttrs[$tmpga]))
            {
                $list = $this->getGoodsAttrs($d['goods_id']);
                foreach($list as $val)
                    $cacheAttrs[$tmpga][$val['attrs_id']] = $val;
            }

            //颜色/尺码的组合方式
            $attsIds = explode(',', $d['attrs_ids']);
            $data[$k]['attrs_names'] = $cacheAttrs[$tmpga][$attsIds[0]]['attrs_name'] . ','
            . $cacheAttrs[$tmpga][$attsIds[1]]['attrs_name'];

            if($cacheAttrs[$tmpga][$attsIds[0]] && strpos($cacheAttrs[$tmpga][$attsIds[0]]['attrs_img'], 'images'))
                $data[$k]['goods_img'] = $cacheAttrs[$tmpga][$attsIds[0]]['attrs_img'];
        }

        return $data;
    }

    public function getCartNames($goodsDates)
    {
     
        //以下模块获取组合商品属性的图片和属性名
        $cacheAttrs = [];
        foreach($goodsDates as $key => $d)
        {
            //判断购物车购买的每个商品的数量是否超过库存  
            if($d['car_good_num'] > $d['g_attr_nums'])
            {
                return self::ERROR;
            }     

            $tmpga = 'gAttrs' . $d['goods_id'];
            //获取该商品的组合属性（属性名）
            if(empty($cacheAttr[$tmpga]))
            {
                $list = $this->getGoodsAttrs($d['goods_id']);
                foreach($list as $val)
                    $cacheAttrs[$tmpga][$val['attrs_id']] = $val;
            }

            $attsIds = explode(',', $d['attrs_ids']);
            $goodsDates[$key]['col'] = $cacheAttrs[$tmpga][$attsIds[0]]['attrs_name'];
            $goodsDates[$key]['size'] = $cacheAttrs[$tmpga][$attsIds[1]]['attrs_name'];

            if(!empty($cacheAttrs[$tmpga][$attsIds[0]]) && strpos($cacheAttrs[$tmpga][$attsIds[0]]['attrs_img'], 'images'))
                $goodsDates[$key]['goods_img'] = $cacheAttrs[$tmpga][$attsIds[0]]['attrs_img'];
        }

        return $goodsDates;
    }

    //立即购买获取组合属性别名
        public function getBuyNowNames($goodsDates, $goodsId, $attrsIds)
    {
        $gAttrs = $this->getGoodsAttrs($goodsId);

        //获取组合属性的颜色缩略图，若没上传，选择默认的商品列表图
        $goodsDates['attrs_id'] = $attrsIds;
        $attsIds = explode(',', $attrsIds);
        $tmp = [];
        foreach ($gAttrs as $val)
        {
            $tmp[$val['attrs_id']] = $val['attrs_name'];
            if($val['attrs_id'] == $attsIds[0] && strpos($val['attrs_img'], 'images'))
                $img = $val['attrs_img'];
        }

        $goodsDates['col'] = $tmp[$attsIds[0]];
         $goodsDates['size'] = $tmp[$attsIds[1]];
        if(isset($img))
            $goodsDates['goods_img'] = $img;

        return $goodsDates;
    }

    public function getGoodsAttrs($goodsId)
    {
        $redis = RedisLib::getRedis($this->di);

        $gAttrs = $redis->get('attr:' . $goodsId);
        if(!$gAttrs)
        {
            //获取该商品的所有属性id
            $gAttrs = (new \Mall\Mdu\Models\GoodsModel())->getGaCates($goodsId);
            //商品搜索的属性id（包括尺码，颜色）缓存1天
            $redis->setex('attr:' . $goodsId, 86400, $gAttrs);
        }
        return $gAttrs;
    }

    public function getGoodsNum($uid)
    {
        return $this->cart->getNumByUid($uid)['num'];
    }

    public function expiredDel($uid)
    {
        if($num = $this->cart->expiredDel($uid))
        {
            //更新购物车商品总数量
            $this->di['session']->set('n', intval($this->di['session']->get('n')) - $num);
            return self::SUCCESS;
        }
        else
            return self::ERROR;
    }
}
