<?php

namespace Mall\Mdu;

class TagsModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const TAGS_NAME_EXIST = 10021;

    protected $tags;

    public function __construct()
    {
        $this->tags = $this->initModel('\Mall\Mdu\Models\TagsModel');
    }

    public function showTagsList($num, $offset, $conditions = [])
    {
        $fieldArr = '';
        if(!empty($conditions['tags_name']))
            $fieldArr ['tags_name'] = $conditions['tags_name'];

        return $this->tags->getAllTags($num,$offset,$fieldArr);
    }

    /**
     * [getTotalTags标签总数]
     * @return [type] [description]
     */
    public function getTotalTags($conditions = [])
    {
        $fieldArr = '';
        if(!empty($conditions['tags_name']))
            $fieldArr ['tags_name'] = $conditions['tags_name'];

        return $this->tags->getTotalTags($fieldArr)['total'];
    }

    public function editTags($tid, $name)
    {
        $ex=$this->tags->getTidByTname($name);
        if($ex)
            return self::TAGS_NAME_EXIST;
        else
        {
            if($this->tags->updateTags($tid, $name))
                return self::SUCCESS;
            else
                return 0;
        }
    }

/**
 * [delTags 删除标签同时删除商品表中包含此标签id的商品去掉此id,添加标签时候格式为标签名加一个半角,]
 * @param  [type] $tid [description]
 * @return [type]      [description]
 */
    public function delTags($tid)
    {
        $this->di['db']->begin();
        if($this->tags->deleteTags($tid))
        {
            //查找该标签在商品表被包含的所有商品ID
            $goodIds = $this->tags->getGoodsByTagId($tid);
            //删除包含此标签ID的商品标签属性
            if(!empty($goodIds[0]['ids']))
            {
                if(!$this->tags->UpdateGoodsByTagId($goodIds[0]['ids'], $tid))
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
            }
            $this->di['db']->commit();
            return self::SUCCESS;
        }
        else
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }
    }

    public function batchDelTags($tids)
    {
        $this->di['db']->begin();
        if($this->tags->deleteTagsByTids($tids))
        {
            //查找该标签在商品表被包含的所有商品ID
            $goodIds = $this->tags->getGoodsByTagIds($tids);

            //删除包含此标签ID的商品标签属性
            if(!empty($goodIds[0]['ids']))
            {
                if(!$this->tags->UpdateGoodsByTagIds($goodIds[0]['ids'], $tids))
                {
                    $this->di['db']->rollback();
                    return self::ERROR;
                }
            }
            $this->di['db']->commit();
            return self::SUCCESS;
        }
        else
        {
            $this->di['db']->rollback();
            return self::ERROR;
        }
    }
}
