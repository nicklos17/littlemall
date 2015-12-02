<?php

namespace Mall\Admin\Controllers;

use  Mall\Mdu\AttributesModule as Attr;


class AttributesController extends ControllerBase
{
    private $attr;

    public function initialize()
    {
        $this->attr = new Attr();
    }

    public function indexAction()
    {
        $res = $this->attr->goodAttributes();
        $this->view->setVars(array(
            'attrs' => $res,
        ));
    }

    public function editAttrAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->attr->editAttributes($this->_sanReq['aid'], $this->_sanReq['name'],
            $this->_sanReq['status'], $this->_sanReq['rank']);
            if($res == 1)
            {
                $this->log('商品属性(a_id: ' . $this->_sanReq['aid'] . ')编辑成功');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg'=> array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }
        $this->view->disable();
        return;
    }

    public function delAttrAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->attr->delAttributes($this->_sanReq['aid']);
            if($res == 1)
            {
                echo json_encode(array('ret' => 1));
                $this->log('商品属性(a_id: ' . $this->_sanReq['aid'] . ')删除成功');
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg'=> array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }
        $this->view->disable();
        return;
    }

    public function addAttrAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->attr->addAttributes($this->_sanReq['aid'], $this->_sanReq['name'], 
            $this->_sanReq['rank']);
            if($res == 1)
            {
                echo json_encode(array('ret' => 1));
                $this->log('商品属性(a_id: ' . $this->_sanReq['aid'] . ',name:' . $this->_sanReq['name'] . ')添加成功');
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg'=> array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }
        $this->view->disable();
        return;
    }
}
