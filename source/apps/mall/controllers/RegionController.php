<?php

namespace Mall\Mall\Controllers;

use Mall\Mdu\RegionModule as Region;

class RegionController extends ControllerBase
{


    private $region;

    public function initialize()
    {
        $this->region = new Region();
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

}