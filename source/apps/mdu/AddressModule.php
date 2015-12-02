<?php

namespace Mall\Mdu;

class AddressModule extends ModuleBase
{

    protected $address;

    public function __construct()
    {
        $this->address = $this->initModel('\Mall\Mdu\Models\AddressModel');
    }

    /**
     * 获取指定用户的收货地址
     *
     * @param $uid
     * @return Array
     */
    public function getUserAddress($uid)
    {
        return $this->address->getUserAddress($uid);
    }

    /**
     * 添加地址
     *
     * @param $uid
     * @param Array $data
     * @return boolean
     */
    public function addAddress($uid, $data)
    {
        //判断省市区是否关联
        if(!(new \Mall\Mdu\Models\RegionModel())->getSpecifyDis($data['pro'], $data['city'], $data['dis']))
        {
            return false;
        }

        $this->di['db']->begin();
        if(isset($data['default']) && $data['default'] == 'on')
        {
            if($this->address->rmDefAddr($uid))
            {
                $data['default'] = 3;
            }
            else
            {
                $this->di['db']->rollback();
            }
        }
        else
        {
            $data['default'] = 1;
        }

        $data['uid'] = $uid;
        $data['tel'] = '';
        if($data['areaCode'])
        {
            $data['tel'] .= $data['areaCode'] . '-' . $data['telNum'];
            if(!empty($data['ext']))
            {
                $data['tel'] .= '-' . $data['ext'];
            }
        }

        if($this->address->addAddress($data))
        {
            $this->di['db']->commit();
            return true;
        }
        else
        {
            $this->di['db']->rollback();
            return false;
        }
    }

     /**
     * 更新地址
     *
     * @param $uid
     * @param Array $data
     * @return boolean
     */
    public function updateAddr($uid, $data)
    {
        $this->di['db']->begin();
        if(isset($data['default']) && $data['default'] == 'on')
        {
            if($this->address->rmDefAddr($uid))
            {
                $data['default'] = 3;
            }
            else
            {
                $this->di['db']->rollback();
            }
        }
        else
        {
            $data['default'] = 1;
        }

        $data['tel'] = '';
        if($data['areaCode'])
        {
            $data['tel'] .= $data['areaCode'] . '-' . $data['telNum'];
            if($data['ext'])
            {
                $data['tel'] .= '-' . $data['ext'];
            }
        }

        if($this->address->updateAddr($uid, $data))
        {
            $this->di['db']->commit();
            return true;
        }
        else
        {
            $this->di['db']->rollback();
            return false;
        }
    }

   /**
     * 手机更新地址
     *
     * @param $uid
     * @param Array $data
     * @return boolean
     */
    public function mobiUpdateAddr($uid, $data)
    {
        $data['tel'] = '';
        if($data['areaCode'])
        {
            $data['tel'] .= $data['areaCode'] . '-' . $data['telNum'];
            if($data['ext'])
            {
                $data['tel'] .= '-' . $data['ext'];
            }
        }

        if($this->address->mobiUpdateAddr($uid, $data))
            return true;
        else
            return false;
    }

    /**
     * 根据ID获取指定收货地址信息
     *
     * @param $uid
     * @param $aid
     * @return Array
     */
    public function getAddrById($uid, $aid)
    {
        return $this->address->getAddrById($uid, $aid);
    }

    /**
     * 根据ID删除指定收货地址
     *
     * @param $uid
     * @param $aid
     * @return boolean
     */
    public function delAddrById($uid, $aid)
    {
        return $this->address->delAddrById($uid, $aid);
    }

    /**
     * 设置默认收货地址
     *
     * @param $uid
     * @param $aid
     * @return boolean
     */
    public function setDefAddr($uid, $aid)
    {
        $this->di['db']->begin();
        if(!$this->address->rmDefAddr($uid))
        {
            $this->di['db']->rollback();
            return false;
        }
        else
        {
            if($this->address->setDefAddr($uid, $aid))
            {
                $this->di['db']->commit();
                return true;
            }
            else
            {
                $this->di['db']->rollback();
                return false;
            }
        }
    }

    /**
     * 设置默认收货地址
     *
     * @param $uid
     * @param $aid
     * @return boolean
     */
    public function cancelDefAddr($uid, $aid)
    {
        if($this->address->cancelDefAddr($uid, $aid))
            return true;
        else
            return false;
    }
}
