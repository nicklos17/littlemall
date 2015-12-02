<?php

namespace Mall\Mall\Controllers;

use  Mall\Mdu\GoodsModule as Goods,
        Mall\Mdu\AttributesModule as Attr,
        Mall\Utils\RedisLib;

class GoodsController extends ControllerBase
{
    private $item;
    private $attr;
    private $redis;

    public function initialize()
    {
        $this->item = new Goods;
        $this->attr = new Attr;
        $this->redis = RedisLib::getRedis($this->di);
    }

    public function indexAction($id)
    {
        if(!preg_match("/^[1-9][0-9]*$/", $id))
             $this->response->redirect('public/error404');

        if(!empty($gInfo = $this->item->getGoodsSection($id)))
        {
            //源码是否有效
            $codeFlag = 0;
            if($codeInfo = $this->di['session']->get('codeInfo'))
            {
                $ids = explode(',', $codeInfo['yc_good_ids']);
                if(($codeInfo['yc_good_ids'] == 0 || in_array($id, $ids)) && !$codeInfo['yc_used_time'])
                {
                    $codeFlag = 1;
                }
            }
            //获取搜索组合属性商品
            $gaInfo = $this->item->getGoodAttrsById($gInfo['goods_id']);

            //组合属性缓存, 用于尺码和颜色过滤
            $tmpArr = [];
            foreach ($gaInfo as $val)
                $tmpArr['item:' . $gInfo['goods_id'] . ':' .$val['attrs_ids']]= $val;
            $this->redis->mset($tmpArr);

            $this->view->setVars(array(
                'gInfo' => $gInfo,
                'gAttrs' => $this->attr->goodAttributes(),
                'gaAttrs' => (new \Mall\Mdu\CartModule())->getGoodsAttrs($gInfo['goods_id']),
                'codeFlag' => $codeFlag
            ));
        }
        else
            $this->response->redirect('public/error404');
    }

    public function filterColAction()
    {
        if(!$this->validFlag)
            exit(json_encode($this->warnMsg));

        echo json_encode($this->item->filterCol($this->_sanReq['gid'], $this->_sanReq['filter-id'],
            $this->_sanReq['parent-other-id']));

        $this->view->disable();
        return;
    }

    public function filterSizeAction()
    {
        if(!$this->validFlag)
            exit(json_encode($this->warnMsg));

        echo json_encode($this->item->filterSize($this->_sanReq['gid'], $this->_sanReq['filter-id'],
            $this->_sanReq['parent-other-id']));

        $this->view->disable();
        return;
    }

}
