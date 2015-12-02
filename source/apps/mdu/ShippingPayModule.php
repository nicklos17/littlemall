<?php

namespace Mall\Mdu;

class ShippingPayModule extends ModuleBase
{
    const SUCCESS = 1;
    const ERROR = 10000;
    const RULE_EXISTS = 10026;//[标识: 快递规则已存在]

    protected $payRule;

    public function __construct()
    {
        $this->payRule = $this->initModel('\Mall\Mdu\Models\ShippingPayModel');
    }

    /**
     * [ruleList 获取所有快递费用规则列表]
     *
     * @param int $offset
     * @param int $num
     * @param Array $conditions
     * @return Array
     */
    public function rulesList($offset, $num, $conditions = [])
    {
        return $this->payRule->getAllRules($offset, $num, $this->filterConditions($conditions));
    }

    /**
     * [addRule 添加快递费用规则]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param int $fee
     * @return boolean
     */
    public function addRule($proId, $cityId, $disId, $fee)
    {
        if($this->payRule->getRule($proId, $cityId, $disId))
        {
            return self::RULE_EXISTS;//快递费用规则已存在
        }
        else
        {
            if($this->payRule->addRule($proId, $cityId, $disId, $fee))
                return self::SUCCESS;
            else
                return self::ERROR;
        }
    }

    /**
     * [updateRule 更新快递费用规则]
     *
     * @param int $proId
     * @param int $cityId
     * @param int $disId
     * @param int $fee
     * @return boolean
     */
    public function updateRule($proId, $cityId, $disId, $fee)
    {
        if($this->payRule->updateRule($proId, $cityId, $disId, $fee))
            return self::SUCCESS;
        else
            return self::ERROR;
    }

    /**
     * [deleteRules 批量删除快递费用规则]
     *
     * @param Array $rulesArr
     * @return boolean
     */
    public function deleteRules($rulesArr)
    {
        if($this->payRule->deleteRules($rulesArr))
            return self::SUCCESS;
        else
            return self::ERROR;
    }

    /**
     * [getTotal 获取所有快递费用数]
     *
     * @param Array $conditions
     * @return integer
     */
    public function getTotal($conditions = [])
    {
        return $this->payRule->getTotalOfRules($this->filterConditions($conditions))['num'];
    }

    /**
     * [filterConditions 过滤搜索字段]
     *
     * @param Array $conditions
     * @return Array
     */
    protected function filterConditions($conditions)
    {
        $fieldArr = [];
        if(!empty($conditions['pro_id']))
            $fieldArr ['pro_id'] = intval($conditions['pro_id']);
        if(!empty($conditions['city_id']))
            $fieldArr ['city_id'] = intval($conditions['city_id']);
        if(!empty($conditions['dis_id']))
            $fieldArr ['dis_id'] = intval($conditions['dis_id']);
        if(!empty($conditions['shipping_pay']))
            $fieldArr ['shipping_pay'] = intval($conditions['shipping_pay']);

        return $fieldArr;
    }

    /**
     * [getShipPayByPro 根据省份获取快递费用规则]
     *
     * @param $proId
     * @return Array
     */
    public function getShipPayByPro($proId)
    {
        if($rule = $this->payRule->getRuleByPro($proId))
        {
            return $rule['shipping_pay'];
        }
        else
        {
            return 0;
        }
    }

}