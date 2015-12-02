<?php

namespace Mall\Admin\Controllers;

use  Mall\Mdu\CategoryModule as Cate;


class CategoryController extends ControllerBase
{
    private $cate;

    public function initialize()
    {
        $this->cate = new Cate();
    }

    public function indexAction()
    {
        $list = $this->cate->getGoodCategory();
        $this->view->setVars(array(
            'list' => $list,
        ));
    }


    public function operateCateAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }
        else
        {
            $cateList = $this->cate->showCateList();
            if(!empty($this->_sanReq['cid']))
            {
                $cateInfo = $this->cate->getCateInfoByCateId($this->_sanReq['cid']);
                $this->view->setVars(array(
                    'cateInfo' => $cateInfo,
                ));
            }
            $this->view->setVars(array(
                'cateList' => $cateList
            ));
        }
    }

    public function editCateAction()
    {
        if($this->request->isPost() == true)
        {
            if(!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            else
            {
                //判断所选择的上级分类不能是当前分类或者当前分类的下级分类!暂用递归
                if($this->_sanReq['gcat_id'] == $this->_sanReq['cid'])
                {
                    echo json_encode(array(
                        'ret' => 0,
                        'msg'=> array(
                            'msg' => array(
                                'msg' => $this->di['sysconfig']['flagMsg']['10033']
                            )
                        )
                    ));
                    $this->view->disable();
                    return;
                }

                $tempPcid = $this->cate->getCatePidByCateid($this->_sanReq['gcat_id'])['gcat_parent_id'];
                $flag = 0;
                while($tempPcid)
                {
                    if($tempPcid == $this->_sanReq['cid'])
                    {
                        $flag = 1;
                        break;
                    }
                    $tempPcid = $this->cate->getCatePidByCateid($tempPcid)['gcat_parent_id'];
                }
                if($flag)
                {
                    echo json_encode(array(
                        'ret' => 0,
                        'msg'=> array(
                            'msg' => array(
                                'msg' => $this->di['sysconfig']['flagMsg']['10034']
                            )
                        )
                    ));
                    $this->view->disable();
                    return;
                }

                $res = $this->cate->editCategories($this->_sanReq['gcat_id'], $this->_sanReq['cid'], $this->_sanReq['name'],  $this->_sanReq['desc'], $this->_sanReq['keyword'], $this->_sanReq['order'], $this->_sanReq['show']);
                if($res == 1)
                {
                    echo json_encode(array('ret' => 1));
                    $this->log('商品分类(分类id: ' . $this->_sanReq['cid'] . ')编辑成功');
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

    public function delCateAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->cate->delCategories($this->_sanReq['cid']);
            if($res == 1)
            {
                $this->log('商品分类(分类id: ' . $this->_sanReq['cid'] . ')删除成功');
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

    public function addCateAction()
    {
        if ($this->request->isPost() == true)
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            else
            {
                $res = $this->cate->addCategories($this->_sanReq['gcat_id'], $this->_sanReq['name'], $this->_sanReq['desc'], $this->_sanReq['keyword'], $this->_sanReq['order'], $this->_sanReq['show']);
                if($res == 1)
                {
                    echo json_encode(array('ret' => 1));
                    $this->log('商品分类(商品名: ' . $this->_sanReq['name'] . ')添加成功');
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
}
