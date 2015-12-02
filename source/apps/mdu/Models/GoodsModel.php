<?php

namespace Mall\Mdu\Models;

class GoodsModel extends ModelBase
{
    public function getAllGoods($num, $offset, $conditions)
    {
        $where = ' WHERE `goods_status` > 0';
        if(!empty($conditions))
        {
            $where .= ' AND ';
            foreach($conditions as $key=>$condition)
            {
                    $where .= "`$key` = '$condition' AND ";
            }
        }
        $query = $this->db->query('SELECT * FROM `cloud_goods`'. rtrim($where, 'AND ') .' limit ?, ?',
            array(
                $num,
                $offset
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [getTotalItem 获取满足条件的商品总数]
     * @return [type] [description]
     */
    public function getTotalItem($conditions)
    {
        $where = ' WHERE `goods_status` > 0';
        if(!empty($conditions))
        {
            $where .=' AND ';
            foreach($conditions as $key=>$condition)
            {
                $where .= "`$key` = '$condition' AND ";
            }
        }
        return $this->db->query('SELECT count(*) as total FROM `cloud_goods`' . rtrim($where,'AND '))
            ->fetch();
    }

    /**
     * [addGoods 新增商品]
     * @return [type] [description]
     */
    public function addGoods($data)
    {
        if($this->db->execute('INSERT INTO `cloud_goods` (`gcat_id`, `goods_sn`, '.
            '`goods_name`, `goods_name_style`, `goods_clicks`, `goods_sales`, `goods_nums`, '.
            '`goods_virtual_sales`, `goods_market`, `goods_price`, `goods_promote`,'.
            '`goods_promote_start`, `goods_promote_end`, `goods_start`, `goods_end`, `goods_tags`,'.
            '`goods_brief`, `goods_desc`, `goods_img`, `goods_pics`, `goods_status`, `goods_type`,'.
            '`goods_order`, `goods_is_new`, `goods_is_promote`, `goods_is_hot`, `goods_addtime`, '.
            '`goods_is_warranty`, `goods_is_shipping`, `goods_attrs`) VALUES (?, ?, ?, ?, ?,?, ?,?, '.
            '?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?)',
            array(
                $data['gcat_id'],
                $data['goods_sn'],
                $data['goods_name'],
                $data['goods_name_style'],
                $data['goods_clicks'],
                $data['goods_sales'],
                $data['goods_nums'],
                $data['goods_virtual_sales'],
                $data['goods_market'],
                $data['goods_price'],
                $data['goods_promote'],
                $data['goods_promote_start'],
                $data['goods_promote_end'],
                $data['goods_start'],
                $data['goods_end'],
                $data['goods_tags'],
                $data['goods_brief'],
                $data['goods_desc'],
                $data['goods_img'],
                $data['goods_pics'],
                $data['goods_status'],
                $data['goods_type'],
                $data['goods_order'],
                $data['goods_is_new'],
                $data['goods_is_promote'],
                $data['goods_is_hot'],
                $data['goods_addtime'],
                $data['goods_is_warranty'],
                $data['goods_is_shipping'],
                $data['goods_attrs'],
            )
        ))
            return $this->db->lastInsertId();
        else
            return FALSE;
    }

    /**
     * [updateGoods 更新商品]
     * @return [type] [description]
     */
    public function updateGoods($data)
    {
        $setStrs = '';
        foreach ($data as $key => $val)
        {
            $setStrs .= ",`$key`='$val'";
        }
        return $this->db->execute('UPDATE `cloud_goods` SET ' . ltrim($setStrs, ',') . ' WHERE `goods_id` =?',
            array(
                $data['goods_id']
            )
        );
    }

    /**
     * [updateGoods 更新商品列表图和缩略图]
     * @return [type] [description]
     */
    public function setGoodsPic($goodsId, $img, $pics)
    {
        return $this->db->execute('UPDATE `cloud_goods` SET `goods_img` = ?, `goods_pics` = ? ' .
        'WHERE `goods_id` =?',
            array(
                $img,
                $pics,
                $goodsId
            )
        );
    }

    /**
     * [updateGoods 更新属性图]
     * @return [type] [description]
     */
    public function setAttrImg($goodsId, $attrsId, $pic)
    {
        return $this->db->execute('UPDATE `cloud_ga_cate` SET `attrs_img` = ? WHERE`goods_id` = ? ' .
        'AND `attrs_id` = ?',
            array(
                $pic,
                $goodsId,
                $attrsId
            )
        );
    }

    public function getAttrsByGid($goodsId)
    {
        $query = $this->db->query('SELECT * FROM `cloud_goods_attrs` WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getGaByGid($goodsId)
    {
        $query = $this->db->query('SELECT * FROM `cloud_ga_cate` WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }


    public function getAidByGid($goodsId, $attrId)
    {
        $query = $this->db->query('SELECT `goods_id` FROM `cloud_ga_cate` WHERE `goods_id` = ? ' .
        'AND attrs_id = ?',
            array(
                $goodsId,
                $attrId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [addGoodsAttrs 批量添加商品组合属性列表]
     * @param [type] $goodsId [商品id]
     */
    public function addGoodsAttrs($goodsId, $attrIds, $attrBarcode, $attrStocks, $attrEnable, $attrNums)
    {
        $insertAttrs = '';
        $len = count($attrIds);
        for ($i = 0; $i < $len; $i++)
        {
            $quot = $i == 0 ?' ':',';
            $attrNums[$i] = $attrNums[$i]?$attrNums[$i]:$attrStocks[$i];
            $insertAttrs .= $quot . '(\'' . $attrIds[$i] . '\',' . $goodsId. ',\'' . $attrBarcode[$i] . '\','
            .(int)$attrStocks[$i]. ',' . $attrEnable[$i] . ',' . (int)$attrNums[$i] . ')';
        }
        return $this->db->execute('INSERT INTO `cloud_goods_attrs` (`attrs_ids`, `goods_id`, '.
        '`g_attr_barcode`, `g_attr_stocks`, `g_attr_enable`, `g_attr_nums`) VALUES ' . $insertAttrs
        );
    }

    /**
     * [addGaCate 批量添加商品组合分类列表]
     * @param [type] $goodsId [商品id]
     */
    public function addGaCate($goodsId, $gacAttr)
    {
        $insertGacAttr = '';
        $attLen = count($gacAttr);
        for ($i = 0; $i < $attLen; $i++) {
            $quot = $i == 0 ?' ':',';
            $insertGacAttr .= $quot . '(' . $goodsId . ',' . $gacAttr[$i] .')';
        }
        return $this->db->execute('INSERT INTO `cloud_ga_cate` (`goods_id`, `attrs_id`, '.
        '`gac_parent_id`, `attrs_name`) VALUES ' . $insertGacAttr
        );
    }

    /**
     * [根据商品id获取商品的信息]
     * @param  [type] $goodsIds[商品id]
     * @return [type]          [description]
     */
    public function getGoodsInfo($goodsId)
    {
        $query = $this->db->query('SELECT `goods_id`, `goods_attrs`, `goods_tags`, `goods_pics` '.
        ' ,`goods_status` FROM `cloud_goods` WHERE `goods_id` = ? LIMIT 1',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [获取商品的某个大属性的所有值]
     * @param  [type] $goodsId [商品id]
     * @param  [type] $parentId [大属性id，比如尺码属性的id]
     * @return [type]           [返回属性的所有值，比如所有尺码]
     */
    public function getGoodsAttrs($goodsId, $parentId)
    {
        $query = $this->db->query('SELECT `goods_id`, `attrs_id`, `attrs_name` FROM'.
        '`cloud_ga_cate` WHERE `goods_id` = ? AND `gac_parent_id` = ?',
            array(
                $goodsId,
                $parentId
            )
            );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [获取某个颜色某个尺码的商品的可卖数]
     * @param  [string] $goodsId [商品id]
     * @param  [string] $colorId [颜色id]
     * @param  [string] $sizeId  [尺码id]
     * @return [string]          [返回可卖数]
     */
    public function getGoodsNum($goodsId, $colorId, $sizeId)
    {
        $query = $this->db->query('SELECT `g_attr_nums` FROM `cloud_goods_attrs` WHERE `goods_id` = ? '.
        'AND `attrs_ids` = ? AND `g_attr_enable` = 1 AND `g_attr_nums` > 0 LIMIT 1',
            array(
                $goodsId,
                $sizeId . ',' . $colorId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }


    public function setImgByGid($goodsId, $goodsPics)
    {
        return $this->db->execute('UPDATE `cloud_goods` SET `goods_pics` = ? WHERE `goods_id` = ?',
            array(
                $goodsPics,
                $goodsId
            )
        );
    }

    /**
     * [根据商品的属性id获取属性值]
     * @param  [string] $goodsId [商品id]
     * @param  [string] $attrIds  [属性id]
     * @return [array]          [description]
     */
    public function getAttrName($goodsId, $attrId)
    {
        $query = $this->db->query("SELECT `attrs_id`, `attrs_name` FROM `cloud_ga_cate` WHERE".
        "`goods_id` = ? AND `attrs_id` = ? LIMIT 1",
            array(
                $goodsId,
                $attrId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [批量获取属性名]
     * @param  [array] $attrIds [id数组]
     * @return [type]          [description]
     */
    public function getAttrNames($attrIds)
    {
        $where = '';
        foreach ($attrIds as $val)
        {
            $where .= ' (goods_id = ' . $val['goodsId'] . ' AND attrs_id = ' . $val['attrId'] . ') OR';
        }
        $where = rtrim($where, 'OR');
        $query = $this->db->query("SELECT `goods_id`, `attrs_id`, `attrs_name` FROM `cloud_ga_cate` " .
            "WHERE" . $where);
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [delAttrCateByGoodsId  删除good_attrs good_ga_cate 对应的商品id数据]
     * @param  [interge] $goodsId [商品id]
     * @return [bool] [description]
     */
    public function delAttrCateByGoodsId($goodsId)
    {
        return $this->db->execute('DELETE g, ga FROM `cloud_goods_attrs` g LEFT JOIN '.
        '`cloud_ga_cate` ga ON g.`goods_id` = ga.`goods_id`  WHERE g.`goods_id` = ?',
            array(
                $goodsId
            )
        );
    }

    public function delItem($goodsId)
    {
        return $this->db->execute('UPDATE `cloud_goods` SET `goods_status` = 0 WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        );
    }

    public function getTagsByGoodsId($goodsId)
    {
        return $this->db->query('SELECT `goods_tags` FROM `cloud_goods` WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        )->fetch();
    }

    public function getInfoByGid($goodsId)
    {
        $query = $this->db->query('SELECT * FROM `cloud_goods` WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [根据商品名称获取商品信息]
     * @param  [type] $goodsName [description]
     * @return [type]            [description]
     */
    public function getGoodsByName($goodsName)
    {
        $query = $this->db->query('SELECT `goods_id`, `goods_sn`, `goods_name`,'.
            ' `goods_market`, `goods_price`, `goods_attrs`'.
            ' FROM `cloud_goods` WHERE `goods_name` = ? LIMIT 1',
            array(
                $goodsName
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [检查商品可卖数]
     * @param  [type] $goodsId [description]
     * @param  [type] $colorId [description]
     * @param  [type] $sizeId  [description]
     * @return [type]          [description]
     */
    public function checkGoods($goodsId, $colorId, $sizeId)
    {
        $attrs = $colorId . ',' . $sizeId;
        $query = $this->db->query('SELECT `g_attr_nums` FROM `cloud_goods_attrs`'.
            ' WHERE `goods_id` = ? AND attrs_ids = ? AND g_attr_enable = 1 LIMIT 1',
            array(
                $goodsId,
                $attrs
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [批量检查商品可卖数]
     * @param  [array] $attrs [属性id]
     * @return [type]        [description]
     */
    public function batchCheckNums($attrs)
    {
        $conditions = '';
        foreach ($attrs as $value) {
            $conditions .= " goods_id = ". $value['goodsId'] . " AND attrs_ids in('" . $value['goodsAttr'] . "') OR";
        }
        $conditions = rtrim($conditions, 'OR');
        $query = $this->db->query("SELECT `goods_id`, `attrs_ids`, `g_attr_barcode`".
            "FROM `cloud_goods_attrs` WHERE g_attr_enable = 1 AND g_attr_nums > 0 AND ($conditions)");
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getAttrsImg($attrs)
    {
        $imgArr = array();
        foreach ($attrs as $key=>$value) {
            $col = explode(',', $value['goodsAttr'])[0];
            $goodsImg = $this->db->query('SELECT a.`attrs_img` , g.`goods_img` FROM `cloud_ga_cate` a LEFT JOIN `cloud_goods` g ON '.
                'a.goods_id = g.goods_id WHERE a.`goods_id`=? AND a.`attrs_id`=?',
                array(
                    $value['goodsId'],
                    $col
                )
            )->fetch();

            $imgArr[$key][$col] = $goodsImg['attrs_img'] ? :$goodsImg['goods_img'];
        }
        return $imgArr;
    }

    /**
     * [批量查询商品信息]
     * @param  [array] $goodsIds [商品id]
     * @return [type]           [description]
     */
    public function batchGoodsInfo($goodsIds)
    {
        if(count($goodsIds) === 1)
            $ids = $goodsIds[0];
        else
            $ids = implode(',', $goodsIds);
        $query = $this->db->query("SELECT `goods_id`, `goods_sn`, `goods_name`, `goods_market`, ".
            "`goods_price` FROM `cloud_goods` WHERE goods_id in($ids)");
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getIdByGoodsName($goodsName)
    {
        return $this->db->query('SELECT goods_id FROM `cloud_goods` WHERE `goods_name` = ?',
            array(
                $goodsName
            )
        )->fetch();
    }

    public function getIdByGoodsSn($goodsSn)
    {
        return $this->db->query('SELECT goods_id FROM `cloud_goods` WHERE `goods_sn` = ?',
            array(
                $goodsSn
            )
        )->fetch();
    }

    public function getGoodsIdByNameId($goodsName, $GoodsId)
    {
        return $this->db->query('SELECT `goods_id` FROM `cloud_goods` WHERE `goods_name` = ?  AND `goods_id` <> ?',
            array(
                $goodsName,
                $GoodsId
            )
        )->fetch();
    }

    public function getGoodsIdBySnId($goodsSn, $GoodsId)
    {
        return $this->db->query('SELECT goods_id FROM `cloud_goods` WHERE `goods_sn` = ? '.
        ' AND (`goods_id` > ? OR `goods_id` < ?)',
            array(
                $goodsSn,
                $GoodsId,
                $GoodsId
            )
        )->fetch();
    }

    public function attrsAlter($goodsId, $attrIds, $gAttrBarcode, $gAttrStocks, $gAttrEnable, $gAttrsNums)
    {
        return $this->db->execute('UPDATE `cloud_goods_attrs` SET `g_attr_barcode` = ?,`g_attr_stocks` = ?, `g_attr_enable` = ?, ' .
        '`g_attr_nums` = ? WHERE `goods_id` = ? AND `attrs_ids` = ?',
            array(
                $gAttrBarcode,
                $gAttrStocks,
                $gAttrEnable,
                $gAttrsNums,
                $goodsId,
                $attrIds
            )
        );
    }

    /**----------------------------------------------------------------------前端---------------------------------------------------------------------**/

    public function getGaCates($goodsId)
    {
        $query = $this->db->query('SELECT `gac_parent_id`, `attrs_id`, `attrs_name`, `attrs_img` FROM `cloud_ga_cate` ' .
        ' WHERE `goods_id` = ?',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    /**
     * [根据商品id获取商品的信息]
     * @param  [type] $goodsIds[商品id]
     * @return [type]          [description]
     */
    public function getGoodsSection($goodsId)
    {
        $query = $this->db->query('SELECT `goods_id`, `goods_name`, `goods_pics`, `goods_img`, `goods_price` '.
        ' ,`goods_brief`, `goods_desc` FROM `cloud_goods` WHERE `goods_id` = ? AND `goods_status` = 1 LIMIT 1',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getGoodsSec($goodsId)
    {
        $query = $this->db->query('SELECT `goods_id`, `goods_name`, `goods_img`, `goods_price` , `goods_sn` , '.
        '`goods_market`, `goods_type`, `gcat_id` FROM `cloud_goods` WHERE `goods_id` = ? AND `goods_status` = 1 LIMIT 1',
            array(
                $goodsId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }
}
