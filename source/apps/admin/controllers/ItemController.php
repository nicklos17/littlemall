<?php

namespace Mall\Admin\Controllers;

use  Mall\Mdu\GoodsModule as Item;
use  Mall\Mdu\CategoryModule as Cate;
use  Mall\Mdu\AttributesModule as Attr;

class ItemController extends ControllerBase
{
    const NUM_PER_PAGE = 30;//每页显示数

    private $item ;

    public function initialize()
    {
        $this->item = new Item();
    }

    public function indexAction()
    {
        $num = $this->item->getTotalItem($this->_sanReq);
        if($num)
        {
            $page = new \Mall\Utils\Pagination($num, self::NUM_PER_PAGE,
            isset($this->_sanReq['page']) ? $this->_sanReq['page'] : 1);
            $list = $this->item->showItemList($page->firstRow >= 0 ? $page->firstRow : 0,
            $page->listRows, $this->_sanReq);
            $cate = (new \Mall\Mdu\CategoryModule())->getAllCate();
            $this->view->setVars(array(
                'list' => $list,
                'page' =>$page->createLink(),
                'cate' => $cate
            ));
        }
        else
        {
            $this->view->setVars(array(
                'list' => [],
                'page' => '',
            ));
        }


    }

    public function addItemAction()
    {
            $cate = new Cate();
            $cateList = $cate->showCateList();
            $attr = new Attr();
            $attrList = $attr->goodAttributes();
            $this->view->setVars(array(
                'cateList' => $cateList,
                'attrList' => $attrList
            ));
    }

    public function insertItemAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
                $this->view->disable();
                return;
            }
            $res = $this->item->addItem($this->_sanReq);
            if($res == 1)
            {
                echo json_encode(array('ret' => 1));
                $this->log('商品(商品名: ' . $this->_sanReq['goods_name'] . ')添加成功');
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

    public function delItemAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                 echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            $res = $this->item->delItem($this->_sanReq['goodsId']);
            if($res == 1)
            {
                $this->log('商品(商品id: ' . $this->_sanReq['goodsId'] . ')删除成功');
                echo json_encode(array(
                    'ret' => 1
                ));
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

    public function attrsAlterAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                 echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            $res = $this->item->attrsAlter($this->_sanReq['goods_id'], $this->_sanReq['attr_ids'], $this->_sanReq['g_attr_barcode'], $this->_sanReq['g_attr_stocks'], $this->_sanReq['g_attr_enable'], $this->_sanReq['g_attrs_nums']);

            if($res == 1)
                echo json_encode(array('ret' => 1));
            else
                echo json_encode(array('ret' => 0));

            $this->view->disable();
            return;
        }
    }

    /**
     * [batchDelGoodsAction ]
     * @return [type] [description]
     */
    public function batchDelGoodsAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                 echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            $res = $this->item->batchDelItem(explode(',', rtrim($this->_sanReq['goodsIds'], ',')));
            if($res)
            {
                $this->log('商品(商品id: ' . $this->_sanReq['goodsIds'] . ')批量删除成功');
                echo json_encode(array(
                    'ret' => 1
                ));
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

    public function edititemAction()
    {
        $gid = intval($this->_sanReq['gid']);
        if($gid === 0)
        {
            echo '错误的商品id';
            $this->view->disable();
            return;
        }
        $goodsInfo = $this->item->getGoodsInfo($gid);
        if($goodsInfo)
        {
            //获取商品标签名
            if(rtrim($goodsInfo['goods_tags'], ','))
                $goodsInfo['goods_tags'] = $this->item->getTagsNames(rtrim($goodsInfo['goods_tags'], ','))['names'];
            $cate = new Cate();
            $cateList = $cate->showCateList();
            $attr = new Attr();
            $attrList = $attr->goodAttributes();

            //获取商品组合属性列表
            $goodsAttrs = $this->item->getGoodAttrsById($goodsInfo['goods_id']);
            //获取商品所包含属性分类列表
            $goodsGaAttrs = $this->item->getGoodGaCate($goodsInfo['goods_id']);
            foreach ($goodsGaAttrs as $val)
            {
                $gaAttr[$val['attrs_id']] = $val['attrs_name'];
                $gaImg[$val['attrs_id']] = $val['attrs_img'];
            }

            foreach ($goodsAttrs as $key =>$gval)
            {
                $tmpIds = explode(',', $gval['attrs_ids']);
                $len = count ($tmpIds);
                $tmpNames = array();
                for ($i=0; $i < $len; $i++)
                {
                    array_push($tmpNames, $gaAttr[$tmpIds[$i]]);
                }
                $goodsAttrs[$key]['g_attr_names'] = implode('/', $tmpNames);
            }

            $this->view->setVars(array(
                'cateList' => $cateList,
                'attrList' => $attrList,
                'goodsInfo' => $goodsInfo,
                'goodsAttrs' => $goodsAttrs,
                'goodsGaAttrs' => json_encode($gaAttr),
                'gaAttrsImg' => json_encode($gaImg)
            ));
        }
        else
        {
            echo json_encode(array(
                'ret' => 0,
                'msg'=> array(
                    'msg' => array(
                        'msg' => $this->di['sysconfig']['flagMsg']['10036']
                    )
                )
            ));
            $this->view->disable();
            return;
        }
    }

    public function updateItemAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }

        if($this->request->isPost())
        {
            $res = $this->item->updateItem($this->_sanReq);
            if($res == 1)
            {
                $this->log('商品(商品id: ' . $this->_sanReq['goods_id'] . ')编辑成功');
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
            $this->view->disable();
            return;
        }
    }

    public function delGoodsImgAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }

        if ($this->request->isPost())
        {
            $res = $this->item->delGoodsImg($this->_sanReq['gid'], $this->_sanReq['img']);
            if($res == 1)
            {
                echo json_encode(array('ret' => 1));
                $this->log('商品(商品id: ' . $this->_sanReq['gid'] . ')删除图片成功');
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

    public function existGoodsNameAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }

        if ($this->request->isPost())
        {
            if(!$this->item->getGoodsIdByName($this->_sanReq['name']))
                echo json_encode(array('ret' => 1));
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg'=> array(
                        'name' => array(
                            'msg' => $this->di['sysconfig']['flagMsg']['10038']
                        )
                    )
                ));
            $this->view->disable();
            return;
        }
    }

    public function existGoodsSnAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }

        if ($this->request->isPost())
        {
            if(!$this->item->getGoodsIdBySn($this->_sanReq['sn']))
                echo json_encode(array('ret' => 1));
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg'=> array(
                        'name' => array(
                            'msg' => $this->di['sysconfig']['flagMsg']['10039']
                        )
                    )
                ));
            $this->view->disable();
            return;
        }
    }

    public function existGoodsNameEditAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }

        if ($this->request->isPost())
        {
            if(!$this->item->getGoodsIdByNameId($this->_sanReq['name'], $this->_sanReq['gid']))
                echo json_encode(array('ret' => 1));
            else
                echo json_encode(array('ret' => 0, 'msg' => array('service' => array('msg' => '商品名已存在'))));
            $this->view->disable();
            return;
        }
    }

    public function existGoodsSnEditAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            $this->view->disable();
            return;
        }

        if ($this->request->isPost())
        {
            if(!$this->item->getGoodsIdBySnId($this->_sanReq['sn'], $this->_sanReq['gid']))
                echo json_encode(array('ret' => 1));
            else
                echo json_encode(array('ret' => 0, 'msg' => array('service' => array('msg' => 'sn码已存在'))));
            $this->view->disable();
            return;
        }
    }

    //商品属性图片上传
    public function uploadAttrImgAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
                $this->view->disable();
                return;
            }

            list($width, $height) = getimagesize($_FILES['upfile']['tmp_name']);
            if($width < 100 || $height < 100)
            {
                echo json_encode(array(
                    'ret' => 0,
                    'msg'=> array(
                        'msg' => array(
                            'msg' => '图片长宽都不能小于100px'
                        )
                    )
                ));
                $this->view->disable();
                return;
            }

            $res = $this->item->setAttrImg($this->_sanReq['gid'], $this->_sanReq['aid'], $_FILES);
            if(is_array($res))
                echo json_encode(array('ret' => 1, 'img' => $res));
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
