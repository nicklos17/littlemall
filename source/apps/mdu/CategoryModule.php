<?php

namespace Mall\Mdu;

class CategoryModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const CATE_NAME_EXIST = 10024;
    const SUB_CATE_NAME_EXIST = 10025;
    const CATE_ITEM_EXIST = 10035;

    protected $cate;

    public function __construct()
    {
        $this->cate = $this->initModel('\Mall\Mdu\Models\CategoryModel');
    }

    public function getGoodCategory()
    {
        $tcate = $this->cate->getTopCategory();
        $list = array();
        foreach ($tcate as $val)
        {
            $list[$val['gcat_id']]=$val;
        }
        $scate = $this->cate->getSubCategory();
        foreach ($scate as $key => $val)
        {
            if(!isset($list[$val['gcat_parent_id']]))
                $list[$val['gcat_parent_id']] = array();

            if(!isset($list[$val['gcat_parent_id']]['child']))
                $list[$val['gcat_parent_id']]['child']=array();

            $list[$val['gcat_parent_id']]['child'][$val['gcat_id']] = $val;
        }

        return $list;
    }

    public function getAllCate()
    {
        $tcate = $this->cate->getAllCate();
        $list = array();
        foreach ($tcate as $val)
        {
            $list[$val['gcat_id']]=$val['gcat_name'];
        }
        return $list;
    }

    public function editCategories($pCateId, $cateId, $name, $desc, $keyword, $sort, $show)
    {
        if($this->cate->getCateIdByPidCidName($pCateId, $name, $cateId))
            return self::CATE_NAME_EXIST;
        else
        {
            if($this->cate->updateCategories($pCateId, $cateId, $name, $desc, $keyword, $sort, $show))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    public function getCateInfoByCateId($cateId)
    {
        return $this->cate->getCateInfoById($cateId);
    }

    public function delCategories($cateId)
    {
        if($this->cate->getCateIdsByPid($cateId))
            return self::SUB_CATE_NAME_EXIST;
        else
        {
            //判断分类下是否有商品
            if($this->cate->getGoodsIdsByCateId($cateId))
                return self::CATE_ITEM_EXIST;

            if($this->cate->deleteCategories($cateId))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    public function getCatePidByCateid($cateid)
    {
        return $this->cate->getCatePidByCateid($cateid);
    }

    public function addCategories($pCateId, $name, $desc, $keyword, $order, $show)
    {
        if($this->cate->getCateIdByPidName($pCateId, $name))
            return self::CATE_NAME_EXIST;
        else
        {
            if($this->cate->addCategories($pCateId, $name, $desc, $keyword, $order, $show))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    /**
    * [_getCate 递归显示所属分类]
    * @param  [type]  $key  [description]
    * @param  [type]  $list [description]
    * @param  integer $num  [description]
    * @param  string  $flag [description]
    * @return [type]        [description]
    */
    private function _getCate($key, $list, $html, $num = 0, $flag = '&nbsp;&nbsp;&nbsp;&nbsp;')
    {
        $flags = '&nbsp;&nbsp;&nbsp;&nbsp;';
        for ($i = 0; $i < $num ; $i++)
        {
            $flags .= $flag;
        }
        foreach ($list[$key]['child'] as $key2 => $val) 
        {
            $html .= "<option value = '$key2'> $flags $val[gcat_name]</option>";
            if(isset($list[$key2]) && !empty($list[$key2]))
            {
                $num++;
                $html = $this->_getCate($key2, $list, $html, $num);
            }
        }

        return $html;
    }

    /**
     * [addCateViewAction 显示所有的分类]
     */
    public function showCateList()
    {
        $html = '<select name="gcat_id">';
        $list = $this->getGoodCategory();
        foreach ($list as $key => $val) 
        {
            if(isset($val['gcat_parent_id'] ) && $val['gcat_parent_id'] == 0)
            {
                $html .= "<option value = '$key'> $val[gcat_name]</option>";
                if(!empty($val['child']))
                {
                    foreach ($val['child'] as $key => $value)
                    {
                        $html .= "<option value = '$key'> &nbsp;&nbsp;&nbsp;&nbsp;$value[gcat_name]</option>";
                        if(isset($list[$key]) && !empty($list[$key]))
                        {
                            $html = $this->_getCate($key, $list, $html);
                        }
                    }
                }
            }
        }
        $html .= '</select>';
        return $html;
    }

}
