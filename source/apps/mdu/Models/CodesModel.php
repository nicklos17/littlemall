<?php

namespace Mall\Mdu\Models;

class CodesModel extends ModelBase
{
    /**
     * [addCode 添加云码]
     * @param [type] $value     [description]
     */
    public function addCode($value)
    {
         return $this->db->execute('INSERT INTO `cloud_yun_codes`(`yc_ctx`, `yc_type`, `yc_good_ids`, ' .
        '`yc_start_time`, `yc_end_time`, `yc_addtime`) '. $value);
    }

    /**
     * [getCodes 获取云码]
     * @param  [type] $num    [description]
     * @param  [type] $offset [description]
     * @param  [type] $arr    [description]
     * @return [type]         [description]
     */
    public function getCodes($num, $offset, $condition)
    {
        return $this->db->query('SELECT * FROM `cloud_yun_codes` ' . $condition . 'ORDER BY `yc_addtime` desc LIMIT ?, ? ',
            array(
                $num,
                $offset
            )
        )->fetchAll();
    }

    /**
     * [getTotalCode 获取云码总数]
     * @return [type] [description]
     */
    public function getTotalCode($condition)
    {
        return $this->db->query('SELECT count(*) as total FROM `cloud_yun_codes` ' . $condition)->fetch();
    }

    /**
     * [getCodeById description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getCodeById($id)
    {
        return $this->db->query('SELECT * FROM `cloud_yun_codes` WHERE `yc_id` = ? LIMIT 1',
            array(
                $id
            )
        )->fetch();
    }

    /**
     * [setStatus 状态使用者ID修改]
     * @param [type] $status [description]
     * @param [type] $uid    [description]
     * @param [type] $id     [description]
     */
    public function setStatus($status, $id)
    {
        return $this->db->execute('UPDATE `cloud_yun_codes` SET `yc_status` = ?  WHERE `yc_id` IN (' . $id . ')',
            array(
                $status,
            )
            );
    }

    /**
     * [getCodeByCode 获取云码]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public function getCodeByCode($code)
    {
        return $this->db->query('SELECT * FROM `cloud_yun_codes` WHERE `yc_ctx` = ?  LIMIT 1',
            array(
                $code
            )
        )->fetch();
    }

    public function getCodeByCodeId($codeId)
    {
        return $this->db->query('SELECT `yc_type`, `u_id`, `yc_good_ids`, `yc_used_time` FROM `cloud_yun_codes` WHERE `yc_id` = ?  LIMIT 1',
            array(
                $codeId
            )
        )->fetch();
    }

    public function setCodeUsed($uid, $orderId, $id, $usedTime)
    {
        return $this->db->execute('UPDATE `cloud_yun_codes` SET `yc_used_time` = ?, `u_id` = ?, `order_id` = ?  WHERE `yc_id` = ?',
            array(
                $usedTime,
                $uid,
                $orderId,
                $id
            )
        );
    }
}
