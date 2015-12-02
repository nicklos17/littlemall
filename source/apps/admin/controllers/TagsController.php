<?php

namespace Mall\Admin\Controllers;

use  Mall\Mdu\TagsModule as Tags;


class TagsController extends ControllerBase
{
    const NUM_PER_PAGE = 20;//每页显示数

    private $tags;

    public function initialize()
    {
        $this->tags = new Tags();
    }

    public function indexAction()
    {
        $num = $this->tags->getTotalTags($this->_sanReq);
        if($num)
        {
            $page = new \Mall\Utils\Pagination($num, self::NUM_PER_PAGE,
            isset($this->_sanReq['page']) ? $this->_sanReq['page'] : 1);
            $list = $this->tags->showTagsList($page->firstRow >= 0 ? $page->firstRow : 0,
            $page->listRows, $this->_sanReq);
            $this->view->setVars(array(
                'tags' => $list,
                'page' =>$page->createLink(),
            ));
        }
        else
        {
            $this->view->setVars(array(
                'tags' => [],
                'page' => ''
            ));
        }

    }

    public function editTagsAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->tags->editTags($this->_sanReq['tid'], $this->_sanReq['name']);
            if($res == 1)
            {
                $this->log('标签(tags_id: ' . $this->_sanReq['tid'] . ',标签名:' . $this->_sanReq['name'] . ')编辑成功');
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

    public function delTagsAction()
    {
        if (!$this->validFlag)
        {
             echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->tags->delTags($this->_sanReq['tid']);
            if($res == 1)
            {
                echo json_encode(array('ret' => 1));
                $this->log('标签(tags_id: ' . $this->_sanReq['tid']. ')删除成功');
            }                else
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

    public function batchDelTagsAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            $res = $this->tags->batchDelTags(trim($this->_sanReq['tagsIds'], ','));
            if($res == 1)
            {
                echo json_encode(array('ret' => 1));
                $this->log('标签(tags_id: ' . $this->_sanReq['tagsIds'] . ')批量删除成功');
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
            $this->view->disable();
            return;
        }
    }
}
