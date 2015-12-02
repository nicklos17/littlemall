<?php

namespace Mall\Mdu\Models;

class TagsModel extends ModelBase
{

    function getAllTags($num, $offset, $conditions)
    {
        $where = '';
        if(!empty($conditions))
        {
            $where = ' WHERE ';
            foreach($conditions as $key=>$condition)
            {
                    $where .= "`$key` = '$condition'";
            }
        }
        return $this->db->query('SELECT * FROM `cloud_tags`' . $where . 'limit ?,?',
            array(
                $num,
                $offset,
            )
        )->fetchAll();
    }

    public function updateTags($tid, $name)
    {
        return $this->db->execute('UPDATE `cloud_tags` SET `tags_name` = ? WHERE `tags_id` = ?',
            array(
                $name,
                $tid
            )
        );
    }

    public function getTidByTname($name)
    {
        return $this->db->query('SELECT `tags_id` FROM `cloud_tags` WHERE `tags_name` = ? LIMIT 1',
            array(
                $name
            )
        )->fetch();
    }

    public function deleteTags($tid)
    {
        return $this->db->execute('DELETE FROM `cloud_tags` WHERE `tags_id` = ?',
            array(
                $tid
            )
        );
    }

    public function deleteTagsByTids($tids)
    {
        $tids = '(' . $tids . ')';
        return $this->db->execute('DELETE FROM `cloud_tags` WHERE `tags_id` in ' . $tids);
    }

    /**
     * [getTagsIdByName 通过标签名获取标签id]
     * @param [string] $tagsName [标签名]
     */
    public function getTagsIdByName($tagsName)
    {
        return $this->db->query('SELECT `tags_id` FROM `cloud_tags` WHERE tags_name = ? Limit 1',
            array(
                $tagsName
            )
        )->fetch();
    }

    /**
     * [addTagsNums 新增标签数量]
     * @param [integer] $tagsId [标签ID]
     */
    public function addTagsNums($tagsId)
    {
        return $this->db->execute('UPDATE `cloud_tags` SET `tags_nums` = `tags_nums` + 1 WHERE `tags_id` = ?',
            array(
                $tagsId
            )
        );
    }

    /**
     * [addTagsNums 批量减少标签数量]
     * @param [string] $tagsId [标签字符串,逗号隔开]
     */
    public function subTagsNums($tagsIds)
    {
        $tags = '('.$tagsIds.')';
        return $this->db->execute('UPDATE `cloud_tags` SET `tags_nums` = `tags_nums` - 1 WHERE' .
        ' `tags_id` in ' . $tags
        );
    }

    /**
     * [addTags 批量标签]
     * @param [type] $tagsName [标签名]
     */
    public function addTags($tagsName)
    {
        if($this->db->execute('INSERT INTO `cloud_tags` (`tags_name`, `tags_addtime`) ' .
        'VALUES (?, ?)',
            array(
                $tagsName,
                $_SERVER['REQUEST_TIME']
            )
        ))
            return $this->db->lastInsertId();
    }

    /**
     * [getTagsNameByIds 通过标签id批量获取标签名]
     * @param  [type] $tagsIds [标签id]
     * @return [type]          [description]
     */
    public function getTagsNameByIds($tagsIds)
    {
        $query = $this->db->query(' SELECT GROUP_CONCAT(`tags_name`) as names FROM `cloud_tags`' .
        ' WHERE tags_id in ('. $tagsIds .')'
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * [getGoodsByTagId 通过标签id获取商品表里包含有此标签id的所有集和]
     * @param  [type] $tid [标签id]
     * @return [type]      [商品id集和]
     */
    public function getGoodsByTagId($tid)
    {
        return $this->db->query('SELECT GROUP_CONCAT(`goods_id`) as ids FROM `cloud_goods`' .
        ' WHERE FIND_IN_SET(?, goods_tags)',
            array(
                $tid
            )
        )->fetchAll();
    }

    /**
     * [getGoodsByTagIds 批量通过标签id获取商品表里包含有此标签id的所有集和]
     * @param  [type] $tids [标签id组合字符串]
     * @return [type]      [商品id集和]
     */
    public function getGoodsByTagIds($tids)
    {
        //拼接
        $tidsArr = explode(',', $tids);
        $len = count($tidsArr);
        $where = '';
        for ($i=0; $i < $len; $i++)
        {
            $tid = intval($tidsArr[$i]);
            if($i == 0)
                $where .= "find_in_set($tid, goods_tags)";
            else
                $where .= " OR find_in_set($tid, goods_tags)";
        }
        return $this->db->query('SELECT GROUP_CONCAT(`goods_id`) as ids FROM `cloud_goods`' .
        ' WHERE ' . $where)->fetchAll();
    }

/**
 * [UpdateGoodsByTagId 删除多个商品中的标签包含标签ID值的属性]
 * @param [type] $goodIds [description]
 * @param [type] $tagId   [description]
 */
    public function UpdateGoodsByTagId($goodIds, $tid)
    {
        return $this->db->execute('UPDATE `cloud_goods` SET `goods_tags` = ' .
        'REPLACE(goods_tags, :tags, :rep) WHERE `goods_id` IN (' . $goodIds . ')',
            array(
                'tags' => $tid . ',',
                'rep' => ''
            )
        );
    }

/**
 * [UpdateGoodsByTagIds 漂亮删除多个商品中的标签包含标签ID值的属性]
 * @param [type] $goodIds [description]
 * @param [type] $tids   [description]
 */
    public function UpdateGoodsByTagIds($goodIds, $tids)
    {
        //拼接
        $tidsArr = explode(',', $tids);
        $len = count($tidsArr);
        for ($i=0; $i < $len; $i++)
        {
            $tidsArr[$i] .= ',';
            if(!$this->db->execute('UPDATE `cloud_goods` SET `goods_tags` = REPLACE(' .
            '`goods_tags`, :tid, :rep) WHERE `goods_id` IN (' . $goodIds . ')',
                array(
                    'tid' => intval($tidsArr[$i]),
                    'rep' => ''
                )
            ))
                return false;
        }
        return true;
    }

    /**
     * [getTotalTags 获取满足条件的商品总数]
     * @return [type] [description]
     */
    public function getTotalTags($conditions)
    {
        $where = '';
        if(!empty($conditions))
        {
            $where = ' WHERE ';
            foreach($conditions as $key=>$condition)
            {
                    $where .= "`$key` = '$condition'";
            }
        }
        return $this->db->query('SELECT count(*) as total FROM `cloud_tags`' . $where)
            ->fetch();
    }
}