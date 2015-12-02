<?php

namespace Mall\Admin\Controllers;

use Mall\Mdu\ShippingPayModule as PayRule;
use Mall\Utils\Pagination as Page;

class ShippingController extends ControllerBase
{

    const NUM_PER_PAGE = 30;//每页显示数

    private $payRule;

    public function initialize()
    {
        $this->payRule = new PayRule();
    }

    /**
     * 快递费用规则列表
     */
    public function indexAction()
    {
        if(!$this->validFlag)
        {
            $page = new Page($this->payRule->getTotal(), self::NUM_PER_PAGE, 1);
        }
        else
        {
            $page = new Page($this->payRule->getTotal($this->_sanReq), self::NUM_PER_PAGE, isset($this->_sanReq['page']) ? $this->_sanReq['page'] : 1);
        }
        $this->view->setVars(
            array(
                'rules' => $this->payRule->rulesList($page->firstRow >= 0 ? $page->firstRow : 0, $page->listRows, $this->_sanReq),
                'page' => $page->createLink()
            )
        );
    }

    /**
     * [ajax]添加快递费用规则
     */
    public function addRuleAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->payRule->addRule($this->_sanReq['pro_id'], $this->_sanReq['city_id'], $this->_sanReq['dis_id'], $this->_sanReq['fee']);
            if($res == 1)
            {
                $this->log(
                    'pro_id: '.$this->_sanReq['pro_id'].', city_id: '.$this->_sanReq['city_id'].
                    ', dis_id: '.$this->_sanReq['dis_id'].', 费用: '.$this->_sanReq['fee'].' 添加成功');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]修改快递费用规则
     */
    public function editRuleAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->payRule->updateRule($this->_sanReq['pro_id'], $this->_sanReq['city_id'], $this->_sanReq['dis_id'], $this->_sanReq['fee']);
            if($res == 1)
            {
                $this->log(
                    '地区(pro_id: ' . $this->_sanReq['pro_id'].', city_id: ' . $this->_sanReq['city_id'] . ', dis_id: '.$this->_sanReq['dis_id']
                    . '), 费用: '.$this->_sanReq['fee'] . ' 编辑成功 ');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
                        'msg' => array(
                            'msg' => $this->di['sysconfig']['flagMsg'][$res]
                        )
                    )
                ));

        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax]删除快递费用规则
     */
    public function deleteRulesAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $ruleArr = array();
            $idArr = explode('|', rtrim($this->_sanReq['id_arr'], '|'));
            foreach($idArr as $key=>$ids)
            {
                $ruleArr[$key] = explode(',', $ids);
            }
            $res = $this->payRule->deleteRules($ruleArr);
            if($res == 1)
            {
                $this->log('地区{(pro_id, city_id, dis_id): (' . rtrim($this->_sanReq['id_arr'], '|') . ')} 删除成功');
                echo json_encode(array('ret' => 1));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => array(
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