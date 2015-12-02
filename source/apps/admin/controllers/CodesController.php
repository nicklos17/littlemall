<?php

namespace Mall\Admin\Controllers;

use  Mall\Mdu\CodesModule as Code;


class CodesController extends ControllerBase
{
    private $code;
    
    public function initialize()
    {
        $this->code= new Code();
    }

    /**
     * [indexAction 云码列表]
     * @return [type] [description]
     */
    public function indexAction()
    {
        if ($this->validFlag)
        {
            $where = $this->_sanReq;
        }
        else
        {
            $where = '';
        }

         $total = $this->code->getTotalCode($where);
         $page = new \Mall\Utils\Pagination($total[0], 20, (int)$this->request->get('page'));

         $codelist = $this->code->showCodeList($page->firstRow, $page->listRows, $where);
         $this->view->setVars(array(
            'codelist' => $codelist,
            'page' =>$page->createLink(),
        ));
    }

    public function searchAction()
    {

    }

    /**
     * [addcodeAction 添加云码]
     * @return [type] [description]
     */
    public function addcodeAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                $error = json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
                $this->view->setVars(array(
                    'error' => $error,
                ));
            }
            else
            {
                for ($i=0; $i<$this->_sanReq['num']; $i++)
               {
                    $code = \Mall\Utils\Inputs::createCode();
                    $type = $this->_sanReq['type'];
                    $starTime = strtotime($this->_sanReq['starTime']);
                    $endTime = strtotime($this->_sanReq['endTime']);
                    $goodsId = $this->_sanReq['gid'];
                    $value .= "('". $code ."', '". $type ."', '". $goodsId ."', '". $starTime ."', '". $endTime ."', '". $_SERVER['REQUEST_TIME'] ."'),";
                    $values = "VALUES ".$value;
               } 
                $this->code->batchInnserCode(trim($values,','));
                $this->log('云码添加成功');
                $this->response->redirect("codes/index");
            }
        }
    }

/**
 * [editeCodeAction 编辑]
 * @return [type] [description]
 */
    public function editeCodeAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {

            $res = $this->code->editeCodeInfo($this->_sanReq['status'],trim($this->_sanReq['id'],','));
            if($res)
            {
                $this->log('id为'. $this->_sanReq['id'] . '状态设置' .$this->_sanReq['status']);
                echo json_encode(array('ret' =>1));
            }
        }
        $this->view->disable();
    }
}