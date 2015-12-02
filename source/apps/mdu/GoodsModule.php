<?php

namespace Mall\Mdu;

use Mall\Utils\GoodsImgUpload as ImgUpload,
       Mall\Utils\RedisLib;

class GoodsModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const NON_EXIST_GOODS = 10036;
    const NON_EXIST_GOODS_IMG = 10037;
    const NON_EXIST_ATTR = 10040;

    private $item;
    private $redis;

    public function __construct()
    {
        $this->item = $this->initModel('\Mall\Mdu\Models\GoodsModel');
        $this->tags = $this->initModel('\Mall\Mdu\Models\TagsModel');
        $this->redis = RedisLib::getRedis($this->di);
    }

    /**
     * [showItemList 商品列表]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $where  [description]
     * @return [type]         [description]
     */
    public function showItemList($num, $offset, $conditions = [])
    {
        $fieldArr = '';
        if(isset($conditions['goods_status']))
            $fieldArr['goods_status'] = $conditions['goods_status'];
        if(!empty($conditions['goods_sn']))
            $fieldArr['goods_sn'] = $conditions['goods_sn'];
        if(!empty($conditions['goods_name']))
            $fieldArr['goods_name'] = $conditions['goods_name'];

        return $this->item->getAllGoods($num,$offset,$fieldArr);
    }

    /**
     * [getGoodsInfo 商品详情]
     * @return [type] [description]
     */
    public function getGoodsInfo($goodId)
    {
        return $this->item->getInfoByGid($goodId);
    }

    /**
     * [getTotalItem 商品总数]
     * @return [type] [description]
     */
    public function getTotalItem($conditions = [])
    {
        $fieldArr = '';
        if(!empty($conditions['goods_status']))
            $fieldArr ['goods_status'] = $conditions['goods_status'];
        if(!empty($conditions['goods_sn']))
            $fieldArr ['goods_sn'] = $conditions['goods_sn'];
        if(!empty($conditions['goods_name']))
            $fieldArr ['goods_name'] = $conditions['goods_name'];

        return $this->item->getTotalItem($fieldArr)['total'];
    }

    /**
     * [getTagsNames 批量获取商品标签名]
     * @param  [string] $tagsIds [标签ids,逗号隔开]
     * @return [array]          [标签名]
     */
    public function getTagsNames($tagsIds)
    {
        return $this->tags->getTagsNameByIds($tagsIds);
    }

    /**
     * [delItem 删除商品]
     * @param  [integer] $goodsId [商品id]
     * @return [integer][1成功,0失败]
     */
    public function delItem($goodsId)
    {
        $goodsTags = $this->item->getTagsByGoodsId($goodsId);
        $this->di['db']->begin();
        if($this->item->delItem($goodsId))
        {
            if($tags = rtrim($goodsTags['goods_tags'], ','))
            {
                if($this->tags->subTagsNums($tags))
                {
                    $this->di['db']->commit();
                    return self::SUCCESS;
                }
                else
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
            }
            else
            {
                $this->di['db']->commit();
                return self::SUCCESS;
            }

        }
        else
            return self::ERROR;
    }

    /**
     * [delItem 编辑商品属性]
     * @return [integer][1成功,0失败]
     */
    public function attrsAlter($goodsId, $attrIds, $gAttrBarcode, $gAttrStocks, $gAttrEnable, $gAttrsNums)
    {
        if($this->item->attrsAlter($goodsId, $attrIds, $gAttrBarcode, $gAttrStocks, $gAttrEnable, $gAttrsNums))
        {
            $this->redis->del('attr:' . $goodsId);
            return self::SUCCESS;
        }
        else
            return self::ERROR;
    }


    /**
     * [batchDelItem 启用事物商品批量删除]
     * @param  [integer] $goodsId [商品id]
     * @return [integer][1成功,0失败]
     */
    public function batchDelItem($goodsIds)
    {
        $len = count($goodsIds);
        $this->di['db']->begin();
        for ($i = 0; $i < $len; $i++)
        {
            $goodsTags = $this->item->getTagsByGoodsId($goodsIds[$i]);
            if($this->item->delItem($goodsIds[$i]))
            {
                if($goodsTags['goods_tags'])
                {
                    if(!$this->tags->subTagsNums(rtrim($goodsTags['goods_tags'], ',')))
                    {
                        $this->di['db']->rollback();
                        return self::ERROR;
                    }
                }
            }
            else
            {
                $this->di['db']->rollback();
                return self::ERROR;
            }
        }
        $this->di['db']->commit();
        return self::SUCCESS;
    }

    /**
     * [getGoodAttrsById 通过商品id获取商品组合分类属性表数据]
     * @return [type] [description]
     */
    public function getGoodAttrsById($goodsId)
    {
        return $this->item->getAttrsByGid($goodsId);
    }

    /**
     * [getGoodGaCateById 通过商品id获取商品组合分类]
     * @return [type] [description]
     */
    public function getGoodGaCate($goodsId)
    {
        return $this->item->getGaByGid($goodsId);
    }

    /**
     * [delGoodsImg 删除对应的商品图片]
     * @param  [type] $goodsId [商品id]
     * @param  [type] $img     [图片地址]
     * @return [type]          [description]
     */
    public function delGoodsImg($goodsId, $img)
    {
        $goodsInfo = $this->item->getGoodsInfo($goodsId);
        if($goodsInfo)
        {
            $goodsImg = explode(',', $goodsInfo['goods_pics']);
            $tmpKey = array_search($img, $goodsImg);
            if($tmpKey !== 'false')

                $key = $tmpKey;
            if(isset($key))
            {
                unset($goodsImg[$key]);
                $goodsImg = implode(',', $goodsImg);
                if($this->item->setImgByGid($goodsId, $goodsImg))
                    return self::SUCCESS;
                else
                    return self::ERROR;
            }
            else
                return self::NON_EXIST_GOODS_IMG;
        }
        else
            return self::NON_EXIST_GOODS;
    }

    /**
     * [getGoodsIdByName 通过商品名获取商品id]
     * @param  [type] $goodsName [商品名]
     * @return [type]            [description]
     */
    public function getGoodsIdByName($goodsName)
    {
        return $this->item->getIdByGoodsName($goodsName);
    }

    /**
     * [getGoodsIdBySn 通过sn码获取商品id]
     * @param  [type] $goodsSn [商品sn码]
     * @return [type]          [description]
     */
    public function getGoodsIdBySn($goodsSn)
    {
        return $this->item->getIdByGoodsSn($goodsSn);
    }

    /**
     * [getGoodsIdByNameId 通过商品名，商品id判断是否存在同名商品]
     * @param  [type] $goodsName [商品名]
     * @param  [type] $GoodsId   [商品id]
     * @return [type]            [description]
     */
    public function getGoodsIdByNameId($goodsName, $GoodsId)
    {
        return $this->item->getGoodsIdByNameId($goodsName, $GoodsId);
    }

    /**
     * [getGoodsIdBySnId 通过商品sn，商品id判断是否存在同名商品]
     * @param  [type] $goodsSn [商品sn码]
     * @param  [type] $GoodsId [商品id]
     * @return [type]          [description]
     */
    public function getGoodsIdBySnId($goodsSn, $GoodsId)
    {
        return $this->item->getGoodsIdBySnId($goodsSn, $GoodsId);
    }

    /**
     * [setAttrImg 通过商品id，属性id修改商品图片]
     * @param  [type] $gid [商品id]
     * @param  [type] $aid [属性id]
     * @param  [type] $file [文件流]
     * @return [type]          [description]
     */
    public function setAttrImg($gid, $aid, $file)
    {
        //判断商品对应的属性是否存在
        if(!$this->item->getAidByGid($gid, $aid))
            return self::NON_EXIST_ATTR;
        else
        {
            //图片上传
            $config=$this->di->get('sysconfig');
            $upload = new ImgUpload($this->di);
            //商品列表图片处理
            $pic = '';
            if(isset($_FILES['upfile']) && $_FILES['upfile']['tmp_name'])
            {
                $pic = $upload->upload_file($_FILES['upfile'], $config['attrsImg'],
                 'attr-original-' . $gid, 'attr-thumb-' . $gid,
                  596, 359, 100 ,100);
                if($upload->errmsg)
                {
                    return self::ERROR;
                }
                $pic = $config['staticServer'].$config['attrsImgAccess'].'/'.$pic;
            }
            if($pic)
            {
                $this->redis->del('attr:' . $gid);
                if($this->item->setAttrImg($gid, $aid, $pic))
                    return array($pic);
                else
                    return self::ERROR;
            }
            else
                return self::ERROR;
        }

    }

    /**
     * [addItem 商品]
     * @return [type] [description]
     */
    public function addItem($data)
    {
        $data['goods_addtime'] = $_SERVER['REQUEST_TIME'];
        $data['goods_promote_start'] = strtotime($data['goods_promote_start']);
        $data['goods_promote_end'] = strtotime($data['goods_promote_end']);
        $data['goods_start'] = strtotime($data['goods_start']);
        $data['goods_end'] = strtotime($data['goods_end']);
        //统计所有组合的商品数量及可卖数
        $len = count($data['attr_ids']);
        $data['goods_nums'] = $data['goods_virtual_sales'] = 0;
        for ($i = 0; $i < $len; $i++)
        {
            $data['goods_nums'] += $data['g_attr_stocks'][$i];
            if($data['g_attr_nums'][$i])
                $data['goods_virtual_sales'] += $data['g_attr_nums'][$i];
            else
                $data['goods_virtual_sales'] += $data['g_attr_stocks'][$i];
        }

        //商品(父)属性组合ID
        foreach ($data['goods-attrs'] as $key => $val)
        {
            if($key==0)
                $data['goods_attrs'] = $val;
            else
                $data['goods_attrs'] .= ','.$val;
        }
            $data['goods_img'] = '';
            $data['goods_pics'] = '';
        //处理商品属性表
        $this->di['db']->begin();

        //处理商品标签表
        if(!empty($data['goods_tags']))
            $tags = explode(',' ,$data['goods_tags']);
        else
            $tags = [];
        //插入标签表不存在的标签,并返回所有的商品标签id
        $len = count($tags);
        $googsTags = array();
        if($len)
        {
            for($i = 0; $i < $len; $i++)
            {
                if($i ==5)
                    break;
                else
                {
                    //判断标签是否存在
                    $ex = $this->tags->getTagsIdByName($tags[$i]);
                    if($ex)
                    {
                        //数量加１
                        if($this->tags->addTagsNums($ex['tags_id']))
                            array_push($googsTags, $ex['tags_id']);
                        else
                        {
                            $this->di['db']->rollback();
                            return self::ERROR;
                        }
                    }
                    else
                    {
                        if($tmpTagsId = $this->tags->addTags($tags[$i]))
                            array_push($googsTags, $tmpTagsId);
                        else
                        {
                            $this->di['db']->rollback();
                            return self::ERROR;
                        }
                    }
                }
            }
            $data['goods_tags'] = implode(',', $googsTags ).',';
        }
        else
            $data['goods_tags'] = '';


        if($goodsId = $this->item->addGoods($data))
        {
            //处理商品图片
            $config=$this->di->get('sysconfig');
            $upload = new ImgUpload($this->di);
            //商品列表图片处理
            if(isset($_FILES['goods_img']) && $_FILES['goods_img']['tmp_name'])
            {
                $pic = $upload->upload_file($_FILES['goods_img'], $config['goodsImg'],
                 'item-list-original-' . $goodsId, 'item-list-thumb-' . $goodsId,
                 200, 200, 100 ,100);
                //抛出报错情况
                if($upload->errmsg)
                {
                    echo $upload->errmsg;
                    return self::ERROR;
                }
                $tmpImg['goods_img'] = $config['staticServer'].$config['goodsImgAccess'].'/'.$pic;
            }
            else
                $tmpImg['goods_img'] = '';

            //商品缩略图片处理
            if(isset($_FILES['goods_pics']) && $_FILES['goods_pics']['tmp_name'][0])
            {
                $imgNum = count($_FILES['goods_pics']['tmp_name']);
                $thumbImg = array();
                for ($i = 0; $i < $imgNum; $i++)
                {
                    if($_FILES['goods_pics']['tmp_name'][$i])
                    {
                        $tmpPicArr['name'] = $_FILES['goods_pics']['name'][$i];
                        $tmpPicArr['type'] = $_FILES['goods_pics']['type'][$i];
                        $tmpPicArr['tmp_name'] = $_FILES['goods_pics']['tmp_name'][$i];
                        $tmpPicArr['error'] = $_FILES['goods_pics']['error'][$i];
                        $tmpPicArr['size'] = $_FILES['goods_pics']['size'][$i];

                        $tmpPicThumb = $upload->upload_file($tmpPicArr, $config['goodsThumb'],
                        'item-original-' . $goodsId .'-' .$i, 'item-thumb-' . $goodsId .'-' .$i,
                        596, 359, 64 , 64);
                        //抛出报错情况
                        if($upload->errmsg)
                        {
                            echo $upload->errmsg;
                            return self::ERROR;
                        }
                        array_push($thumbImg, $config['staticServer'].$config['goodsThumbAccess'].'/'.$tmpPicThumb);
                    }

                }
                $tmpImg['goods_pics'] = implode(',', $thumbImg);
            }
            else
                $tmpImg['goods_pics'] = '';

            //添加商品图片
            if(!empty($tmpImg['goods_img']) || !empty($tmpImg['goods_pics']))
            {
                if(!$this->item->setGoodsPic($goodsId, $tmpImg['goods_img'], $tmpImg['goods_pics']))
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
            }
            //添加组合商品各属性
            if($this->item->addGoodsAttrs($goodsId, $data['attr_ids'], $data['g_attr_barcode'],
             $data['g_attr_stocks'], $data['g_attr_enable'], $data['g_attr_nums']))
            {
                if($this->item->addGaCate($goodsId, $data['gac_attr']))
                {
                    $this->di['db']->commit();
                    return self::SUCCESS;
                }
                else
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
            }
            else
            {
                $this->di['db']->rollback();
                return self::ERROR;
            }
        }
        else
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }
    }

    /**
     * [updateItem 商品编辑]
     * @return [type] [description]
     */
    public function updateItem($data)
    {
        $data['goods_promote_start'] = strtotime($data['goods_promote_start']);
        $data['goods_promote_end'] = strtotime($data['goods_promote_end']);
        $data['goods_start'] = strtotime($data['goods_start']);
        $data['goods_end'] = strtotime($data['goods_end']);

        //统计所有组合的商品数量及可卖数(未修改不更新)
        if(!empty($data['attr_ids']))
        {
            $len = count($data['attr_ids']);
            $data['goods_nums'] = $data['goods_virtual_sales'] = 0;
            for ($i = 0; $i < $len; $i++)
            {
                $data['goods_nums'] += $data['g_attr_stocks'][$i];
                if($data['g_attr_nums'][$i])
                    $data['goods_virtual_sales'] += $data['g_attr_nums'][$i];
                else
                    $data['goods_virtual_sales'] += $data['g_attr_stocks'][$i];
            }

            //商品(父)属性组合ID
            foreach ($data['goods-attrs'] as $key => $val)
            {
                if($key==0)
                    $data['goods_attrs'] = $val;
                else
                    $data['goods_attrs'] .= ','.$val;
            }
            //操作是属性表的数据
            $dataAttrs['gac_attr'] = $data['gac_attr'];
            $dataAttrs['attr_ids'] = $data['attr_ids'];
            $dataAttrs['g_attr_barcode'] = $data['g_attr_barcode'];
            $dataAttrs['g_attr_stocks'] = $data['g_attr_stocks'];
            $dataAttrs['g_attr_enable'] = $data['g_attr_enable'];
            $dataAttrs['g_attr_nums'] = $data['g_attr_nums'];

            unset($data['goods-attrs']);
            unset($data['gac_attr']);
            unset($data['attr_ids']);
            unset($data['g_attr_barcode']);
            unset($data['g_attr_stocks']);
            unset($data['g_attr_enable']);
            unset($data['g_attr_nums']);
            if(isset($data['upfile']))
                unset($data['upfile']);
        }
        else
        {
            //不更改商品组合属性,此字段不更新
            unset($data['goods-attrs']);
            unset($data['gac_attr']);
            unset($data['g_attr_enable']);
        }

        //获取商品部分信息
        $goodsInfo = $this->item->getGoodsInfo($data['goods_id']);
        //商品列表图片处理
        $config=$this->di->get('sysconfig');
        $upload = new ImgUpload($this->di);

        if(!empty($_FILES['goods_img']['tmp_name']))
        {
            $pic = $upload->upload_file($_FILES['goods_img'], $config['goodsImg'],
             'item-list-original-' . $data['goods_id'], 'item-list-thumb-' . $data['goods_id'],
             200, 200, 100 ,100);
            //抛出报错情况
            if($upload->errmsg)
            {
                echo $upload->errmsg;
                return self::ERROR;
            }
            $data['goods_img'] = $config['staticServer'].$config['goodsImgAccess'].'/'.$pic;
        }
        //商品缩略图片处理

        if(!empty($_FILES['goods_pics']['tmp_name'][0]))
        {
            $imgNum = count($_FILES['goods_pics']['tmp_name']);
            $thumbImg = array();
            for ($i = 0; $i < $imgNum; $i++)
            {
                if($_FILES['goods_pics']['tmp_name'][$i])
                {
                    $tmpPicArr['name'] = $_FILES['goods_pics']['name'][$i];
                    $tmpPicArr['type'] = $_FILES['goods_pics']['type'][$i];
                    $tmpPicArr['tmp_name'] = $_FILES['goods_pics']['tmp_name'][$i];
                    $tmpPicArr['error'] = $_FILES['goods_pics']['error'][$i];
                    $tmpPicArr['size'] = $_FILES['goods_pics']['size'][$i];

                    $tmpPicThumb = $upload->upload_file($tmpPicArr, $config['goodsThumb'],
                    'item-original-' . $data['goods_id'] .'-' .$i, 'item-thumb-' . $data['goods_id'] .'-' .$i,
                    596, 359, 64 , 64);
                    //抛出报错情况
                    if($upload->errmsg)
                    {
                        echo $upload->errmsg;
                        return self::ERROR;
                    }
                    array_push($thumbImg, $config['staticServer'].$config['goodsThumbAccess'].'/'.$tmpPicThumb);
                }
            }
            //拼接原有的数据
            if($goodsInfo['goods_pics'])
                $data['goods_pics'] = $goodsInfo['goods_pics'] . ',' . implode(',', $thumbImg);
            else
                $data['goods_pics'] = implode(',', $thumbImg);
        }
        if(empty($data['goods_img']))
            unset($data['goods_img']);

        if(empty($data['goods_pics'][0]))
            unset($data['goods_pics']);
        //处理商品属性表
        $this->di['db']->begin();
        //处理商品标签表
        if(!empty($data['goods_tags']))
            $tags = explode(',' ,$data['goods_tags']);
        else
            $tags = [];
        //扣除之前商品对应标签字段包含的标签id -１
        //若是已删除的商品不需要扣除
        if($goodsInfo['goods_status']&&rtrim($goodsInfo['goods_tags']))
        {
            if(!$this->tags->subTagsNums(rtrim($goodsInfo['goods_tags'], ',')))
            {
                $this->di['db']->rollback();
                return self::ERROR;
            }
        }

        //插入标签表不存在的标签,并返回所有的商品标签id
        $len = count($tags);
        $googsTags = array();
        if($len)
        {
            for($i = 0; $i < $len; $i++)
            {
                if($i ==5)
                    break;
                else
                {
                    //判断标签是否存在
                    $ex = $this->tags->getTagsIdByName($tags[$i]);
                    if($ex)
                    {
                        //数量加１
                        if($this->tags->addTagsNums($ex['tags_id']))
                            array_push($googsTags, $ex['tags_id']);
                        else
                        {
                            $this->di['db']->rollback();
                            return self::ERROR;
                        }
                    }
                    else
                    {
                        if($tmpTagsId = $this->tags->addTags($tags[$i]))
                            array_push($googsTags, $tmpTagsId);
                        else
                        {
                            $this->di['db']->rollback();
                            return self::ERROR;
                        }
                    }
                }
            }
            $data['goods_tags'] = implode(',', $googsTags ).',';
        }
        else
            $data['goods_tags'] = '';

        if($this->item->updateGoods($data))
        {
            if(!empty($dataAttrs))
            {
                //删除good_attrs good_ga_cate 对应的商品id数据
                if(!$this->item->delAttrCateByGoodsId($data['goods_id']))
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
                //添加组合商品各属性
                if($this->item->addGoodsAttrs($data['goods_id'], $dataAttrs['attr_ids'], $dataAttrs['g_attr_barcode'],
                $dataAttrs['g_attr_stocks'], $dataAttrs['g_attr_enable'], $dataAttrs['g_attr_nums']))
                {
                    if(!$this->item->addGaCate($data['goods_id'], $dataAttrs['gac_attr']))
                    {
                        $this->di['db']->rollback();
                        return self::ERROR;
                    }
                }
                else
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
            }

            $this->redis->del('attr:' . $data['goods_id']);
            //清除商品信息缓存
            $arr = $this->redis->keys ("item:{$data['goods_id']}*");
            foreach ($arr as $val)
            {
                $this->redis->del($val);
            }

            $this->di['db']->commit();
            return self::SUCCESS;
        }
        else
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }
    }


    /**----------------------------------------------------------------------前端---------------------------------------------------------------------**/

    /**
     * [getGaCates 通过商品id获取所有商品组合属性]
     * @return [type] [description]
     */
    public function getGaCates($goodsId)
    {
        return $this->item->getGaCates($goodsId);
    }

        /**
     * [getGoodsSection 商品部分详情]
     * @return [type] [description]
     */
    public function getGoodsSection($goodsId)
    {
        return $this->item->getGoodsSection($goodsId);
    }

        /**
     * [filterCol 过滤商品属性]
     * @param  [type] $filterId [过滤的商品属性Id]
     * @param  [type] $pAttrsId [对应的属性的父id]
     * @return [type] [description]
     */
    public function filterCol($goodsId, $filterId, $pAttrsId)
    {
        $gaAttrs = $this->redis->get('attr:' . $goodsId);
        if(!$gaAttrs)
            return self::ERROR;
        //通过商品id和过滤的属性id查询数据库匹配
        $tmpFilter = $tmpKey = [];

        foreach ($gaAttrs as $val)
        {
            if($val['gac_parent_id'] == $pAttrsId)
            {
                $tmpFilter[$val['attrs_id']] = 'item:' . $goodsId . ':' . $filterId . ',' . $val['attrs_id'];
                array_push($tmpKey, $val['attrs_id']);
            }
        }

        $filterDate = $this->redis->mget($tmpFilter);
        $filterRet = [];
        //返回库存不足或者不存在组合属性的商品属性id
        foreach ($filterDate as $key => $val)
        {
            if(!$val || $val['g_attr_nums'] <= 0 || $val['g_attr_enable'] == 3)
                array_push($filterRet, $tmpKey[$key]);
        }

        return $filterRet;
    }

        /**
     * [filterSize 过滤商品尺码]
     * @param  [type] $filterId [过滤的商品属性Id]
     * @param  [type] $pAttrsId [对应的属性的父id]
     * @return [type] [description]
     */
    public function filterSize($goodsId, $filterId, $pAttrsId)
    {
        $gaAttrs = $this->redis->get('attr:' . $goodsId);
        if(!$gaAttrs)
            return self::ERROR;
        //通过商品id和过滤的属性id查询数据库匹配
        $tmpFilter = $tmpKey = [];

        foreach ($gaAttrs as $val)
        {
            if($val['gac_parent_id'] == $pAttrsId)
            {
                $tmpFilter[$val['attrs_id']] = 'item:' . $goodsId . ':' . $val['attrs_id'] . ',' . $filterId;
                array_push($tmpKey, $val['attrs_id']);
            }
        }

        $filterDate = $this->redis->mget($tmpFilter);
        $filterRet = [];
        //返回库存不足或者不存在组合属性的商品属性id
        foreach ($filterDate as $key => $val)
        {
            if(!$val || $val['g_attr_nums'] <= 0)
                array_push($filterRet, $tmpKey[$key]);
        }

        return $filterRet;
    }
}

