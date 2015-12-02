<?php

namespace Mall\Admin\Controllers;

class MallHomeController extends ControllerBase
{
    public function indexAction()
    {
        $indexContent = file_get_contents(__DIR__ . '/../../mall/views/index/index.phtml');
        $this->view->setVars(array(
            'indexContent' => $indexContent
        ));
    }

    public function mobiAction()
    {
        $indexContent = file_get_contents(__DIR__ . '/../../../mobipublic/ng/views/index/index.html');
        $this->view->setVars(array(
            'indexContent' => $indexContent
        ));
    }


    public function mobiEditAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
                $this->view->disable();
                return;
            }

            if(file_put_contents(__DIR__ . '/../../../mobipublic/ng/views/index/index.html', $this->_sanReq['content']))
            {
                echo json_encode(array(
                    'ret' => 1
                ));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => '操作失败,请重新尝试'
                    )
                ));
            $this->view->disable();
            return;
        }
    }

    public function editContentAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
                $this->view->disable();
                return;
            }

            if(file_put_contents(__DIR__ . '/../../mall/views/index/index.phtml', $this->_sanReq['content']))
            {
                echo json_encode(array(
                    'ret' => 1
                ));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => '操作失败,请重新尝试'
                    )
                ));
            $this->view->disable();
            return;
        }
    }
}
