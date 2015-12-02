<?php

namespace Mall\Mdu\Models;

class AttributesModel extends ModelBase
{
    public function getAllAttrs()
    {
        $query = $this->db->query('SELECT * FROM `cloud_attributes` WHERE `attrs_status` = 1 ' .
        ' ORDER BY `attrs_parent_id`, `attrs_rank` DESC');
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function setNameStatus($attrId, $name, $status, $rank)
    {
        return $this->db->execute('UPDATE `cloud_attributes` SET `attrs_name` = ?, ' .
        '`attrs_status` = ?, `attrs_rank` = ? WHERE `attrs_id` = ?',
            array(
                $name,
                $status,
                $rank,
                $attrId
            )
        );
    }

    public function getIdByName($attrId, $name)
    {
        return $this->db->query('SELECT `attrs_id` FROM `cloud_attributes` WHERE ' .
        '`attrs_name` = ? AND `attrs_id` <> ? LIMIT 1',
            array(
                $name,
                $attrId
            )
        )->fetch();
    }

    public function getIdByPidName($pAttrId, $name)
    {
        return $this->db->query('SELECT `attrs_id` FROM `cloud_attributes` WHERE ' .
        '`attrs_name` = ? AND `attrs_parent_id` = ? LIMIT 1',
            array(
                $name,
                $pAttrId
            )
        )->fetch();
    }

    /**
     * [getAttrIdByPid 判断当前父id下是否存在未删除的子属性]
     * @param  [type] $id [属性父id]
     * @return [type]     [description]
     */
    public function getIdByPid($id)
    {
        return $this->db->query('SELECT `attrs_id` FROM `cloud_attributes` WHERE ' .
        '`attrs_parent_id` = ? AND `attrs_status` = 1 LIMIT 1',
            array(
                $id
                )
        )->fetch();
    }

    public function delAttr($id)
    {
        return $this->db->execute('UPDATE `cloud_attributes` SET `attrs_status` = 3 WHERE ' .
        '`attrs_id`= ?',
            array(
                $id
            )
        );
    }

    public function addAttributes($pAttrId, $name, $rank)
    {
        return $this->db->execute('INSERT INTO `cloud_attributes` (`attrs_name`, ' .
        '`attrs_addtime`, `attrs_rank`, `attrs_parent_id`) VALUES (:name, :time, :rank, :id)', 
            array(
                'name' => $name,
                'time' => $_SERVER['REQUEST_TIME'],
                'rank' => $rank,
                'id' => $pAttrId
            )
        );
    }

    public function getParentName($attrId)
    {
        $query = $this->db->query('SELECT `attrs_name` FROM cloud_attributes WHERE ' .
        '`attrs_id` = (select `attrs_parent_id` FROM `cloud_attributes` WHERE `attrs_id` = ?) LIMIT 1',
            array(
                $attrId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**--------------------------前端----------------------------------------**/
}