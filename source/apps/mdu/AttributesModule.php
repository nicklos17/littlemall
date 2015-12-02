<?php

namespace Mall\Mdu;

class AttributesModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const ATTR_NAME_EXIST = 10022;
    const SUB_ATTR_NAME_EXIST = 10023;

    protected $attr;

    public function __construct()
    {
        $this->attr = $this->initModel('\Mall\Mdu\Models\AttributesModel');
    }

    public function goodAttributes()
    {
        $attrs = $this->attr->getAllAttrs();
        $list = array();
        foreach ($attrs as $val)
        {
            if($val['attrs_parent_id'] == 0)
                $list[$val['attrs_id']] = $val;
            else
            {
                if($val['attrs_parent_id'] != 0)
                    $list[$val['attrs_parent_id']]['child'][$val['attrs_id']] = $val;
            }
        }
        return $list;
    }

    public function editAttributes($attrId, $name, $status, $rank)
    {
        if($this->attr->getIdByName($attrId, $name))
            return self::ATTR_NAME_EXIST;
        else
        {
            if($this->attr->setNameStatus($attrId, $name, $status, $rank))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    public function delAttributes($attrId)
    {
        if($this->attr->getIdByPid($attrId))
            return self::SUB_ATTR_NAME_EXIST;
        else
        {
            if($this->attr->delAttr($attrId))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    public function addAttributes($pAttrId, $name, $rank)
    {
        if($this->attr->getIdByPidName($pAttrId, $name))
            return self::ATTR_NAME_EXIST;
        else
        {
            if($this->attr->addAttributes($pAttrId, $name, $rank))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }
}