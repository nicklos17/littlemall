<?php

namespace Mall\Admin\Controllers;

use Mall\Mdu\RegionModule as Region;

class RegionController extends ControllerBase
{

    const TYPE_PROVINCE = 1;//[标识: 省份]
    const TYPE_CITY     = 2;//[标识: 城市]
    const TYPE_DISTRICT = 3;//[标识: 县区]
    const TYPE_STREET   = 4;//[标识: 街道]

    private $region;

    public function initialize()
    {
        $this->region = new Region();
    }

    public function indexAction()
    {

    }

    /**
     * [ajax]返回所有省份
     *
     * @return JSON
     */
    public function getProvincesAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            echo json_encode($this->region->provincesList());
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]返回对应省份的所有市
     *
     * @return JSON
     */
    public function getCitiesAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            echo json_encode($this->region->citiesList($this->_sanReq['pro_id']));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]返回对应市的所有县区
     *
     * @return JSON
     */
    public function getDistrictsAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            echo json_encode($this->region->districtsList($this->_sanReq['city_id']));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]返回对应县区的所有街道
     *
     * @return JSON
     */
    public function getStreetsAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            echo json_encode($this->region->streetsList($this->_sanReq['dis_id']));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]更改地名
     *
     * @return JSON
     */
    public function editRegionAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->region->updateRegion($this->_sanReq['type'],$this->_sanReq['region_id'], $this->_sanReq['name'], $this->_sanReq['code']);
            if($res == 1)
            {
                $this->log('['.$this->getRegionLogTxt($this->_sanReq['type']).'] id:'.$this->_sanReq['region_id'].', 地名: '.$this->_sanReq['name'].', 地区代码: '.$this->_sanReq['code'].' 编辑成功');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]添加地区
     *
     * @return JSON
     */
    public function addRegionAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            switch($this->_sanReq['type'])
            {
                case self::TYPE_PROVINCE:
                    $res = $this->region->addProvince($this->_sanReq['name'], $this->_sanReq['code']);
                    $logContent = '[省份] 地名:'.$this->_sanReq['name'].', 地区代码:'.$this->_sanReq['code'];
                    break;
                case self::TYPE_CITY:
                    $res = $this->region->addCity($this->_sanReq['name'], $this->_sanReq['pro_id'], $this->_sanReq['code']);
                    $logContent = '[城市] 省份(pro_id: '.$this->_sanReq['pro_id'].'), 地名:'.$this->_sanReq['name'].', 地区代码:'.$this->_sanReq['code'];
                    break;
                case self::TYPE_DISTRICT:
                    $res =  $this->region->addDistrict($this->_sanReq['name'], $this->_sanReq['pro_id'], $this->_sanReq['city_id'], $this->_sanReq['code']);
                    $logContent = '[县区] 省份(pro_id: '.$this->_sanReq['pro_id'].'), 城市(city_id: '.$this->_sanReq['city_id'].'), 地名:'.$this->_sanReq['name'].', 地区代码:'.$this->_sanReq['code'];
                    break;
                case self::TYPE_STREET:
                    $res =  $this->region->addStreet($this->_sanReq['name'], $this->_sanReq['pro_id'], $this->_sanReq['city_id'], $this->_sanReq['dis_id'], $this->_sanReq['code']);
                    $logContent = '[街道] 省份(pro_id: '.$this->_sanReq['pro_id'].'), 城市(city_id: '.$this->_sanReq['city_id'].'), 县区(dis_id: '.$this->_sanReq['dis_id'].'), 地名:'.$this->_sanReq['name'].', 地区代码:'.$this->_sanReq['code'];
                    break;
                default:
                    $res = false;break;
            }
            if($res == 1)
            {
                $this->log($logContent.' 添加成功');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]删除地区
     *
     * @return JSON
     */
    public function deleteRegionAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $regIdArr = explode(',', rtrim($this->_sanReq['reg_id_arr'], ','));
            $res = $this->region->deleteRegion($this->_sanReq['type'], $regIdArr);
            if($res == 1)
            {
                $this->log('['.$this->getRegionLogTxt($this->_sanReq['type']).'] id: ('.$this->_sanReq['reg_id_arr'].') 删除成功');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * 根据type返回对应地区类型名[1:省份, 2:城市, 3:县区, 4:街道 ]
     *
     * @param $type
     * @return String
     */
    protected function getRegionLogTxt($type)
    {
        if($type == self::TYPE_PROVINCE) return '省份';
        if($type == self::TYPE_CITY) return '城市';
        if($type == self::TYPE_DISTRICT) return '县区';
        if($type == self::TYPE_STREET) return '街道';
    }

}