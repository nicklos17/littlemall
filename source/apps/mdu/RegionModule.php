<?php

namespace Mall\Mdu;

class RegionModule extends ModuleBase
{

    const TYPE_PROVINCE = 1;//[标识: 省份]
    const TYPE_CITY = 2;//[标识: 城市]
    const TYPE_DISTRICT = 3;//[标识: 县区]
    const TYPE_STREET = 4;//[标识: 街道]

    const SUCCESS = 1;
    const ERROR = 10000;

    protected $region;

    public function __construct()
    {
        $this->region = $this->initModel('\Mall\Mdu\Models\RegionModel');
    }

    /**
     * [provincesList 获取所有省份列表]
     *
     * @return Array
     */
    public function provincesList()
    {
        return $this->region->getAllProvinces();
    }

    /**
     * [citiesList 获取对应省份的市]
     *
     * @param int $proId
     * @return Array
     */
    public function citiesList($proId)
    {
        return $this->region->getCitiesByProId($proId);
    }

    /**
     * [districtsList 获取对应市的县区]
     *
     * @param int $cityId
     * @return Array
     */
    public function districtsList($cityId)
    {
        return $this->region->getDistrictsByCityId($cityId);
    }

    /**
     * [streetsList 获取对应县区的街道]
     *
     * @param int $disId
     * @return Array
     */
    public function streetsList($disId)
    {
        return $this->region->getStreetsByDisId($disId);
    }

    /**
     * [updateRegion 更新地名]
     *
     * @param int $type [1:省名 2：市名 3：县区名 4：街道名]
     * @param int $regionId
     * @param string $name
     * @param string $code
     * @return boolean
     */
    public function updateRegion($type, $regionId, $name, $code)
    {
        switch($type)
        {
            case self::TYPE_PROVINCE : return $this->retCode($this->region->updateProvince($regionId, $name, $code));
            case self::TYPE_CITY     : return $this->retCode($this->region->updateCity($regionId, $name, $code));
            case self::TYPE_DISTRICT : return $this->retCode($this->region->updateDistrict($regionId, $name, $code));
            case self::TYPE_STREET   : return $this->retCode($this->region->updateStreet($regionId, $name, $code));
            default : return self::ERROR;
        }
    }

    /**
     * [addProvince 添加省]
     *
     * @param string $name
     * @param string $code [optional]
     * @return boolean
     */
    public function addProvince($name, $code = '')
    {
        return $this->retCode($this->region->addProvince($name, $code));
    }

    /**
     * [addCity 添加市]
     *
     * @param string $name
     * @param int $proId
     * @param string $code [optional]
     * @return boolean
     */
    public function addCity($name, $proId, $code = '')
    {
        if(!$this->getProvinceById($proId))
        {
            return self::ERROR;
        }
        return $this->retCode($this->region->addCity($name, $proId, $code));
    }

    /**
     * [addDistrict 添加县区]
     *
     * @param string $name
     * @param int $proId
     * @param int $cityId
     * @param string $code [optional]
     * @return boolean
     */
    public function addDistrict($name, $proId, $cityId, $code = '')
    {
        if(!$this->validRegionByCity($proId, $cityId))
        {
            return self::ERROR;
        }
        return $this->retCode($this->region->addDistrict($name, $proId, $cityId, $code));
    }

    /**
     * [addStreet 添加街道]
     *
     * @param string $name
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param string $code [optional]
     * @return boolean
     */
    public function addStreet($name, $proId, $cityId, $disId, $code = '')
    {
        if(!$this->validRegionByDis($proId, $cityId, $disId))
        {
            return self::ERROR;
        }
        return $this->retCode($this->region->addStreet($name, $proId, $cityId, $disId, $code));
    }

    /**
     * [deleteRegion 删除地名]
     *
     * @param int $type [1:省名 2：市名 3：县区名 4：街道名]
     * @param Array $regIdArr
     * @return boolean
     */
    public function deleteRegion($type, $regIdArr)
    {
        switch($type)
        {
            case self::TYPE_PROVINCE : return $this->retCode($this->region->deleteProvince($regIdArr));
            case self::TYPE_CITY     : return $this->retCode($this->region->deleteCity($regIdArr));
            case self::TYPE_DISTRICT : return $this->retCode($this->region->deleteDistrict($regIdArr));
            case self::TYPE_STREET   : return $this->retCode($this->region->deleteStreetByType('street_id', $regIdArr));
            default : return false;
        }
    }

    /**
     * [deleteProvince 删除省及下属地区]
     *
     * @param int $proId
     * @return boolean
     */
    public function deleteProvince($proId)
    {
        return $this->retCode($this->region->deleteProvince($proId));
    }

    /**
     * [deleteCity 删除市及下属地区]
     *
     * @param int $cityId
     * @return boolean
     */
    public function deleteCity($cityId)
    {
        return $this->retCode($this->region->deleteCity($cityId));
    }

    /**
     * [deleteCity 删除县区及下属地区]
     *
     * @param int $disId
     * @return boolean
     */
    public function deleteDistrict($disId)
    {
        return $this->retCode($this->region->deleteDistrict($disId));
    }

    /**
     * [validRegionByStreet 判断给出的然省市区街道关系是否存在]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param int $streetId
     * @return Array
     */
    public function validRegionByStreet($proId, $cityId, $disId, $streetId)
    {
        return $this->region->getSpecifyStreet($proId, $cityId, $disId, $streetId);
    }

    /**
     * [validRegionByDis 判断给出的省市区关系是否存在]
     *
     * @param $proId
     * @param $cityId
     * @param $disId
     * @return Array
     */
    public function validRegionByDis($proId, $cityId, $disId)
    {
        return $this->region->getSpecifyDis($proId, $cityId, $disId);
    }

    /**
     * [validRegionByCity 判断给出的省市关系是否存在]
     *
     * @param $proId
     * @param $cityId
     * @return Array
     */
    public function validRegionByCity($proId, $cityId)
    {
        return $this->region->getSpecifyCity($proId, $cityId);
    }

    /**
     * [getProvinceById 根据pro_id获取省]
     *
     * @param int $proId
     * @return Array
     */
    public function getProvinceById($proId)
    {
        return $this->region->getProvinceById($proId);
    }

    /**
     * [getCityNameById 获取市名]
     *
     * @param int $cityId
     * @return Array
     */
    public function getCityNameById($cityId)
    {
        return $this->region->getCityNameById($cityId);
    }

    /**
     * [getDisNameById 获取县区名]
     *
     * @param $disId
     * @return Array
     */
    public function getDisNameById($disId)
    {
        return $this->region->getDisNameById($disId);
    }

    /**
     * [getDisNameById 获取街道名]
     *
     * @param $disId
     * @return Array
     */
    public function getStreetNameById($id)
    {
        return $this->region->getStreetNameById($id);
    }

    /**
     * [retCode 根据结果返回相应code]
     *
     * @param $flag
     * @return Code
     */
    protected function retCode($flag)
    {
        if($flag)
            return self::SUCCESS;
        else
            return self::ERROR;
    }

}