<?php

namespace Mall\Mdu\Models;

class CategoryModel extends ModelBase
{
    /**
     * [addCategory 添加商品分类]
     * @param [type] $pid   [分类所属分类ID]
     * @param [type] $name [分类名]
     * @param [type] $keywords [关键字]
     * @param [type] $order [排序,数字大排前面]
     * @param [type] $show [是否显示,1显示,3不显示]
     * @param [type] $desc [描述]
     */
    public function addCategory($pid, $name, $keywords, $order, $show, $desc)
    {
        return $this->db->execute('INSERT INTO  `cloud_good_cats` (`gcat_parent_id`, ' .
        '`gcat_name`, `gcat_keywords`, `gcat_order`, `gcat_show`, `gcat_desc`) VALUES (?, ?, ?, ?, ?, ?)',
            array(
                $pid,
                $name,
                $keywords,
                $order,
                $show,
                $desc
            )
        );
    }

    /**
     * [getTopCategory 获取顶级分类列表]
     * @return [type] [description]
     */
    public function getTopCategory()
    {
        $res = $this->db->query('SELECT * FROM `cloud_good_cats` WHERE `gcat_parent_id` = 0' .
        ' ORDER BY `gcat_order` DESC');
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [getSubCategory 获取子分类列表]
     * @return [type] [description]
     */
    public function getSubCategory()
    {
        $res = $this->db->query('SELECT * FROM `cloud_good_cats` WHERE `gcat_parent_id` > 0' .
        ' ORDER BY `gcat_order` DESC');
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [getSubCategory 获取分类列表]
     * @return [type] [description]
     */
    public function getAllCate()
    {
        $res = $this->db->query('SELECT `gcat_id`, `gcat_name` FROM `cloud_good_cats`');
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    public function updateCategories($catePid, $cateId, $name, $desc, $keyword, $sort, $show)
    {
        return $this->db->execute('UPDATE `cloud_good_cats` SET `gcat_parent_id` = ?, '.
        ' `gcat_name` = ?, `gcat_desc` = ?, `gcat_keywords` = ?, `gcat_order` = ?, `gcat_show` = ? WHERE `gcat_id` =  ?', 
            array(
                $catePid,
                $name,
                $desc,
                $keyword,
                $sort,
                $show,
                $cateId,
            )
            );
    }

    public function getCateInfoById($cid)
    {
        $query = $this->db->query('SELECT * FROM `cloud_good_cats` WHERE `gcat_id` = ? LIMIT 1',
            array(
                $cid
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getGoodsIdsByCateId($cid)
    {
        $query = $this->db->query('SELECT `goods_id` FROM `cloud_goods` WHERE `gcat_id` = ?' .
        ' AND `goods_status` = 1 LIMIT 1',
            array(
                $cid
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [getCateIdByPidName 通过pid和分类名查找分类id(不包含本身)]
     * @param  [type]  $pid  [description]
     * @param  [type]  $name [description]
     * @param  integer $cid  [description]
     * @return [type]        [description]
     */
    public function getCateIdByPidCidName($pid, $name, $cid)
    {
        return $this->db->query('SELECT `gcat_id` FROM `cloud_good_cats` WHERE `gcat_name`' .
        ' = ? AND `gcat_parent_id` = ? AND `gcat_id` <> ? LIMIT 1',
            array(
                $name,
                $pid,
                $cid
            )
        )->fetch();
    }

    /**
     * [getCateIdByPidName 通过pid和分类名查找分类id]
     * @param  [type]  $pid  [description]
     * @param  [type]  $name [description]
     * @param  integer $cid  [description]
     * @return [type]        [description]
     */
    public function getCateIdByPidName($pid, $name)
    {
        return $this->db->query('SELECT `gcat_id` FROM `cloud_good_cats` WHERE `gcat_name`' .
        ' = ? AND `gcat_parent_id` = ?  LIMIT 1',
                array(
                    $name,
                    $pid
                )
            )->fetch();
    }

    /**
     * [getCateIdsByPid 通过父id获取所有的子分类id,只获取一个]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getCateIdsByPid($id)
    {
        return $this->db->query('SELECT `gcat_id` FROM `cloud_good_cats` WHERE ' .
        '`gcat_parent_id` = ? LIMIT 1',
            array(
                $id
            )
        )->fetch();
    }

    /**
     * [getCateIdsByCatePid 通过分类id获取父id]
     * @param  [type] $cateId [分类id]
     * @return [type]          [description]
     */
    public function getCatePidByCateid($cateId)
    {
        return $this->db->query('SELECT `gcat_parent_id` FROM `cloud_good_cats` WHERE ' .
        '`gcat_id` = ? LIMIT 1',
            array(
                $cateId
            )
        )->fetch();
    }

    public function deleteCategories($id)
    {
        return $this->db->execute('DELETE FROM `cloud_good_cats` WHERE `gcat_id` = ?',
            array(
                $id
            )
        );
    }

    public function addCategories($pCateId, $name, $desc, $keyword, $order, $show)
    {
        return $this->db->execute('INSERT INTO `cloud_good_cats` (`gcat_name`, `gcat_desc`,' .
        '`gcat_keywords`, `gcat_order`, `gcat_show`, `gcat_parent_id`) VALUES (:name, :desc, ' .
        ':keyword, :order, :show, :pcid)',
            array(
                'name' => $name,
                'desc' => $desc,
                'keyword' => $keyword,
                'order' => $order,
                'show' => $show,
                'pcid' => $pCateId
            ));
    }
}