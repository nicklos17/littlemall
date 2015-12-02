<?php

namespace Mall\Mdu\Models;

class RegionModel extends ModelBase
{

    /**
     * [getAllProvinces 获取所有省份]
     *
     * @return Array
     */
    public function getAllProvinces()
    {
        $res = $this->db->query('SELECT * FROM `cloud_provinces`');
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [getCitiesByProId 获取对应省份的市]
     *
     * @param int $proId
     * @return Array
     */
    public function getCitiesByProId($proId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_citys` WHERE `pro_id` = ?',
            array(
                $proId
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [getDistrictsByCityId 获取对应市的县区]
     *
     * @param int $cityId
     * @return Array
     */
    public function getDistrictsByCityId($cityId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_districts` WHERE `city_id` = ?',
            array(
                $cityId
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [getDistrictsByDisId 获取对应县区的乡镇街道]
     *
     * @param int $disId
     * @return Array
     */
    public function getStreetsByDisId($disId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_streets` WHERE `dis_id` = ?',
            array(
                $disId
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * [getDistrictsByCityId 获取街道名]
     *
     * @param int $id
     * @return Array
     */
    public function getStreetNameById($id)
    {
        $res = $this->db->query('SELECT * FROM `cloud_streets` WHERE `street_id` = ?',
            array(
                $id
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [updateProvince 更新省名]
     *
     * @param $proId
     * @param $name
     * @param $code
     * @return boolean
     */
    public function updateProvince($proId, $name, $code)
    {
        return $this->db->execute('UPDATE `cloud_provinces` SET `pro_name` = ?, `pro_code` = ? WHERE `pro_id` = ?',
            array(
                $name,
                $code,
                $proId
            )
        );
    }

    /**
     * [updateCity 更新市名]
     *
     * @param $cityId
     * @param $name
     * @param $code
     * @return boolean
     */
    public function updateCity($cityId, $name, $code)
    {
        return $this->db->execute('UPDATE `cloud_citys` SET `city_name` = ?, `city_code` = ? WHERE `city_id` = ?',
            array(
                $name,
                $code,
                $cityId
            )
        );
    }

    /**
     * [updateDistrict 更新县区名]
     *
     * @param $disId
     * @param $name
     * @param $code
     * @return boolean
     */
    public function updateDistrict($disId, $name, $code)
    {
        return $this->db->execute('UPDATE `cloud_districts` SET `dis_name` = ?, `dis_code` = ? WHERE `dis_id` = ?',
            array(
                $name,
                $code,
                $disId
            )
        );
    }

    /**
     * [updateStreet 更新街道名]
     *
     * @param $streetId
     * @param $name
     * @param $code
     * @return boolean
     */
    public function updateStreet($streetId, $name, $code)
    {
        return $this->db->execute('UPDATE `cloud_streets` SET `street_name` = ?, `street_code` =? WHERE `street_id` = ?',
            array(
                $name,
                $code,
                $streetId
            )
        );
    }

    /**
     * [addProvince 添加省]
     *
     * @param $name
     * @param $code
     */
    public function addProvince($name, $code)
    {
        return $this->db->execute('INSERT INTO `cloud_provinces` (`country_id`, `pro_name`, `pro_code`) VALUES (1, :name, :code)',
            array(
                'name' => $name,
                'code' => $code
            )
        );
    }

    /**
     * [addCity 添加市]
     *
     * @param $name
     * @param $proId
     * @param $code
     */
    public function addCity($name, $proId, $code)
    {
        return $this->db->execute('INSERT INTO `cloud_citys` (`pro_id`, `city_name`, `city_code`) VALUES (:proId, :name, :code)',
            array(
                'proId' => $proId,
                'name'  => $name,
                'code'  => $code
            )
        );
    }

    /**
     * [addDistrict 添加县区]
     *
     * @param $name
     * @param $proId
     * @param $cityId
     * @param $code
     */
    public function addDistrict($name, $proId, $cityId, $code)
    {
        return $this->db->execute('INSERT INTO `cloud_districts` (`pro_id`, `city_id`, `dis_name`, `dis_code`) VALUES (:proId, :cityId, :name, :code)',
            array(
                'proId' => $proId,
                'cityId'=> $cityId,
                'name'  => $name,
                'code'  => $code
            )
        );
    }

    /**
     * [addStreet 添加街道]
     *
     * @param $name
     * @param $proId
     * @param $cityId
     * @param $disId
     * @param $code
     */
    public function addStreet($name, $proId, $cityId, $disId, $code)
    {
        return $this->db->execute('INSERT INTO `cloud_streets` (`street_name`, `pro_id`, `city_id`, `dis_id`, `street_code`) VALUES (:name, :proId, :cityId, :disId, :code)',
            array(
                'proId' => $proId,
                'cityId'=> $cityId,
                'disId' => $disId,
                'name'  => $name,
                'code'  => $code
            )
        );
    }

    /**
     * [deleteProvinceById 删除省]
     *
     * @param $proIdArr
     */
    public function deleteProvinceById($proIdArr)
    {
        $filteredStr = '';
        foreach($proIdArr as $regId)
        {
            $filteredStr .= intval($regId) . ',';
        }
        $filteredStr = rtrim($filteredStr, ',');
        return $this->db->execute("DELETE FROM `cloud_provinces` WHERE `pro_id` in ($filteredStr)");
    }

    /**
     * [deleteCityByType 删除市]
     *
     * @param $type [pro_id, city_id]
     * @param $typeIdArr
     */
    public function deleteCityByType($type, $typeIdArr)
    {
        $filteredStr = '';
        foreach($typeIdArr as $regId)
        {
            $filteredStr .= intval($regId) . ',';
        }
        $filteredStr = rtrim($filteredStr, ',');
        return $this->db->execute("DELETE FROM `cloud_citys` WHERE `$type` in ($filteredStr)");
    }

    /**
     * [deleteDistrictByType 删除县区]
     *
     * @param $type [pro_id, city_id, dis_id]
     * @param $typeIdArr
     */
    public function deleteDistrictByType($type, $typeIdArr)
    {
        $filteredStr = '';
        foreach($typeIdArr as $regId)
        {
            $filteredStr .= intval($regId) . ',';
        }
        $filteredStr = rtrim($filteredStr, ',');
        return $this->db->execute("DELETE FROM `cloud_districts` WHERE `$type` in ($filteredStr)");
    }

    /**
     * [deleteStreetByType 删除街道]
     *
     * @param $type [pro_id, city_id, dis_id, street_id]
     * @param $typeIdArr
     */
    public function deleteStreetByType($type, $typeIdArr)
    {
        $filteredStr = '';
        foreach($typeIdArr as $regId)
        {
            $filteredStr .= intval($regId) . ',';
        }
        $filteredStr = rtrim($filteredStr, ',');
        return $this->db->execute("DELETE FROM `cloud_streets` WHERE `$type` in($filteredStr)");
    }

    /**
     * [deleteProvince 删除省及下属地区]
     *
     * @param $proIdArr
     * @return boolean
     */
    public function deleteProvince($proIdArr)
    {
        $this->db->begin();
        if($this->deleteStreetByType('pro_id', $proIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        if($this->deleteDistrictByType('pro_id', $proIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        if($this->deleteCityByType('pro_id', $proIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        if($this->deleteProvinceById($proIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        $this->db->commit();

        return true;
    }


    /**
     * [deleteCity 删除市及下属地区]
     *
     * @param $cityIdArr
     * @return boolean
     */
    public function deleteCity($cityIdArr)
    {
        $this->db->begin();
        if($this->deleteStreetByType('city_id', $cityIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        if($this->deleteDistrictByType('city_id', $cityIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        if($this->deleteCityByType('city_id', $cityIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        $this->db->commit();

        return true;
    }

    /**
     * [deleteDistrict 删除县区及下属地区]
     *
     * @param $disIdArr
     * @return boolean
     */
    public function deleteDistrict($disIdArr)
    {
        $this->db->begin();
        if($this->deleteStreetByType('dis_id', $disIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        if($this->deleteDistrictByType('dis_id', $disIdArr) == false)
        {
            $this->db->rollback();
            return false;
        }
        $this->db->commit();

        return true;
    }

    /**
     * [getSpecifyStreet 根据pro_id, city_id, dis_id, street_id获取街道]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param int $streetId
     *
     * @return Array
     */
    public function getSpecifyStreet($proId, $cityId, $disId, $streetId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_streets` WHERE `pro_id`=? AND `city_id`=? AND `dis_id`=? AND `street_id` = ? LIMIT 1',
            array(
                $proId,
                $cityId,
                $disId,
                $streetId
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [getSpecifyStreet 根据pro_id, city_id, dis_id获取县区]
     *
     * @param $proId
     * @param $cityId
     * @param $disId
     *
     * @return Array
     */
    public function getSpecifyDis($proId, $cityId, $disId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_districts` WHERE `pro_id`=? AND `city_id`=? AND `dis_id`=? LIMIT 1',
            array(
                $proId,
                $cityId,
                $disId,
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [getSpecifyCity 根据pro_id, city_id获取市]
     *
     * @param $proId
     * @param $cityId
     *
     * @return Array
     */
    public function getSpecifyCity($proId, $cityId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_citys` WHERE `pro_id`=? AND `city_id`=? LIMIT 1',
            array(
                $proId,
                $cityId,
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [getProvinceById 根据pro_id获取省]
     *
     * @param int $proId
     * @return Array
     */
    public function getProvinceById($proId)
    {
        $res = $this->db->query('SELECT * FROM `cloud_provinces` WHERE `pro_id`=? LIMIT 1',
            array(
                $proId,
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [getCityNameById 获取市名]
     *
     * @param int $cityId
     * @return Array
     */
    public function getCityNameById($cityId)
    {
        $res = $this->db->query('SELECT `city_name` FROM `cloud_citys` WHERE `city_id`=? LIMIT 1',
            array(
                $cityId,
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

    /**
     * [getDisNameById 获取县区名]
     *
     * @param $disId
     * @return Array
     */
    public function getDisNameById($disId)
    {
        $res = $this->db->query('SELECT `dis_name` FROM `cloud_districts` WHERE `dis_id`=? LIMIT 1',
            array(
                $disId,
            )
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetch();
    }

}