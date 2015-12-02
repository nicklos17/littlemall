<?php

namespace Mall\Mall\Controllers;

use  Mall\Mdu\AddressModule as Address;

class AddressController extends ControllerBase
{

    private $address;

    public function initialize()
    {
        $this->address = new Address();
    }

    public function indexAction()
    {
        $this->view->setVars(
            array(
                'addressList' => $this->address->getUserAddress($this->uid),
            )
        );
    }

    /**
     * [ajax:添加收货地址]
     */
    public function addAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            if($this->address->addAddress($this->uid, $this->_sanReq))
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax:修改收货地址]
     */
    public function editAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            if($this->address->updateAddr($this->uid, $this->_sanReq))
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax:获取指定收货地址信息]
     */
    public function getAddrAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            if($res = $this->address->getAddrById($this->uid, $this->_sanReq['aid']))
            {
                echo json_encode(array('ret' => 1, 'data' => $res));
            }
            else
            {
                echo json_encode(array('ret' => 0));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax:删除指定收货地址]
     */
    public function delAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0));
        }
        else
        {
            if($res = $this->address->delAddrById($this->uid, $this->_sanReq['aid']))
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax:设置默认收货地址]
     */
    public function setDefaultAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0));
        }
        else
        {
            if($res = $this->address->setDefAddr($this->uid, $this->_sanReq['aid']))
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0));
            }
        }

        $this->view->disable();
        return;
    }

}